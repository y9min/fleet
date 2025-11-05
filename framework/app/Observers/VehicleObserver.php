<?php

namespace App\Observers;

use App\Model\VehicleModel;
use App\Model\Company;
use App\Services\StripeSubscriptionService;
use Illuminate\Support\Facades\Log;

class VehicleObserver
{
    public function created(VehicleModel $vehicle): void
    {
        $this->sync($vehicle->company_id, false);
    }

    public function deleted(VehicleModel $vehicle): void
    {
        $this->sync($vehicle->company_id, true);
    }

    protected function sync($companyId, bool $afterDelete): void
    {
        if (!$companyId) return;

        try {
            $company = Company::find($companyId);
            if (!$company) return;

            $svc = new StripeSubscriptionService();

            if (!$company->stripe_customer_id) {
                $svc->createCustomer($company);
                $company->refresh();
            }

            $count = VehicleModel::where('company_id', $companyId)->count();

            if (!$company->stripe_subscription_id) {
                $svc->createSubscription($company->stripe_customer_id, $count, $company);
            } else {
                $svc->updateSubscriptionQuantity($company->stripe_subscription_id, $count, $company);
            }

            Log::info('VehicleObserver Stripe sync', [
                'company_id' => $companyId,
                'count' => $count,
                'after_delete' => $afterDelete,
            ]);
        } catch (\Throwable $e) {
            Log::warning('VehicleObserver Stripe sync failed', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}


