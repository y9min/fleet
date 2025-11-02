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
            // Ensure company has Stripe customer
            if (!$company->stripe_customer_id) {
                $customerId = $this->stripeService->createCustomer($company);
                if (!$customerId) {
                    Log::error('Cannot send payment email: Stripe customer creation failed', [
                        'company_id' => $company->id,
                    ]);
                    return false;
                }
            }

            // Generate Billing Portal link
            $returnUrl = route('admin.yamz.companies.show', $company->id);
            $portalUrl = $this->stripeService->createBillingPortalSession(
                $company->stripe_customer_id,
                $returnUrl
            );

            if (!$portalUrl) {
                Log::error('Cannot send payment email: Billing Portal session creation failed', [
                    'company_id' => $company->id,
                ]);
                return false;
            }

            // Get vehicle count for the email
            $vehicleCount = $company->vehicles()->count();
            $monthlyAmount = $vehicleCount * 7; // £7 per vehicle

            // Render email template
            $emailContent = View::make('emails.company-payment-setup', [
                'company' => $company,
                'superAdmin' => $superAdmin,
                'portalUrl' => $portalUrl,
                'vehicleCount' => $vehicleCount,
                'monthlyAmount' => $monthlyAmount,
            ])->render();

            // Send email via Resend
            $resend = Resend::client(env('RESEND_API_KEY'));

            $result = $resend->emails->send([
                'from' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                'to' => $superAdmin->email,
                'subject' => 'PCO Flow - Payment Setup Required for ' . $company->name,
                'html' => $emailContent,
            ]);

            Log::info('Payment setup email sent', [
                'company_id' => $company->id,
                'super_admin_email' => $superAdmin->email,
                'resend_id' => $result->id ?? null,
            ]);

            return true;
        } catch (ResendException $e) {
            Log::error('Resend API error when sending payment email', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to send payment setup email', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

