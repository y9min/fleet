<?php

namespace App\Services;

use App\Model\Company;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Subscription;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StripeSubscriptionService
{
    protected $priceId;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create or retrieve Stripe customer for company
     */
    public function createCustomer(Company $company): ?string
    {
        try {
            // If customer already exists, verify it still exists in Stripe
            if ($company->stripe_customer_id) {
                if ($this->verifyCustomerExists($company->stripe_customer_id)) {
                    return $company->stripe_customer_id;
                } else {
                    // Customer was deleted, clear it and create new one
                    Log::warning('Stripe customer was deleted, recreating', [
                        'company_id' => $company->id,
                        'old_customer_id' => $company->stripe_customer_id,
                    ]);
                    $company->update(['stripe_customer_id' => null]);
                }
            }

            $customer = Customer::create([
                'name' => $company->name,
                'email' => $company->email,
                'metadata' => [
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                ],
            ]);

            $company->update(['stripe_customer_id' => $customer->id]);

            Log::info('Stripe customer created', [
                'company_id' => $company->id,
                'customer_id' => $customer->id,
            ]);

            return $customer->id;
        } catch (ApiErrorException $e) {
            Log::error('Failed to create Stripe customer', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Verify if a Stripe customer exists
     * 
     * @param string $customerId The Stripe customer ID
     * @return bool True if customer exists, false otherwise
     */
    public function verifyCustomerExists(string $customerId): bool
    {
        try {
            Customer::retrieve($customerId);
            return true;
        } catch (ApiErrorException $e) {
            // Check if error is "No such customer"
            if (strpos($e->getMessage(), 'No such customer') !== false || 
                strpos($e->getMessage(), 'does not exist') !== false) {
                Log::info('Stripe customer does not exist', [
                    'customer_id' => $customerId,
                ]);
                return false;
            }
            // For other errors, log and assume customer exists (to avoid false positives)
            Log::warning('Error verifying customer existence', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);
            return true; // Assume exists to avoid breaking flow
        } catch (\Exception $e) {
            Log::error('Unexpected error verifying customer', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);
            return true; // Assume exists to avoid breaking flow
        }
    }

    /**
     * Recover a deleted customer by recreating it
     * 
     * @param Company $company The company to recover customer for
     * @return string|null The new customer ID, or null if failed
     */
    public function recoverCustomer(Company $company): ?string
    {
        try {
            Log::info('Recovering deleted Stripe customer', [
                'company_id' => $company->id,
                'old_customer_id' => $company->stripe_customer_id,
            ]);

            // Clear the old customer ID
            $oldCustomerId = $company->stripe_customer_id;
            $company->update(['stripe_customer_id' => null]);

            // Create new customer
            $newCustomerId = $this->createCustomer($company);

            if ($newCustomerId) {
                Log::info('Stripe customer recovered successfully', [
                    'company_id' => $company->id,
                    'old_customer_id' => $oldCustomerId,
                    'new_customer_id' => $newCustomerId,
                ]);
            }

            return $newCustomerId;
        } catch (\Exception $e) {
            Log::error('Failed to recover Stripe customer', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create or retrieve the £7/month price
     */
    public function createPrice(): ?string
    {
        try {
            // Check if we have a price ID stored in config
            $storedPriceId = config('services.stripe.vehicle_price_id');
            
            if ($storedPriceId) {
                // Verify the price still exists
                try {
                    Price::retrieve($storedPriceId);
                    return $storedPriceId;
                } catch (ApiErrorException $e) {
                    // Price doesn't exist, create new one
                }
            }

            // Create new price
            $price = Price::create([
                'unit_amount' => 700, // £7.00 in pence
                'currency' => 'gbp',
                'recurring' => [
                    'interval' => 'month',
                ],
                'product_data' => [
                    'name' => 'PCO Flow - Vehicle Subscription',
                    // Stripe rejects product_data[description] on Price.create in newer API versions
                ],
            ]);

            Log::info('Stripe price created', ['price_id' => $price->id]);

            return $price->id;
        } catch (ApiErrorException $e) {
            Log::error('Failed to create Stripe price', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create Stripe subscription
     */
    public function createSubscription(string $customerId, int $vehicleCount, Company $company): ?array
    {
        try {
            // If subscription already exists in database, verify it exists in Stripe first
            if ($company->stripe_subscription_id) {
                // Verify subscription actually exists in Stripe
                try {
                    $existingSubscription = Subscription::retrieve($company->stripe_subscription_id);
                    // Subscription exists, update quantity if needed
                    Log::info('Subscription exists in Stripe, updating quantity', [
                        'subscription_id' => $company->stripe_subscription_id,
                        'current_status' => $existingSubscription->status,
                    ]);
                    return $this->updateSubscriptionQuantity($company->stripe_subscription_id, $vehicleCount, $company);
                } catch (ApiErrorException $e) {
                    // Subscription was deleted in Stripe, clear it and create new one
                    Log::warning('Stripe subscription was deleted, creating new one', [
                        'company_id' => $company->id,
                        'old_subscription_id' => $company->stripe_subscription_id,
                        'error' => $e->getMessage(),
                    ]);
                    $company->update([
                        'stripe_subscription_id' => null,
                        'stripe_subscription_item_id' => null,
                        'subscription_status' => null,
                    ]);
                    // Continue to create new subscription below
                }
            }

            $priceId = $this->createPrice();
            if (!$priceId) {
                Log::error('Cannot create subscription: price creation failed');
                return null;
            }

            // Calculate billing cycle anchor (last day of current month)
            $now = Carbon::now();
            $anchor = $now->copy()->endOfMonth()->timestamp;

            Log::info('Creating new Stripe subscription', [
                'company_id' => $company->id,
                'customer_id' => $customerId,
                'vehicle_count' => $vehicleCount,
            ]);

            $subscription = Subscription::create([
                'customer' => $customerId,
                'items' => [
                    [
                        'price' => $priceId,
                        'quantity' => $vehicleCount,
                    ],
                ],
                'billing_cycle_anchor' => $anchor,
                'proration_behavior' => 'create_prorations',
                'metadata' => [
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                ],
                // Subscription will be incomplete until payment method is added
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Get subscription item ID
            $subscriptionItemId = $subscription->items->data[0]->id ?? null;

            $company->update([
                'stripe_subscription_id' => $subscription->id,
                'stripe_subscription_item_id' => $subscriptionItemId,
                'subscription_status' => $subscription->status,
            ]);

            Log::info('Stripe subscription created', [
                'company_id' => $company->id,
                'subscription_id' => $subscription->id,
                'vehicle_count' => $vehicleCount,
                'status' => $subscription->status,
            ]);

            return [
                'subscription_id' => $subscription->id,
                'subscription_item_id' => $subscriptionItemId,
                'status' => $subscription->status,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Failed to create Stripe subscription', [
                'company_id' => $company->id,
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode(),
            ]);
            return null;
        }
    }

    /**
     * Update subscription quantity
     */
    public function updateSubscriptionQuantity(string $subscriptionId, int $newQuantity, Company $company): ?array
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);
            $subscriptionItemId = $subscription->items->data[0]->id ?? $company->stripe_subscription_item_id;

            if (!$subscriptionItemId) {
                Log::error('Cannot update subscription: no subscription item ID found');
                return null;
            }

            Subscription::update($subscriptionId, [
                'items' => [
                    [
                        'id' => $subscriptionItemId,
                        'quantity' => $newQuantity,
                    ],
                ],
                'proration_behavior' => 'create_prorations',
            ]);

            // Refresh subscription to get updated status
            $subscription = Subscription::retrieve($subscriptionId);

            $company->update([
                'subscription_status' => $subscription->status,
            ]);

            Log::info('Stripe subscription quantity updated', [
                'company_id' => $company->id,
                'subscription_id' => $subscriptionId,
                'new_quantity' => $newQuantity,
            ]);

            return [
                'subscription_id' => $subscriptionId,
                'quantity' => $newQuantity,
                'status' => $subscription->status,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Failed to update Stripe subscription quantity', [
                'company_id' => $company->id,
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create Stripe Billing Portal session
     */
    public function createBillingPortalSession(string $customerId, string $returnUrl): ?string
    {
        try {
            // Verify customer exists before creating session
            if (!$this->verifyCustomerExists($customerId)) {
                Log::error('Cannot create Billing Portal session: customer does not exist', [
                    'customer_id' => $customerId,
                ]);
                return null;
            }

            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $customerId,
                'return_url' => $returnUrl,
            ]);

            Log::info('Stripe Billing Portal session created', [
                'customer_id' => $customerId,
                'session_id' => $session->id,
            ]);

            return $session->url;
        } catch (ApiErrorException $e) {
            // Check if error is due to customer not existing
            if (strpos($e->getMessage(), 'No such customer') !== false || 
                strpos($e->getMessage(), 'does not exist') !== false) {
                Log::error('Cannot create Billing Portal session: customer does not exist', [
                    'customer_id' => $customerId,
                    'error' => $e->getMessage(),
                ]);
            } else {
                Log::error('Failed to create Stripe Billing Portal session', [
                    'customer_id' => $customerId,
                    'error' => $e->getMessage(),
                ]);
            }
            return null;
        }
    }

    /**
     * Get upcoming invoice
     */
    public function getUpcomingInvoice(string $customerId)
    {
        try {
            $invoices = \Stripe\Invoice::upcoming([
                'customer' => $customerId,
            ]);

            return $invoices;
        } catch (ApiErrorException $e) {
            Log::error('Failed to retrieve upcoming invoice', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Confirm payment intent for an incomplete subscription
     * 
     * @param string $subscriptionId The Stripe subscription ID
     * @param string $paymentMethodId The payment method ID to use for confirmation
     * @return bool True if confirmation was successful or already confirmed, false otherwise
     */
    public function confirmSubscriptionPaymentIntent(string $subscriptionId, string $paymentMethodId): bool
    {
        try {
            // Retrieve subscription with expanded latest invoice and payment intent
            $subscription = Subscription::retrieve($subscriptionId, [
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Check if subscription has a latest invoice with a payment intent
            if (!$subscription->latest_invoice || !$subscription->latest_invoice->payment_intent) {
                Log::info('Subscription has no payment intent to confirm', [
                    'subscription_id' => $subscriptionId,
                ]);
                return false;
            }

            $paymentIntent = $subscription->latest_invoice->payment_intent;
            
            // If payment intent is already succeeded or processing, no need to confirm
            if (in_array($paymentIntent->status, ['succeeded', 'processing', 'requires_capture'])) {
                Log::info('Payment intent already confirmed or processing', [
                    'subscription_id' => $subscriptionId,
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                ]);
                return true;
            }

            // Only confirm if payment intent requires a payment method
            if ($paymentIntent->status !== 'requires_payment_method') {
                Log::info('Payment intent in unexpected state', [
                    'subscription_id' => $subscriptionId,
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                ]);
                return false;
            }

            // Confirm the payment intent with the provided payment method
            $confirmedIntent = PaymentIntent::confirm($paymentIntent->id, [
                'payment_method' => $paymentMethodId,
            ]);

            Log::info('Payment intent confirmed for subscription', [
                'subscription_id' => $subscriptionId,
                'payment_intent_id' => $paymentIntent->id,
                'payment_method_id' => $paymentMethodId,
                'new_status' => $confirmedIntent->status,
            ]);

            return true;
        } catch (ApiErrorException $e) {
            Log::error('Failed to confirm subscription payment intent', [
                'subscription_id' => $subscriptionId,
                'payment_method_id' => $paymentMethodId,
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Unexpected error confirming subscription payment intent', [
                'subscription_id' => $subscriptionId,
                'payment_method_id' => $paymentMethodId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Check if subscription needs payment intent confirmation
     * 
     * @param string $subscriptionId The Stripe subscription ID
     * @return array|null Returns array with subscription_id, payment_intent_id, payment_method_id if confirmation needed, null otherwise
     */
    public function checkIfConfirmationNeeded(string $subscriptionId): ?array
    {
        try {
            // Retrieve subscription with expanded payment intent, customer, and payment method data
            $subscription = Subscription::retrieve($subscriptionId, [
                'expand' => [
                    'latest_invoice.payment_intent',
                    'default_payment_method',
                    'customer.invoice_settings.default_payment_method',
                ],
            ]);

            // Only check incomplete subscriptions
            if (!in_array($subscription->status, ['incomplete', 'incomplete_expired'])) {
                Log::info('Subscription is not incomplete, confirmation not needed', [
                    'subscription_id' => $subscriptionId,
                    'status' => $subscription->status,
                ]);
                return null;
            }

            // Check if there's a payment intent that needs confirmation
            if (!$subscription->latest_invoice || !$subscription->latest_invoice->payment_intent) {
                Log::info('Subscription has no payment intent', [
                    'subscription_id' => $subscriptionId,
                ]);
                return null;
            }

            $paymentIntent = $subscription->latest_invoice->payment_intent;
            
            // Only proceed if payment intent requires payment method
            if ($paymentIntent->status !== 'requires_payment_method') {
                Log::info('Payment intent does not require payment method', [
                    'subscription_id' => $subscriptionId,
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                ]);
                return null;
            }

            // Get payment method ID from subscription, customer invoice settings, or customer default
            $paymentMethodId = null;
            
            if ($subscription->default_payment_method) {
                $paymentMethodId = is_string($subscription->default_payment_method) 
                    ? $subscription->default_payment_method 
                    : $subscription->default_payment_method->id;
            } elseif ($subscription->customer && is_object($subscription->customer) && $subscription->customer->invoice_settings->default_payment_method ?? null) {
                $paymentMethodId = is_string($subscription->customer->invoice_settings->default_payment_method)
                    ? $subscription->customer->invoice_settings->default_payment_method
                    : $subscription->customer->invoice_settings->default_payment_method->id;
            } else {
                // Retrieve customer separately if not expanded
                try {
                    $customer = Customer::retrieve($subscription->customer, [
                        'expand' => ['invoice_settings.default_payment_method'],
                    ]);
                    
                    if ($customer->invoice_settings->default_payment_method ?? null) {
                        $paymentMethodId = is_string($customer->invoice_settings->default_payment_method)
                            ? $customer->invoice_settings->default_payment_method
                            : $customer->invoice_settings->default_payment_method->id;
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not retrieve customer to check payment method', [
                        'subscription_id' => $subscriptionId,
                        'customer_id' => $subscription->customer,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (!$paymentMethodId) {
                Log::info('No payment method found for incomplete subscription', [
                    'subscription_id' => $subscriptionId,
                ]);
                return null;
            }

            Log::info('Confirmation needed for subscription', [
                'subscription_id' => $subscriptionId,
                'payment_intent_id' => $paymentIntent->id,
                'payment_method_id' => $paymentMethodId,
            ]);

            return [
                'subscription_id' => $subscriptionId,
                'payment_intent_id' => $paymentIntent->id,
                'payment_method_id' => $paymentMethodId,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Error checking if confirmation needed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Unexpected error checking if confirmation needed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Ensure payment intent exists for incomplete subscription
     * Creates payment intent if subscription is incomplete and has no payment intent
     * 
     * @param string $subscriptionId The Stripe subscription ID
     * @return bool True if payment intent exists or was created, false otherwise
     */
    public function ensurePaymentIntentExists(string $subscriptionId): bool
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId, [
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Only process incomplete subscriptions
            if (!in_array($subscription->status, ['incomplete', 'incomplete_expired'])) {
                return true; // Subscription is not incomplete, no action needed
            }

            // Check if payment intent exists
            if ($subscription->latest_invoice && $subscription->latest_invoice->payment_intent) {
                Log::info('Payment intent already exists for subscription', [
                    'subscription_id' => $subscriptionId,
                    'payment_intent_id' => $subscription->latest_invoice->payment_intent->id,
                ]);
                return true;
            }

            // If no payment intent, we need to pay the invoice
            // For incomplete subscriptions, we can't create a payment intent directly
            // Instead, we need to update the subscription with a payment method
            // This will be handled by the confirmation process
            Log::info('Subscription has no payment intent, will be created during confirmation', [
                'subscription_id' => $subscriptionId,
            ]);

            return false;
        } catch (ApiErrorException $e) {
            Log::error('Error ensuring payment intent exists', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Unexpected error ensuring payment intent exists', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Sync subscription status from Stripe to database
     * 
     * @param string $subscriptionId The Stripe subscription ID
     * @param Company|null $company Optional company object, will be looked up if not provided
     * @return bool True if sync was successful, false otherwise
     */
    public function syncSubscriptionStatus(string $subscriptionId, ?Company $company = null): bool
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);

            // If company not provided, find it by customer ID
            if (!$company) {
                $company = Company::where('stripe_subscription_id', $subscriptionId)->first();
                if (!$company && $subscription->customer) {
                    $company = Company::where('stripe_customer_id', $subscription->customer)->first();
                }
            }

            if (!$company) {
                Log::warning('Cannot sync subscription status: company not found', [
                    'subscription_id' => $subscriptionId,
                ]);
                return false;
            }

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

            Log::info('Subscription status synced', [
                'company_id' => $company->id,
                'subscription_id' => $subscriptionId,
                'status' => $subscription->status,
            ]);

            return true;
        } catch (ApiErrorException $e) {
            Log::error('Error syncing subscription status', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Unexpected error syncing subscription status', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

