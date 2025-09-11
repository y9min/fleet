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
use Auth;
use DB;
use Hyvikk;
use Illuminate\Support\Facades\Redirect;
use App\Model\BookingAlert;


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

        // Basic dashboard statistics
        $data['total_vehicles'] = \App\Model\VehicleModel::count();
        $data['total_drivers'] = \App\Model\User::where('user_type', 'D')->count();
        $data['total_customers'] = \App\Model\User::where('user_type', 'C')->count();
        $data['total_bookings'] = \App\Model\Bookings::count();
        
        // Onboarding statistics
        $data['onboarding_pending'] = OnboardingDriver::submitted()->count();
        $data['onboarding_total'] = OnboardingDriver::count();

        return view('home', $data);
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
                return redirect('admin/');
        }
}