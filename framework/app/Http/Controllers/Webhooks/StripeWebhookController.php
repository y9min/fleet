<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Model\Company;
use App\Services\StripeSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;

class StripeWebhookController extends Controller
{
    protected $stripeService;

    public function __construct(StripeSubscriptionService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        // Verify webhook signature
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        try {
            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event->data->object);
                    break;

                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;

                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($event->data->object);
                    break;

                case 'payment_method.attached':
                    $this->handlePaymentMethodAttached($event->data->object);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'type' => $event->type,
                        'event_id' => $event->id,
                    ]);
            }

            return response()->json(['received' => true]);
        } catch (\Exception $e) {
            Log::error('Error handling Stripe webhook', [
                'event_type' => $event->type,
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Webhook handling failed'], 500);
        }
    }

    /**
     * Handle successful invoice payment
     */
    private function handleInvoicePaymentSucceeded($invoice)
    {
        try {
            $customerId = $invoice->customer;
            $subscriptionId = $invoice->subscription;

            if ($subscriptionId) {
                $company = Company::where('stripe_customer_id', $customerId)->first();

                if ($company) {
                    $company->update([
                        'subscription_status' => 'active',
                    ]);

                    Log::info('Invoice payment succeeded - subscription activated', [
                        'company_id' => $company->id,
                        'invoice_id' => $invoice->id,
                        'subscription_id' => $subscriptionId,
                        'amount' => $invoice->amount_paid / 100, // Convert from pence
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error handling invoice.payment_succeeded webhook', [
                'invoice_id' => $invoice->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle failed invoice payment
     */
    private function handleInvoicePaymentFailed($invoice)
    {
        try {
            $customerId = $invoice->customer;
            $subscriptionId = $invoice->subscription;

            if ($subscriptionId) {
                $company = Company::where('stripe_customer_id', $customerId)->first();

                if ($company) {
                    $company->update([
                        'subscription_status' => 'past_due',
                    ]);

                    Log::warning('Invoice payment failed', [
                        'company_id' => $company->id,
                        'invoice_id' => $invoice->id,
                        'subscription_id' => $subscriptionId,
                        'amount_due' => $invoice->amount_due / 100,
                    ]);

                    // TODO: Send notification email to company admin
                }
            }
        } catch (\Exception $e) {
            Log::error('Error handling invoice.payment_failed webhook', [
                'invoice_id' => $invoice->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle subscription updated
     */
    private function handleSubscriptionUpdated($subscription)
    {
        try {
            $customerId = $subscription->customer;
            $subscriptionId = $subscription->id;

            $company = Company::where('stripe_customer_id', $customerId)->first();

            if ($company) {
                // Update subscription item ID if it changed
                $subscriptionItemId = null;
                if (!empty($subscription->items->data)) {
                    $subscriptionItemId = $subscription->items->data[0]->id;
                }

                $company->update([
                    'stripe_subscription_id' => $subscriptionId,
                    'stripe_subscription_item_id' => $subscriptionItemId,
                    'subscription_status' => $subscription->status,
                ]);

                Log::info('Subscription updated', [
                    'company_id' => $company->id,
                    'subscription_id' => $subscriptionId,
                    'status' => $subscription->status,
                    'quantity' => $subscription->items->data[0]->quantity ?? 0,
                ]);

                // Check if subscription is incomplete and has a payment method
                // If so, attempt to confirm the payment intent
                if (in_array($subscription->status, ['incomplete', 'incomplete_expired'])) {
                    $this->attemptPaymentIntentConfirmation($subscriptionId, $customerId);
                    
                    // Sync subscription status after confirmation attempt
                    // This ensures we get the latest status even if webhook arrived before payment processed
                    $this->syncSubscriptionStatus($subscriptionId, $company);
                } else {
                    // For non-incomplete subscriptions, always sync status to ensure database is up to date
                    $this->syncSubscriptionStatus($subscriptionId, $company);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error handling customer.subscription.updated webhook', [
                'subscription_id' => $subscription->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sync subscription status from Stripe to database
     */
    private function syncSubscriptionStatus(string $subscriptionId, Company $company): void
    {
        try {
            $synced = $this->stripeService->syncSubscriptionStatus($subscriptionId, $company);
            if ($synced) {
                Log::info('Subscription status synced via webhook', [
                    'company_id' => $company->id,
                    'subscription_id' => $subscriptionId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error syncing subscription status in webhook', [
                'company_id' => $company->id ?? null,
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Attempt to confirm payment intent for incomplete subscription
     */
    private function attemptPaymentIntentConfirmation(string $subscriptionId, string $customerId): void
    {
        try {
            // Retrieve customer to get default payment method
            $customer = Customer::retrieve($customerId);
            
            // Get default payment method from subscription, customer, or invoice settings
            $paymentMethodId = null;
            
            // First, try to get the subscription with expanded data to check for default_payment_method
            $subscription = Subscription::retrieve($subscriptionId, [
                'expand' => ['latest_invoice.payment_intent'],
            ]);
            
            if ($subscription->default_payment_method) {
                $paymentMethodId = is_string($subscription->default_payment_method) 
                    ? $subscription->default_payment_method 
                    : $subscription->default_payment_method->id;
            } elseif ($customer->invoice_settings->default_payment_method) {
                $paymentMethodId = is_string($customer->invoice_settings->default_payment_method)
                    ? $customer->invoice_settings->default_payment_method
                    : $customer->invoice_settings->default_payment_method->id;
            } elseif ($customer->default_source) {
                // Fallback to default source if no payment method
                Log::info('Customer has default source but no payment method, skipping confirmation', [
                    'subscription_id' => $subscriptionId,
                    'customer_id' => $customerId,
                ]);
                return;
            }

            if (!$paymentMethodId) {
                Log::info('No payment method found for incomplete subscription', [
                    'subscription_id' => $subscriptionId,
                    'customer_id' => $customerId,
                ]);
                return;
            }

            // Attempt to confirm the payment intent
            $confirmed = $this->stripeService->confirmSubscriptionPaymentIntent($subscriptionId, $paymentMethodId);
            
            if ($confirmed) {
                Log::info('Payment intent confirmation attempted for incomplete subscription', [
                    'subscription_id' => $subscriptionId,
                    'customer_id' => $customerId,
                    'payment_method_id' => $paymentMethodId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error attempting payment intent confirmation', [
                'subscription_id' => $subscriptionId,
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle payment method attached
     */
    private function handlePaymentMethodAttached($paymentMethod)
    {
        try {
            $customerId = $paymentMethod->customer;
            $paymentMethodId = $paymentMethod->id;

            if (!$customerId) {
                Log::info('Payment method attached but no customer associated', [
                    'payment_method_id' => $paymentMethodId,
                ]);
                return;
            }

            $company = Company::where('stripe_customer_id', $customerId)->first();

            if (!$company) {
                Log::info('Payment method attached for customer with no associated company', [
                    'customer_id' => $customerId,
                    'payment_method_id' => $paymentMethodId,
                ]);
                return;
            }

            Log::info('Payment method attached', [
                'company_id' => $company->id,
                'customer_id' => $customerId,
                'payment_method_id' => $paymentMethodId,
            ]);

            // Find all incomplete subscriptions for this customer
            $subscriptions = Subscription::all([
                'customer' => $customerId,
                'status' => 'incomplete',
                'limit' => 100,
            ]);

            if (empty($subscriptions->data)) {
                // Also check incomplete_expired
                $subscriptions = Subscription::all([
                    'customer' => $customerId,
                    'status' => 'incomplete_expired',
                    'limit' => 100,
                ]);
            }

            foreach ($subscriptions->data as $subscription) {
                Log::info('Found incomplete subscription for payment method attachment', [
                    'company_id' => $company->id,
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status,
                ]);

                // Set the attached payment method as the subscription's default
                try {
                    Subscription::update($subscription->id, [
                        'default_payment_method' => $paymentMethodId,
                    ]);
                    Log::info('Set payment method as subscription default', [
                        'subscription_id' => $subscription->id,
                        'payment_method_id' => $paymentMethodId,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to set payment method as subscription default', [
                        'subscription_id' => $subscription->id,
                        'payment_method_id' => $paymentMethodId,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue anyway - might still work
                }

                // Ensure payment intent exists
                $this->stripeService->ensurePaymentIntentExists($subscription->id);

                // Confirm payment intent using the payment method ID directly from webhook
                $confirmed = $this->stripeService->confirmSubscriptionPaymentIntent($subscription->id, $paymentMethodId);
                
                if ($confirmed) {
                    Log::info('Payment intent confirmed for incomplete subscription', [
                        'subscription_id' => $subscription->id,
                        'payment_method_id' => $paymentMethodId,
                    ]);
                }

                // Sync subscription status after confirmation
                $this->syncSubscriptionStatus($subscription->id, $company);
            }
        } catch (\Exception $e) {
            Log::error('Error handling payment_method.attached webhook', [
                'payment_method_id' => $paymentMethod->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle subscription deleted
     */
    private function handleSubscriptionDeleted($subscription)
    {
        try {
            $customerId = $subscription->customer;
            $subscriptionId = $subscription->id;

            $company = Company::where('stripe_customer_id', $customerId)->first();

            if ($company) {
                $company->update([
                    'subscription_status' => 'canceled',
                ]);

                Log::info('Subscription deleted', [
                    'company_id' => $company->id,
                    'subscription_id' => $subscriptionId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling customer.subscription.deleted webhook', [
                'subscription_id' => $subscription->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle subscription created
     */
    private function handleSubscriptionCreated($subscription)
    {
        try {
            $customerId = $subscription->customer;
            $subscriptionId = $subscription->id;

            $company = Company::where('stripe_customer_id', $customerId)->first();

            if ($company) {
                // Update subscription item ID
                $subscriptionItemId = null;
                if (!empty($subscription->items->data)) {
                    $subscriptionItemId = $subscription->items->data[0]->id;
                }

                $company->update([
                    'stripe_subscription_id' => $subscriptionId,
                    'stripe_subscription_item_id' => $subscriptionItemId,
                    'subscription_status' => $subscription->status,
                ]);

                Log::info('Subscription created', [
                    'company_id' => $company->id,
                    'subscription_id' => $subscriptionId,
                    'status' => $subscription->status,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling customer.subscription.created webhook', [
                'subscription_id' => $subscription->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

