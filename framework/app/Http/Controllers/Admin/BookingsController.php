<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Enums\BookingStatus;
use App\Http\Requests\BookingRequest;
use App\Mail\BookingCancelled;
use App\Mail\CustomerInvoice;
use App\Mail\DriverBooked;
use App\Mail\VehicleBooked;
use App\Utils\ResendEmailService;
use App\Model\Address;
use App\Model\BookingIncome;
use App\Model\BookingPaymentsModel;
use App\Model\Bookings;
use App\Model\Hyvikk;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\ReasonsModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use App\Model\Company;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Illuminate\Support\Facades\Http;
use App\Traits\NotificationTrait;

class BookingsController extends Controller {
	use NotificationTrait;
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Bookings add', ['only' => ['create']]);
		$this->middleware('permission:Bookings edit', ['only' => ['edit']]);
		$this->middleware('permission:Bookings delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Bookings list');
	}
	public function transactions() {
		$data['data'] = BookingPaymentsModel::orderBy('id', 'desc')->get();
		return view('bookings.transactions', $data);
	}
	public function transactions_fetch_data(Request $request) {
		if ($request->ajax()) {
			$date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
			$payments = BookingPaymentsModel::select('booking_payments.*')->with('booking.customer')->orderBy('id', 'desc');
			return DataTables::eloquent($payments)
				->addColumn('customer', function ($row) {
					return ($row->booking->customer->name) ?? "";
				})
				->editColumn('amount', function ($row) {
					return ($row->amount) ? Hyvikk::get('currency') . " " . $row->amount : "";
				})
				->editColumn('created_at', function ($row) use ($date_format_setting) {
					$created_at = '';
					$created_at = [
						'display' => '',
						'timestamp' => '',
					];
					if (!is_null($row->created_at)) {
						$created_at = date($date_format_setting . ' h:i A', strtotime($row->created_at));
						return [
							'display' => date($date_format_setting . ' h:i A', strtotime($row->created_at)),
							'timestamp' => Carbon::parse($row->created_at),
						];
					}
					return $created_at;
				})
				->filterColumn('created_at', function ($query, $keyword) {
					$query->whereRaw("TO_CHAR(created_at, 'DD-MM-YYYY HH12:MI AM') LIKE ?", ["%$keyword%"]);
				})
				->make(true);
		}
	}
	public function index() {
		try {
			\Log::info('[Invitations Index] Starting view render', [
				'user_id' => Auth::id(),
				'user_type' => Auth::user()->user_type ?? 'unknown'
			]);
			
			$data['types'] = IncCats::get();
			$data['reasons'] = ReasonsModel::get();
			
			// Pre-process notification data for JavaScript
			$data['pnotifyTitle'] = '';
			$data['pnotifyText'] = '';
			$data['showNotification'] = false;
			if(Session::has('msg')) {
				$msg = Session::get('msg');
				if(strpos($msg, 'Vehicle Pickup Invitation') !== false) {
					$data['pnotifyTitle'] = 'Vehicle Pickup Invitation Successfully Sent';
					$data['pnotifyText'] = 'An email has been sent to the driver with all pickup details.';
				} else {
					$data['pnotifyTitle'] = 'Success!';
					$data['pnotifyText'] = $msg;
				}
				$data['showNotification'] = true;
			}
			
			\Log::info('[Invitations Index] Data prepared, rendering view', [
				'types_count' => $data['types']->count(),
				'reasons_count' => $data['reasons']->count(),
				'has_notification' => !empty($data['pnotifyTitle'])
			]);
			
			return view("bookings.index", $data);
		} catch (\Throwable $e) {
			\Log::error('[Invitations Index] Error rendering view', [
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
				'file' => $e->getFile(),
				'line' => $e->getLine()
			]);
			throw $e;
		}
	}
	public function fetch_data(Request $request) {
		if ($request->ajax()) {
			$date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
			if (Auth::user()->user_type == "C") {
				$bookings = Bookings::where('customer_id', Auth::id())->latest();
			} elseif (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$bookings = Bookings::latest();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
				$bookings = Bookings::whereIn('vehicle_id', $vehicle_ids)->latest();
			}
            // Exclude collected (Ongoing) bookings from the index listing via meta
            $bookings->leftJoin('bookings_meta as ride_status_meta', function ($join) {
                $join->on('ride_status_meta.booking_id', '=', 'bookings.id')
                    ->where('ride_status_meta.key', '=', 'ride_status');
            })
            ->where(function($q){
                $q->whereNull('ride_status_meta.value')
                  ->orWhere('ride_status_meta.value','!=','Ongoing');
            })
            ->select('bookings.*')
                ->leftJoin('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
				->leftJoin('bookings_meta', function ($join) {
					$join->on('bookings_meta.booking_id', '=', 'bookings.id')
						->where('bookings_meta.key', '=', 'vehicle_typeid');
				})
				->leftJoin('vehicle_types', function ($join) {
					$join->on(DB::raw("CAST(bookings_meta.value AS uuid)"), '=', 'vehicle_types.id');
				})
				->with(['customer', 'driver', 'metas']);
			return DataTables::eloquent($bookings)
				->addColumn('customer', function ($row) {
					return ($row->customer->name) ?? "";
				})
				->addColumn('driver', function ($row) {
					// Check if this booking has an onboarding driver
					$onboardingDriverId = $row->getMeta('onboarding_driver_id');
					if ($onboardingDriverId) {
						$onboardingDriverName = $row->getMeta('onboarding_driver_name');
						return $onboardingDriverName;
					}
					return ($row->driver->name) ?? "";
				})
				->addColumn('travellers', function ($row) {
					return ($row->travellers) ?? "";
				})
				->addColumn('ride_status', function ($row) {
					return ($row->getMeta('ride_status')) ?? "";
				})
				->addColumn('return_booking', function ($row) {
					if($row->getMeta('return_flag') == 1)
					{
						$b=Bookings::where('id',$row->getMeta('parent_booking_id'))->first();
						if(isset($b))
						{
							return url('/assets/customer_dashboard/assets/img/return_way.svg');
						}
						else
						{
							return url('/assets/customer_dashboard/assets/img/one_way.svg');
						}
					}
					else
					{
						return url('/assets/customer_dashboard/assets/img/one_way.svg');
					}
				})
				->editColumn('pickup_addr', function ($row) {
					return str_replace(",", "<br/>", $row->pickup_addr);
				})
				->editColumn('dest_addr', function ($row) {
					// dd($row->dest_addr);
					return $row->dest_addr ? str_replace(",", "<br/>", $row->dest_addr) : '-';
				})
                ->editColumn('pickup_date', function ($row) use ($date_format_setting) {
                    $pickupDate = [
                        'display' => '',
                        'timestamp' => '',
                    ];
                    if (!is_null($row->pickup)) {
                        return [
                            'display' => date($date_format_setting, strtotime($row->pickup)),
                            'timestamp' => Carbon::parse($row->pickup),
                        ];
                    }
                    return $pickupDate;
                })
                ->editColumn('pickup_time', function ($row) {
                    $pickupTime = [
                        'display' => '',
                        'timestamp' => '',
                    ];
                    if (!is_null($row->pickup)) {
                        return [
                            'display' => date('h:i A', strtotime($row->pickup)),
                            'timestamp' => Carbon::parse($row->pickup),
                        ];
                    }
                    return $pickupTime;
                })
				->editColumn('dropoff', function ($row) use ($date_format_setting) {
					$dropoff = [
						'display' => '',
						'timestamp' => '',
					];
					if (!is_null($row->dropoff)) {
						$dropoff = date($date_format_setting . ' h:i A', strtotime($row->dropoff));
						return [
							'display' => date($date_format_setting . ' h:i A', strtotime($row->dropoff)),
							'timestamp' => Carbon::parse($row->dropoff),
						];
					}
					return $dropoff;
				})
				->editColumn('payment', function ($row) {
					if ($row->payment == 1) {
						return '<span class="text-success"> ' . __('fleet.paid1') . ' </span>';
					} else {
						return '<span class="text-warning"> ' . __('fleet.pending') . ' </span>';
					}
				})
				->editColumn('tax_total', function ($row) {
					return ($row->tax_total) ? Hyvikk::get('currency') . " " . $row->tax_total : "";
				})
				->addColumn('vehicle', function ($row) {
					$vehicle_type = VehicleTypeModel::find($row->getMeta('vehicle_typeid'));
					return !empty($row->vehicle_id) ? $row->vehicle->make_name . '-' . $row->vehicle->model_name . '-' . $row->vehicle->license_plate : ($vehicle_type->displayname) ?? "";
				})
				->filterColumn('vehicle', function ($query, $keyword) {
					$query->whereRaw("CONCAT(vehicles.make_name , '-' , vehicles.model_name , '-' , vehicles.license_plate) like ?", ["%$keyword%"])
						->orWhereRaw("(vehicle_types.display_name like ? and bookings.vehicle_id IS NULL)", ["%$keyword%"]);
					return $query;
				})
				->filterColumn('ride_status', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'ride_status');
						$q->whereRaw("value like ?", ["%{$keyword}%"]);
					});
					return $query;
				})
				->filterColumn('tax_total', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'tax_total');
						$q->whereRaw("value like ?", ["%{$keyword}%"]);
					});
					return $query;
				})
				->addColumn('action', function ($user) {
					return view('bookings.list-actions', ['row' => $user]);
				})
				->filterColumn('payment', function ($query, $keyword) {
					$query->whereRaw("CASE WHEN payment = 1 THEN '" . __('fleet.paid1') . "' ELSE '" . __('fleet.pending') . "' END LIKE ?", ["%{$keyword}%"]);
				})
				->filterColumn('pickup_date', function ($query, $keyword) {
                    $query->whereRaw("TO_CHAR(pickup, 'DD-MM-YYYY') LIKE ?", ["%$keyword%"]);
                })
                ->filterColumn('pickup_time', function ($query, $keyword) {
                    $query->whereRaw("TO_CHAR(pickup, 'HH12:MI AM') LIKE ?", ["%$keyword%"]);
                })
                ->orderColumn('pickup_date', function($query, $order) {
                    $query->orderBy('pickup', $order);
                })
                ->orderColumn('pickup_time', function($query, $order) {
                    $query->orderBy('pickup', $order);
                })
				->filterColumn('dropoff', function ($query, $keyword) {
					$query->whereRaw("TO_CHAR(dropoff, 'DD-MM-YYYY HH12:MI AM') LIKE ?", ["%$keyword%"]);
				})
				->filterColumn('travellers', function ($query, $keyword) {
					$query->where("travellers", 'LIKE', '%' . $keyword . '%');
				})
				->rawColumns(['payment', 'action', 'check', 'pickup_addr', 'dest_addr'])
				->make(true);
			//return datatables(User::all())->toJson();
		}
	}
	public function receipt($id) {
		$data['id'] = $id;
		$data['i'] = $book = BookingIncome::whereBooking_id($id)->first();
		// $data['info'] = IncomeModel::whereId($book['income_id'])->first();
		$data['booking'] = Bookings::find($id);
		return view("bookings.receipt", $data);
	}
	function print($id) {
		$data['i'] = $book = BookingIncome::whereBooking_id($id)->first();
		// $data['info'] = IncomeModel::whereId($book['income_id'])->first();
		$data['booking'] = Bookings::whereId($id)->get()->first();
		return view("bookings.print", $data);
	}
	public function payment($id) {
		$booking = Bookings::find($id);
		$booking->payment = 1;
		$booking->payment_method = "cash";
		$booking->save();
		BookingPaymentsModel::create(['method' => 'cash', 'booking_id' => $id, 'amount' => $booking->tax_total, 'payment_details' => null, 'transaction_id' => null, 'payment_status' => __('fleet.succeeded')]);
		return redirect()->route('invitations.index');
	}

	public function mark_collected($id) {
		$booking = Bookings::find($id);
		if ($booking) {
			$booking->ride_status = 'Ongoing';
			$booking->save();
		}
		return redirect()->route('bookings.calendar');
	}
	public function complete_post(Request $request) {
		// dd($request->all());
		if ($request->get('total') < 1) {
			return redirect()->back()->withErrors(["error" => "Invoice amount cannot be Zero or less than 0"]);
		}
		$booking = Bookings::find($request->get("booking_id"));
		$booking->setMeta([
			'customerId' => $request->get('customerId'),
			'vehicleId' => $request->get('vehicleId'),
			'day' => $request->get('day'),
			'mileage' => $request->get('mileage'),
			'waiting_time' => $request->get('waiting_time'),
			'date' => $request->get('date'),
			'total' => round($request->get('total'), 2),
			'total_kms' => $request->get('mileage'),
			'ride_status' => 'Ongoing',
			'tax_total' => round($request->get('tax_total'), 2),
			'total_tax_percent' => round($request->get('total_tax_charge'), 2),
			'total_tax_charge_rs' => round($request->total_tax_charge_rs, 2),
		]);
		if ($booking->driver && $booking->driver->driver_commision != null) {
			$commision = $booking->driver->driver_commision;
			$amnt = $commision;
			if ($booking->driver->driver_commision_type == 'percent') {
				$amnt = ($booking->total * $commision) / 100;
			}
			// $driver_amount = round($booking->total - $amnt, 2);
			$booking->driver_amount = $amnt;
			$booking->driver_commision = $booking->driver->driver_commision;
			$booking->driver_commision_type = $booking->driver->driver_commision_type;
			$booking->save();
		}
		$booking->save();
		$id = IncomeModel::create([
			"vehicle_id" => $request->get("vehicleId"),
			// "amount" => $request->get('total'),
			"amount" => $request->get('tax_total'),
			"driver_amount" => $booking->driver_amount ?? $request->get('tax_total'),
			"user_id" => $request->get("customerId"),
			"date" => $request->get('date'),
			"mileage" => $request->get("mileage"),
			"income_cat" => $request->get("income_type"),
			"income_id" => $booking->id,
			"tax_percent" => $request->get('total_tax_charge'),
			"tax_charge_rs" => $request->total_tax_charge_rs,
		])->id;
		BookingIncome::create(['booking_id' => $request->get("booking_id"), "income_id" => $id]);
		$xx = Bookings::whereId($request->get("booking_id"))->first();
		// $xx->status = 1;
		$xx->receipt = 1;
		$xx->save();
		if (Hyvikk::email_msg('email') == 1) {
			try {
			Mail::to($booking->customer->email)->send(new CustomerInvoice($booking));
			} catch (\Throwable $e) {
			}
		}
		return redirect()->route("invitations.index");
	}
    public function complete($id) {
        $xx = Bookings::find($id);
        $xx->status = BookingStatus::Completed;
		$xx->completed_at = date('Y-m-d H:i:s');
		$xx->ride_status = "Completed";
		$xx->save();
		return redirect()->route("invitations.index");
	}
	public function get_driver(Request $request) {
        //  dd($request->all());
        $from_date = $request->get("from_date");
        $to_date = $request->get("to_date");
		$driverInterval = Hyvikk::get('driver_interval').' MINUTE';
        $req_type = $request->get("req");
        if ($req_type == "new" || $request->req == 'true') {
				$q = "SELECT id, name AS text
			FROM users
			WHERE user_type = 'D'
			AND deleted_at IS NULL
			AND id NOT IN (
				SELECT DISTINCT driver_id
				FROM bookings
				WHERE deleted_at IS NULL
				AND cancellation = 0
				AND (
					(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
				)
			)
			AND id NOT IN (
				SELECT DISTINCT driver_id
				FROM bookings
				WHERE deleted_at IS NULL
				AND cancellation = 0
				AND (
					(pickup BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
					OR (dropoff BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
					OR (dropoff > '" . $to_date . "' AND pickup < DATE_ADD('" . $to_date . "', INTERVAL " . $driverInterval . "))
				)
			)";
            $new = [];
            $d = collect(DB::select($q));
            foreach ($d as $ro) {
				$d=User::where('id',$ro->id)->first();
				if(Hyvikk::api('api') == "1")
				{
					if($d && $d->getMeta('is_available') == '1')
					{
						$st="- (Online)";
					}
					else
					{
						$st="- (Offline)";
					}
				}
				else
				{
					$st="";
				}
                array_push($new, array("id" => $ro->id, "text" => $ro->text.$st));
            }
            $r['data'] = $new;
        } else {
            // dd('test');
            $id = $request->get("id");
            $current = Bookings::find($id);
			$b = Bookings::select("bookings.*")->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
			->where('bookings_meta.key', 'parent_booking_id')
			->where('bookings_meta.value',$current->id)->first();
			if(isset($b))
			{
				$q = "SELECT id, name AS text
			FROM users
			WHERE user_type = 'D'
			AND deleted_at IS NULL
			AND id NOT IN (
				SELECT DISTINCT driver_id
				FROM bookings
				WHERE deleted_at IS NULL
				AND cancellation = 0
				AND (
					(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
				)
			)
			AND id NOT IN (
				SELECT DISTINCT driver_id
				FROM bookings
				WHERE deleted_at IS NULL
				AND cancellation = 0
				AND (
					(pickup BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
					OR (dropoff BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
					OR (dropoff > '" . $to_date . "' AND pickup < DATE_ADD('" . $to_date . "', INTERVAL " . $driverInterval . "))
				)
				  AND driver_id <> '" . $current->driver_id . "' 
              		AND driver_id <> '" . $b->driver_id . "'
			)";
			}
			else
			{
				$q = "SELECT id, name AS text
				FROM users
				WHERE user_type = 'D'
				AND deleted_at IS NULL
				AND id NOT IN (
					SELECT DISTINCT driver_id
					FROM bookings
					WHERE deleted_at IS NULL
					AND cancellation = 0
					AND (
						(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
						OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
						OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					)
				)
				AND id NOT IN (
					SELECT DISTINCT driver_id
					FROM bookings
					WHERE deleted_at IS NULL
					AND cancellation = 0
					AND (
						(pickup BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
						OR (dropoff BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
						OR (dropoff > '" . $to_date . "' AND pickup < DATE_ADD('" . $to_date . "', INTERVAL " . $driverInterval . "))
					)
					AND driver_id <> '" . $current->driver_id . "'
				)";
			}
            $d = collect(DB::select($q));
            $chk = $d->where('id', $current->driver_id);
            $r['show_error'] = "yes";
            if (count($chk) > 0) {
                $r['show_error'] = "no";
            }
            $new = array();
            foreach ($d as $ro) {
				$d=User::where('id',$ro->id)->first();

				if(Hyvikk::api('api') == "1")
				{
					if($d && $d->getMeta('is_available') == '1')
					{
						$st="- (Online)";
					}
					else
					{
						$st="- (Ofline)";
					}
				}
				else
				{
					$st="";
				}
				

                if ($ro->id === $current->driver_id) {
                    array_push($new, array("id" => $ro->id, "text" => $ro->text.$st, 'selected' => true));
                } else {
                    array_push($new, array("id" => $ro->id, "text" => $ro->text.$st));
                }
            }
            $r['data'] = $new;
        }
        // dd($r);
        $new1 = [];
        foreach ($r['data'] as $r1) {
            $user = User::where('id', $r1['id'])->first();
            if ($user->getMeta('is_active') == 1) {
                // dd($r1);
                $new1[] = $r1;
            }
        }
        $r['data'] = $new1;
        return $r;
    }


public function get_vehicle(Request $request) {
    $from_date = $request->get("from_date");
    $to_date = $request->get("to_date");
    $req_type = $request->get("req");
    $vehicleInterval = Hyvikk::get('vehicle_interval').' MINUTE';
    if ($req_type == "new") {
        $xy = array();
        if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
            $q = "SELECT id
            FROM vehicles
            WHERE in_service = true
            AND deleted_at IS NULL
            AND id NOT IN (
                SELECT DISTINCT vehicle_id
                FROM bookings
                WHERE deleted_at IS NULL
                AND cancellation = 0
                AND (
                    (dropoff BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                    OR pickup BETWEEN '" . $from_date . "' AND '" . $to_date . "')
                    OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
                )
            )";
        } else {
            $q = "SELECT id
            FROM vehicles
            WHERE in_service = true
            AND deleted_at IS NULL
            AND group_id = " . Auth::user()->group_id . "
            AND id NOT IN (
                SELECT DISTINCT vehicle_id
                FROM bookings
                WHERE deleted_at IS NULL
                AND cancellation = 0
                AND (
                    (dropoff BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                    OR pickup BETWEEN '" . $from_date . "' AND '" . $to_date . "')
                    OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
                )
            )";
        }
        $d = collect(DB::select($q));
        //$new = array();

		$groupedVehicles = [];

        foreach ($d as $ro) {
            $vhc = VehicleModel::find($ro->id);

			if(isset($vhc->type_id))
			{
				$vt=VehicleTypeModel::where('id',$vhc->type_id)->first();
				if(isset($vt))
				{
					$vt_name=$vt->vehicletype;
				}
				else
				{
					$vt_name="Other";
				}
			}
				
			
           
            if(Hyvikk::get('fare_mode') == "price_wise")
            {
                if($vhc && $vhc->getMeta('price') != 0 )
                {
                    $text = ($vhc->make_name??'-') . " - " . ($vhc->model_name??'-') . " - " . ($vhc->license_plate??'-');
                    // array_push($new, array("id" => $ro->id, "text" => $text));

					   $groupedVehicles[$vt_name][] = ["id" => $ro->id, "text" => $text];
                }
            }
           else if(Hyvikk::get('fare_mode') == "type_wise")
           {
                $text = ($vhc->make_name??'-') . " - " . ($vhc->model_name??'-') . " - " . ($vhc->license_plate??'-');
                // array_push($new, array("id" => $ro->id, "text" => $text));

				$groupedVehicles[$vt_name][] = ["id" => $ro->id, "text" => $text];
            }
        }
       // Final formatting for Select2 optgroups
		$new = [];
		foreach ($groupedVehicles as $type => $vehicles) {
			$new[] = [
				"text" => $type,       // optgroup label
				"children" => $vehicles // options under optgroup
			];
		}
		$r['data'] = $new;
        return $r;
    } else {

        $id = $request->get("id");
        $current = Bookings::find($id);


        if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
            $b = Bookings::select("bookings.*")->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
            ->where('bookings_meta.key', 'parent_booking_id')
            ->where('bookings_meta.value',$id)->first();
            if(isset($b))
            {
                $from=$request->get("from_date");
                $to=$request->get("to_date");
                $q = "SELECT id
                FROM vehicles
                WHERE in_service = true
				AND deleted_at IS NULL
                AND id NOT IN (
                    SELECT DISTINCT vehicle_id
                    FROM bookings
                    WHERE id != $id and  id != $b->id
                    AND deleted_at IS NULL
                    AND cancellation = 0
                    AND (
                        (dropoff BETWEEN DATE_ADD('" . $from . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to . "', INTERVAL " . $vehicleInterval . "))
                        OR (pickup BETWEEN DATE_ADD('" . $from . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to . "', INTERVAL " . $vehicleInterval . "))
                        OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to . "')
                    )
                )";

            }
            else
            {
                $q = "SELECT id
                FROM vehicles
                WHERE in_service = true
				AND deleted_at IS NULL
                AND id NOT IN (
                    SELECT DISTINCT vehicle_id
                    FROM bookings
                    WHERE id != $id
                    AND deleted_at IS NULL
                    AND cancellation = 0
                    AND (
                        (dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
                        OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
                        OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
                    )
                )";
            }
        } else {
            $b = Bookings::select("bookings.*")->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
            ->where('bookings_meta.key', 'parent_booking_id')
            ->where('bookings_meta.value',$id)->first();
            if(isset($b))
            {
                $from1=$request->get("from_date");
                $to1=$request->get("to_date");
                $q = "SELECT id
                FROM vehicles
                WHERE in_service = true
                AND group_id = " . Auth::user()->group_id . "
                AND id NOT IN (
                    SELECT DISTINCT vehicle_id
                    FROM bookings
                    WHERE id != $id and  id != $b->id
                    AND deleted_at IS NULL
                    AND cancellation = 0
                    AND (
                        (dropoff BETWEEN DATE_ADD('" . $from1 . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to1 . "', INTERVAL " . $vehicleInterval . "))
                        OR (pickup BETWEEN DATE_ADD('" . $from1 . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to1 . "', INTERVAL " . $vehicleInterval . "))
                        OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from1 . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to1 . "')
                    )
                )";


				
            }
            else
            {
                $q = "SELECT id
                FROM vehicles
                WHERE in_service = true
                AND group_id = " . Auth::user()->group_id . "
                AND id NOT IN (
                    SELECT DISTINCT vehicle_id
                    FROM bookings
                    WHERE id != $id
                    AND deleted_at IS NULL
                    AND cancellation = 0
                    AND (
                        (dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
                        OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
                        OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
                    )
                )";

				
            }
        }
        $d = collect(DB::select($q));

		

        $chk = $d->where('id', $current->vehicle_id);
        $r['show_error'] = "yes";
        if (count($chk) > 0) {
            $r['show_error'] = "no";
        }
        //$new = array();

		$groupedVehicles = [];

        foreach ($d as $ro) {
            $vhc = VehicleModel::find($ro->id);

			if(isset($vhc->type_id))
			{
				$vt=VehicleTypeModel::where('id',$vhc->type_id)->first();
				if(isset($vt))
				{
					$vt_name=$vt->vehicletype;
				}
				else
				{
					$vt_name="Other";
				}
			}

            if(Hyvikk::get('fare_mode') == "price_wise")
            {
                if($vhc && $vhc->getMeta('price') != 0 )
                {
                    $text = ($vhc->make_name??'-') . " - " . ($vhc->model_name??'-') . " - " . ($vhc->license_plate??'-');
                    if ($ro->id == $current->vehicle_id)
                    {
                        // array_push($new, array("id" => $ro->id, "text" => $text, "selected" => true));

						$groupedVehicles[$vt_name][] = ["id" => $ro->id, "text" => $text, "selected" => true];
                    }
                    else
                    {
						

                        $groupedVehicles[$vt_name][] = ["id" => $ro->id, "text" => $text];
                    }
                }
            }
           else if(Hyvikk::get('fare_mode') == "type_wise")
           {
                $text = ($vhc->make_name??'-') . " - " . ($vhc->model_name??'-') . " - " . ($vhc->license_plate??'-');
                if ($ro->id == $current->vehicle_id)
                {
                    // array_push($new, array("id" => $ro->id, "text" => $text, "selected" => true));

					$groupedVehicles[$vt_name][] = ["id" => $ro->id, "text" => $text, "selected" => true];
                }
                else
                {
                    // array_push($new, array("id" => $ro->id, "text" => $text));

					$groupedVehicles[$vt_name][] = ["id" => $ro->id, "text" => $text];
                }
            }
        }
        $new = [];
		foreach ($groupedVehicles as $type => $vehicles) {
			$new[] = [
				"text" => $type,       // optgroup label
				"children" => $vehicles // options under optgroup
			];
		}
		$r['data'] = $new;
        return $r;
    }
}


public function assign_driver($id)
{	
	$data=Bookings::find($id);
	$v_model=null;
	$v_type=null;
	if(isset($data->vehicle_id))
	{
		 $v_model = VehicleModel::where('id', $data->vehicle_id)->first();
		if ($v_model) { // Check if a model was found
			$v_type = VehicleTypeModel::where('id', $v_model->type_id)->first();
		}
	}
	$ba=Bookings::select("bookings.*")->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
              ->where('bookings_meta.key', 'parent_booking_id')
              ->where('bookings_meta.value',$id)
              ->first();
	if(isset($ba))
	{
		$from_date = $data->pickup;
		$to_date = isset($ba->dropoff) ? $ba->dropoff : $ba->pickup;
		$driverInterval = Hyvikk::get('driver_interval').' MINUTE';
		$current = Bookings::find($data->id);
		$q = "SELECT id, name AS text
		FROM users
		WHERE user_type = 'D'
		AND deleted_at IS NULL
		AND id NOT IN (
			SELECT DISTINCT driver_id
			FROM bookings
			WHERE deleted_at IS NULL
			AND cancellation = 0
			AND (
				(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
				OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
				OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
			)
		)
		AND id NOT IN (
			SELECT DISTINCT driver_id
			FROM bookings
			WHERE deleted_at IS NULL
			AND cancellation = 0
			AND (
				(pickup BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
				OR (dropoff BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
				OR (dropoff > '" . $to_date . "' AND pickup < DATE_ADD('" . $to_date . "', INTERVAL " . $driverInterval . "))
			)
			AND driver_id <> '" . $current->driver_id . "' AND driver_id <> '" . $ba->driver_id . 
			"'
		)";
	}
	else
	{
		$from_date = $data->pickup;
		$to_date = isset($data->dropoff) ? $data->dropoff : $data->pickup;
		$driverInterval = Hyvikk::get('driver_interval').' MINUTE';
		$current = Bookings::find($data->id);
		$q = "SELECT id, name AS text
		FROM users
		WHERE user_type = 'D'
		AND deleted_at IS NULL
		AND id NOT IN (
			SELECT DISTINCT driver_id
			FROM bookings
			WHERE deleted_at IS NULL
			AND cancellation = 0
			AND (
				(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
				OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
				OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
			)
		)
		AND id NOT IN (
			SELECT DISTINCT driver_id
			FROM bookings
			WHERE deleted_at IS NULL
			AND cancellation = 0
			AND (
				(pickup BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
				OR (dropoff BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
				OR (dropoff > '" . $to_date . "' AND pickup < DATE_ADD('" . $to_date . "', INTERVAL " . $driverInterval . "))
			)
			AND driver_id <> '" . $current->driver_id . "'
		)";
	}
		$d = collect(DB::select($q));
		$chk = $d->where('id', $current->driver_id);
		$r['show_error'] = "yes";
		if (count($chk) > 0) {
			$r['show_error'] = "no";
		}
		$new = array();
		foreach ($d as $ro) {
			if ($ro->id === $current->driver_id) {
				array_push($new, array("id" => $ro->id, "text" => $ro->text,'selected' => true));
			} else {
				array_push($new, array("id" => $ro->id, "text" => $ro->text));
			}
		}
		$r['data'] = $new;
		$new1 = [];
		foreach ($r['data'] as $r1) {
			$user = User::where('id', $r1['id'])->first();
			if($user->getMeta('is_active') == 1)
			{
				$r1['is_available'] = $user->getMeta('is_available');
				array_push($new1, $r1);
			}
		}
		$r['data'] = $new1;
		
	return view("bookings.Assigndrivers",compact('data','v_model','v_type','r'));
}	
	public function update_assign_driver(Request $request)
	{
				$data=Bookings::find($request->b_id);
				$data->driver_id=$request->driver_id;
				$data->ride_status='Upcoming';
				if($data->save())
				{
					$cus=User::where('id',$data->customer_id)->first();
					$driver=User::where('id',$data->driver_id)->first();
					if(isset($cus) && isset($driver))
					{
						$cusimg = $cus->getMeta('profile_pic'); 
						if (isset($cusimg) && $cusimg !== '') {
							$custmerprofile = url('/').'/'.'uploads/'. $cusimg;
						} else {
							$custmerprofile = '';
						}
						$driverimg = $driver->getMeta('driver_image'); 
						if (isset($driverimg) && $driverimg !== '') {
							$driverprofile = url('/').'/'.'uploads/'. $driverimg;
						} else {
							$driverprofile = '';
						}
						if($cus->fcm_id !=null)
						{
							$title="Your Booking Has Been Accepted";
							$notification =array(
								'id' =>$driver->id ,
								'name' => $driver->name,
								'image' =>$driverprofile,
								'time' => date('d-M-Y H:i A',strtotime($driver->created_at)),
							);
							$data1 =array(
								'booking_id' =>$data->id,
							);
							$this->sendNotification($title,$notification,$data1,$cus->fcm_id);
						}
						if($driver->fcm_id !=null)
						{
							$title="A New Ride has been Assigned.";
							$notification =array(
								'id' =>$cus->id ,
								'name' => "Journey Date: ".$data->journey_date.' | '.'Destination: '.$data->dest_addr,
								// 'image' =>isset($custmerprofile) ? $custmerprofile : url('assets/images/p2pride_mobile_app.png'),
								'time' => date('d-M-Y H:i A',strtotime($cus->created_at)),
								'status'=>1
							);
							$data2 =array(
								'booking_id' =>$data->id,
							);
							$this->sendNotification($title,$notification,$data2,$driver->fcm_id);
						}
					}
				}
		$ba=Bookings::select("bookings.*")->join('bookings_meta', 'bookings_meta.booking_id', 	'=', 'bookings.id')
				->where('bookings_meta.key', 'parent_booking_id')
				->where('bookings_meta.value',$request->b_id)
				->first();
		if(isset($ba))
		{
			$data1=Bookings::find($ba->id);
			$data1->driver_id=$request->driver_id;
			$data1->ride_status='Upcoming';
			if($data1->save())
			{
				$cus1=User::where('id',$data1->customer_id)->first();
				$driver1=User::where('id',$data1->driver_id)->first();
				if(isset($cus1) && isset($driver1))
				{
					$cusimg1 = $cus1->getMeta('profile_pic'); 
					if (isset($cusimg1) && $cusimg1 !== '') {
						$custmerprofile1 = url('/').'/'.'uploads/'. $cusimg1;
					} else {
						$custmerprofile1 = '';
					}
					$driverimg1 = $driver1->getMeta('driver_image'); 
					if (isset($driverimg1) && $driverimg1 !== '') {
						$driverprofile1 = url('/').'/'.'uploads/'. $driverimg1;
					} else {
						$driverprofile1 = '';
					}
					if($cus1->fcm_id !=null)
					{
						$title1="Your Booking Has Been Accepted";
						$notification1 =array(
							'id' =>$driver1->id ,
							'name' => $driver1->name,
							'image' =>$driverprofile1,
							'time' => date('d-M-Y H:i A',strtotime($driver1->created_at)),
						);
						$data3 =array(
							'booking_id' =>$data1->id,
						);
						$this->sendNotification($title1,$notification1,$data3,$cus1->fcm_id);
					}
					if($driver1->fcm_id !=null)
					{
						$title1="A New Ride has been Assigned.";
						$notification1 =array(
							'id' =>$cus1->id ,
							'name' => "Journey Date: ".$data1->journey_date.' | '.'Destination: '.$data1->dest_addr,
							// 'image' =>isset($custmerprofile) ? $custmerprofile : url('assets/images/p2pride_mobile_app.png'),
							'time' => date('d-M-Y H:i A',strtotime($cus1->created_at)),
							'status'=>1
						);
						$data4 =array(
							'booking_id' =>$data1->id,
						);
						$this->sendNotification($title1,$notification1,$data4,$driver1->fcm_id);
					}
				}
			}
		}
		return redirect()->route("invitations.index");
	}
	// public function get_vehicle(Request $request) {
	// 	$from_date = $request->get("from_date");
	// 	$to_date = $request->get("to_date");
	// 	$req_type = $request->get("req");
	// 	$vehicleInterval = Hyvikk::get('vehicle_interval').' MINUTE';
	// 	if ($req_type == "new") {
	// 		$xy = array();
	// 		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
	// 			// $q = "select id from vehicles where in_service = true and deleted_at is null  and  id not in(select vehicle_id from bookings where  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
	// 			$q = "SELECT id
	// 			FROM vehicles
	// 			WHERE in_service = 1
	// 			AND deleted_at IS NULL
	// 			AND id NOT IN (
	// 				SELECT DISTINCT vehicle_id
	// 				FROM bookings
	// 				WHERE deleted_at IS NULL
	// 				AND cancellation = 0
	// 				AND (
	// 					(dropoff BETWEEN '" . $from_date . "' AND '" . $to_date . "'
	// 					OR pickup BETWEEN '" . $from_date . "' AND '" . $to_date . "')
	// 					OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
	// 				)
	// 			)";
	// 		} else {
	// 			// $q = "select id from vehicles where in_service = true and deleted_at is null and group_id=" . Auth::user()->group_id . " and  id not in(select vehicle_id from bookings where  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
	// 			$q = "SELECT id
	// 			FROM vehicles
	// 			WHERE in_service = 1
	// 			AND deleted_at IS NULL
	// 			AND group_id = " . Auth::user()->group_id . "
	// 			AND id NOT IN (
	// 				SELECT DISTINCT vehicle_id
	// 				FROM bookings
	// 				WHERE deleted_at IS NULL
	// 				AND cancellation = 0
	// 				AND (
	// 					(dropoff BETWEEN '" . $from_date . "' AND '" . $to_date . "'
	// 					OR pickup BETWEEN '" . $from_date . "' AND '" . $to_date . "')
	// 					OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
	// 				)
	// 			)";
	// 		}
	// 		$d = collect(DB::select($q));
	// 		$new = array();
	// 		foreach ($d as $ro) {
	// 			$vhc = VehicleModel::find($ro->id);
	// 			$text = $vhc->make_name . "-" . $vhc->model_name . "-" . $vhc->license_plate;
	// 			array_push($new, array("id" => $ro->id, "text" => $text));
	// 		}
	// 		//dd($new);
	// 		$r['data'] = $new;
	// 		return $r;
	// 	} else {
	// 		$id = $request->get("id");
	// 		$current = Bookings::find($id);
	// 		if ($current->vehicle_typeid != null) {
	// 			$condition = " and type_id = '" . $current->vehicle_typeid . "'";
	// 		} else {
	// 			$condition = "";
	// 		}
	// 		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
	// 			// $q = "select id from vehicles where in_service = true " . $condition . " and id not in (select vehicle_id from bookings where id!=$id and  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
	// 			$q = "SELECT id
	// 			FROM vehicles
	// 			WHERE in_service = 1" . $condition . "
	// 			AND id NOT IN (
	// 				SELECT DISTINCT vehicle_id
	// 				FROM bookings
	// 				WHERE id != $id
	// 				AND deleted_at IS NULL
	// 				AND cancellation = 0
	// 				AND (
	// 					(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
	// 					OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
	// 					OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
	// 				)
	// 			)";
	// 		} else {
	// 			// $q = "select id from vehicles where in_service = true " . $condition . " and group_id=" . Auth::user()->group_id . " and id not in (select vehicle_id from bookings where id!=$id and  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
	// 			$q = "SELECT id
	// 			FROM vehicles
	// 			WHERE in_service = 1" . $condition . "
	// 			AND group_id = " . Auth::user()->group_id . "
	// 			AND id NOT IN (
	// 				SELECT DISTINCT vehicle_id
	// 				FROM bookings
	// 				WHERE id != $id
	// 				AND deleted_at IS NULL
	// 				AND cancellation = 0
	// 				AND (
	// 					(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
	// 					OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
	// 					OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
	// 				)
	// 			)";
	// 		}
	// 		$d = collect(DB::select($q));
	// 		$chk = $d->where('id', $current->vehicle_id);
	// 		$r['show_error'] = "yes";
	// 		if (count($chk) > 0) {
	// 			$r['show_error'] = "no";
	// 		}
	// 		$new = array();
	// 		foreach ($d as $ro) {
	// 			$vhc = VehicleModel::find($ro->id);
	// 			$text = $vhc->make_name . "-" . $vhc->model_name . "-" . $vhc->license_plate;
	// 			if ($ro->id == $current->vehicle_id) {
	// 				array_push($new, array("id" => $ro->id, "text" => $text, "selected" => true));
	// 			} else {
	// 				array_push($new, array("id" => $ro->id, "text" => $text));
	// 			}
	// 		}
	// 		$r['data'] = $new;
	// 		return $r;
	// 	}
	// }
	public function calendar_event($id) {
		$data['booking'] = Bookings::find($id);
		return view("bookings.event", $data);
	}
	public function calendar_view() {
		$booking = Bookings::where('user_id', Auth::user()->id)->exists();
		return view("bookings.calendar", compact('booking'));
	}
	public function service_view($id) {
		$data['service'] = ServiceReminderModel::find($id);
		return view("bookings.service_event", $data);
	}
	public function calendar(Request $request) {
		$data = array();
		$start = $request->get("start");
		$end = $request->get("end");
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$b = Bookings::get();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			$b = Bookings::whereIn('vehicle_id', $vehicle_ids)->get();
		}
		foreach ($b as $booking) {
			$x['start'] = date('Y-m-d H:i:s', strtotime($booking->pickup));
			$x['end'] = $booking->dropoff ? date('Y-m-d H:i:s', strtotime($booking->dropoff)) : date('Y-m-d H:i:s', strtotime($booking->pickup . ' +2 hours'));
			if ($booking->status == 1) {
				$color = "#7FD7E1"; // PCO Flow secondary color for completed bookings
				$textColor = '#3A87AC'; // Blue text
			} elseif ($booking->getMeta('ride_status') == 'Ongoing') {
				$color = "#808080"; // Grey for collected pickups
				$textColor = '#FFFFFF'; // White text for greyed out
			} else {
				$color = "#CEFAFF"; // Light blue for active bookings
				$textColor = '#3A87AC'; // Blue text
			}
			$x['backgroundColor'] = $color;
			$x['textColor'] = $textColor;
			$x['classNames'] = 'font-weight-bold'; // Bold text
			$x['title'] = ($booking->driver->name ?? 'No Driver')."\n".($booking->vehicle->make_name ?? '')." ".($booking->vehicle->model_name ?? '')." ".($booking->vehicle->license_plate ?? '');
			$x['id'] = $booking->id;
			$x['type'] = 'calendar';
			array_push($data, $x);
		}
		$reminders = ServiceReminderModel::get();
		foreach ($reminders as $r) {
			$interval = substr($r->services->overdue_unit, 0, -3);
			$int = $r->services->overdue_time . $interval;
			$date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
			if ($r->last_date != 'N/D') {
				$date = date('Y-m-d', strtotime($int, strtotime($r->last_date)));
			}
			$x['start'] = $date;
			$x['end'] = $date;
			$color = "#6B7280"; // PCO Flow button color for service reminders
			$x['backgroundColor'] = $color;
			$x['textColor'] = '#3A87AC'; // Blue text
			$x['classNames'] = 'font-weight-bold'; // Bold text
			$x['title'] = $r->services->description."\n".($r->vehicle->make_name ?? '')." ".($r->vehicle->model_name ?? '')." ".($r->vehicle->license_plate ?? '');
			$x['id'] = $r->id;
			$x['type'] = 'service';
			array_push($data, $x);
		}
		return $data;
	}
	public function create() {
		$user = Auth::user()->group_id;
		$data['customers'] = User::where('user_type', 'C')->get();
		$drivers = User::whereUser_type("D")->get();
		$data['drivers'] = [];
		foreach ($drivers as $d) {
			if ($d->getMeta('is_active') == 1) {
				$data['drivers'][] = $d;
			}
		}
		
		// Add onboarding drivers (including those yet to be approved)
		$auth = Auth::user();
		$onboardingDrivers = \App\OnboardingDriver::whereIn('status', ['submitted', 'approved']);
		
		// Apply company scoping if applicable
		if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
			$vehicleIds = \App\Model\VehicleModel::where('company_id', $auth->company_id)->pluck('id')->toArray();
			if (!empty($vehicleIds)) {
				$onboardingDrivers->whereIn('vehicle_id', $vehicleIds);
			}
		} elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
			$onboardingDrivers->whereRaw('1=0'); // No results for this user type
		}
		
		$onboardingDrivers = $onboardingDrivers->get();
		
		// Convert onboarding drivers to a format compatible with the view
		foreach ($onboardingDrivers as $onboardingDriver) {
			$driverObj = new \stdClass();
			$driverObj->id = 'onboarding_' . $onboardingDriver->id; // Prefix to distinguish from regular drivers
			$driverObj->name = $onboardingDriver->name;
			$driverObj->email = $onboardingDriver->email;
			$driverObj->phone = $onboardingDriver->phone;
			$driverObj->is_onboarding = true;
			$driverObj->onboarding_status = $onboardingDriver->status;
			$driverObj->onboarding_id = $onboardingDriver->id;
			$data['drivers'][] = $driverObj;
		}
		
		$data['addresses'] = Address::where('customer_id', Auth::user()->id)->get();
        // Prefill pickup address from company settings if available
        $companyAddress = null;
        if (Auth::user()->company_id) {
            $company = Company::find(Auth::user()->company_id);
            $companyAddress = $company ? $company->address : null;
        }
        $data['company_address'] = $companyAddress;
		if ($user == null) {
			$data['vehicles'] = VehicleModel::whereRaw('in_service IS TRUE')
				->whereMeta('vehicle_status', 'Available')
				->get();
		} else {
			$data['vehicles'] = VehicleModel::where('group_id', $user)
				->whereRaw('in_service IS TRUE')
				->whereMeta('vehicle_status', 'Available')
				->get();
		}

		// Diagnostics + fallback: if no vehicles via Eloquent, try raw DB (Postgres boolean/scoping issues)
		try {
			\Log::info('[Invitations] Vehicles via Eloquent', [
				'count' => $data['vehicles'] ? $data['vehicles']->count() : 0,
				'user_group' => $user,
				'user_id' => Auth::id()
			]);
			if (!$data['vehicles'] || $data['vehicles']->count() === 0) {
				$raw = \DB::table('vehicles')
					->select('id','make_name','model_name','year','license_plate','type_id','group_id','in_service','company_id')
					->get();
				\Log::warning('[Invitations] Fallback raw vehicles used', [ 'count' => $raw->count() ]);
				$data['vehicles'] = collect($raw)->map(function($r){ return (object) $r; });
			}
		} catch (\Throwable $e) {
			\Log::error('[Invitations] Vehicle load error', [ 'error' => $e->getMessage() ]);
		}
		return view("bookings.create", $data);
		//dd($data['vehicles']);
	}
	public function edit($id) {
		$booking = Bookings::whereId($id)->get()->first();
		if ($booking && $booking->vehicle_typeid != null) {

			// $type_check=VehicleTypeModel::where('id',$booking->vehicle_typeid)->first();

			// if(isset($type_check))
			// {
			// 	$condition = " and type_id = '" . $booking->vehicle_typeid . "'";
			// }
			// else
			// {
			// 	$condition = "";
			// }

			$condition = "";
			
		
		
		} else {
			$condition = "";
		}

		$ba = Bookings::select("bookings.*")->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
		->where('bookings_meta.key', 'parent_booking_id')
		->where('bookings_meta.value',$booking->id)->first();
		if(isset($ba))
		{
			$pickup=$booking->pickup;
			$dropoff=isset($b->dropoff) ? $b->dropoff : $pickup;
			$q = "select id,name,deleted_at from users where user_type='D' and deleted_at is null and id not in (select user_id from bookings where status=0 and  id!=" . $id . " and "."id!=" . $ba->id." and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or dropoff between '" . $booking->pickup . "' and '" . $booking->dropoff . "'))";
		}
		else
		{
			$q = "select id,name,deleted_at from users where user_type='D' and deleted_at is null and id not in (select user_id from bookings where status=0 and  id!=" . $id . " and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or dropoff between '" . $booking->pickup . "' and '" . $booking->dropoff . "'))";
		}
		// $drivers = collect(DB::select($q));
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$b = Bookings::select("bookings.*")->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
			->where('bookings_meta.key', 'parent_booking_id')
			->where('bookings_meta.value',$booking->id)->first();
			if(isset($b))
			{
				$pickup=$booking->pickup;
				$dropoff=isset($b->dropoff) ? $b->dropoff : $pickup;
				$q1 = "select * from vehicles where in_service = true" . $condition . " and deleted_at is null and id not in (select vehicle_id from bookings where status=0 and  id!=" . $id . " and "."id!=" . $b->id." and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $pickup . "' and '" . $dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $pickup . "' and '" . $dropoff . "'  or dropoff between '" . $pickup . "' and '" . $dropoff . "'))";
			}
			else
			{
				$q1 = "select * from vehicles where in_service = true" . $condition . " and deleted_at is null and id not in (select vehicle_id from bookings where status=0 and  id!=" . $id . " and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "'  or dropoff between '" . $booking->pickup . "' and '" . $booking->dropoff . "'))";
			}
		} else {
			$b = Bookings::select("bookings.*")->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
			->where('bookings_meta.key', 'parent_booking_id')
			->where('bookings_meta.value',$booking->id)->first();
			if(isset($b))
			{
				$pickup=$booking->pickup;
				$dropoff=isset($b->dropoff) ? $b->dropoff : $pickup;
				$q1 = "select * from vehicles where in_service = true" . $condition . " and deleted_at is null and group_id=" . Auth::user()->group_id . " and id not in (select vehicle_id from bookings where status=0 and  id!=" . $id . " and  "."id!=" . $b->id." and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $pickup . "' and '" . $dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $pickup . "' and '" . $dropoff . "'  or dropoff between '" . $pickup . "' and '" . $dropoff . "'))";
			}
			else
			{
				$q1 = "select * from vehicles where in_service = true" . $condition . " and deleted_at is null and group_id=" . Auth::user()->group_id . " and id not in (select vehicle_id from bookings where status=0 and  id!=" . $id . " and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "'  or dropoff between '" . $booking->pickup . "' and '" . $booking->dropoff . "'))";
			}
		}
		$v_ids = array();
		$vehicles_data = collect(DB::select($q1));
		foreach ($vehicles_data as $v) {
			$vhc = VehicleModel::find($v->id);
			if(Hyvikk::get('fare_mode') == "price_wise")
			{
				if($vhc && $vhc->getMeta('price') != 0 )
				{
					$v_ids[] = $vhc->id;
				}
			}
			else if(Hyvikk::get('fare_mode') == "type_wise")
			{
				$v_ids[] = $vhc->id;
			}
			//$v_ids[] = $v->id;
		}
		$vehicles = VehicleModel::whereIn('id', $v_ids)->get();
		$index['drivers'] = [];
		$drivers = User::whereUser_type("D")->get();
		// $drivers = $this->get_driver($from_date,$to_date);
		foreach ($drivers as $d) {
			if ($d->getMeta('is_active') == 1) {
				$index['drivers'][] = $d;
			}
		}
		$index['vehicles'] = $vehicles;
		$index['data'] = $booking;
		$index['udfs'] = unserialize($booking->getMeta('udf'));
		$d=$complete_booking = DB::table('bookings_meta')
		->where('bookings_meta.key', 'parent_booking_id')
		->where('bookings_meta.value', $id)
		->first();
		if(isset($d))
		{
			$return_booking=Bookings::where('id',$d->booking_id)->first();
			$index['return_booking']=$return_booking;
		}
		return view("bookings.edit", $index);
	}
	// public function destroy(Request $request) {
	// 	$b=Bookings::find($request->get('id'))->delete();	
	// 	IncomeModel::where('income_id', $request->get('id'))->where('income_cat', 1)->delete();
	// 	if(isset($request->check) && $request->check == 1)
	// 	{
	// 		if(isset($b->parent_booking_id))
	// 		{
	// 			Bookings::find($b->parent_booking_id)->delete();
	// 			IncomeModel::where('income_id', $b->parent_booking_id)->where('income_cat', 1)->delete();
	// 		}
	// 		else
	// 		{
	// 			$c= Bookings::select("bookings.*")
	// 			->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
	// 			->where('bookings_meta.key', 'parent_booking_id')
	// 			->where('bookings_meta.value',$b->id)
	// 			->first();
	// 			Bookings::find($c->id)->delete();
	// 			IncomeModel::where('income_id', $c->id)->where('income_cat', 1)->delete();
	// 		}
	// 	}
	// 	return redirect()->route('bookings.index');
	// }
	public function destroy(Request $request)
{
    $booking = Bookings::find($request->get('id'));
    if (!$booking) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }
        return redirect()->route('invitations.index')->with('error', 'Booking not found');
    }
    // Delete related income records via booking_income table
    $bookingIncomes = BookingIncome::where('booking_id', $booking->id)->get();
    foreach ($bookingIncomes as $bookingIncome) {
        if ($bookingIncome->income_id) {
            $income = IncomeModel::find($bookingIncome->income_id);
            if ($income && $income->income_cat == 1) {
                $income->delete();
            }
        }
    }
    // Also delete the booking_income records
    BookingIncome::where('booking_id', $booking->id)->delete();
    
    // Check if we also need to delete parent or child booking
    if ($request->has('check') && $request->check == 1) {
        // If the booking has a parent, delete the parent
        if ($booking->parent_booking_id) {
            $parent = Bookings::find($booking->parent_booking_id);
            if ($parent) {
                $parentBookingIncomes = BookingIncome::where('booking_id', $parent->id)->get();
                foreach ($parentBookingIncomes as $parentBookingIncome) {
                    if ($parentBookingIncome->income_id) {
                        $income = IncomeModel::find($parentBookingIncome->income_id);
                        if ($income && $income->income_cat == 1) {
                            $income->delete();
                        }
                    }
                }
                BookingIncome::where('booking_id', $parent->id)->delete();
                $parent->delete();
            }
        } else {
            // Else find the child booking using meta table
            $child = Bookings::select("bookings.*")
                ->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                ->where('bookings_meta.key', 'parent_booking_id')
                ->where('bookings_meta.value', $booking->id)
                ->first();
            if ($child) {
                $childBookingIncomes = BookingIncome::where('booking_id', $child->id)->get();
                foreach ($childBookingIncomes as $childBookingIncome) {
                    if ($childBookingIncome->income_id) {
                        $income = IncomeModel::find($childBookingIncome->income_id);
                        if ($income && $income->income_cat == 1) {
                            $income->delete();
                        }
                    }
                }
                BookingIncome::where('booking_id', $child->id)->delete();
                $child->delete();
            }
        }
    }
    // Finally delete the main booking
    $booking->delete();
    
    // Return JSON response for AJAX requests
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => true, 'message' => 'Booking deleted successfully.']);
    }
    
    return redirect()->route('invitations.index')->with('success', 'Booking deleted successfully.');
}
    protected function check_booking($pickup, $dropoff, $vehicle) {
        // Simple check: only verify vehicle status is "Available" and in_service
        $vehicleModel = VehicleModel::find($vehicle);
        
        if (!$vehicleModel) {
            return false;
        }
        
        // Check if vehicle is in service
        if (!$vehicleModel->in_service) {
            \Log::info('Vehicle not available - not in service', [
                'vehicle_id' => $vehicle,
                'in_service' => $vehicleModel->in_service
            ]);
            return false;
        }
        
        // Check vehicle status from metadata (Available, Rented, Workshop, Disabled)
        $vehicleStatus = $vehicleModel->getMeta('vehicle_status') ?: 'Available';
        
        if ($vehicleStatus !== 'Available') {
            \Log::info('Vehicle not available - status check', [
                'vehicle_id' => $vehicle,
                'status' => $vehicleStatus,
                'required_status' => 'Available'
            ]);
            return false;
        }
        
        // Vehicle is available
        return true;
    }
    public function store(BookingRequest $request) {
        $max_seats = VehicleModel::find($request->get('vehicle_id'))->types->seats;
        // Support separate pickup_date and pickup_time; fall back to legacy 'pickup'
        $pickupInput = $request->get('pickup');
        if (!$pickupInput) {
            $pickupDate = $request->get('pickup_date');
            $pickupTime = $request->get('pickup_time');
            if ($pickupDate && $pickupTime) {
                $pickupInput = $pickupDate.' '.$pickupTime;
                // Inject into request payload so downstream code (create payload, models) still sees 'pickup'
                $request->merge(['pickup' => $pickupInput]);
            }
        }
        $pickupTs = Carbon::parse($pickupInput);
        $dropoffInput = $request->get("dropoff");
        $computedDropoff = $dropoffInput ? Carbon::parse($dropoffInput) : $pickupTs->copy()->addMinutes(15);
        if ($computedDropoff->lessThanOrEqualTo($pickupTs)) {
            $computedDropoff = $pickupTs->copy()->addMinutes(15);
        }
        $xx = $this->check_booking($pickupTs->format('Y-m-d H:i:s'), $computedDropoff->format('Y-m-d H:i:s'), $request->get("vehicle_id"));
		if ($xx) {
            $travellers = $request->get("travellers", 1);
            if ($travellers > $max_seats) {
				return redirect()->route("invitations.create")->withErrors(["error" => "Number of Travellers exceed seating capity of the vehicle | Seats Available : " . $max_seats . ""])->withInput();
			} else {
                $payload = $request->all();
                if (!isset($payload['travellers'])) { $payload['travellers'] = 1; }
                if (!isset($payload['dest_addr'])) { $payload['dest_addr'] = ''; }
                // Ensure status is set to enum value, not integer
                $payload['status'] = BookingStatus::Pending;
                
                // Handle onboarding driver selection
                $driverId = $request->get('driver_id');
                $onboardingDriverId = null;
                $onboardingDriverEmail = null;
                $onboardingDriverName = null;
                
                if (strpos($driverId, 'onboarding_') === 0) {
                    // Extract onboarding driver ID
                    $onboardingDriverId = str_replace('onboarding_', '', $driverId);
                    $onboardingDriver = \App\OnboardingDriver::find($onboardingDriverId);
                    
                    if ($onboardingDriver) {
                        // Clear the driver_id for onboarding drivers
                        $payload['driver_id'] = null;
                        $onboardingDriverEmail = $onboardingDriver->email;
                        $onboardingDriverName = $onboardingDriver->name;
                    }
                }
                
                // ensure dropoff is at least pickup + 15 minutes
                $payload['dropoff'] = $computedDropoff->format('Y-m-d H:i:s');
                $id = Bookings::create($payload)->id;
				Address::updateOrCreate(['customer_id' => $request->get('customer_id'), 'address' => $request->get('pickup_addr')]);
				$booking = Bookings::with(['driver', 'customer', 'vehicle'])->find($id);
				$booking->user_id = $request->get("user_id");
				
				// Handle driver assignment for onboarding drivers
				if ($onboardingDriverId && $onboardingDriver) {
					// For onboarding drivers, we don't set driver_id in the main booking
					// The onboarding driver info is stored in the booking metadata
					$booking->driver_id = null;
					// Save onboarding driver metadata using setMeta
					$booking->setMeta('onboarding_driver_id', $onboardingDriverId);
					$booking->setMeta('onboarding_driver_name', $onboardingDriver->name);
					$booking->setMeta('onboarding_driver_email', $onboardingDriver->email);
					$booking->setMeta('onboarding_driver_phone', $onboardingDriver->phone);
					$booking->setMeta('onboarding_driver_status', $onboardingDriver->status);
				} else {
					$booking->driver_id = $driverId;
				}
				$dropoff = Carbon::parse($booking->dropoff);
				$pickup = Carbon::parse($booking->pickup);
				$diff = $pickup->diffInMinutes($dropoff);
				$booking->note = $request->get('note');
				$booking->duration = $diff;
				$booking->udf = serialize($request->get('udf'));
				$booking->accept_status = 1; //0=yet to accept, 1= accept
				$booking->ride_status = "Upcoming";
				$booking->booking_type = 1;
				$booking->journey_date = date('d-m-Y', strtotime($booking->pickup));
				$booking->journey_time = date('H:i:s', strtotime($booking->pickup));
                // Set default values for pickup-only bookings
                $booking->total_time = "00:00:00";
                $booking->total_kms = "0";
				$booking->save();
				
				// Reload booking with relationships and metas for email sending
				$booking = Bookings::with(['driver', 'customer', 'vehicle', 'metas'])->find($booking->id);
				
				if(isset($request->booking_type) && $request->booking_type  == "return_way")
				{
					$ids = Bookings::create(['customer_id' => $request->customer_id,
					'pickup_addr' => $request->pickup_addr,
					'dest_addr' => '',
					'note' => $request->get('note'),
					'pickup' => $request->return_pickup_date_time,
					'dropoff'=>$request->return_dropoff_date_time,
					'vehicle_id'=>$request->vehicle_id,
					'user_id' => Auth::user()->id
					])->id;
					$return_date_time = Carbon::parse($request->return_pickup_date_time);
					$bookings = Bookings::find($ids);
					
					// Handle driver assignment for return booking with onboarding drivers
					$driverId = $request->get('driver_id');
					if (strpos($driverId, 'onboarding_') === 0) {
						$bookings->driver_id = null;
					} else {
						$bookings->driver_id = $driverId;
					}
					$bookings->journey_date = date('d-m-Y', strtotime($return_date_time));
					$bookings->journey_time =date('H:i:s', strtotime($return_date_time));
					$bookings->booking_type = 1;
					$bookings->accept_status = 0; //0=yet to accept, 1= accept
					$bookings->ride_status = "Upcoming";
					$bookings->return_flag=1;
					$bookings->parent_booking_id=$booking->id;
					// Set default values for return booking
					$bookings->total_time = "00:00:00";
					$bookings->total_kms = "0";
					$bookings->save();
				}
				$mail = Bookings::find($id);
				$this->booking_notification($booking->id);
				// send sms to customer while adding new booking
				$this->sms_notification($booking->id);
				// browser notification
				$this->push_notification($booking->id);
				if (Hyvikk::email_msg('email') == 1) {
					try{
						Mail::to($mail->customer->email)->send(new VehicleBooked($booking));
					} catch (\Throwable $e) {
						\Log::error('Error sending customer vehicle booked email', [
							'booking_id' => $booking->id,
							'error' => $e->getMessage()
						]);
					}
				}

				// Always attempt to send driver invitation email (independent of email toggle)
				try {
					// Check if this is an onboarding driver booking
					$onboardingDriverId = $booking->getMeta('onboarding_driver_id');
					$onboardingDriverEmail = $booking->getMeta('onboarding_driver_email');
					$onboardingDriverName = $booking->getMeta('onboarding_driver_name');
					
					if ($onboardingDriverId && $onboardingDriverEmail) {
						\Log::info('Attempting driver invitation email for onboarding driver', [
							'booking_id' => $booking->id,
							'onboarding_driver_id' => $onboardingDriverId,
							'driver_email' => $onboardingDriverEmail
						]);
						$this->sendOnboardingDriverInvitationEmail($booking, $onboardingDriverEmail, $onboardingDriverName);
					} elseif ($booking->driver && $booking->driver->email) {
						\Log::info('Attempting driver invitation email for regular driver', [
							'booking_id' => $booking->id,
							'driver_email' => $booking->driver->email
						]);
						$this->sendDriverInvitationEmail($booking);
					} else {
						\Log::warning('Cannot send driver invitation email - missing driver or email', [
							'booking_id' => $booking->id,
							'driver_id' => $booking->driver_id,
							'onboarding_driver_id' => $onboardingDriverId,
							'driver_exists' => $booking->driver ? 'yes' : 'no',
							'driver_email' => $booking->driver ? $booking->driver->email : 'N/A',
							'onboarding_driver_email' => $onboardingDriverEmail ?: 'N/A'
						]);
					}
				} catch (\Throwable $e) {
					\Log::error('Error in driver invitation email path', [
						'booking_id' => $booking->id,
						'error' => $e->getMessage()
					]);
				}
				return redirect()->route("invitations.index")->with('msg', 'Vehicle Pickup Invitation Successfully Sent');
			}
		} else {
			return redirect()->route("invitations.create")->withErrors(["error" => "Selected Vehicle is not Available in Given Timeframe"])->withInput();
		}
	}
	public function sms_notification($booking_id) {
		$booking = Bookings::find($booking_id);
		
		// Check if this is an onboarding driver booking
		$onboardingDriverId = $booking->getMeta('onboarding_driver_id');
		$onboardingDriverName = $booking->getMeta('onboarding_driver_name');
		$onboardingDriverPhone = $booking->getMeta('onboarding_driver_phone');
		
		if ($onboardingDriverId) {
			// For onboarding drivers, we can't send SMS since they don't have phone codes yet
			\Log::info('Skipping SMS notification for onboarding driver', [
				'booking_id' => $booking->id,
				'onboarding_driver_id' => $onboardingDriverId
			]);
			return;
		}
		
		// Check if driver exists
		if (!$booking->driver) {
			\Log::info('Skipping SMS notification - no driver found', [
				'booking_id' => $booking->id,
				'driver_id' => $booking->driver_id
			]);
			return;
		}
		
		$id = Hyvikk::twilio('sid');
		$token = Hyvikk::twilio('token');
		$from = Hyvikk::twilio('from');
		$to = $booking->customer->mobno; // twilio trial verified number
		$driver_no = $booking->driver->phone_code . $booking->driver->phone;
		$customer_name = $booking->customer->name;
		$customer_contact = $booking->customer->mobno;
		$driver_name = $booking->driver->name;
		$driver_contact = $booking->driver->phone;
		$pickup_address = $booking->pickup_addr;
		$destination_address = $booking->dest_addr;
		$pickup_datetime = date(Hyvikk::get('date_format') . " H:i", strtotime($booking->pickup));
		$dropoff_datetime = date(Hyvikk::get('date_format') . " H:i", strtotime($booking->dropoff));
		$passengers = $booking->travellers;
		$search = ['$customer_name', '$customer_contact', '$pickup_address', '$pickup_datetime', '$passengers', '$destination_address', '$dropoff_datetime', '$driver_name', '$driver_contact'];
		$replace = [$customer_name, $customer_contact, $pickup_address, $pickup_datetime, $passengers, $destination_address, $dropoff_datetime, $driver_name, $driver_contact];
		// send sms to customer
		$body = str_replace($search, $replace, Hyvikk::twilio("customer_message"));
		$url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
		// $new_body = str_split($body, 120);
		$new_body = explode("\n", wordwrap($body, 120));
		foreach ($new_body as $row) {
			$data = array(
				'From' => $from,
				'To' => $to,
				'Body' => $row,
			);
			$post = http_build_query($data);
			$x = curl_init($url);
			curl_setopt($x, CURLOPT_POST, true);
			curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
			curl_setopt($x, CURLOPT_POSTFIELDS, $post);
			$y = curl_exec($x);
			curl_close($x);
		}
		// send sms to drivers
		$driver_body = str_replace($search, $replace, Hyvikk::twilio("driver_message"));
		$msg_body = explode("\n", wordwrap($driver_body, 120));
		foreach ($msg_body as $row) {
			$data = array(
				'From' => $from,
				'To' => $driver_no,
				'Body' => $row,
			);
			$post = http_build_query($data);
			$x = curl_init($url);
			curl_setopt($x, CURLOPT_POST, true);
			curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
			curl_setopt($x, CURLOPT_POSTFIELDS, $post);
			$y = curl_exec($x);
			curl_close($x);
		}
		// dd($y);
	}
	public function push_notification($id) {
		try {
			$booking = Bookings::find($id);
			// Use environment variables for VAPID keys instead of hardcoded values
			$vapidConfig = config('webpush.vapid');
			if (!$vapidConfig['public_key'] || !$vapidConfig['private_key']) {
				\Log::warning('VAPID keys not configured, skipping push notification', ['booking_id' => $id]);
				return;
			}
			$auth = array(
				'VAPID' => array(
					'subject' => $vapidConfig['subject'] ?? env('VAPID_SUBJECT', 'mailto:admin@example.com'),
					'publicKey' => $vapidConfig['public_key'],
					'privateKey' => $vapidConfig['private_key'],
				),
			);
			$select1 = DB::table('push_notification')->select('*')->whereIn('user_id', [$booking->user_id])->get()->toArray();
		} catch (\Exception $e) {
			// Table doesn't exist or other database error - log and continue without push notification
			\Log::warning('Push notification table not available', [
				'booking_id' => $id,
				'error' => $e->getMessage()
			]);
			return;
		}
		$webPush = new WebPush($auth);
		foreach ($select1 as $fetch) {
			$sub = Subscription::create([
				'endpoint' => $fetch->endpoint, // Firefox 43+,
				'publicKey' => $fetch->publickey, // base 64 encoded, should be 88 chars
				'authToken' => $fetch->authtoken, // base 64 encoded, should be 24 chars
				'contentEncoding' => $fetch->contentencoding,
			]);
			$user = User::find($fetch->user_id);
			$title = __('fleet.new_booking');
			$body = __('fleet.customer') . ": " . $booking->customer->name . ", " . __('fleet.pickup') . ": " . date(Hyvikk::get('date_format') . ' g:i A', strtotime($booking->pickup)) . ", " . __('fleet.pickup_addr') . ": " . $booking->pickup_addr . ", " . __('fleet.dropoff_addr') . ": " . $booking->dest_addr;
			$url = url('admin/invitations');
			$array = array(
				'title' => $title ?? "",
				'body' => $body ?? "",
				'img' => url('assets/images/' . Hyvikk::get('icon_img')),
				'url' => $url ?? url('admin/'),
			);
			$object = json_encode($array);
			if ($fetch->user_id == $user->id) {
				$test = $webPush->sendOneNotification($sub, $object);
			}
			foreach ($webPush->flush() as $report) {
				$endpoint = $report->getRequest()->getUri()->__toString();
			}
		}
	}
	public function update(BookingRequest $request) {
		//   dd($request->all());
		$max_seats = VehicleModel::find($request->get('vehicle_id'))->types->seats;
		if ($request->get("travellers") > $max_seats) {
			return redirect()->route("invitations.edit", $request->get('id'))->withErrors(["error" => "Number of Travellers exceed seating capity of the vehicle | Seats Available : " . $max_seats . ""])->withInput();
		}
		$booking = Bookings::whereId($request->get("id"))->first();
		$booking->vehicle_id = $request->get("vehicle_id");
		$booking->user_id = $request->get("user_id");
		$booking->driver_id = $request->get('driver_id');
		$booking->travellers = $request->get("travellers");
		$booking->pickup = $request->get("pickup");
		$booking->dropoff = $request->get("dropoff");
		$booking->pickup_addr = $request->get("pickup_addr");
		$booking->dest_addr = $request->get("dest_addr");
		if ($booking->ride_status == null || $booking->ride_status == "Pending") {
			$booking->ride_status = "Upcoming";
		}
		$dropoff = Carbon::parse($request->get("dropoff"));
		$pickup = Carbon::parse($request->get("pickup"));
		$booking->note = $request->get('note');
		$diff = $pickup->diffInMinutes($dropoff);
		$booking->duration = $diff;
		$booking->journey_date = date('d-m-Y', strtotime($request->get("pickup")));
		$booking->journey_time = date('H:i:s', strtotime($request->get("pickup")));
		$booking->udf = serialize($request->get('udf'));
		$key = (Hyvikk::api('api_key') ?? '-');
		$pickupAddress = urlencode($request->get('pickup_addr'));
		$dropoffAddress = urlencode($request->get('dest_addr'));
		$url = "https://maps.googleapis.com/maps/api/directions/json?origin={$pickupAddress}&destination={$dropoffAddress}&key={$key}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Turn off SSL certificate verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($ch);
		curl_close($ch);
		$dataFetch = json_decode($response, true);
		if ($dataFetch['status'] === 'OK') {
			$totalTimeInSeconds = $dataFetch['routes'][0]['legs'][0]['duration']['value'];
			$hours = floor($totalTimeInSeconds / 3600);
			$minutes = floor(($totalTimeInSeconds % 3600) / 60);
			$seconds = $totalTimeInSeconds % 60;
			$totalTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
			$booking->total_time=$totalTime;
			$total_kms=explode(" ", str_replace(",", "", $dataFetch['routes'][0]['legs'][0]['distance']['text']))[0];
			$booking->total_kms = (string)$total_kms;
		} else {
			$totalTime = "00:00:00";
			$booking->total_time=$totalTime;
			$booking->total_kms="0";
		}
		if($booking->save())
		{
			$cus=User::where('id',$booking->customer_id)->first();
					$driver=User::where('id',$booking->driver_id)->first();
					if(isset($cus) && isset($driver))
					{
						$cusimg = $cus->getMeta('profile_pic'); 
						if (isset($cusimg) && $cusimg !== '') {
							$custmerprofile = url('/').'/'.'uploads/'. $cusimg;
						} else {
							$custmerprofile = '';
						}
						$driverimg = $driver->getMeta('driver_image'); 
						if (isset($driverimg) && $driverimg !== '') {
							$driverprofile = url('/').'/'.'uploads/'. $driverimg;
						} else {
							$driverprofile = '';
						}
						if($cus->fcm_id !=null)
						{
							$title="Your Booking Has Been Accepted";
							$notification =array(
								'id' =>$driver->id ,
								'name' => $driver->name,
								'image' =>$driverprofile,
								'time' => date('d-M-Y H:i A',strtotime($driver->created_at)),
							);
							$data1 =array(
								'booking_id' =>$booking->id,
							);
							$this->sendNotification($title,$notification,$data1,$cus->fcm_id);
						}
						if($driver->fcm_id !=null)
						{
							$title="A New Ride has been Assigned.";
							$notification =array(
								'id' =>$cus->id ,
								'name' => "Journey Date: ".$booking->journey_date.' | '.'Destination: '.$booking->dest_addr,
								// 'image' =>isset($custmerprofile) ? $custmerprofile : url('assets/images/p2pride_mobile_app.png'),
								'time' => date('d-M-Y H:i A',strtotime($cus->created_at)),
								'status'=>1
							);
							$data2 =array(
								'booking_id' =>$booking->id,
							);
							$this->sendNotification($title,$notification,$data2,$driver->fcm_id);
						}
					}
		}
		if(isset($request->booking_type) && $request->booking_type == "return_way")
		{
			$max_seats1 = VehicleModel::find($request->get('vehicle_id'))->types->seats;
			if ($request->get("return_travellers") > $max_seats1) {
				return redirect()->route("invitations.edit", $request->get('id'))->withErrors(["error" => "Number of Travellers exceed seating capity of the vehicle | Seats Available : " . $max_seats1 . ""])->withInput();
			}
			$booking1 = Bookings::where('id',$request->get("return_booking_id"))->first();
			$booking1->vehicle_id = $booking->vehicle_id;
			//$booking->user_id = $request->get("user_id");
			$booking1->driver_id = $booking->driver_id;
			$booking1->travellers = $request->get("return_travellers");
			$booking1->pickup = $request->get("return_pickup_date_time");
			$booking1->dropoff = $request->get("return_dropoff_date_time");
			$booking1->pickup_addr = $request->get("return_pickup_addr");
			$booking1->dest_addr = $request->get("return_dest_addr");
			if ($booking1->ride_status == null || $booking1->ride_status == "Pending") {
				$booking1->ride_status = "Upcoming";
			}
			$dropoff1 = Carbon::parse($request->get("return_dropoff_date_time"));
			$pickup1 = Carbon::parse($request->get("return_pickup_date_time"));
			$booking->note = $request->get('return_note');
			$diff1 = $pickup->diffInMinutes($dropoff1);
			$booking1->duration = $diff1;
			$booking1->journey_date = date('d-m-Y', strtotime($request->get("return_pickup_date_time")));
			$booking1->journey_time = date('H:i:s', strtotime($request->get("return_pickup_date_time")));
			//$booking->udf = serialize($request->get('udf'));
			$key1 = (Hyvikk::api('api_key') ?? '-');
			$pickupAddress1 = urlencode($request->get('return_dest_addr'));
			$dropoffAddress1 = urlencode($request->get('return_pickup_addr'));
			$url1 = "https://maps.googleapis.com/maps/api/directions/json?origin={$pickupAddress1}&destination={$dropoffAddress1}&key={$key1}";
			$ch1 = curl_init();
			curl_setopt($ch1, CURLOPT_URL, $url1);
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
			// Turn off SSL certificate verification
			curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
			$response1 = curl_exec($ch1);
			curl_close($ch1);
			$dataFetch1 = json_decode($response1, true);
			if ($dataFetch1['status'] === 'OK') {
				$totalTimeInSeconds1 = $dataFetch1['routes'][0]['legs'][0]['duration']['value'];
				$hours1 = floor($totalTimeInSeconds1 / 3600);
				$minutes1 = floor(($totalTimeInSeconds1 % 3600) / 60);
				$seconds1 = $totalTimeInSeconds1 % 60;
				$totalTime1 = sprintf('%02d:%02d:%02d', $hours1, $minutes1, $seconds1);
				$booking1->total_time=$totalTime;
				$total_kms1=explode(" ", str_replace(",", "", $dataFetch1['routes'][0]['legs'][0]['distance']['text']))[0];
				$booking1->total_kms = (string)$total_kms1;
			} else {
				$totalTime1 = "00:00:00";
				$booking1->total_time=$totalTime1;
				$booking1->total_kms="0";
			}
			if($booking1->save())
			{
				$cus=User::where('id',$booking1->customer_id)->first();
					$driver=User::where('id',$booking1->driver_id)->first();
					
					// Check if this is an onboarding driver booking
					$onboardingDriverId = $booking1->getMeta('onboarding_driver_id');
					
					if(isset($cus))
					{
						$cusimg = $cus->getMeta('profile_pic'); 
						if (isset($cusimg) && $cusimg !== '') {
							$custmerprofile = url('/').'/'.'uploads/'. $cusimg;
						} else {
							$custmerprofile = '';
						}
						
						if($cus->fcm_id !=null)
						{
							$title="Your Booking Has Been Accepted";
							$notification =array(
								'id' =>$onboardingDriverId ?: ($driver ? $driver->id : 0),
								'name' => $onboardingDriverId ? $booking1->getMeta('onboarding_driver_name') : ($driver ? $driver->name : 'Driver'),
								'image' =>$driver ? ($driver->getMeta('driver_image') ? url('/').'/'.'uploads/'. $driver->getMeta('driver_image') : '') : '',
								'time' => date('d-M-Y H:i A',strtotime($driver ? $driver->created_at : now())),
							);
							$data1 =array(
								'booking_id' =>$booking1->id,
							);
							$this->sendNotification($title,$notification,$data1,$cus->fcm_id);
						}
						
						// Only send driver notification if it's a regular driver (not onboarding)
						if($driver && $driver->fcm_id !=null)
						{
							$title="A New Ride has been Assigned.";
							$notification =array(
								'id' =>$cus->id ,
								'name' => "Journey Date: ".$booking1->journey_date.' | '.'Destination: '.$booking1->dest_addr,
								// 'image' =>isset($custmerprofile) ? $custmerprofile : url('assets/images/p2pride_mobile_app.png'),
								'time' => date('d-M-Y H:i A',strtotime($cus->created_at)),
								'status'=>1
							);
							$data2 =array(
								'booking_id' =>$booking1->id,
							);
							$this->sendNotification($title,$notification,$data2,$driver->fcm_id);
						} elseif ($onboardingDriverId) {
							\Log::info('Skipping driver push notification for onboarding driver', [
								'booking_id' => $booking1->id,
								'onboarding_driver_id' => $onboardingDriverId
							]);
						}
					}
			}
		}
		return redirect()->route('invitations.index');
	}
	public function prev_address(Request $request) {
		$booking = Bookings::where('customer_id', $request->get('id'))->orderBy('id', 'desc')->first();
		if ($booking != null) {
			$r = array('pickup_addr' => $booking->pickup_addr, 'dest_addr' => $booking->dest_addr);
		} else {
			$r = array('pickup_addr' => "", 'dest_addr' => "");
		}
		return $r;
	}
	public function print_bookings() {
		if (Auth::user()->user_type == "C") {
			$data['data'] = Bookings::where('customer_id', Auth::user()->id)->orderBy('id', 'desc')->get();
		} else {
			$data['data'] = Bookings::orderBy('id', 'desc')->get();
		}
		return view('bookings.print_bookings', $data);
	}
	public function booking_notification($id) {
		$booking = Bookings::find($id);
		$data['success'] = 1;
		$data['key'] = "upcoming_ride_notification";
		$data['message'] = 'New Ride has been Assigned to you.';
		$data['title'] = "New Upcoming Ride for you !";
		$data['description'] = $booking->pickup_addr . " - " . $booking->dest_addr . " on " . date('d-m-Y', strtotime($booking->pickup));
		$data['timestamp'] = date('Y-m-d H:i:s');
		$data['data'] = array('rideinfo' => array(
			'booking_id' => $booking->id,
			'source_address' => $booking->pickup_addr,
			'dest_address' => $booking->dest_addr,
			'book_timestamp' => date('Y-m-d H:i:s', strtotime($booking->created_at)),
			'ridestart_timestamp' => null,
			'journey_date' => date('d-m-Y', strtotime($booking->pickup)),
			'journey_time' => date('H:i:s', strtotime($booking->pickup)),
			'ride_status' => "Upcoming"),
			'user_details' => array('user_id' => $booking->customer_id, 'user_name' => $booking->customer->name, 'mobno' => $booking->customer->getMeta('mobno'), 'profile_pic' => $booking->customer->getMeta('profile_pic')),
		);
		// dd($data);
		$driver = User::find($booking->driver_id);
		
		// Check if this is an onboarding driver booking
		$onboardingDriverId = $booking->getMeta('onboarding_driver_id');
		
		if ($onboardingDriverId) {
			// For onboarding drivers, we can't send push notifications since they don't have FCM tokens yet
			\Log::info('Skipping push notification for onboarding driver', [
				'booking_id' => $booking->id,
				'onboarding_driver_id' => $onboardingDriverId
			]);
		} elseif ($driver && $driver->getMeta('fcm_id') != null && $driver->getMeta('is_available') == 1) {
			$push = new PushNotification('fcm');
			$push->setMessage($data)
				->setApiKey(env('server_key'))
				->setDevicesToken([$driver->getMeta('fcm_id')])
				->send();
			// PushNotification::app('appNameAndroid')
			//     ->to($driver->getMeta('fcm_id'))
			//     ->send($data);
		} else {
			\Log::info('Skipping push notification - no driver or FCM token', [
				'booking_id' => $booking->id,
				'driver_id' => $booking->driver_id,
				'driver_exists' => $driver ? 'yes' : 'no'
			]);
		}
	}
	public function bulk_delete(Request $request) {
		Bookings::whereIn('id', $request->ids)->delete();
		IncomeModel::whereIn('income_id', $request->ids)->where('income_cat', 1)->delete();
		return back();
	}
	public function cancel_booking(Request $request) {
		// dd($request->all());
		$booking = Bookings::find($request->cancel_id);
		$booking->cancellation = 1;
		$booking->ride_status = "Cancelled";
		$booking->reason = $request->reason;
		$booking->save();
		// if booking->status != 1 then delete income record
		IncomeModel::where('income_id', $request->cancel_id)->where('income_cat', 1)->delete();
		if (Hyvikk::email_msg('email') == 1) {
			try{
			Mail::to($booking->customer->email)->send(new BookingCancelled($booking, $booking->customer->name));
			Mail::to($booking->driver->email)->send(new BookingCancelled($booking, $booking->driver->name));
			} catch (\Throwable $e) {
			}
		}
		return back()->with(['msg' => 'Booking cancelled successfully!']);
	}

	/**
	 * Send onboarding driver invitation email using ResendEmailService
	 */
	private function sendOnboardingDriverInvitationEmail($booking, $driverEmail, $driverName) {
		try {
			\Log::info('Starting onboarding driver invitation email process', [
				'booking_id' => $booking->id,
				'driver_email' => $driverEmail,
				'driver_name' => $driverName
			]);

			$resendService = new ResendEmailService();
			
			// Format pickup date and time
			$pickupDateTime = Carbon::parse($booking->pickup);
			$pickupDate = $pickupDateTime->format('d/m/Y');
			$pickupTime = $pickupDateTime->format('g:i A');
			
			// Get vehicle information
			$vehicleInfo = '';
			if ($booking->vehicle) {
				$vehicleInfo = $booking->vehicle->make_name . ' ' . $booking->vehicle->model_name . ' (' . $booking->vehicle->license_plate . ')';
			}
			
			\Log::info('Onboarding driver email parameters prepared', [
				'booking_id' => $booking->id,
				'driver_email' => $driverEmail,
				'driver_name' => $driverName,
				'customer_name' => $booking->customer->name,
				'pickup_date' => $pickupDate,
				'pickup_time' => $pickupTime,
				'pickup_addr' => $booking->pickup_addr,
				'vehicle_info' => $vehicleInfo,
				'note' => $booking->note ?: ''
			]);
			
			// Send the invitation email
			$result = $resendService->sendDriverBookingInvitationEmail(
				$driverEmail,
				$driverName,
				$booking->customer->name,
				$pickupDate,
				$pickupTime,
				$booking->pickup_addr,
				$vehicleInfo,
				$booking->note ?: ''
			);
			
			// Log the result for debugging
			\Log::info('Onboarding driver invitation email result', [
				'booking_id' => $booking->id,
				'driver_email' => $driverEmail,
				'success' => $result['success'],
				'message' => $result['message']
			]);
			
			if (!$result['success']) {
				\Log::error('Failed to send onboarding driver invitation email', [
					'booking_id' => $booking->id,
					'driver_email' => $driverEmail,
					'error' => $result['message']
				]);
			}
			
		} catch (\Exception $e) {
			\Log::error('Exception while sending onboarding driver invitation email', [
				'booking_id' => $booking->id,
				'driver_email' => $driverEmail,
				'error' => $e->getMessage()
			]);
		}
	}

	/**
	 * Send driver invitation email using ResendEmailService
	 */
	private function sendDriverInvitationEmail($booking) {
		try {
			\Log::info('Starting driver invitation email process', [
				'booking_id' => $booking->id,
				'driver_email' => $booking->driver->email ?? 'unknown',
				'driver_name' => $booking->driver->name ?? 'unknown'
			]);

			$resendService = new ResendEmailService();
			
			// Format pickup date and time
			$pickupDateTime = Carbon::parse($booking->pickup);
			$pickupDate = $pickupDateTime->format('d/m/Y');
			$pickupTime = $pickupDateTime->format('g:i A');
			
			// Get vehicle information
			$vehicleInfo = '';
			if ($booking->vehicle) {
				$vehicleInfo = $booking->vehicle->make_name . ' ' . $booking->vehicle->model_name . ' (' . $booking->vehicle->license_plate . ')';
			}
			
			\Log::info('Email parameters prepared', [
				'booking_id' => $booking->id,
				'driver_email' => $booking->driver->email,
				'driver_name' => $booking->driver->name,
				'customer_name' => $booking->customer->name,
				'pickup_date' => $pickupDate,
				'pickup_time' => $pickupTime,
				'pickup_addr' => $booking->pickup_addr,
				'vehicle_info' => $vehicleInfo,
				'note' => $booking->note ?: ''
			]);
			
			// Send the invitation email
			$result = $resendService->sendDriverBookingInvitationEmail(
				$booking->driver->email,
				$booking->driver->name,
				$booking->customer->name,
				$pickupDate,
				$pickupTime,
				$booking->pickup_addr,
				$vehicleInfo,
				$booking->note ?: ''
			);
			
			// Log the result for debugging
			\Log::info('Driver invitation email result', [
				'booking_id' => $booking->id,
				'driver_email' => $booking->driver->email,
				'success' => $result['success'],
				'message' => $result['message']
			]);
			
			if (!$result['success']) {
				\Log::error('Failed to send driver invitation email', [
					'booking_id' => $booking->id,
					'driver_email' => $booking->driver->email,
					'error' => $result['message']
				]);
			}
			
		} catch (\Exception $e) {
			\Log::error('Exception while sending driver invitation email', [
				'booking_id' => $booking->id,
				'driver_email' => $booking->driver->email ?? 'unknown',
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
		}
	}
}