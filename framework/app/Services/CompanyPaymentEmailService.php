<?php

namespace App\Services;

use App\Model\Company;
use App\Model\User;
use App\Services\StripeSubscriptionService;
use Resend;
use Resend\Exceptions\ResendException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CompanyPaymentEmailService
{
    protected $stripeService;

    public function __construct(StripeSubscriptionService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Send payment setup email to company super admin
     */
    public function sendPaymentSetupEmail(Company $company, User $superAdmin): bool
    {
        try {
            // Check Resend API key
            $resendApiKey = env('RESEND_API_KEY');
            if (!$resendApiKey) {
                Log::error('Cannot send payment email: RESEND_API_KEY is not set in environment', [
                    'company_id' => $company->id,
                ]);
                throw new \Exception('RESEND_API_KEY environment variable is not set');
            }

            // Sender (payment emails only) - configurable via env
            $fromAddress = env('MAIL_FROM_PAYMENT', 'billing@pcoflow.com');
            $fromName = 'PCO Flow Team';
            $replyToAddress = 'support@pcoflow.com';
            if (!$fromAddress) {
                Log::error('Cannot send payment email: MAIL_FROM_ADDRESS is not set', [
                    'company_id' => $company->id,
                ]);
                throw new \Exception('MAIL_FROM_ADDRESS environment variable is not set');
            }

            // Ensure company has Stripe customer
            if (!$company->stripe_customer_id) {
                Log::info('Creating Stripe customer for payment email', [
                    'company_id' => $company->id,
                ]);
                $customerId = $this->stripeService->createCustomer($company);
                if (!$customerId) {
                    Log::error('Cannot send payment email: Stripe customer creation failed', [
                        'company_id' => $company->id,
                    ]);
                    throw new \Exception('Failed to create Stripe customer. Please check your Stripe API keys.');
                }
                // Refresh company to get updated stripe_customer_id
                $company->refresh();
            } else {
                // Verify customer exists in Stripe (may have been deleted)
                if (!$this->stripeService->verifyCustomerExists($company->stripe_customer_id)) {
                    Log::warning('Stripe customer was deleted, recovering', [
                        'company_id' => $company->id,
                        'customer_id' => $company->stripe_customer_id,
                    ]);
                    $customerId = $this->stripeService->recoverCustomer($company);
                    if (!$customerId) {
                        Log::error('Cannot send payment email: Failed to recover deleted Stripe customer', [
                            'company_id' => $company->id,
                        ]);
                        throw new \Exception('Stripe customer was deleted and could not be recovered. Please try again.');
                    }
                    // Refresh company to get updated stripe_customer_id
                    $company->refresh();
                }
            }

            // Generate billing portal token
            $token = Str::random(32);
            
            // Only update token if column exists (migration may not have run yet)
            try {
                if (Schema::hasColumn('companies', 'billing_portal_token')) {
                    $company->update(['billing_portal_token' => $token]);
                    Log::info('Billing portal token generated and stored for payment email', [
                        'company_id' => $company->id,
                        'customer_id' => $company->stripe_customer_id,
                        'token' => $token,
                    ]);
                } else {
                    Log::warning('Billing portal token column does not exist, token generated but not stored', [
                        'company_id' => $company->id,
                        'token' => $token,
                        'note' => 'Run migration: 2025_11_08_000001_add_billing_portal_token_to_companies_table.php',
                    ]);
                }
            } catch (\Exception $e) {
                // If token update fails, log but continue - token is still generated for email
                Log::warning('Failed to store billing portal token, but continuing with email send', [
                    'company_id' => $company->id,
                    'token' => $token,
                    'error' => $e->getMessage(),
                ]);
            }

            // Get vehicle count for the email
            $vehicleCount = $company->vehicles()->count();
            $monthlyAmount = $vehicleCount * 7; // £7 per vehicle

            // Render email template (HTML)
            Log::info('Rendering email template', [
                'company_id' => $company->id,
                'super_admin_email' => $superAdmin->email,
            ]);
            
            $emailContent = View::make('emails.company-payment-setup', [
                'company' => $company,
                'superAdmin' => $superAdmin,
                'vehicleCount' => $vehicleCount,
                'monthlyAmount' => $monthlyAmount,
                'token' => $token,
            ])->render();

            // Render plaintext alternative for deliverability
            $textContent = View::make('emails.company-payment-setup-text', [
                'company' => $company,
                'superAdmin' => $superAdmin,
                'vehicleCount' => $vehicleCount,
                'monthlyAmount' => $monthlyAmount,
                'token' => $token,
            ])->render();

            // Send email via Resend
            Log::info('Sending email via Resend', [
                'company_id' => $company->id,
                'to' => $superAdmin->email,
                'from' => $fromAddress,
            ]);

            $resend = Resend::client($resendApiKey);

            $result = $resend->emails->send([
                'from' => $fromName . ' <' . $fromAddress . '>',
                'to' => $superAdmin->email,
                'reply_to' => $replyToAddress,
                'subject' => 'Complete your setup on PCO Flow',
                'html' => $emailContent,
                'text' => $textContent,
            ]);

            Log::info('Payment setup email sent successfully', [
                'company_id' => $company->id,
                'super_admin_email' => $superAdmin->email,
                'resend_id' => $result->id ?? null,
            ]);

            return true;
        } catch (ResendException $e) {
            Log::error('Resend API error when sending payment email', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Resend API error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to send payment setup email', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e; // Re-throw to be caught by controller
        }
    }
}

