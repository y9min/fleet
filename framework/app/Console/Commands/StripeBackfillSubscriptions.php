<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Company;
use App\Model\VehicleModel;
use App\Services\StripeSubscriptionService;
use Illuminate\Support\Facades\Log;

class StripeBackfillSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stripe:backfill-subscriptions {--company=} {--dry-run}';

    /**
     * The console command description.
     */
    protected $description = 'Create or update Stripe subscriptions to match vehicle counts for companies';

    public function handle(): int
    {
        $companyId = $this->option('company');
        $dryRun = (bool) $this->option('dry-run');

        $query = Company::query();
        if ($companyId) {
            $query->where('id', $companyId);
        }

        $companies = $query->get();
        if ($companies->isEmpty()) {
            $this->warn('No companies found for backfill.');
            return Command::SUCCESS;
        }

        $svc = new StripeSubscriptionService();

        foreach ($companies as $company) {
            try {
                $vehicleCount = VehicleModel::where('company_id', $company->id)->count();
                $this->line("Company {$company->id} ({$company->name}) vehicles={$vehicleCount}");

                if ($dryRun) {
                    $this->info('[dry-run] Would ensure customer, then create/update subscription');
                    continue;
                }

                if (!$company->stripe_customer_id) {
                    $svc->createCustomer($company);
                    $company->refresh();
                }

                if (!$company->stripe_subscription_id) {
                    $svc->createSubscription($company->stripe_customer_id, $vehicleCount, $company);
                } else {
                    $svc->updateSubscriptionQuantity($company->stripe_subscription_id, $vehicleCount, $company);
                }

                $company->refresh();
                $this->info("Synced subscription: id={$company->stripe_subscription_id} status={$company->subscription_status}");
                Log::info('Stripe backfill sync complete', [
                    'company_id' => $company->id,
                    'vehicle_count' => $vehicleCount,
                    'subscription_id' => $company->stripe_subscription_id,
                    'status' => $company->subscription_status,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Stripe backfill failed', [
                    'company_id' => $company->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed for company {$company->id}: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}


