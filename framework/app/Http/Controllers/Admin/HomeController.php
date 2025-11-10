<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Model\Bookings;
use App\Model\Expense;
use App\Model\IncomeModel;
use App\Model\ReviewModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\Vendor;
use App\OnboardingDriver;
use App\Fine;
use Auth;
use DB;
use Hyvikk;
use Illuminate\Support\Facades\Redirect;
use App\Model\BookingAlert;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;


class HomeController extends Controller {
        public function export_calendar() {
                $bookings = Bookings::where('pickup', '!=', null)->where('dropoff', '!=', null)->get();
                $vCalendar = new \Eluceo\iCal\Component\Calendar("Fleet manager");
                foreach ($bookings as $booking) {
                        $vehicle = null;
                        if ($booking->vehicle_id != null) {
                                $vehicle = $booking->vehicle->make_name . " -" . $booking->vehicle->model_name . "-" . $booking->vehicle->license_plate;
                        }
                        $vEvent = new \Eluceo\iCal\Component\Event();
                        $vEvent
                                ->setDtStart(new \DateTime($booking->pickup))
                                ->setDtEnd(new \DateTime($booking->dropoff))
                                ->setNoTime(true)
                                ->setSummary($booking->customer->name)
                                ->setDescription("Customer: " . $booking->customer->name . "\nVehicle: " . $vehicle . "\nTravellers: " . $booking->travellers . "\nNote: " . $booking->note . "\nPickup Date & Time: " . date('d/m/Y g:i A', strtotime($booking->pickup)) . "\nDropoff Date & Time: " . date('d/m/Y g:i A', strtotime($booking->dropoff)) . "\nPickup Address: " . $booking->pickup_addr . "\nDestination Address: " . $booking->dest_addr);
                        $vCalendar->addComponent($vEvent);
                }
                $reminders = ServiceReminderModel::get();
                foreach ($reminders as $r) {
                        $interval = substr($r->services->overdue_unit, 0, -3);
                        $int = $r->services->overdue_time . $interval;
                        if ($r->last_meter == 0) {
                                $next_due = $r->vehicle->int_mileage + $r->services->overdue_meter . " " . Hyvikk::get('dis_format');
                        } else {
                                $next_due = $r->last_meter + $r->services->overdue_meter . " " . Hyvikk::get('dis_format');
                        }
                        $interval = $r->services->overdue_time . " " . $r->services->overdue_unit;
                        if ($r->services->overdue_meter != null) {
                                $interval .= $r->services->overdue_meter . " " . Hyvikk::get('dis_format');
                        }
                        $date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
                        $vEvent = new \Eluceo\iCal\Component\Event();
                        $vEvent
                                ->setDtStart(new \DateTime($date))
                                ->setDtEnd(new \DateTime($date))
                                ->setNoTime(true)
                                ->setSummary($r->services->description)
                                ->setDescription("Vehicle: " . $r->vehicle->make_name . "-" . $r->vehicle->model_name . "-" . $r->vehicle->license_plate . "\n Service Item: " . $r->services->description . "\n Next due(meter):" . $next_due . "\n Next due(date): " . $date . "\n Last performed: Date:" . $r->last_date . ", meter: $r->last_meter" . "\n Interval: " . $interval);
                        // ->setDescriptionHTML("<b>html text</b>");
                        $vCalendar->addComponent($vEvent);
                }
                header('Content-Type: text/calendar; charset=utf-8');
                header('Content-Disposition: attachment; filename="calendar.ics"');
                echo $vCalendar->render();
        }
        public function cal() {
                $vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');
                $vEvent = new \Eluceo\iCal\Component\Event();
                $vEvent
                        ->setDtStart(new \DateTime('2020-02-05'))
                        ->setDtEnd(new \DateTime('2020-02-05'))
                        ->setNoTime(true)
                        ->setSummary('testing1')
                ;
                $vEvent1 = new \Eluceo\iCal\Component\Event();
                $vEvent1
                        ->setDtStart(new \DateTime('2020-02-09'))
                        ->setDtEnd(new \DateTime('2020-02-09'))
                        ->setNoTime(true)
                        ->setSummary('testing2')
                ;
                $vCalendar->addComponent($vEvent);
                $vCalendar->addComponent($vEvent1);
                header('Content-Type: text/calendar; charset=utf-8');
                header('Content-Disposition: attachment; filename="cal.ics"');
                echo $vCalendar->render();
        }
        public function index()
    {
        if (Auth::user()->user_type == "C") {
            return redirect('customer/dashboard/');
        }

        $data['page_title'] = "Dashboard";
        $data['page_description'] = "Fleet Management Dashboard";
        $data['page_keywords'] = "fleet, management, dashboard";

        // Check if this is Yamz (Boss Admin with no company)
        $user = Auth::user();
        
        // Cache dashboard statistics for 30 minutes to dramatically improve performance
        $cacheKey = 'dashboard_stats_' . $user->id . '_' . ($user->company_id ?? 'null');
        
        $dashboardData = Cache::remember($cacheKey, 1800, function() use ($user) {
            return $this->loadDashboardStatistics($user);
        });
        
        // Merge cached data with standard data
        $data = array_merge($data, $dashboardData);
        
        // Always expose currency for dashboard cards
        $data['currency'] = Hyvikk::get('currency');
        
        return view('home', $data);
    }
    
    /**
     * Warm cache for dashboard statistics (called during login)
     */
    public function warmCache($user)
    {
        $cacheKey = 'dashboard_stats_' . $user->id . '_' . ($user->company_id ?? 'null');
        // Clear old cache to ensure fresh data
        Cache::forget($cacheKey);
        // Warm new cache
        return $this->loadDashboardStatistics($user);
    }
    
    /**
     * AJAX endpoint for loading dashboard statistics
     */
    public function getDashboardStats(Request $request)
    {
        $user = Auth::user();
        $cacheKey = 'dashboard_stats_' . $user->id . '_' . ($user->company_id ?? 'null');
        
        $data = Cache::remember($cacheKey, 1800, function() use ($user) {
            return $this->loadDashboardStatistics($user);
        });
        
        return response()->json($data);
    }
    
    /**
     * Load dashboard statistics - cached for 30 minutes
     */
    private function loadDashboardStatistics($user)
    {
        $data = [];
        if ($user->getRawOriginal('user_type') === 'B' && is_null($user->company_id) && $user->email === 'yamzahmed@hotmail.com') {
            // Yamz ONLY: Show snapshot of all users and companies
            $data['total_vehicles'] = \App\Model\VehicleModel::count();
            $data['total_drivers'] = \App\Model\User::where('user_type', 'D')->count();
            $data['total_customers'] = \App\Model\User::where('user_type', 'C')->count();
            $data['total_bookings'] = \App\Model\Bookings::count();
            $data['onboarding_pending'] = OnboardingDriver::submitted()->count();
            $data['onboarding_total'] = OnboardingDriver::count();
            $data['total_fines'] = Fine::where('status', '!=', 'paid')->count();
            $data['pending_fines'] = Fine::where('status', 'pending')->count();
            $data['total_inspections'] = \App\Model\VehicleReviewModel::count();
            $data['pending_inspections'] = \App\Model\VehicleModel::whereMeta('vehicle_status', 'Workshop')->count();
            
            // MOT statistics (all vehicles)
            $data['upcoming_mots'] = \App\Model\VehicleModel::with('metas')
                ->where(function($query) {
                    $query->whereHas('metas', function($q) {
                        $q->where('key', 'mot_expiry_date')->whereNotNull('value');
                    })
                    ->orWhereHas('metas', function($q) {
                        $q->where('key', 'exp_date')->whereNotNull('value');
                    })
                    ->orWhereNotNull('lic_exp_date');
                })
                ->get()
                ->filter(function($vehicle) {
                    $motExpiryDate = $vehicle->getMeta('mot_expiry_date') ?: 
                                   $vehicle->getMeta('exp_date') ?: 
                                   $vehicle->lic_exp_date;
                    
                    if (!$motExpiryDate) return false;
                    
                    try {
                        $expiryDate = \Carbon\Carbon::parse($motExpiryDate);
                        // Count MOTs expiring within next 30 days
                        return $expiryDate->isAfter(now()) && $expiryDate->isBefore(now()->addDays(30));
                    } catch (\Exception $e) {
                        return false;
                    }
                })
                ->count();
                
            // Add company and user statistics for Yamz
            $data['total_companies'] = \App\Model\Company::count();
            $data['total_super_admins'] = \App\Model\User::where('user_type', 'S')->count();
            $data['total_office_admins'] = \App\Model\User::where('user_type', 'O')->count();
            $data['total_boss_admins'] = \App\Model\User::where('user_type', 'B')->count();

            // Expected Revenue (global scope for Yamz)
            [$weeklyRevenue, $monthlyRevenue] = $this->calculateExpectedRevenue();
            $data['expected_weekly_revenue'] = $weeklyRevenue;
            $data['expected_monthly_revenue'] = $monthlyRevenue;
        } else {
            // All other users: Keep existing logic
            // Basic dashboard statistics (scoped by company)
            if (in_array($user->getRawOriginal('user_type'), ['S','O']) && !is_null($user->company_id)) {
                // Super/Office Admin: show only their company's data
                $data['total_vehicles'] = \App\Model\VehicleModel::where('company_id', $user->company_id)->count();
                $data['total_drivers'] = \App\Model\User::where('user_type', 'D')->where('company_id', $user->company_id)->count();
                $data['total_customers'] = \App\Model\User::where('user_type', 'C')->where('company_id', $user->company_id)->count();
                $data['total_bookings'] = \App\Model\Bookings::where('company_id', $user->company_id)->count();
                // Expected Revenue (company-scoped)
                [$weeklyRevenue, $monthlyRevenue] = $this->calculateExpectedRevenue($user->company_id);
                $data['expected_weekly_revenue'] = $weeklyRevenue;
                $data['expected_monthly_revenue'] = $monthlyRevenue;
            } elseif ($user->getRawOriginal('user_type') === 'B' && is_null($user->company_id)) {
                // Boss Admin with no company assigned: show zeros
                $data['total_vehicles'] = 0;
                $data['total_drivers'] = 0;
                $data['total_customers'] = 0;
                $data['total_bookings'] = 0;
                $data['expected_weekly_revenue'] = 0;
                $data['expected_monthly_revenue'] = 0;
            } else {
                // Fallback (e.g., drivers/customers): if they have a company, scope to it; else zero
                if (!is_null($user->company_id)) {
                    $data['total_vehicles'] = \App\Model\VehicleModel::where('company_id', $user->company_id)->count();
                    $data['total_drivers'] = \App\Model\User::where('user_type', 'D')->where('company_id', $user->company_id)->count();
                    $data['total_customers'] = \App\Model\User::where('user_type', 'C')->where('company_id', $user->company_id)->count();
                    $data['total_bookings'] = \App\Model\Bookings::where('company_id', $user->company_id)->count();
                    // Expected Revenue (company-scoped)
                    [$weeklyRevenue, $monthlyRevenue] = $this->calculateExpectedRevenue($user->company_id);
                    $data['expected_weekly_revenue'] = $weeklyRevenue;
                    $data['expected_monthly_revenue'] = $monthlyRevenue;
                } else {
                    $data['total_vehicles'] = 0;
                    $data['total_drivers'] = 0;
                    $data['total_customers'] = 0;
                    $data['total_bookings'] = 0;
                    $data['expected_weekly_revenue'] = 0;
                    $data['expected_monthly_revenue'] = 0;
                }
            }
            
            // Onboarding & Fines statistics (company-scoped)
            if (in_array($user->getRawOriginal('user_type'), ['S','O']) && !is_null($user->company_id)) {
                // Filter onboarding by company_id to match table filtering
                $data['onboarding_pending'] = OnboardingDriver::submitted()
                    ->where('company_id', $user->company_id)
                    ->count();
                $data['onboarding_total'] = OnboardingDriver::where('company_id', $user->company_id)->count();
                // Fines still scoped by company vehicles
                $companyVehicleIds = \App\Model\VehicleModel::where('company_id', $user->company_id)->pluck('id')->toArray();
                if (empty($companyVehicleIds)) {
                    $data['total_fines'] = 0;
                    $data['pending_fines'] = 0;
                } else {
                    $data['total_fines'] = Fine::where('status', '!=', 'paid')
                        ->whereIn('vehicle_id', $companyVehicleIds)->count();
                    $data['pending_fines'] = Fine::where('status', 'pending')
                        ->whereIn('vehicle_id', $companyVehicleIds)->count();
                }
            } elseif ($user->getRawOriginal('user_type') === 'B' && is_null($user->company_id)) {
                // Boss Admin with no company: show global onboarding counts
                $data['onboarding_pending'] = OnboardingDriver::submitted()->count();
                $data['onboarding_total'] = OnboardingDriver::count();
                // Keep fines behavior unchanged
                $data['total_fines'] = 0;
                $data['pending_fines'] = 0;
            } else {
                // For all other users, show global onboarding counts for accuracy
                $data['onboarding_pending'] = OnboardingDriver::submitted()->count();
                $data['onboarding_total'] = OnboardingDriver::count();
                if (!is_null($user->company_id)) {
                    $companyVehicleIds = \App\Model\VehicleModel::where('company_id', $user->company_id)->pluck('id')->toArray();
                    if (empty($companyVehicleIds)) {
                        $data['total_fines'] = 0;
                        $data['pending_fines'] = 0;
                    } else {
                        $data['total_fines'] = Fine::where('status', '!=', 'paid')
                            ->whereIn('vehicle_id', $companyVehicleIds)->count();
                        $data['pending_fines'] = Fine::where('status', 'pending')
                            ->whereIn('vehicle_id', $companyVehicleIds)->count();
                    }
                } else {
                    $data['total_fines'] = 0;
                    $data['pending_fines'] = 0;
                }
            }
            
            // Vehicle inspection statistics
            if ($user->group_id == null || $user->getRawOriginal('user_type') == "S") {
                // Company-scoped inspections for Super Admin
                if (!is_null($user->company_id)) {
                    $vehicle_ids_company = \App\Model\VehicleModel::where('company_id', $user->company_id)->pluck('id')->toArray();
                    if (empty($vehicle_ids_company)) {
                        $data['total_inspections'] = 0;
                    } else {
                        $data['total_inspections'] = \App\Model\VehicleReviewModel::whereIn('vehicle_id', $vehicle_ids_company)->count();
                    }
                    $data['pending_inspections'] = \App\Model\VehicleModel::where('company_id', $user->company_id)->whereMeta('vehicle_status', 'Workshop')->count();
                } else {
                    // No company: show zeros
                    $data['total_inspections'] = 0;
                    $data['pending_inspections'] = 0;
                }
                
                // MOT statistics (company-scoped)
                $data['upcoming_mots'] = \App\Model\VehicleModel::with('metas')
                    ->when(!is_null($user->company_id), function($q) use ($user) {
                        return $q->where('company_id', $user->company_id);
                    })
                    ->where(function($query) {
                        $query->whereHas('metas', function($q) {
                            $q->where('key', 'mot_expiry_date')->whereNotNull('value');
                        })
                        ->orWhereHas('metas', function($q) {
                            $q->where('key', 'exp_date')->whereNotNull('value');
                        })
                        ->orWhereNotNull('lic_exp_date');
                    })
                    ->get()
                    ->filter(function($vehicle) {
                        $motExpiryDate = $vehicle->getMeta('mot_expiry_date') ?: 
                                       $vehicle->getMeta('exp_date') ?: 
                                       $vehicle->lic_exp_date;
                        
                        if (!$motExpiryDate) return false;
                        
                        try {
                            $expiryDate = \Carbon\Carbon::parse($motExpiryDate);
                            // Count MOTs expiring within next 30 days
                            return $expiryDate->isAfter(now()) && $expiryDate->isBefore(now()->addDays(30));
                        } catch (\Exception $e) {
                            return false;
                        }
                    })
                    ->count();
            } else {
                // Group user - show only inspections for vehicles in their group
                $vehicle_ids = \App\Model\VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                if (empty($vehicle_ids)) {
                    $data['total_inspections'] = 0;
                } else {
                    $data['total_inspections'] = \App\Model\VehicleReviewModel::whereIn('vehicle_id', $vehicle_ids)->count();
                }
                $data['pending_inspections'] = \App\Model\VehicleModel::where('group_id', $user->group_id)
                    ->whereMeta('vehicle_status', 'Workshop')->count();
                
                // MOT statistics for group user
                $data['upcoming_mots'] = \App\Model\VehicleModel::where('group_id', $user->group_id)
                    ->with('metas')
                    ->where(function($query) {
                        $query->whereHas('metas', function($q) {
                            $q->where('key', 'mot_expiry_date')->whereNotNull('value');
                        })
                        ->orWhereHas('metas', function($q) {
                            $q->where('key', 'exp_date')->whereNotNull('value');
                        })
                        ->orWhereNotNull('lic_exp_date');
                    })
                    ->get()
                    ->filter(function($vehicle) {
                        $motExpiryDate = $vehicle->getMeta('mot_expiry_date') ?: 
                                       $vehicle->getMeta('exp_date') ?: 
                                       $vehicle->lic_exp_date;
                        
                        if (!$motExpiryDate) return false;
                        
                        try {
                            $expiryDate = \Carbon\Carbon::parse($motExpiryDate);
                            // Count MOTs expiring within next 30 days
                            return $expiryDate->isAfter(now()) && $expiryDate->isBefore(now()->addDays(30));
                        } catch (\Exception $e) {
                            return false;
                        }
                    })
                    ->count();
            }
        }
        
        return $data;
    }
    
    /**
     * Calculate expected weekly and monthly revenue across vehicles with status "Rented".
         * If $companyId is provided, scope to that company; otherwise include all vehicles.
         * Returns array [weeklyRevenue, monthlyRevenue].
         */
        private function calculateExpectedRevenue($companyId = null)
        {
                $query = VehicleModel::query()->with('metas');
                if (!is_null($companyId)) {
                        $query->where('company_id', $companyId);
                }
                $vehicles = $query->get();

                $weekly = 0.0;
                $monthly = 0.0;
                $weeksPerMonth = 4.0; // use 4 weeks per month for conversion

                foreach ($vehicles as $vehicle) {
                        $status = $vehicle->getMeta('vehicle_status') ?: 'Available';
                        if (strcasecmp($status, 'Rented') !== 0) {
                                continue;
                        }

                        // Price: support both 'vehicle_price' and legacy 'price'
                        $rawPrice = $vehicle->getMeta('vehicle_price');
                        if ($rawPrice === null || $rawPrice === '') {
                                $rawPrice = $vehicle->getMeta('price');
                        }
                        $price = is_numeric($rawPrice) ? (float)$rawPrice : 0.0;
                        if ($price <= 0) { continue; }

                        $period = strtolower((string)($vehicle->getMeta('price_period') ?: 'weekly'));
                        if ($period === 'monthly') {
                                $monthly += $price;
                                $weekly += $price / $weeksPerMonth;
                        } else { // default weekly
                                $weekly += $price;
                                $monthly += $price * $weeksPerMonth;
                        }
                }

                return [round($weekly, 2), round($monthly, 2)];
        }
        private function yearly_income($year) {
                if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
                        $all_vehicles = VehicleModel::get();
                } else {
                        $all_vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->get();
                }
                $vehicle_ids = array(0);
                foreach ($all_vehicles as $key) {
                        $vehicle_ids[] = $key->id;
                }
                $incomes = DB::select('select to_char(income_date, \'Month\') as mnth,sum(amount) as tot from income where extract(year from income_date)=? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by extract(month from income_date), to_char(income_date, \'Month\') order by extract(month from income_date)', [$year]);
                $months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
                $income2 = array();
                foreach ($incomes as $income) {
                        $income2[$income->mnth] = $income->tot;
                }
                $yr = array_merge($months, $income2);
                return implode(",", $yr);
        }
        private function yearly_expense($year) {
                if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
                        $all_vehicles = VehicleModel::get();
                } else {
                        $all_vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->get();
                }
                $vehicle_ids = array(0);
                foreach ($all_vehicles as $key) {
                        $vehicle_ids[] = $key->id;
                }
                $incomes = DB::select('select to_char(exp_date, \'Month\') as mnth,sum(amount) as tot from expense where extract(year from exp_date)=? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by extract(month from exp_date), to_char(exp_date, \'Month\') order by extract(month from exp_date)', [$year]);
                $months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
                $income2 = array();
                foreach ($incomes as $income) {
                        $income2[$income->mnth] = $income->tot;
                }
                $yr = array_merge($months, $income2);
                return implode(",", $yr);
        }
        public function test() {
                $start = '2019-09-05';
                $end = '2019-09-30';
                $exp = DB::select('select date,sum(amount) as tot from expense where  deleted_at is null and date between "' . $start . '" and "' . $end . '" group by date');
                $inc = DB::select('select date,sum(amount) as tot from income where  deleted_at is null and date between "' . $start . '" and "' . $end . '" group by date');
                $date1 = IncomeModel::whereBetween('date', [$start, $end])->pluck('date')->toArray();
                $date2 = Expense::whereBetween('date', [$start, $end])->pluck('date')->toArray();
                $all_dates = array_unique(array_merge($date1, $date2));
                $dates = array_count_values($all_dates);
                ksort($dates);
                $dates = array_slice($dates, -12, 12);
                $index['dates'] = $dates;
                $temp = array();
                foreach ($all_dates as $key) {
                        $temp[$key] = 0;
                }
                $income2 = array();
                foreach ($inc as $income) {
                        $income2[$income->date] = $income->tot;
                }
                $inc_data = array_merge($temp, $income2);
                ksort($inc_data);
                $index['incomes'] = implode(",", array_slice($inc_data, -12, 12));
                $expense2 = array();
                foreach ($exp as $e) {
                        $expense2[$e->date] = $e->tot;
                }
                $expenses = array_merge($temp, $expense2);
                ksort($expenses);
                $index['expenses1'] = implode(",", array_slice($expenses, -12, 12));
                dd($expenses, $inc_data, $dates);
                return view('chart', $index);
        }
        public function logout() {
                Auth::logout();
                return redirect('/');
        }
}