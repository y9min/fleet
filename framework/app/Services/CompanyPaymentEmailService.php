<?php

namespace App\Services;

use App\Model\Company;
use App\Model\User;
use App\Services\StripeSubscriptionService;
use Resend;
use Resend\Exceptions\ResendException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

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

            // Check from address
            $fromAddress = env('MAIL_FROM_ADDRESS', 'noreply@pcoflow.com');
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
            }

            // Generate Billing Portal link
            $returnUrl = route('admin.yamz.companies.show', $company->id);
            Log::info('Creating Billing Portal session', [
                'company_id' => $company->id,
                'customer_id' => $company->stripe_customer_id,
            ]);
            
            $portalUrl = $this->stripeService->createBillingPortalSession(
                $company->stripe_customer_id,
                $returnUrl
            );

            if (!$portalUrl) {
                Log::error('Cannot send payment email: Billing Portal session creation failed', [
                    'company_id' => $company->id,
                ]);
                throw new \Exception('Failed to create Stripe Billing Portal session. Please check your Stripe configuration.');
            }

            // Get vehicle count for the email
            $vehicleCount = $company->vehicles()->count();
            $monthlyAmount = $vehicleCount * 7; // £7 per vehicle

            // Render email template
            Log::info('Rendering email template', [
                'company_id' => $company->id,
                'super_admin_email' => $superAdmin->email,
            ]);
            
            $emailContent = View::make('emails.company-payment-setup', [
                'company' => $company,
                'superAdmin' => $superAdmin,
                'portalUrl' => $portalUrl,
                'vehicleCount' => $vehicleCount,
                'monthlyAmount' => $monthlyAmount,
            ])->render();

            // Send email via Resend
            Log::info('Sending email via Resend', [
                'company_id' => $company->id,
                'to' => $superAdmin->email,
                'from' => $fromAddress,
            ]);

            $resend = Resend::client($resendApiKey);

            $result = $resend->emails->send([
                'from' => $fromAddress,
                'to' => $superAdmin->email,
                'subject' => 'PCO Flow - Payment Setup Required for ' . $company->name,
                'html' => $emailContent,
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

