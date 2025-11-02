<?php

namespace App\Services;

use App\Model\Company;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Subscription;
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
            // If customer already exists, return it
            if ($company->stripe_customer_id) {
                return $company->stripe_customer_id;
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
                    'description' => 'Monthly subscription per vehicle (£7/vehicle/month)',
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
            // If subscription already exists, return it
            if ($company->stripe_subscription_id) {
                // Update quantity if needed
                return $this->updateSubscriptionQuantity($company->stripe_subscription_id, $vehicleCount, $company);
            }

            $priceId = $this->createPrice();
            if (!$priceId) {
                Log::error('Cannot create subscription: price creation failed');
                return null;
            }

            // Calculate billing cycle anchor (last day of current month)
            $now = Carbon::now();
            $anchor = $now->copy()->endOfMonth()->timestamp;

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
            Log::error('Failed to create Stripe Billing Portal session', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);
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
}

