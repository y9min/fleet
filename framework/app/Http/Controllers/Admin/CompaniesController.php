<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\Company;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\Bookings;
use App\Services\StripeSubscriptionService;
use App\Services\CompanyPaymentEmailService;
use Illuminate\Support\Facades\Log;

class CompaniesController extends Controller {
    public function index() {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        // Aggregate counts per company
        $companies = Company::select('companies.*')
            ->withCount(['users as accounts_count' => function($q){}])
            ->withCount(['vehicles as vehicles_count' => function($q){}])
            ->withCount(['bookings as bookings_count' => function($q){}])
            ->orderBy('name')
            ->get();

        // Include a pseudo row for users without company (company_id null)
        $uncountedUsers = User::whereNull('company_id')->count();

        return view('admin.yamz.companies-index', compact('companies', 'uncountedUsers'));
    }

    public function show($companyId) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);

        $supers = User::where('user_type','S')->where('company_id',$company->id)->get();
        $offices = User::where('user_type','O')->where('company_id',$company->id)->get();
        $drivers = User::where('user_type','D')->where('company_id',$company->id)->get();

        $vehiclesCount = VehicleModel::where('company_id',$company->id)->count();
        $vehicles = VehicleModel::where('company_id',$company->id)->orderBy('id','desc')->limit(50)->get();

        // Check if payment intent confirmation is needed
        $confirmationNeeded = null;
        if ($company->stripe_subscription_id) {
            try {
                $stripeService = new StripeSubscriptionService();
                $confirmationNeeded = $stripeService->checkIfConfirmationNeeded($company->stripe_subscription_id);
            } catch (\Exception $e) {
                // Log error but don't break page load
                Log::error('Error checking if payment confirmation needed', [
                    'company_id' => $companyId,
                    'subscription_id' => $company->stripe_subscription_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('admin.yamz.company-show', compact('company','supers','offices','drivers','vehicles','vehiclesCount','confirmationNeeded'));
    }

    public function create() {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        return view('admin.yamz.company-create');
    }

    public function store(Request $request) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000'
        ]);

        // Create company with explicit boolean casting for PostgreSQL
        // Using raw SQL to ensure boolean is properly handled (PDO converts bools to ints)
        $companyId = \Illuminate\Support\Str::uuid()->toString();
        $now = now();
        
        DB::statement('
            INSERT INTO companies (id, name, email, phone, address, description, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, true, ?, ?)
        ', [
            $companyId,
            $request->name,
            $request->email ?? null,
            $request->phone ?? null,
            $request->address ?? null,
            $request->description ?? null,
            $now,
            $now,
        ]);
        
        $company = Company::findOrFail($companyId);

        // Create Stripe customer if company has super admin
        try {
            $superAdmin = User::where('company_id', $company->id)
                ->where('user_type', 'S')
                ->first();

            if ($superAdmin) {
                $stripeService = new StripeSubscriptionService();
                $stripeService->createCustomer($company);
            }
        } catch (\Exception $e) {
            // Don't fail company creation if Stripe fails
            Log::warning('Failed to create Stripe customer during company creation', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.yamz.companies')->with('success', 'Company created successfully!');
    }

    public function edit($companyId) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);
        return view('admin.yamz.company-edit', compact('company'));
    }

    public function update(Request $request, $companyId) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);

        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean'
        ]);

        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.yamz.companies')->with('success', 'Company updated successfully!');
    }

    public function destroy($companyId) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);
        
        // Check if company has users
        if ($company->users()->count() > 0) {
            return redirect()->route('admin.yamz.companies')->with('error', 'Cannot delete company with existing users. Please reassign users first.');
        }

        $company->delete();

        return redirect()->route('admin.yamz.companies')->with('success', 'Company deleted successfully!');
    }

    /**
     * Send payment setup email to company super admin
     */
    public function sendPaymentSetupEmail($companyId)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);

        // Find super admin for this company
        $superAdmin = User::where('company_id', $company->id)
            ->where('user_type', 'S')
            ->first();

        if (!$superAdmin) {
            return redirect()->route('admin.yamz.companies.show', $companyId)
                ->with('error', 'No super admin found for this company.');
        }

        try {
            $emailService = new CompanyPaymentEmailService(new StripeSubscriptionService());
            $sent = $emailService->sendPaymentSetupEmail($company, $superAdmin);

            if ($sent) {
                return redirect()->route('admin.yamz.companies.show', $companyId)
                    ->with('success', 'Payment setup email sent successfully to ' . $superAdmin->email . '!');
            } else {
                return redirect()->route('admin.yamz.companies.show', $companyId)
                    ->with('error', 'Failed to send payment setup email. Please check logs.');
            }
        } catch (\Exception $e) {
            Log::error('Error sending payment setup email', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Provide more specific error messages
            $errorMessage = 'An error occurred while sending the email.';
            if (strpos($e->getMessage(), 'RESEND_API_KEY') !== false) {
                $errorMessage = 'RESEND_API_KEY is not configured. Please check your environment variables.';
            } elseif (strpos($e->getMessage(), 'MAIL_FROM_ADDRESS') !== false) {
                $errorMessage = 'MAIL_FROM_ADDRESS is not configured. Please check your environment variables.';
            } elseif (strpos($e->getMessage(), 'Stripe') !== false) {
                $errorMessage = 'Stripe configuration error: ' . $e->getMessage();
            } else {
                $errorMessage = 'Error: ' . $e->getMessage();
            }

            return redirect()->route('admin.yamz.companies.show', $companyId)
                ->with('error', $errorMessage);
        }
    }

    /**
     * Admin action: Sync Stripe subscription to current vehicle count now
     */
    public function syncStripeSubscription($companyId)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);

        try {
            $svc = new StripeSubscriptionService();

            if (!$company->stripe_customer_id) {
                $customerId = $svc->createCustomer($company);
                if (!$customerId) {
                    return redirect()->route('admin.yamz.companies.show', $companyId)
                        ->with('error', 'Failed to create Stripe customer.');
                }
                $company->refresh();
            }

            $vehicleCount = VehicleModel::where('company_id', $company->id)->count();

            if (!$company->stripe_subscription_id) {
                $result = $svc->createSubscription($company->stripe_customer_id, $vehicleCount, $company);
                if (!$result) {
                    return redirect()->route('admin.yamz.companies.show', $companyId)
                        ->with('error', 'Failed to create Stripe subscription. Check logs for details.');
                }
                $company->refresh();
            } else {
                $result = $svc->updateSubscriptionQuantity($company->stripe_subscription_id, $vehicleCount, $company);
                if (!$result) {
                    return redirect()->route('admin.yamz.companies.show', $companyId)
                        ->with('error', 'Failed to update Stripe subscription. Subscription may have been deleted. Try syncing again.');
                }
            }

            return redirect()->route('admin.yamz.companies.show', $companyId)
                ->with('success', 'Stripe subscription synced. Vehicles: ' . $vehicleCount);
        } catch (\Throwable $e) {
            Log::error('Failed to sync Stripe subscription from admin action', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.yamz.companies.show', $companyId)
                ->with('error', 'Failed to sync Stripe subscription: ' . $e->getMessage());
        }
    }

    /**
     * Confirm payment intent for incomplete subscription
     */
    public function confirmPayment($companyId, Request $request)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $company = Company::findOrFail($companyId);

        if (!$company->stripe_subscription_id) {
            return response()->json(['success' => false, 'message' => 'No subscription found'], 400);
        }

        try {
            $stripeService = new StripeSubscriptionService();
            
            // Double-check if confirmation is still needed (race condition protection)
            $confirmationData = $stripeService->checkIfConfirmationNeeded($company->stripe_subscription_id);

            if (!$confirmationData) {
                // Payment intent may have already been confirmed by webhook
                // Refresh company to get latest status
                $company->refresh();
                return response()->json([
                    'success' => true,
                    'message' => 'Payment already confirmed or not needed',
                    'subscription_status' => $company->subscription_status,
                ]);
            }

            // Confirm the payment intent
            $confirmed = $stripeService->confirmSubscriptionPaymentIntent(
                $confirmationData['subscription_id'],
                $confirmationData['payment_method_id']
            );

            if ($confirmed) {
                // Refresh company to get updated subscription status
                $company->refresh();
                
                Log::info('Payment intent confirmed via user action', [
                    'company_id' => $companyId,
                    'subscription_id' => $confirmationData['subscription_id'],
                    'payment_method_id' => $confirmationData['payment_method_id'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully!',
                    'subscription_status' => $company->subscription_status,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to confirm payment. Please try again or wait for automatic confirmation.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error confirming payment', [
                'company_id' => $companyId,
                'subscription_id' => $company->stripe_subscription_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'An error occurred while confirming payment.';
            if (strpos($e->getMessage(), 'No such payment_intent') !== false) {
                $errorMessage = 'Payment intent not found. It may have already been processed.';
            } elseif (strpos($e->getMessage(), 'already been confirmed') !== false) {
                $errorMessage = 'Payment has already been confirmed.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 500);
        }
    }

    /**
     * Generate Billing Portal session on-demand
     * Creates a fresh session when user clicks the email link
     * Uses token-based authentication (no login required)
     */
    public function generateBillingPortal($token)
    {
        try {
            // Find company by token
            $company = Company::where('billing_portal_token', $token)->first();
            
            if (!$company) {
                Log::error('Invalid billing portal token', [
                    'token' => $token,
                ]);
                return view('admin.yamz.billing-portal-error', [
                    'message' => 'Invalid or expired link. Please request a new payment setup email.'
                ]);
            }

            $stripeService = new StripeSubscriptionService();

            // Ensure customer exists
            if (!$company->stripe_customer_id) {
                Log::info('Creating Stripe customer for billing portal', [
                    'company_id' => $company->id,
                ]);
                $customerId = $stripeService->createCustomer($company);
                if (!$customerId) {
                    Log::error('Failed to create Stripe customer for billing portal', [
                        'company_id' => $company->id,
                    ]);
                    return view('admin.yamz.billing-portal-error', [
                        'message' => 'Failed to create Stripe customer. Please contact support.'
                    ]);
                }
                // Refresh company to get updated stripe_customer_id
                $company->refresh();
            } else {
                // Verify customer exists in Stripe (may have been deleted)
                if (!$stripeService->verifyCustomerExists($company->stripe_customer_id)) {
                    Log::warning('Stripe customer was deleted, recovering for billing portal', [
                        'company_id' => $company->id,
                        'customer_id' => $company->stripe_customer_id,
                    ]);
                    $customerId = $stripeService->recoverCustomer($company);
                    if (!$customerId) {
                        Log::error('Failed to recover deleted Stripe customer for billing portal', [
                            'company_id' => $company->id,
                        ]);
                        return view('admin.yamz.billing-portal-error', [
                            'message' => 'Stripe customer was deleted and could not be recovered. Please contact support.'
                        ]);
                    }
                    // Refresh company to get updated stripe_customer_id
                    $company->refresh();
                }
            }

            // Ensure subscription exists (for existing companies with vehicles)
            if (!$company->stripe_subscription_id) {
                $vehicleCount = \App\Model\VehicleModel::where('company_id', $company->id)->count();
                if ($vehicleCount > 0) {
                    Log::info('Creating subscription for existing company with vehicles', [
                        'company_id' => $company->id,
                        'vehicle_count' => $vehicleCount,
                    ]);
                    $subscriptionResult = $stripeService->createSubscription($company->stripe_customer_id, $vehicleCount, $company);
                    if ($subscriptionResult) {
                        $company->refresh();
                    }
                }
            }

            // Create fresh Billing Portal session
            $returnUrl = url('/admin/yamz/billing-portal/' . $token);
            Log::info('Creating on-demand Billing Portal session', [
                'company_id' => $company->id,
                'customer_id' => $company->stripe_customer_id,
            ]);

            $portalUrl = $stripeService->createBillingPortalSession(
                $company->stripe_customer_id,
                $returnUrl
            );

            if (!$portalUrl) {
                Log::error('Failed to create Billing Portal session on-demand', [
                    'company_id' => $company->id,
                    'customer_id' => $company->stripe_customer_id,
                ]);
                return view('admin.yamz.billing-portal-error', [
                    'message' => 'Failed to create Billing Portal session. Please try again or contact support.'
                ]);
            }

            // Redirect to Billing Portal
            Log::info('Redirecting to Billing Portal', [
                'company_id' => $company->id,
                'portal_url' => $portalUrl,
            ]);
            return redirect($portalUrl);
        } catch (\Exception $e) {
            Log::error('Error generating Billing Portal session', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('admin.yamz.billing-portal-error', [
                'message' => 'An error occurred. Please contact support if this persists.'
            ]);
        }
    }
}
