<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\ImportRequest;
use App\Imports\DriverImport;
use App\Model\Bookings;
use App\Model\DriverLogsModel;
use App\Model\DriverVehicleModel;
use App\Model\ExpCats;
use App\Model\Expense;
use App\Model\Hyvikk;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\ServiceItemsModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Maatwebsite\Excel\Facades\Excel;
use Redirect;

use Illuminate\Support\Facades\Validator;

use App\Traits\FirebasePassword;


use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Session;

use Exception;
use Illuminate\Support\Facades\Http;

use App\Model\ReasonsModel;

use App\Model\ReviewModel;

use App\Traits\NotificationTrait;

use Edujugon\PushNotification\PushNotification;





class DriversController extends Controller {

        use FirebasePassword;

        use NotificationTrait;


        




        public function verify_driver($id)

        {

                $driver=User::find($id);

                $vehicle=VehicleModel::find($driver->getMeta('vehicle_id'));



                return view("drivers.verifydriver",compact('driver','vehicle'));

        }



        public function update_verify_driver(Request $request)

        {

                $validator = Validator::make($request->all(), [

            'status' => 'required',

                ]);

                if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();

        }



                $driver=User::find($request->d_id);

                $driver->is_verified=$request->status;

                $driver->save();



                return redirect('/admin/drivers');



        }




        public function __construct() {

                

                $this->middleware('permission:Drivers add', ['only' => ['create']]);
                $this->middleware('permission:Drivers edit', ['only' => ['edit']]);
                $this->middleware('permission:Drivers delete', ['only' => ['bulk_delete', 'destroy']]);
                $this->middleware('permission:Drivers list');
                $this->middleware('permission:Drivers import', ['only' => ['importDrivers']]);
                $this->middleware('permission:Drivers map', ['only' => ['driver_maps']]);
                $this->phone_code = config("phone_codes.codes");


        }

        public function complete_ride(Request $request,$id)
        {
                
                $book=Bookings::find($id);

                if($book && $book->getMeta('ride_status') == "Completed")
                {       
                        $data=$book;
                        return view('drivers.complete_ride',compact('data'));
                }
                

                 $latitude = $request->input('lat');
                 $longitude = $request->input('long');

                // Google Geocoding API URL
                $apiKey =Hyvikk::api('api_key'); 
                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

                $response = Http::get($url);

                
                if (empty($apiKey)) {
                        return redirect()->back()->with('error', 'Enter API key');
                }

                // Default address
                $address = null;
                

                if ($response->ok()) {
                        $data = $response->json();

                        if (isset($data['results'][0]['formatted_address'])) {
                                $address = $data['results'][0]['formatted_address'];
                        }
                }

                $booking = Bookings::find($id);

                $pickupAddress = urlencode($booking->pickup_addr);
                $dropoffAddress = urlencode($booking->dest_addr);

                

                $url2 = "https://maps.googleapis.com/maps/api/directions/json?origin={$pickupAddress}&destination={$dropoffAddress}&key={$apiKey}";
                                                
                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_URL, $url2);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                
                // Turn off SSL certificate verification
                curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
                
                $response2 = curl_exec($ch2);
                curl_close($ch2);
                
                $dataFetch2 = json_decode($response2, true);

                
                
                if ($dataFetch2['status'] === 'OK') {

                        $totalTimeInSeconds2 = $dataFetch2['routes'][0]['legs'][0]['duration']['value'];
                        $hours2 = floor($totalTimeInSeconds2 / 3600);
                        $minutes2 = floor(($totalTimeInSeconds2 % 3600) / 60);
                        $seconds2 = $totalTimeInSeconds2 % 60;

                        $DrivingTime = sprintf('%02d:%02d:%02d', $hours2, $minutes2, $seconds2);
                } else {
                        $DrivingTime = "00:00:00";
                        
                }


                if ($booking == null) {

                        $data['success'] = 0;

                        $data['message'] = "Unable to Fetch Ride Info. Please, Try again Later !";

                        $data['data'] = "";

                } else {

                        $driver = User::find($booking->driver_id);

                        $driver->is_on = 0;

                        $driver->save();

                        if (Hyvikk::get('dis_format') == 'meter') {

                                $unit = 'm';

                        }if (Hyvikk::get('dis_format') == 'km') {

                                $unit = 'km';

                        }

                        $booking->end_address = $address;

                        $booking->end_lat = $latitude;

                        $booking->end_long =$longitude;

                        $booking->dropoff = date('Y-m-d H:i:s');

                        $booking->rideend_timestamp = date('Y-m-d H:i:s');

                        $booking->ride_status = "Completed";

                        $booking->driving_time = $DrivingTime;

                        $booking->date = date('Y-m-d');

                        $booking->waiting_time = 0;

                        $booking->mileage = $request->get('total_kms');

                        $booking->save();

                        
                
                        $this->dest_reach_or_ride_complete($booking->id);

                        $cus=User::where('id',$booking->customer_id)->first();

                        $drivers=User::where('id',$booking->driver_id)->first();

                        $driverimg = $drivers->getMeta('driver_image'); 

                        if (isset($driverimg) && $driverimg !== '') {

                                $driverprofile = url('/').'/'.'uploads/'. $driverimg;

                        } else {

                                $driverprofile = '';

                        }

                        if($cus->fcm_id !=null)

                        {

                                $title="Your Ride Completed";

                                $notification =array(

                                        'id' =>$drivers->id ,

                                        'name' => $drivers->name,

                                        'image' =>$driverprofile,

                                        'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),

                                );

                                $data1 =array(

                                        'booking_id' =>$booking->id,

                                );

                                $this->sendNotification($title,$notification,$data1,$cus->fcm_id);

                        }

                        $data=$booking;

                        Session::flash('success', 'Your Ride Completed Successfully.'); 
                
                        return view('drivers.complete_ride',compact('data'));

                
                }

        }

        public function start_ride(Request $request,$id)
        {
                
       
                $book=Bookings::find($id);

                if($book && $book->getMeta('ride_status') == "Completed")
                {       
                        $data=$book;
                        return view('drivers.start_ride',compact('data'));
                }

                 $latitude = $request->input('lat');
                 $longitude = $request->input('long');

                // Google Geocoding API URL
                $apiKey =Hyvikk::api('api_key'); 
                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

                $response = Http::get($url);

                
                if (empty($apiKey)) {
                        return redirect()->back()->with('error', 'Enter API key');
                }

                // Default address
                $address = null;
                

                if ($response->ok()) {
                        $data = $response->json();

                        if (isset($data['results'][0]['formatted_address'])) {
                                $address = $data['results'][0]['formatted_address'];
                        }
                }

                

                $booking=Bookings::where('id',$id)->first();


                $booking->start_address = $address;

                $booking->start_lat = $request->get('lat');

                $booking->start_long = $request->get('long');

                $booking->pickup = date('Y-m-d H:i:s');

                $booking->ridestart_timestamp = date('Y-m-d H:i:s');

                $booking->ride_status = "Ongoing";

                $booking->completion_source="admin_side";

                $booking->save();

                $driver = User::find($booking->driver_id);

                $driver->is_on = 1;

                $driver->save();

                $cus=User::where('id',$booking->customer_id)->first();

                $drivers=User::where('id',$booking->driver_id)->first();

                $driverimg = $drivers->getMeta('driver_image'); 

                if (isset($driverimg) && $driverimg !== '') {

                        $driverprofile = url('/').'/'.'uploads/'. $driverimg;

                } else {

                        $driverprofile = '';

                }

                if($cus->fcm_id !=null)

                {

                        $title="Your Ride Started";

                        $notification =array(

                                'id' =>$drivers->id ,

                                'name' => $drivers->name,

                                'image' =>$driverprofile,

                                'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),

                        );

                        $data1 =array(

                                'booking_id' =>$booking->id,

                        );

                        $this->sendNotification($title,$notification,$data1,$cus->fcm_id);

                }

                $this->ride_started_notification($booking->id);

                $this->ride_ongoing_notification($booking->id);

                $data=$booking;

                

                return view('drivers.start_ride',compact('data','latitude','longitude'));
        }


        public function dest_reach_or_ride_complete($id)

    {

        $booking = Bookings::find($id);

        $rating = ReviewModel::where('booking_id', $id)->first();

        if ($rating != null) {

            $r = $rating->ratings;

        } else {

            $r = null;

        }

        if (Hyvikk::get('dis_format') == 'meter') {

            $unit = 'm';

        }if (Hyvikk::get('dis_format') == 'km') {

            $unit = 'km';

        }

        $vehicle_type = VehicleTypeModel::find($booking->getMeta('vehicle_typeid'));

        // $data['success'] = 1;

        // $data['key'] = "ride_completed_notification";

        // $data['message'] = 'Data Received.';

        // $data['title'] = "Ride Completed ";

        // $data['description'] = "You have Reached your Destination, Thank you !";

        // $data['timestamp'] = date('Y-m-d H:i:s');

        $data['data'] = array(

            'success' => 1,

            'key' => "ride_completed_notification",

            'message' => 'Data Received.',

            'title' => "Ride Completed ",

            'description' => "You have Reached your Destination, Thank you !",

            'timestamp' => date('Y-m-d H:i:s'),

            'rideinfo' => array('booking_id' => $booking->id,

            'source_address' => $booking->pickup_addr,

            'dest_address' => $booking->dest_addr,

            'source_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),

            'dest_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('rideend_timestamp'))),

            'book_timestamp' => date('Y-m-d', strtotime($booking->created_at)),

            'ridestart_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),

            'driving_time' => $booking->getMeta('driving_time'),

            'total_kms' => $booking->getMeta('total_kms') . " " . $unit,

            'amount' => $booking->getMeta('tax_total'),

            'ride_status' => $booking->getMeta('ride_status')),

            'user_details' => array('user_id' => $booking->customer_id,

                'user_name' => $booking->customer->name,

                'profile_pic' => $booking->customer->getMeta('profile_pic')),

            'fare_breakdown' => array('base_fare' => Hyvikk::fare(strtolower(str_replace(' ', '', ($booking->vehicle_id != null ? $booking->vehicle->types->vehicletype : ($vehicle_type->vehicletype ?? "")))) . '_base_fare'), //done

                'ride_amount' => $booking->getMeta('tax_total'),

                'extra_charges' => 0),

            'driver_details' => array('driver_id' => $booking->driver_id,

                'driver_name' => $booking->driver->name,

                'profile_pic' => $booking->driver->getMeta('driver_image'),

                'ratings' => $r));

        if ($booking->customer->getMeta('fcm_id') != null) {

            // PushNotification::app('appNameAndroid')

            //     ->to($booking->customer->getMeta('fcm_id'))

            //     ->send($data);

            $push = new PushNotification('fcm');

            $push->setMessage($data)

                ->setApiKey(env('server_key'))

                ->setDevicesToken([$booking->customer->getMeta('fcm_id')])

                ->send();

        }

    }

        public function ride_started_notification($id)

    {

        $booking = Bookings::find($id);

        // $data['success'] = 1;

        // $data['key'] = "ride_started_notification";

        // $data['message'] = 'Data Received.';

        // $data['title'] = "Ride Started";

        // $data['description'] = $booking->pickup_addr . "-" . $booking->dest_addr . ": Driver Name " . $booking->driver->name;

        // $data['timestamp'] = date('Y-m-d H:i:s');

        $data['data'] = array(

            'success' => 1,

            'key' => "ride_started_notification",

            'message' => 'Data Received.',

            'title' => "Ride Started",

            'description' => '"' .$booking->pickup_addr . "-" . $booking->dest_addr . ": Driver Name " . $booking->driver->name. '"',

            'timestamp' => date('Y-m-d H:i:s'),

            'ride_info' => array('user_id' => $booking->customer_id,

            'booking_id' => $id,

            'source_address' => $booking->pickup_addr,

            'dest_address' => $booking->dest_addr,

            'start_lat' => $booking->getMeta('start_lat'),

            'start_long' => $booking->getMeta('start_long'),

            'ridestart_timestamp' => $booking->getMeta('ridestart_timestamp'),

        ));

        if ($booking->customer->getMeta('fcm_id') != null) {

            // PushNotification::app('appNameAndroid')

            //     ->to($booking->customer->getMeta('fcm_id'))

            //     ->send($data);

            $push = new PushNotification('fcm');

            $push->setMessage($data)

                ->setApiKey(env('server_key'))

                ->setDevicesToken([$booking->customer->getMeta('fcm_id')])

                ->send();

        }

    }

    public function ride_ongoing_notification($id)

    {

        $booking = Bookings::find($id);

        $data['success'] = 1;

        $data['key'] = "ride_ongoing_notification";

        $data['message'] = 'Data Received.';

        $data['title'] = "Heading Towards [ " . $booking->dest_addr . " ]";

        $data['description'] = "Ongoing Ride From [ " . $booking->pickup_addr . " ]";

        $data['timestamp'] = date('Y-m-d H:i:s');

        $data['data'] = array(

            'user_id' => $booking->customer_id,

            'booking_id' => $id,

            'source_address' => $booking->pickup_addr,

            'dest_address' => $booking->dest_addr,

            'start_lat' => $booking->getMeta('start_lat'),

            'start_long' => $booking->getMeta('start_long'),

            'approx_timetoreach' => $booking->getMeta('approx_timetoreach'),

            'user_name' => $booking->customer->name,

            'user_profile' => $booking->customer->getMeta('profile_pic'),

            'ridestart_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),

        );

        // PushNotification::app('appNameAndroid')

        //     ->to($booking->customer->getMeta('fcm_id'))

        //     ->send($data);

        //not send to cutomer

        if ($booking->driver->getMeta('fcm_id') != null) {

            // PushNotification::app('appNameAndroid')

            //     ->to($booking->driver->getMeta('fcm_id'))

            //     ->send($data);

            $push = new PushNotification('fcm');

            $push->setMessage($data)

                ->setApiKey(env('server_key'))

                ->setDevicesToken([$booking->driver->getMeta('fcm_id')])

                ->send();

        }

    }


        public function view_ride($id)
        {

                $book_all = Bookings::select('bookings.*')
                        ->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                        ->where('bookings.driver_id', Auth::user()->id)
                        ->where('bookings_meta.key', 'ride_status')
                        ->where('bookings_meta.value', 'Ongoing')
                        ->count();

        
                if ($book_all > 0) {
                        return redirect()->back()->with('error', 'Please complete your ongoing ride before starting a new one.');
                }



                $data=Bookings::where('id',$id)->first();

                $reason=ReasonsModel::all();
                
                return view('drivers.ride_view',compact('data','reason'));
        }

        public function ride_cancel(Request $request)
        {

        
        
                $b=Bookings::select('bookings.*')->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                ->where('bookings_meta.key', 'parent_booking_id')
                ->where('bookings_meta.value', $request->get('booking_id'))
                ->first();

                
                $c=Bookings::select('bookings.*')->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                ->where('bookings_meta.key', 'return_flag')
                ->where('bookings_meta.value', 1)
                ->where('bookings.id',$request->get('booking_id'))
                ->first();

                
        

                
                if(isset($b) || isset($c))
                {
                        if(isset($c))
                        {
                                $ba=Bookings::where('id',$c->getMeta('parent_booking_id'))->first();

                                

                                if(isset($ba) && $ba->getMeta('ride_status') == "Completed")
                                {

                                        return back()->with('error', 'You can not cancel this ride.');

                                }
                                else if(isset($ba) && $ba->getMeta('ride_status') == "Upcoming")
                                {
                                        if($ba)
                                        {
                                                                $bookingId = $ba->id;
                                                                $driverId = $request->get('user_id');
                                                                $booking = Bookings::where('id', $bookingId)
                                                                                                ->where('driver_id', $driverId)
                                                                                                ->first();
                                                                $reason = $request->get('reason');
                                                                if ($booking == null || $reason == null) {
                                                                        
                                                                } else {
                                                                        $booking->ride_status = "Cancelled";
                                                                        $booking->reason = $reason;
                                                                        $booking->completion_source="admin_side";
                                                                        $booking->save();
                                                                        $this->cancel_ride_notification($booking->id);
                                                                        $cus=User::where('id',$booking->customer_id)->first();
                                                                        $drivers=User::where('id',$booking->driver_id)->first();
                                                                        $driverimg = $drivers->getMeta('driver_image'); 
                                                                        if (isset($driverimg) && $driverimg !== '') {
                                                                                $driverprofile = url('/').'/'.'uploads/'. $driverimg;
                                                                        } else {
                                                                                $driverprofile = '';
                                                                        }
                                                                        if($cus->fcm_id !=null)
                                                                        {
                                                                                $title="Your Ride has been Cancelled";
                                                                                $notification =array(
                                                                                        'id' =>$drivers->id ,
                                                                                        'name' => $drivers->name,
                                                                                        'image' =>$driverprofile,
                                                                                        'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),
                                                                                );
                                                                                $data1 =array(
                                                                                        'booking_id' =>$booking->id,
                                                                                );
                                                                                $this->sendNotification($title,$notification,$data1,$cus->fcm_id);
                                                                        }


                                                                                // return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');

                                                                }


                                        }


                                        $bookingId = $request->get('booking_id');
                                        $driverId = $request->get('user_id');
                                        $booking = Bookings::where('id', $bookingId)
                                                                        ->where('driver_id', $driverId)
                                                                        ->first();
                                        $reason = $request->get('reason');
                                        if ($booking == null || $reason == null) {
                        
                                                        return back()->with('error', 'Unable to Cancel Ride. Please, Try again Later !');

                                        } else {
                                                $booking->ride_status = "Cancelled";
                                                $booking->reason = $reason;
                                                $booking->completion_source="admin_side";
                                                $booking->save();
                                                $this->cancel_ride_notification($booking->id);
                                                $cus=User::where('id',$booking->customer_id)->first();
                                                $drivers=User::where('id',$booking->driver_id)->first();
                                                $driverimg = $drivers->getMeta('driver_image'); 
                                                if (isset($driverimg) && $driverimg !== '') {
                                                        $driverprofile = url('/').'/'.'uploads/'. $driverimg;
                                                } else {
                                                        $driverprofile = '';
                                                }
                                                if($cus->fcm_id !=null)
                                                {
                                                        $title="Your Ride has been Cancelled";
                                                        $notification =array(
                                                                'id' =>$drivers->id ,
                                                                'name' => $drivers->name,
                                                                'image' =>$driverprofile,
                                                                'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),
                                                        );
                                                        $data1 =array(
                                                                'booking_id' =>$booking->id,
                                                        );
                                                        $this->sendNotification($title,$notification,$data1,$cus->fcm_id);
                                                }
                                                return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');
                                        }
                                }
                                else if(isset($ba) && $ba->getMeta('ride_status') == "Cancelled")
                                {
                                                $bookingId = $request->get('booking_id');
                                                $driverId = $request->get('user_id');
                                                $booking = Bookings::where('id', $bookingId)
                                                                                ->where('driver_id', $driverId)
                                                                                ->first();
                                                $reason = $request->get('reason');
                                                if ($booking == null || $reason == null) {
                                                                return back()->with('error', 'Unable to Cancel Ride. Please, Try again Later !');
                                                } else {
                                                        $booking->ride_status = "Cancelled";
                                                        $booking->completion_source="admin_side";
                                                        $booking->reason = $reason;
                                                        $booking->save();
                                                        
                                                        $cus=User::where('id',$booking->customer_id)->first();
                                                        $drivers=User::where('id',$booking->driver_id)->first();
                                                        $driverimg = $drivers->getMeta('driver_image'); 
                                                        if (isset($driverimg) && $driverimg !== '') {
                                                                $driverprofile = url('/').'/'.'uploads/'. $driverimg;
                                                        } else {
                                                                $driverprofile = '';
                                                        }
                                                        if($cus->fcm_id !=null)
                                                        {
                                                                $title="Your Ride has been Cancelled";
                                                                $notification =array(
                                                                        'id' =>$drivers->id ,
                                                                        'name' => $drivers->name,
                                                                        'image' =>$driverprofile,
                                                                        'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),
                                                                );
                                                                $data1 =array(
                                                                        'booking_id' =>$booking->id,
                                                                );
                                                                $this->sendNotification($title,$notification,$data1,$cus->fcm_id);
                                                        }
                                                        return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');
                                                }
                                }
                        }

                        if(isset($b))
                        {
                                        $ba=Bookings::where('id',$request->get('booking_id'))->first();
                                        if(isset($ba) && $ba->getMeta('ride_status') == "Completed")
                                        {
                                
                                                return back()->with('error', 'You can not cancel this ride.');
                                        }

                                        else if(isset($ba) && $ba->getMeta('ride_status') == "Upcoming")
                                        {

                                                        if($b)
                                                        {
                                                                                $bookingId = $b->id;
                                                                                $driverId = $request->get('user_id');
                                                                                $booking = Bookings::where('id', $bookingId)
                                                                                                                ->where('driver_id', $driverId)
                                                                                                                ->first();
                                                                                $reason = $request->get('reason');
                                                                                if ($booking == null || $reason == null) {
                                                                                        return back()->with('error', 'Unable to Cancel Ride. Please, Try again Later !');
                                                                                } else {
                                                                                        $booking->ride_status = "Cancelled";
                                                                                        $booking->reason = $reason;
                                                                                        $booking->completion_source="admin_side";
                                                                                        $booking->save();
                                                                                        $this->cancel_ride_notification($booking->id);
                                                                                        $cus=User::where('id',$booking->customer_id)->first();
                                                                                        $drivers=User::where('id',$booking->driver_id)->first();
                                                                                        $driverimg = $drivers->getMeta('driver_image'); 
                                                                                        if (isset($driverimg) && $driverimg !== '') {
                                                                                                $driverprofile = url('/').'/'.'uploads/'. $driverimg;
                                                                                        } else {
                                                                                                $driverprofile = '';
                                                                                        }
                                                                                        if($cus->fcm_id !=null)
                                                                                        {
                                                                                                $title="Your Ride has been Cancelled";
                                                                                                $notification =array(
                                                                                                        'id' =>$drivers->id ,
                                                                                                        'name' => $drivers->name,
                                                                                                        'image' =>$driverprofile,
                                                                                                        'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),
                                                                                                );
                                                                                                $data1 =array(
                                                                                                        'booking_id' =>$booking->id,
                                                                                                );
                                                                                                $this->sendNotification($title,$notification,$data1,$cus->fcm_id);
                                                                                        }
                                                                                // return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');
                                                                                }


                                                        }


                                                        $bookingId = $request->get('booking_id');
                                                        $driverId = $request->get('user_id');
                                                        $booking = Bookings::where('id', $bookingId)
                                                                                        ->where('driver_id', $driverId)
                                                                                        ->first();
                                                        $reason = $request->get('reason');
                                                        if ($booking == null || $reason == null) {
                                                                return back()->with('error', 'Unable to Cancel Ride. Please, Try again Later !');
                                                        } else {
                                                                $booking->ride_status = "Cancelled";
                                                                $booking->completion_source="admin_side";
                                                                $booking->reason = $reason;
                                                                $booking->save();
                                                                $this->cancel_ride_notification($booking->id);
                                                                $cus=User::where('id',$booking->customer_id)->first();
                                                                $drivers=User::where('id',$booking->driver_id)->first();
                                                                $driverimg = $drivers->getMeta('driver_image'); 
                                                                if (isset($driverimg) && $driverimg !== '') {
                                                                        $driverprofile = url('/').'/'.'uploads/'. $driverimg;
                                                                } else {
                                                                        $driverprofile = '';
                                                                }
                                                                if($cus->fcm_id !=null)
                                                                {
                                                                        $title="Your Ride has been Cancelled";
                                                                        $notification =array(
                                                                                'id' =>$drivers->id ,
                                                                                'name' => $drivers->name,
                                                                                'image' =>$driverprofile,
                                                                                'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),
                                                                        );
                                                                        $data1 =array(
                                                                                'booking_id' =>$booking->id,
                                                                        );
                                                                        $this->sendNotification($title,$notification,$data1,$cus->fcm_id);
                                                                }
                                                        return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');
                                                        }
                                        }
                                        else if(isset($b) && $b->getMeta('ride_status') == "Completed")
                                        {
                                                        $bookingId = $request->get('booking_id');
                                                        $driverId = $request->get('user_id');
                                                        $booking = Bookings::where('id', $bookingId)
                                                                                        ->where('driver_id', $driverId)
                                                                                        ->first();
                                                        $reason = $request->get('reason');
                                                        if ($booking == null || $reason == null) {
                                                                return back()->with('error', 'Unable to Cancel Ride. Please, Try again Later !');
                                                        } else {
                                                                $booking->ride_status = "Cancelled";
                                                                $booking->completion_source="admin_side";
                                                                $booking->reason = $reason;
                                                                $booking->save();
                                                                $this->cancel_ride_notification($booking->id);
                                                                $cus=User::where('id',$booking->customer_id)->first();
                                                                $drivers=User::where('id',$booking->driver_id)->first();
                                                                $driverimg = $drivers->getMeta('driver_image'); 
                                                                if (isset($driverimg) && $driverimg !== '') {
                                                                        $driverprofile = url('/').'/'.'uploads/'. $driverimg;
                                                                } else {
                                                                        $driverprofile = '';
                                                                }
                                                                if($cus->fcm_id !=null)
                                                                {
                                                                        $title="Your Ride has been Cancelled";
                                                                        $notification =array(
                                                                                'id' =>$drivers->id ,
                                                                                'name' => $drivers->name,
                                                                                'image' =>$driverprofile,
                                                                                'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),
                                                                        );
                                                                        $data1 =array(
                                                                                'booking_id' =>$booking->id,
                                                                        );
                                                                        $this->sendNotification($title,$notification,$data1,$cus->fcm_id);
                                                                }
                                                                return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');
                                                        }
                                        }
                                        else if(isset($ba) && $ba->getMeta('ride_status') == "Cancelled")
                                        {
                                                        $bookingId = $request->get('booking_id');
                                                        $driverId = $request->get('user_id');
                                                        $booking = Bookings::where('id', $bookingId)
                                                                                        ->where('driver_id', $driverId)
                                                                                        ->first();
                                                        $reason = $request->get('reason');
                                                        if ($booking == null || $reason == null) {
                                                                return back()->with('error', 'Unable to Cancel Ride. Please, Try again Later !');
                                                        } else {
                                                                $booking->ride_status = "Cancelled";
                                                                $booking->completion_source="admin_side";
                                                                $booking->reason = $reason;
                                                                $booking->save();
                                                                $this->cancel_ride_notification($booking->id);
                                                                $cus=User::where('id',$booking->customer_id)->first();
                                                                $drivers=User::where('id',$booking->driver_id)->first();
                                                                $driverimg = $drivers->getMeta('driver_image'); 
                                                                if (isset($driverimg) && $driverimg !== '') {
                                                                        $driverprofile = url('/').'/'.'uploads/'. $driverimg;
                                                                } else {
                                                                        $driverprofile = '';
                                                                }
                                                                if($cus->fcm_id !=null)
                                                                {
                                                                        $title="Your Ride has been Cancelled";
                                                                        $notification =array(
                                                                                'id' =>$drivers->id ,
                                                                                'name' => $drivers->name,
                                                                                'image' =>$driverprofile,
                                                                                'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),
                                                                        );
                                                                        $data1 =array(
                                                                                'booking_id' =>$booking->id,
                                                                        );
                                                                        $this->sendNotification($title,$notification,$data1,$cus->fcm_id);
                                                                }
                                                                return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');
                                                        }
                                        }
                        }
                        
                        
                }
                else
                {
                                $bookingId = $request->get('booking_id');
                                $driverId = $request->get('user_id');
                                $booking = Bookings::where('id', $bookingId)
                                                                ->where('driver_id', $driverId)
                                                                ->first();
                                $reason = $request->get('reason');
                                if ($booking == null || $reason == null) {
                                        return back()->with('error', 'Unable to Cancel Ride. Please, Try again Later !');
                                } else {
                                        $booking->ride_status = "Cancelled";
                                        $booking->completion_source="admin_side";
                                        $booking->reason = $reason;
                                        $booking->save();
                                        $this->cancel_ride_notification($booking->id);
                                        $cus=User::where('id',$booking->customer_id)->first();
                                        $drivers=User::where('id',$booking->driver_id)->first();
                                        $driverimg = $drivers->getMeta('driver_image'); 
                                        if (isset($driverimg) && $driverimg !== '') {
                                                $driverprofile = url('/').'/'.'uploads/'. $driverimg;
                                        } else {
                                                $driverprofile = '';
                                        }
                                        if($cus->fcm_id !=null)
                                        {
                                                $title="Your Ride has been Cancelled";
                                                $notification =array(
                                                        'id' =>$drivers->id ,
                                                        'name' => $drivers->name,
                                                        'image' =>$driverprofile,
                                                        'time' => date('d-M-Y H:i A',strtotime($drivers->created_at)),
                                                );
                                                $data1 =array(
                                                        'booking_id' =>$booking->id,
                                                );
                                                $this->sendNotification($title,$notification,$data1,$cus->fcm_id);
                                        }
                                        return redirect(url('admin/my_bookings'))->with('success', 'Your Ride has been Cancelled Successfully.');
                                }
                }
                

        }

        public function cancel_ride_notification($id)

    {

        $booking = Bookings::find($id);

        // $data['success'] = 1;

        // $data['key'] = "cancel_ride_notification";

        // $data['message'] = 'Oops, Your Ride has been Cancelled by the Driver.';

        // $data['title'] = "Ride Cancelled - " . $id;

        // $data['description'] = $booking->pickup_addr . " - " . $booking->dest_addr . ". Reason is " . $booking->reason;

        // $data['timestamp'] = date('Y-m-d H:i:s');

        $data['data'] = array(

            'success' => 1,

            'key' => "cancel_ride_notification",

            'message' => 'Oops, Your Ride has been Cancelled by the Driver.',

            'title' => "Ride Cancelled - " . $id,

            'description' =>'"' . $booking->pickup_addr . " - " . $booking->dest_addr . ". Reason is " . $booking->reason .'"' ,

            'timestamp' => date('Y-m-d H:i:s'),

            'booking _id' => $id,

            'source_address' => $booking->pickup_addr,

            'dest_address' => $booking->dest_addr,

            'book_date' => date('d-m-Y', strtotime($booking->created_at)),

            'book_time' => date('H:i:s', strtotime($booking->created_at)),

            'journey_date' => $booking->getMeta('journey_date'),

            'journey_time' => $booking->getMeta('journey_time'),

            'ride_status' => $booking->ride_status,

            'reason' => $booking->reason,

        );

        if ($booking->customer->getMeta('fcm_id') != null) {

            // PushNotification::app('appNameAndroid')

            //     ->to($booking->customer->getMeta('fcm_id'))

            //     ->send($data);

            $push = new PushNotification('fcm');

            $push->setMessage($data)

                ->setApiKey(env('server_key'))

                ->setDevicesToken([$booking->customer->getMeta('fcm_id')])

                ->send();

        }

    }


        public function checkEmail(Request $request) {
                $email = $request->input('email');
                
                if (empty($email)) {
                        return response()->json(['available' => false, 'message' => 'Email is required']);
                }
                
                $exists = User::where('email', $email)->where('user_type', 'D')->exists();
                
                return response()->json([
                        'available' => !$exists,
                        'message' => $exists ? 'Email already exists' : 'Email is available'
                ]);
        }
        
        public function importDrivers(ImportRequest $request) {
                try {
                        // Check if the file is uploaded and valid
                        if (!$request->hasFile('excel') || !$request->file('excel')->isValid()) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'The uploaded file is not valid.'
                                ], 400);
                        }
        
                        $file = $request->file('excel');
                        $destinationPath = './uploads/xml';
                        $extension = $file->getClientOriginalExtension();
                        $fileName = Str::uuid() . '.' . $extension;
        
                        // Ensure the uploads directory exists and is writable
                        if (!is_dir($destinationPath)) {
                                mkdir($destinationPath, 0755, true); // Create directory if not exists
                        }
                        if (!is_writable($destinationPath)) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'The upload directory is not writable.'
                                ], 500);
                        }
        
                        $file->move($destinationPath, $fileName);
        
                        // Import Excel file with statistics tracking
                        $import = new DriverImport();
                        Excel::import($import, $destinationPath . '/' . $fileName);
                        
                        // Get import statistics
                        $stats = $import->importStats;
                        
                        // Prepare detailed success/error message
                        if ($stats['successfully_imported'] > 0) {
                                $message = "Successfully imported {$stats['successfully_imported']} drivers";
                                
                                if ($stats['duplicates_skipped'] > 0) {
                                    $message .= ", skipped {$stats['duplicates_skipped']} duplicates";
                                }
                                
                                if ($stats['validation_failed'] > 0) {
                                    $message .= ", {$stats['validation_failed']} validation errors";
                                }
                                
                                if ($stats['errors'] > 0) {
                                    $message .= ", {$stats['errors']} import errors";
                                }
                                
                                $message .= ".";
                                
                                return response()->json([
                                    'success' => true,
                                    'message' => $message,
                                    'stats' => $stats
                                ]);
                        } else {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'No drivers were imported. Please check your file format and data.',
                                    'stats' => $stats
                                ], 400);
                        }
                        
                } catch (ValidationException $e) {
                        // Handle validation exceptions (if any validation rules fail in the import process)
                        $failures = $e->failures();
                        $errorMessages = [];
        
                        foreach ($failures as $failure) {
                                $errorMessages[] = "Row " . $failure->row() . ": " . implode(", ", $failure->errors());
                        }
        
                        return response()->json([
                            'success' => false,
                            'message' => 'Validation failed: ' . implode('; ', $errorMessages),
                            'errors' => $errorMessages
                        ], 422);
                        
                } catch (\Exception $e) {
                        // Log the exact error to the Laravel log file for debugging
                        \Log::error('Error importing drivers: ' . $e->getMessage());
                        
                        // Return a more informative error to the user
                        return response()->json([
                            'success' => false,
                            'message' => 'An error occurred while importing drivers. Please check the server logs for more details.',
                            'error' => $e->getMessage()
                        ], 500);
                }
        }
        
        public function index() {
                $auth = \Auth::user();
                
                // Pre-load all drivers with relationships and metadata
                $users = User::select('users.*')
                        ->with(['metas', 'vehicles']) // Eager load relationships
                        ->whereUser_type("D");
                
                // Company scoping
                if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
                        $users = $users->where('users.company_id', $auth->company_id);
                } elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
                        // Boss Admin without company sees no drivers
                        $users = $users->whereRaw('1=0');
                }
                
                // Default ordering: newest first
                $users = $users->orderBy('users.created_at', 'desc')->get();
                
                // Convert to array format with all needed fields
                $drivers = $users->map(function($user) {
                        $driverArray = $user->toArray();
                        
                        // Add metadata fields
                        $phoneCode = $user->metas->firstWhere('key', 'phone_code')?->value ?? '';
                        $phone = $user->metas->firstWhere('key', 'phone')?->value ?? '';
                        $driverArray['phone_code'] = $phoneCode;
                        $driverArray['phone'] = $phone;
                        $driverArray['phone_display'] = $phoneCode && $phone ? $phoneCode . ' ' . $phone : 'N/A';
                        
                        // License document
                        $licenseMeta = $user->metas->firstWhere('key', 'license_upload_path');
                        if (!$licenseMeta || !$licenseMeta->value) {
                                $licenseMeta = $user->metas->firstWhere('key', 'license_image');
                        }
                        $driverArray['license_document'] = $licenseMeta?->value ?? null;
                        
                        // Insurance document
                        $insuranceMeta = $user->metas->firstWhere('key', 'insurance_upload_path');
                        if (!$insuranceMeta || !$insuranceMeta->value) {
                                $insuranceMeta = $user->metas->firstWhere('key', 'insurance_image');
                                if (!$insuranceMeta || !$insuranceMeta->value) {
                                        $insuranceMeta = $user->metas->firstWhere('key', 'documents');
                                }
                        }
                        $driverArray['insurance_document'] = $insuranceMeta?->value ?? null;
                        
                        // License number
                        $driverArray['license_number'] = $user->metas->firstWhere('key', 'license_number')?->value ?? 'N/A';
                        
                        // Add all metas for easy access
                        $driverArray['all_metas'] = [];
                        foreach ($user->metas as $meta) {
                                if (!in_array($meta->key, ['token', 'method', 'edit', 'user_id', 'emp_id', 'detail_id'])) {
                                        $driverArray['all_metas'][$meta->key] = $meta->value;
                                }
                        }
                        
                        // Vehicle data
                        $vehicle = $user->vehicles->first();
                        if ($vehicle) {
                                $driverArray['vehicle'] = [
                                        'id' => $vehicle->id,
                                        'license_plate' => $vehicle->license_plate,
                                        'make_name' => $vehicle->make_name,
                                        'model_name' => $vehicle->model_name
                                ];
                        } else {
                                $driverArray['vehicle'] = null;
                        }
                        
                        return $driverArray;
                });
                
                return view("drivers.index", compact('drivers'));
        }
        public function fetch_data(Request $request) {
                if ($request->ajax()) {
                        $auth = \Auth::user();
                        
                        // Simplified query - remove JOINs, rely on eager loading
                        $users = User::select('users.*')
                                ->with(['metas', 'vehicles']) // Eager load relationships
                                ->whereUser_type("D");

                        // Company scoping
                        if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
                                $users = $users->where('users.company_id', $auth->company_id);
                        } elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
                                // Boss Admin without company sees no drivers
                                $users = $users->whereRaw('1=0');
                        }
                        
                        // Default ordering: newest first
                        $users = $users->orderBy('users.created_at', 'desc');
                        
                        return DataTables::eloquent($users)
                                ->addColumn('check', function ($user) {
                                        return '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
                                })
                                ->editColumn('name', function ($user) {
                                        return "<a href=" . route('drivers.show', $user->id) . ">$user->name</a>";
                                })
                                ->addColumn('is_active', function ($user) {
                                        $isActive = (bool) $user->is_active;
                                        $driverId = $user->id;
                                        $checked = $isActive ? 'checked' : '';
                                        
                                        return '<div class="d-flex justify-content-center">
                                                    <label class="switch">
                                                        <input type="checkbox" class="driver-status-toggle" data-driver-id="' . $driverId . '" ' . $checked . '>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>';
                                })
                                ->addColumn('phone', function ($user) {
                                        // Retrieve phone code and phone from metadata
                                        $phoneCode = $user->metas->firstWhere('key', 'phone_code')?->value ?? '';
                                        $phone = $user->metas->firstWhere('key', 'phone')?->value ?? '';
                                        return $phoneCode && $phone ? $phoneCode . ' ' . $phone : 'N/A';
                                })
                                ->addColumn('documents', function ($user) {
                                        // Access preloaded metas collection
                                        // Check for license_upload_path first, then fall back to license_image
                                        $licenseMeta = $user->metas->firstWhere('key', 'license_upload_path');
                                        if (!$licenseMeta || !$licenseMeta->value) {
                                                $licenseMeta = $user->metas->firstWhere('key', 'license_image');
                                        }
                                        
                                        // Check for insurance_upload_path first, then fall back to documents
                                        $insuranceMeta = $user->metas->firstWhere('key', 'insurance_upload_path');
                                        if (!$insuranceMeta || !$insuranceMeta->value) {
                                                $insuranceMeta = $user->metas->firstWhere('key', 'insurance_image');
                                                if (!$insuranceMeta || !$insuranceMeta->value) {
                                                        $insuranceMeta = $user->metas->firstWhere('key', 'documents');
                                                }
                                        }
                                        
                                        $docs = '';
                                        
                                        // Check if S3 is configured
                                        $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                                        $s3BaseUrl = $useS3 ? 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/' : '';
                                        
                                        if ($licenseMeta && $licenseMeta->value) {
                                                // Files are stored in uploads/ directory
                                                $licenseUrl = $useS3 ? ($s3BaseUrl . 'uploads/' . $licenseMeta->value) : asset('uploads/' . $licenseMeta->value);
                                                $docs .= '<a href="' . $licenseUrl . '" target="_blank" class="btn btn-sm btn-primary" title="View License"><i class="fa fa-id-card"></i></a> ';
                                        }
                                        if ($insuranceMeta && $insuranceMeta->value) {
                                                // Files are stored in uploads/ directory
                                                $insuranceUrl = $useS3 ? ($s3BaseUrl . 'uploads/' . $insuranceMeta->value) : asset('uploads/' . $insuranceMeta->value);
                                                $docs .= '<a href="' . $insuranceUrl . '" target="_blank" class="btn btn-sm btn-info" title="View Insurance"><i class="fa fa-shield-alt"></i></a>';
                                        }
                                        return $docs ?: '<span class="text-muted">No documents</span>';
                                })
                                ->addColumn('assigned_vehicle', function ($user) {
                                        // Use preloaded vehicles relationship
                                        $vehicle = $user->vehicles->first();
                                        if ($vehicle) {
                                                return '<a href="' . route('vehicles.show', $vehicle->id) . '" class="badge badge-warning text-dark" style="background-color: #EABE14; color: #333; border-radius: 4px; padding: 6px 12px; text-decoration: none; font-weight: bold;" title="View Vehicle Details">' . $vehicle->license_plate . '</a>';
                                        }
                                        return 'N/A';
                                })
                                ->addColumn('action', function ($user) {
                                        $buttons = '<div class="d-flex justify-content-center gap-2">';
                                        
                                        // Prepare driver data for instant display
                                        $driverData = [
                                                'id' => $user->id,
                                                'name' => $user->name,
                                                'email' => $user->email,
                                                'phone' => $user->phone_code . ' ' . $user->phone,
                                                'license_number' => $user->metas->firstWhere('key', 'license_number')?->value ?? 'N/A',
                                                'is_active' => $user->is_active,
                                                'assigned_vehicle' => null
                                        ];
                                        
                                        // Add vehicle data if exists
                                        $vehicle = $user->vehicles->first();
                                        if ($vehicle) {
                                                $driverData['assigned_vehicle'] = [
                                                        'id' => $vehicle->id,
                                                        'license_plate' => $vehicle->license_plate,
                                                        'make_name' => $vehicle->make_name,
                                                        'model_name' => $vehicle->model_name
                                                ];
                                        }
                                        
                                        // Add additional meta fields that are commonly displayed
                                        foreach ($user->metas as $meta) {
                                                if (!in_array($meta->key, ['license_number', 'token', 'method', 'edit', 'user_id', 'emp_id', 'detail_id'])) {
                                                        $driverData[$meta->key] = $meta->value;
                                                }
                                        }
                                        
                                        // Add S3 URLs for document files if S3 is configured
                                        $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                                        if ($useS3) {
                                                $s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
                                                
                                                if (isset($driverData['license_image']) || isset($driverData['license_upload_path'])) {
                                                        $driverData['license_url'] = $s3BaseUrl . 'uploads/' . ($driverData['license_upload_path'] ?? $driverData['license_image'] ?? '');
                                                }
                                                if (isset($driverData['insurance_image']) || isset($driverData['insurance_upload_path']) || isset($driverData['documents'])) {
                                                        $driverData['insurance_url'] = $s3BaseUrl . 'uploads/' . ($driverData['insurance_upload_path'] ?? $driverData['insurance_image'] ?? $driverData['documents'] ?? '');
                                                }
                                        }
                                        
                                        $jsonData = htmlspecialchars(json_encode($driverData), ENT_QUOTES, 'UTF-8');
                                        
                                        // View Details Button with embedded data
                                        $buttons .= '<button class="btn btn-sm btn-info" 
                                                        data-driver-id="' . $user->id . '" 
                                                        data-driver-info=\'' . $jsonData . '\'
                                                        onclick="toggleDriverDetailsInstant(this)" 
                                                        title="View Details" 
                                                        style="padding: 6px 8px;">
                                                            <i class="fas fa-eye"></i>
                                                        </button>';
                                        
                                        // Change Password Button
                                        $buttons .= '<button class="btn btn-sm btn-warning" data-id="' . $user->id . '" data-toggle="modal" data-target="#changepass" title="Change Password" style="padding: 6px 8px;">
                                                        <i class="fas fa-key"></i>
                                                    </button>';
                                        
                                        // Edit Button
                                        $buttons .= '<a href="' . url("admin/drivers/" . $user->id . "/edit") . '" class="btn btn-sm btn-primary" title="Edit Driver" style="padding: 6px 8px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>';
                                        
                                        // Delete Button
                                        $buttons .= '<button class="btn btn-sm btn-danger" data-id="' . $user->id . '" data-toggle="modal" data-target="#myModal" title="Delete Driver" style="padding: 6px 8px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>';
                                        
                                        $buttons .= '</div>';
                                        return $buttons;
                                })
                                ->filterColumn('is_active', function ($query, $keyword) {
                                        $query->whereHas("metas", function ($q) use ($keyword) {
                                                $q->where('key', 'is_active');
                                                $q->whereRaw("IF(value = 1 , 'YES', 'NO') like ? ", ["%{$keyword}%"]);
                                        });
                                        return $query;
                                })
                                ->filterColumn('phone', function ($query, $keyword) {
                                        $query->whereHas("metas", function ($q) use ($keyword) {
                                                $q->where(function ($q) use ($keyword) {
                                                        $q->where('key', 'phone');
                                                        $q->where("value", 'like', "%$keyword%");
                                                })->orWhere(function ($q) use ($keyword) {
                                                        $q->where('key', 'phone_code');
                                                        $q->where("value", 'like', "%$keyword%");
                                                });
                                        });
                                        return $query;
                                })
                                ->filterColumn('assigned_vehicle', function ($query, $keyword) {
                                        $query->whereHas("vehicles", function ($q) use ($keyword) {
                                                $q->where("license_plate", 'like', "%$keyword%");
                                        });
                                        return $query;
                                })
                                ->rawColumns(['action', 'check', 'name', 'documents', 'assigned_vehicle', 'is_active'])
                                ->make(true);
                        //return datatables(User::all())->toJson();
                }
        }
        public function show($id) {
                $index['driver'] = User::find($id);
                return view('drivers.show', $index);
        }
        
        public function getDriverDetails($id) {
                try {
                        // Eager load relationships to avoid N+1 queries
                        $driver = User::with(['vehicles', 'metas'])->findOrFail($id);
                        $driverData = $driver->toArray();
                        
                        // Use preloaded metas instead of querying again - add all metadata fields
                        foreach ($driver->metas as $meta) {
                                if (!in_array($meta->key, ['token', 'method', 'edit', 'user_id', 'emp_id', 'detail_id'])) {
                                        $driverData[$meta->key] = $meta->value;
                                }
                        }
                        
                        // Also get scheme and insurance_selection fields from metas if they exist
                        $schemeMeta = $driver->metas->firstWhere('key', 'scheme');
                        if ($schemeMeta && !isset($driverData['scheme'])) {
                            $driverData['scheme'] = $schemeMeta->value;
                        }
                        
                        $schemeSelectionMeta = $driver->metas->firstWhere('key', 'scheme_selection');
                        if ($schemeSelectionMeta && !isset($driverData['scheme_selection'])) {
                            $driverData['scheme_selection'] = $schemeSelectionMeta->value;
                        }
                        
                        $insuranceSelectionMeta = $driver->metas->firstWhere('key', 'insurance_selection');
                        if ($insuranceSelectionMeta && !isset($driverData['insurance_selection'])) {
                            $driverData['insurance_selection'] = $insuranceSelectionMeta->value;
                        }
                        
                        $vehicleSelectionMeta = $driver->metas->firstWhere('key', 'vehicle_selection');
                        if ($vehicleSelectionMeta && !isset($driverData['vehicle_selection'])) {
                            $driverData['vehicle_selection'] = $vehicleSelectionMeta->value;
                        }
                        
                        $licenseExpiryMeta = $driver->metas->firstWhere('key', 'license_expiry');
                        if ($licenseExpiryMeta && !isset($driverData['license_expiry'])) {
                            $driverData['license_expiry'] = $licenseExpiryMeta->value;
                        }
                        
                        $emergencyContactMeta = $driver->metas->firstWhere('key', 'emergency_contact');
                        if ($emergencyContactMeta && !isset($driverData['emergency_contact'])) {
                            $driverData['emergency_contact'] = $emergencyContactMeta->value;
                        }
                        
                        $emergencyPhoneMeta = $driver->metas->firstWhere('key', 'emergency_phone');
                        if ($emergencyPhoneMeta && !isset($driverData['emergency_phone'])) {
                            $driverData['emergency_phone'] = $emergencyPhoneMeta->value;
                        }
                        
                        // Decode custom_data JSON if it exists and merge fields into driverData
                        if (isset($driverData['custom_data'])) {
                            $customData = is_string($driverData['custom_data']) ? json_decode($driverData['custom_data'], true) : $driverData['custom_data'];
                            if (is_array($customData)) {
                                // Merge ALL custom_data fields directly into driverData for easier access
                                foreach ($customData as $key => $value) {
                                    // Only set if the key doesn't already exist to preserve existing values
                                    if (!isset($driverData[$key])) {
                                        $driverData[$key] = $value;
                                    }
                                }
                                // Keep the original custom_data for backwards compatibility
                                $driverData['custom_data'] = $customData;
                            }
                        }
                        
                        // Also check for all custom field patterns (custom_1, custom_2, etc.) in metas
                        foreach ($driver->metas as $meta) {
                            if (strpos($meta->key, 'custom_') === 0 && !isset($driverData[$meta->key])) {
                                $driverData[$meta->key] = $meta->value;
                            }
                        }
                        
                        // Generate proper document URLs (matching onboarding format) - MOVED UP TO DEFINE VARIABLES
                        $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                        $s3BaseUrl = '';
                        if ($useS3) {
                            $s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
                        }
                        
                        // Generate URLs for custom file fields with proper S3/local storage handling
                        foreach ($driverData as $key => $value) {
                            if (strpos($key, 'custom_') === 0 && $value) {
                                // Get the field ID to check if it's a file field
                                $fieldId = str_replace('custom_', '', $key);
                                $isFileField = false;
                                try {
                                    $customField = \App\CustomFormField::find($fieldId);
                                    if ($customField && $customField->field_type === 'file') {
                                        $isFileField = true;
                                    }
                                } catch (\Exception $e) {
                                    // Not a file field or field doesn't exist
                                }
                                
                                // Generate URL for file fields
                                if ($isFileField && !isset($driverData[$key . '_url'])) {
                                    $filePath = $value;
                                    if ($useS3) {
                                        // S3 URL generation
                                        if (strpos($filePath, 'onboarding/documents/') === 0) {
                                            $driverData[$key . '_url'] = $s3BaseUrl . $filePath;
                                        } elseif (strpos($filePath, 'uploads/onboarding/') === 0) {
                                            $driverData[$key . '_url'] = $s3BaseUrl . $filePath;
                                        } elseif (strpos($filePath, 'uploads/') === 0) {
                                            $driverData[$key . '_url'] = $s3BaseUrl . 'uploads/onboarding/' . basename($filePath);
                                        } else {
                                            $driverData[$key . '_url'] = $s3BaseUrl . 'uploads/onboarding/' . $filePath;
                                        }
                                    } else {
                                        // Local storage URL generation
                                        if (strpos($filePath, 'onboarding/documents/') === 0) {
                                            $driverData[$key . '_url'] = asset('storage/' . $filePath);
                                        } elseif (strpos($filePath, 'uploads/onboarding/') === 0) {
                                            $driverData[$key . '_url'] = asset($filePath);
                                        } elseif (strpos($filePath, 'uploads/') === 0) {
                                            $driverData[$key . '_url'] = asset('uploads/onboarding/' . basename($filePath));
                                        } else {
                                            $driverData[$key . '_url'] = asset('uploads/onboarding/' . $filePath);
                                        }
                                    }
                                }
                            }
                        }
                        
                        // Get assigned vehicle from preloaded relationship
                        $vehicle = $driver->vehicles->first();
                        if ($vehicle) {
                                $driverData['assigned_vehicle'] = [
                                        'id' => $vehicle->id,
                                        'license_plate' => $vehicle->license_plate,
                                        'make_name' => $vehicle->make_name,
                                        'model_name' => $vehicle->model_name
                                ];
                        }
                        
                        // Also check for vehicle selection in custom_data and add vehicle details
                        if (isset($driverData['vehicle_selection']) && $driverData['vehicle_selection']) {
                                $selectedVehicle = \App\Model\VehicleModel::find($driverData['vehicle_selection']);
                                if ($selectedVehicle) {
                                    // Get fuel_type from preloaded metas if available
                                    $fuelType = 'Petrol'; // Default value
                                    $fuelMeta = $driver->metas->firstWhere('key', 'fuel_type');
                                    if ($fuelMeta) {
                                        $fuelType = $fuelMeta->value;
                                    }
                                    
                                    $driverData['vehicle_details'] = [
                                        'id' => $selectedVehicle->id,
                                        'license_plate' => $selectedVehicle->license_plate,
                                        'make_name' => $selectedVehicle->make_name,
                                        'model_name' => $selectedVehicle->model_name,
                                        'year' => $selectedVehicle->year,
                                        'fuel_type' => $fuelType
                                    ];
                                }
                        }
                        
                        // Generate proper document URLs for license and insurance (matching onboarding format)
                        if ($useS3) {
                            // License URL
                            if (isset($driverData['license_upload_path']) && $driverData['license_upload_path']) {
                                $licensePath = $driverData['license_upload_path'];
                                if (strpos($licensePath, 'onboarding/documents/') === 0) {
                                    $driverData['license_url'] = $s3BaseUrl . $licensePath;
                                } elseif (strpos($licensePath, 'uploads/onboarding/') === 0) {
                                    $driverData['license_url'] = $s3BaseUrl . $licensePath;
                                } elseif (strpos($licensePath, 'uploads/') === 0) {
                                    $driverData['license_url'] = $s3BaseUrl . 'uploads/onboarding/' . basename($licensePath);
                                } else {
                                    $driverData['license_url'] = $s3BaseUrl . 'uploads/onboarding/' . $licensePath;
                                }
                            }
                            
                            // Insurance URL
                            if (isset($driverData['insurance_upload_path']) && $driverData['insurance_upload_path']) {
                                $insurancePath = $driverData['insurance_upload_path'];
                                if (strpos($insurancePath, 'onboarding/documents/') === 0) {
                                    $driverData['insurance_url'] = $s3BaseUrl . $insurancePath;
                                } elseif (strpos($insurancePath, 'uploads/onboarding/') === 0) {
                                    $driverData['insurance_url'] = $s3BaseUrl . $insurancePath;
                                } elseif (strpos($insurancePath, 'uploads/') === 0) {
                                    $driverData['insurance_url'] = $s3BaseUrl . 'uploads/onboarding/' . basename($insurancePath);
                                } else {
                                    $driverData['insurance_url'] = $s3BaseUrl . 'uploads/onboarding/' . $insurancePath;
                                }
                            }
                        } else {
                            // Local storage URLs
                            if (isset($driverData['license_upload_path']) && $driverData['license_upload_path']) {
                                $licensePath = $driverData['license_upload_path'];
                                if (strpos($licensePath, 'onboarding/documents/') === 0) {
                                    $driverData['license_url'] = asset('storage/' . $licensePath);
                                } elseif (strpos($licensePath, 'uploads/onboarding/') === 0) {
                                    $driverData['license_url'] = asset($licensePath);
                                } else {
                                    $driverData['license_url'] = asset('uploads/onboarding/' . $licensePath);
                                }
                            }
                            
                            if (isset($driverData['insurance_upload_path']) && $driverData['insurance_upload_path']) {
                                $insurancePath = $driverData['insurance_upload_path'];
                                if (strpos($insurancePath, 'onboarding/documents/') === 0) {
                                    $driverData['insurance_url'] = asset('storage/' . $insurancePath);
                                } elseif (strpos($insurancePath, 'uploads/onboarding/') === 0) {
                                    $driverData['insurance_url'] = asset($insurancePath);
                                } else {
                                    $driverData['insurance_url'] = asset('uploads/onboarding/' . $insurancePath);
                                }
                            }
                        }
                        
                        // Get custom form fields for display with company-aware filtering
                        $auth = \Auth::user();
                        try {
                            $customFieldsQuery = \App\CustomFormField::ordered();
                            // Filter by company_id for Super Admin and Office Admin users
                            if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
                                $customFieldsQuery->where('company_id', $auth->company_id);
                            }
                            // Broker users (user_type 'B') see all custom fields
                            $customFields = $customFieldsQuery->get();
                        } catch (\Exception $e) {
                            $customFields = collect(); // Empty collection if custom fields don't exist
                        }
                        
                        // Enrich custom fields with metadata for better frontend display
                        $customFieldsMap = [];
                        foreach ($customFields as $field) {
                            $customFieldsMap['custom_' . $field->id] = [
                                'id' => $field->id,
                                'field_name' => $field->field_name,
                                'field_label' => $field->field_label ?? $field->field_name,
                                'field_type' => $field->field_type,
                                'is_required' => $field->is_required
                            ];
                        }
                        
                        return response()->json([
                                'success' => true,
                                'driver' => $driverData,
                                'customFields' => $customFields,
                                'customFieldsMap' => $customFieldsMap
                        ]);
                } catch (\Exception $e) {
                        return response()->json([
                                'success' => false,
                                'message' => 'Error loading driver details: ' . $e->getMessage()
                        ], 500);
                }
        }
        public function fetch_bookings_data(Request $request) {
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
                        $bookings->select('bookings.*')
                                ->leftJoin('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
                                ->leftJoin('bookings_meta', function ($join) {
                                        $join->on('bookings_meta.booking_id', '=', 'bookings.id')
                                                ->where('bookings_meta.key', '=', 'vehicle_typeid');
                                })
                                ->leftJoin('vehicle_types', function ($join) {
                                        $join->on(DB::raw("CAST(bookings_meta.value AS uuid)"), '=', 'vehicle_types.id');
                                })
                                ->when($request->driver_id, function ($q, $driver_id) {
                                        $q->where('bookings.driver_id', $driver_id);
                                })
                                ->when($request->customer_id, function ($q, $customer_id) {
                                        $q->where('bookings.customer_id', $customer_id);
                                })
                                ->with(['customer', 'metas']);
                        return DataTables::eloquent($bookings)
                                ->addColumn('check', function ($user) {
                                        return '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
                                })
                                ->addColumn('customer', function ($row) {
                                        return ($row->customer->name) ?? "";
                                })
                                ->addColumn('ride_status', function ($row) {
                                        return ($row->getMeta('ride_status')) ?? "";
                                })
                                ->editColumn('pickup_addr', function ($row) {
                                        return str_replace(",", "<br/>", $row->pickup_addr);
                                })
                                ->editColumn('dest_addr', function ($row) {
                                        // dd($row->dest_addr);
                                        return str_replace(",", "<br/>", $row->dest_addr);
                                })
                                ->editColumn('pickup', function ($row) use ($date_format_setting) {
                                        $pickup = '';
                                        $pickup = [
                                                'display' => '',
                                                'timestamp' => '',
                                        ];
                                        if (!is_null($row->pickup)) {
                                                $pickup = date($date_format_setting . ' h:i A', strtotime($row->pickup));
                                                return [
                                                        'display' => date($date_format_setting . ' h:i A', strtotime($row->pickup)),
                                                        'timestamp' => Carbon::parse($row->pickup),
                                                ];
                                        }
                                        return $pickup;
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
                                                ->orWhereRaw("(vehicle_types.displayname like ? and bookings.vehicle_id IS NULL)", ["%$keyword%"]);
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
                                        $query->whereRaw("IF(payment = 1 , '" . __('fleet.paid1') . "', '" . __('fleet.pending') . "') like ? ", ["%{$keyword}%"]);
                                })
                                ->filterColumn('pickup', function ($query, $keyword) {
                                        $query->whereRaw("DATE_FORMAT(pickup,'%d-%m-%Y %h:%i %p') LIKE ?", ["%$keyword%"]);
                                })
                                ->filterColumn('dropoff', function ($query, $keyword) {
                                        $query->whereRaw("DATE_FORMAT(dropoff,'%d-%m-%Y %h:%i %p') LIKE ?", ["%$keyword%"]);
                                })
                                ->rawColumns(['payment', 'action', 'check', 'pickup_addr', 'dest_addr'])
                                ->make(true);
                        //return datatables(User::all())->toJson();
                }
        }
        public function destroy(Request $request) {
                \Log::info('Destroy method called with request data:', $request->all());
                try {
                        $driver = User::find($request->get('id'));
                        
                        if (!$driver) {
                                \Log::error('Driver not found with ID: ' . $request->get('id'));
                                return redirect()->route('drivers.index')->with('error', 'Driver not found.');
                        }

                        \Log::info('Found driver:', ['id' => $driver->id, 'email' => $driver->email]);

                        // Delete from Firebase first
                        $this->deleteUser($driver->email);

                        // Detach vehicles if any
                        if ($driver->vehicles->count()) {
                                $driver->vehicles()->detach($driver->vehicles->pluck('id')->toArray());
                        }
                        
                        // Delete driver-vehicle relationships
                        DriverVehicleModel::where('driver_id', $request->id)->delete();
                        
                        // Delete uploaded files
                        if (file_exists('./uploads/' . $driver->driver_image) && !is_dir('./uploads/' . $driver->driver_image)) {
                                unlink('./uploads/' . $driver->driver_image);
                        }
                        if (file_exists('./uploads/' . $driver->license_image) && !is_dir('./uploads/' . $driver->license_image)) {
                                unlink('./uploads/' . $driver->license_image);
                        }
                        if (file_exists('./uploads/' . $driver->documents) && !is_dir('./uploads/' . $driver->documents)) {
                                unlink('./uploads/' . $driver->documents);
                        }
                        
                        // Delete user data
                        $driver->user_data()->delete();
                        
                        // Update email to prevent conflicts and then delete
                        $driver->update([
                                'email' => time() . "_deleted_" . $driver->email,
                        ]);
                        
                        // Finally delete the driver record
                        $driver->delete();
                        
                        \Log::info('Driver deleted successfully:', ['id' => $request->get('id')]);
                        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
                        
                } catch (\Exception $e) {
                        \Log::error('Error deleting driver: ' . $e->getMessage());
                        return redirect()->route('drivers.index')->with('error', 'An error occurred while deleting the driver.');
                }
        }
        public function bulk_delete(Request $request) {
                try {
                        $drivers = User::whereIn('id', $request->ids)->get();
                        
                        foreach ($drivers as $driver) {
                                // Delete from Firebase first
                                $this->deleteUser($driver->email);

                                // Detach vehicles if any
                                if ($driver->vehicles->count()) {
                                        $driver->vehicles()->detach($driver->vehicles->pluck('id')->toArray());
                                }
                                
                                // Delete uploaded files
                                if (file_exists('./uploads/' . $driver->driver_image) && !is_dir('./uploads/' . $driver->driver_image)) {
                                        unlink('./uploads/' . $driver->driver_image);
                                }
                                if (file_exists('./uploads/' . $driver->license_image) && !is_dir('./uploads/' . $driver->license_image)) {
                                        unlink('./uploads/' . $driver->license_image);
                                }
                                if (file_exists('./uploads/' . $driver->documents) && !is_dir('./uploads/' . $driver->documents)) {
                                        unlink('./uploads/' . $driver->documents);
                                }
                                
                                // Update email to prevent conflicts and then delete
                                $driver->update([
                                        'email' => time() . "_deleted_" . $driver->email,
                                ]);
                                
                                // Delete user data
                                $driver->user_data()->delete();
                                
                                // Finally delete the driver record
                                $driver->delete();
                        }
                        
                        // Delete driver-vehicle relationships
                        DriverVehicleModel::whereIn('driver_id', $request->ids)->delete();
                        
                        return back()->with('success', 'Selected drivers deleted successfully.');
                        
                } catch (\Exception $e) {
                        \Log::error('Error bulk deleting drivers: ' . $e->getMessage());
                        return back()->with('error', 'An error occurred while deleting the drivers.');
                }
        }
        public function create() {
                $data['vehicles'] = VehicleModel::get();
                $data['phone_code'] = $this->phone_code;
                return view("drivers.create", $data);
        }
        public function edit(User $driver) {
                if ($driver->user_type != "D") {
                        return redirect("admin/drivers");
                }
                $driver->load('vehicles');
                if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
                        $vehicles = VehicleModel::get();
                } else {
                        $vehicles = VehicleModel::where('group_id', Auth::user()->group_id)
                                ->get();
                }
                $phone_code = $this->phone_code;
                return view("drivers.edit", compact("driver", "phone_code", 'vehicles'));
        }
        private function upload_file($file, $field, $id) {
                // Check if S3 is configured, otherwise use local storage
                $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                
                if ($useS3) {
                        // Upload to S3
                        try {
                                $extension = $file->getClientOriginalExtension();
                                $fileName = Str::uuid() . '.' . $extension;
                                $path = Storage::disk('s3')->putFileAs('uploads', $file, $fileName);
                                $user = User::find($id);
                                $user->setMeta([$field => $fileName]);
                                $user->save();
                                \Log::info('File uploaded to S3 successfully', ['field' => $field, 'filename' => $fileName, 'path' => $path]);
                        } catch (\Exception $e) {
                                \Log::error('S3 upload failed', [
                                        'field' => $field,
                                        'error' => $e->getMessage(),
                                        'trace' => $e->getTraceAsString()
                                ]);
                                // Fallback to local storage
                                $destinationPath = public_path('uploads');
                                $extension = $file->getClientOriginalExtension();
                                $fileName = Str::uuid() . '.' . $extension;
                                $file->move($destinationPath, $fileName);
                                $user = User::find($id);
                                $user->setMeta([$field => $fileName]);
                                $user->save();
                        }
                } else {
                        // Fallback to local storage
                        $destinationPath = public_path('uploads');
                        $extension = $file->getClientOriginalExtension();
                        $fileName = Str::uuid() . '.' . $extension;
                        $file->move($destinationPath, $fileName);
                        $user = User::find($id);
                        $user->setMeta([$field => $fileName]);
                        $user->save();
                }
        }
        
        private function delete_file($fileName) {
                // Check if S3 is configured
                $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                
                if ($useS3 && $fileName) {
                        // Delete from S3
                        try {
                                Storage::disk('s3')->delete('uploads/' . $fileName);
                        } catch (\Exception $e) {
                                // Silently fail if file doesn't exist
                        }
                } else if ($fileName && file_exists(public_path('uploads/' . $fileName))) {
                        // Delete from local storage
                        unlink(public_path('uploads/' . $fileName));
                }
        }
        
        public function update(DriverRequest $request) {
                $id = $request->get('id');
                $user = User::find($id);
                if ($request->file('driver_image') && $request->file('driver_image')->isValid()) {
                        $oldDriverImage = $user->getMeta('driver_image');
                        $this->delete_file($oldDriverImage);
                        $this->upload_file($request->file('driver_image'), "driver_image", $id);
                }
                if ($request->file('license_image') && $request->file('license_image')->isValid()) {
                        $oldLicenseImage = $user->getMeta('license_image');
                        $this->delete_file($oldLicenseImage);
                        $this->upload_file($request->file('license_image'), "license_image", $id);
                        // Store id_proof_type in metadata instead of trying to save to users table
                        $user->setMeta(['id_proof_type' => 'License']);
                        // Store license_upload_path for consistency with index view
                        $licensePath = $user->metas->firstWhere('key', 'license_image')?->value;
                        if ($licensePath) {
                                $user->setMeta(['license_upload_path' => $licensePath]);
                        }
                }
                if ($request->file('insurance_image') && $request->file('insurance_image')->isValid()) {
                        $oldInsurancePath = $user->metas->firstWhere('key', 'insurance_image')?->value;
                        $this->delete_file($oldInsurancePath);
                        $this->upload_file($request->file('insurance_image'), "insurance_image", $id);
                        // Store insurance document path for consistency
                        $insurancePath = $user->metas->firstWhere('key', 'insurance_image')?->value;
                        if ($insurancePath) {
                                $user->setMeta(['insurance_upload_path' => $insurancePath]);
                        }
                }
                if ($request->file('documents')) {
                        $oldDocuments = $user->getMeta('documents');
                        $this->delete_file($oldDocuments);
                        $this->upload_file($request->file('documents'), "documents", $id);
                }
                // dd($request->all());
                $user->name = $request->get("first_name") . " " . $request->get("last_name");
                $user->email = $request->get('email');
                $user->save();
                // $user->driver_image = $request->get('driver_image');
                $form_data = $request->all();
                unset($form_data['driver_image']);
                unset($form_data['documents']);
                unset($form_data['license_image']);
                $user->setMeta($form_data);
                
                // Check expiration date only if it exists and is not empty
                if ($request->get('exp_date') && !empty($request->get('exp_date'))) {
                        try {
                                $to = \Carbon\Carbon::now();
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('exp_date'));
                                $diff_in_days = $to->diffInDays($from);
                                if ($diff_in_days > 20) {
                                        $t = DB::table('notifications')
                                                ->where('type', 'like', '%RenewDriverLicence%')
                                                ->where('data', 'like', '%"vid":' . $user->id . '%')
                                                ->delete();
                                }
                        } catch (\Exception $e) {
                                \Log::warning('Invalid exp_date format: ' . $request->get('exp_date'));
                        }
                }
                $user->save();
                return redirect()->route("drivers.index");
        }
        public function store(DriverRequest $request) {
                $id = User::create([
                        "name" => $request->get("first_name") . " " . $request->get("last_name"),
                        "email" => $request->get("email"),
                        "password" => bcrypt($request->get("password")),
                        "user_type" => "D",
                        'api_token' => str_random(60),
                        'company_id' => Auth::user()->company_id,
                ])->id;
                $user = User::find($id);
                if ($request->file('driver_image') && $request->file('driver_image')->isValid()) {
                        $this->upload_file($request->file('driver_image'), "driver_image", $id);
                }
                if ($request->file('license_image') && $request->file('license_image')->isValid()) {
                        $this->upload_file($request->file('license_image'), "license_image", $id);
                        // Store id_proof_type in metadata instead of trying to save to users table
                        $user->setMeta(['id_proof_type' => 'License']);
                }
                if ($request->file('insurance_image') && $request->file('insurance_image')->isValid()) {
                        $this->upload_file($request->file('insurance_image'), "insurance_image", $id);
                        // Store insurance document path for consistency
                        $insurancePath = $user->metas->firstWhere('key', 'insurance_image')?->value;
                        if ($insurancePath) {
                                $user->setMeta(['insurance_upload_path' => $insurancePath]);
                        }
                }
                if ($request->file('documents')) {
                        $this->upload_file($request->file('documents'), "documents", $id);
                }
                // Store license_upload_path for consistency with index view
                $licensePath = $user->metas->firstWhere('key', 'license_image')?->value;
                if ($licensePath) {
                        $user->setMeta(['license_upload_path' => $licensePath]);
                }
                $form_data = $request->all();
                unset($form_data['driver_image']);
                unset($form_data['documents']);
                unset($form_data['license_image']);
                unset($form_data['insurance_image']);
                // setMeta will handle storing first_name and last_name in users_meta table
                $user->setMeta($form_data);
                $user->save();
                $user->givePermissionTo(['Notes add', 'Notes edit', 'Notes delete', 'Notes list', 'Drivers list', 'Fuel add', 'Fuel edit', 'Fuel delete', 'Fuel list', 'VehicleInspection add', 'Transactions list', 'Transactions add', 'Transactions edit', 'Transactions delete']);
                return redirect()->route("drivers.index");
        }
        public function enable($id) {
				DB::table('users')->where('id', $id)->update([
					'is_active' => DB::raw('TRUE'),
					'updated_at' => now(),
				]);
                return redirect()->route("drivers.index");
        }
        public function disable($id) {
				$bookings = Bookings::where('driver_id', $id)->whereIn('status', ['pending', 'confirmed', 'in_progress'])->get();
                if (count($bookings) > 0) {
                        $newErrors = [
                                'error' => 'Some active Bookings still have this driver, please either change the driver in those bookings or you can deactivate this driver after those bookings are complete!',
                                'data' => $bookings->pluck('id')->toArray(),
                        ];
                        return redirect()->route('drivers.index')->with('errors', $newErrors)->with('bookings', $bookings);
                } else {
						DB::table('users')->where('id', $id)->update([
							'is_active' => DB::raw('FALSE'),
							'updated_at' => now(),
						]);
                        return redirect()->route('drivers.index');
                }
        }
        public function my_bookings() {

                
                $bookings = Bookings::orderBy('id', 'desc')->whereDriver_id(Auth::user()->id)->get();
                $data = [];
                foreach ($bookings as $booking) {
                        if ($booking->getMeta('ride_status') != 'Cancelled') {
                                $data[] = $booking;
                        }
                }
                return view('drivers.my_bookings', compact('data'));
        }
        public function yearly() {
                $bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
                $v_id = array('0');
                $c = array();
                foreach ($bookings as $key) {
                        if ($key->vehicle_id != null) {
                                $v_id[] = $key->vehicle_id;
                        }
                }
                $years = DB::select("select distinct extract(year from income_date) as years from income  union select distinct extract(year from exp_date) as years from expense order by years desc");
                $y = array();
                foreach ($years as $year) {
                        $y[$year->years] = $year->years;
                }
                if ($years == null) {
                        $y[date('Y')] = date('Y');
                }
                $data['vehicles'] = VehicleModel::whereIn('id', $v_id)->get();
                $data['year_select'] = date("Y");
                $data['vehicle_select'] = null;
                $data['years'] = $y;
                $in = join(",", $v_id);
                $data['income'] = IncomeModel::select(DB::raw("sum(IFNULL(driver_amount,amount)) as income"))->whereYear('date', date('Y'))->whereIn('vehicle_id', $v_id)->get();
                $data['expenses'] = Expense::select(DB::raw('sum(IFNULL(driver_amount,amount)) as expense'))->whereYear('date', date('Y'))->whereIn('vehicle_id', $v_id)->get();
                $data['expense_by_cat'] = Expense::select('type', 'expense_type', DB::raw('sum(amount) as expense'))->whereYear('date', date('Y'))->whereIn('vehicle_id', $v_id)->groupBy(['expense_type', 'type'])->get();
                $ss = ServiceItemsModel::get();
                foreach ($ss as $s) {
                        $c[$s->id] = $s->description;
                }
                $kk = ExpCats::get();
                foreach ($kk as $k) {
                        $b[$k->id] = $k->name;
                }
                $hh = IncCats::get();
                foreach ($hh as $k) {
                        $i[$k->id] = $k->name;
                }
                $data['service'] = $c;
                $data['expense_cats'] = $b;
                $data['income_cats'] = $i;
                $data['result'] = "";
                $data['yearly_income'] = $this->yearly_income();
                $data['yearly_expense'] = $this->yearly_expense();
                return view('drivers.yearly', $data);
        }
        public function yearly_post(Request $request) {
                $bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
                $v_id = array();
                foreach ($bookings as $key) {
                        $v_id[] = $key->vehicle_id;
                }
                $years = DB::select("select distinct extract(year from income_date) as years from income  union select distinct extract(year from exp_date) as years from expense order by years desc");
                $y = array();
                $b = array();
                $i = array();
                foreach ($years as $year) {
                        $y[$year->years] = $year->years;
                }
                if ($years == null) {
                        $y[date('Y')] = date('Y');
                }
                $data['vehicles'] = VehicleModel::whereIn('id', $v_id)->get();
                $data['year_select'] = $request->get("year");
                $data['vehicle_select'] = $request->get("vehicle_id");
                $data['yearly_income'] = $this->yearly_income();
                $data['yearly_expense'] = $this->yearly_expense();
                $income1 = IncomeModel::select(DB::raw("sum(amount) as income"))->whereYear('date', $data['year_select']);
                $expense1 = Expense::select(DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select']);
                $expense2 = Expense::select('type', 'expense_type', DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select'])->groupBy(['expense_type', 'type']);
                if ($data['vehicle_select'] != "") {
                        $data['income'] = $income1->where('vehicle_id', $data['vehicle_select'])->get();
                        $data['expenses'] = $expense1->where('vehicle_id', $data['vehicle_select'])->get();
                        $data['expense_by_cat'] = $expense2->where('vehicle_id', $data['vehicle_select'])->get();
                } else {
                        $data['income'] = $income1->whereIn('vehicle_id', $v_id)->get();
                        $data['expenses'] = $expense1->whereIn('vehicle_id', $v_id)->get();
                        $data['expense_by_cat'] = $expense2->whereIn('vehicle_id', $v_id)->get();
                }
                $ss = ServiceItemsModel::get();
                foreach ($ss as $s) {
                        $c[$s->id] = $s->description;
                }
                $kk = ExpCats::get();
                foreach ($kk as $k) {
                        $b[$k->id] = $k->name;
                }
                $hh = IncCats::get();
                foreach ($hh as $k) {
                        $i[$k->id] = $k->name;
                }
                $data['service'] = $c;
                $data['expense_cats'] = $b;
                $data['income_cats'] = $i;
                $data['years'] = $y;
                $data['result'] = "";
                return view('drivers.yearly', $data);
        }
        public function monthly() {
                $bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
                $v_id = array('0');
                foreach ($bookings as $key) {
                        if ($key->vehicle_id != null) {
                                $v_id[] = $key->vehicle_id;
                        }
                }
                $years = DB::select("select distinct extract(year from income_date) as years from income  union select distinct extract(year from exp_date) as years from expense order by years desc");
                $y = array();
                foreach ($years as $year) {
                        $y[$year->years] = $year->years;
                }
                if ($years == null) {
                        $y[date('Y')] = date('Y');
                }
                $data['vehicles'] = VehicleModel::whereIn('id', $v_id)->get();
                $data['year_select'] = date("Y");
                $data['month_select'] = date("n");
                $data['vehicle_select'] = null;
                $data['years'] = $y;
                $data['yearly_income'] = $this->yearly_income();
                $data['yearly_expense'] = $this->yearly_expense();
                $in = join(",", $v_id);
                $data['income'] = IncomeModel::select(DB::raw('sum(IFNULL(driver_amount,amount)) as income'))->whereYear('date', date('Y'))->whereMonth('date', date('n'))->whereIn('vehicle_id', $v_id)->get();
                $data['expenses'] = Expense::select(DB::raw('sum(IFNULL(driver_amount,amount)) as expense'))->whereYear('date', date('Y'))->whereMonth('date', date('n'))->whereIn('vehicle_id', $v_id)->get();
                $data['expense_by_cat'] = DB::select("select type,expense_type,sum(amount) as expense from expense where deleted_at is null and year(date)=" . date("Y") . " and month(date)=" . date("n") . " and vehicle_id in(" . $in . ") group by expense_type,type");
                $ss = ServiceItemsModel::get();
                $c = array();
                foreach ($ss as $s) {
                        $c[$s->id] = $s->description;
                }
                $kk = ExpCats::get();
                foreach ($kk as $k) {
                        $b[$k->id] = $k->name;
                }
                $hh = IncCats::get();
                foreach ($hh as $k) {
                        $i[$k->id] = $k->name;
                }
                $data['service'] = $c;
                $data['expense_cats'] = $b;
                $data['income_cats'] = $i;
                $data['result'] = "";
                return view("drivers.monthly", $data);
        }
        public function monthly_post(Request $request) {
                $bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
                $v_id = array('0');
                foreach ($bookings as $key) {
                        if ($key->vehicle_id != null) {
                                $v_id[] = $key->vehicle_id;
                        }
                }
                $years = DB::select("select distinct extract(year from income_date) as years from income  union select distinct extract(year from exp_date) as years from expense order by years desc");
                $y = array();
                $b = array();
                $i = array();
                $c = array();
                foreach ($years as $year) {
                        $y[$year->years] = $year->years;
                }
                if ($years == null) {
                        $y[date('Y')] = date('Y');
                }
                $data['vehicles'] = VehicleModel::whereIn('id', $v_id)->get();
                $data['year_select'] = $request->get("year");
                $data['month_select'] = $request->get("month");
                $data['vehicle_select'] = $request->get("vehicle_id");
                $data['yearly_income'] = $this->yearly_income();
                $data['yearly_expense'] = $this->yearly_expense();
                $income1 = IncomeModel::select(DB::raw('sum(amount) as income'))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select']);
                $expense1 = Expense::select(DB::raw('sum(amount) as expense'))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select']);
                $expense2 = Expense::select('type', 'expense_type', DB::raw('sum(amount) as expense'))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->groupBy(['expense_type', 'type']);
                if ($data['vehicle_select'] != "") {
                        $data['income'] = $income1->where('vehicle_id', $data['vehicle_select'])->get();
                        $data['expenses'] = $expense1->where('vehicle_id', $data['vehicle_select'])->get();
                        $data['expense_by_cat'] = $expense2->where('vehicle_id', $data['vehicle_select'])->get();
                } else {
                        $data['income'] = $income1->whereIn('vehicle_id', $v_id)->get();
                        $data['expenses'] = $expense1->whereIn('vehicle_id', $v_id)->get();
                        $data['expense_by_cat'] = $expense2->whereIn('vehicle_id', $v_id)->get();
                }
                $ss = ServiceItemsModel::get();
                foreach ($ss as $s) {
                        $c[$s->id] = $s->description;
                }
                $kk = ExpCats::get();
                foreach ($kk as $k) {
                        $b[$k->id] = $k->name;
                }
                $hh = IncCats::get();
                foreach ($hh as $k) {
                        $i[$k->id] = $k->name;
                }
                $data['service'] = $c;
                $data['expense_cats'] = $b;
                $data['income_cats'] = $i;
                $data['years'] = $y;
                $data['result'] = "";
                return view("drivers.monthly", $data);
        }
        private function yearly_income() {
                $bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
                $v_id = array('0');
                foreach ($bookings as $key) {
                        if ($key->vehicle_id != null) {
                                $v_id[] = $key->vehicle_id;
                        }
                }
                $in = join(",", $v_id);
                $incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from income where year(date)=? and  deleted_at is null and vehicle_id in(' . $in . ') group by month(date)', [date("Y")]);
                $months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
                $income2 = array();
                foreach ($incomes as $income) {
                        $income2[$income->mnth] = $income->tot;
                }
                $yr = array_merge($months, $income2);
                return implode(",", $yr);
        }
        private function yearly_expense() {
                $bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
                $v_id = array('0');
                foreach ($bookings as $key) {
                        if ($key->vehicle_id != null) {
                                $v_id[] = $key->vehicle_id;
                        }
                }
                $in = join(",", $v_id);
                $incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from expense where year(date)=? and  deleted_at is null and vehicle_id in(' . $in . ') group by month(date)', [date("Y")]);
                $months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
                $income2 = array();
                foreach ($incomes as $income) {
                        $income2[$income->mnth] = $income->tot;
                }
                $yr = array_merge($months, $income2);
                return implode(",", $yr);
        }
 // driver records from firebase
        public function firebase() {
                $database = app('firebase.database');
                $details = $database
                        ->getReference('/User_Locations')
                        ->orderByChild('user_type')
                        ->equalTo('D')
                        ->getValue();
                //$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);

                // $data = Firebase::get('/User_Locations/');

                // dd($data);
                //$details = json_decode($data, true);

                //dd($details);
                $markers = array();
                foreach ($details as $d) {
                        // echo $d['user_name'] . "</br>";
                        if ($d['user_type'] == "D") {

                                $markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude'], 'av' => $d['availability']],
                                );
                        }
                }
                // dd($markers);
        }

        public function driver_maps() {
                
                if(Hyvikk::api('firebase_url') != NULL)
                {

                try {
                        // Firestore and Google API configurations
                
                        

                        $serviceAccountKeyFile = storage_path('firebase/'.Hyvikk::api('firebase_url'));

                                
                
                        // Load the service account credentials
                        if (!file_exists($serviceAccountKeyFile)) {
                                throw new Exception('Service account key file not found.');
                        }
                        $serviceAccount = json_decode(file_get_contents($serviceAccountKeyFile), true);

                                $projectId = $serviceAccount['project_id'];
                                $collectionName = 'User_Locations';

                
                        if (!isset($serviceAccount['client_email'], $serviceAccount['private_key'])) {
                                throw new Exception('Invalid service account configuration.');
                        }
                
                        // Create JWT
                        $now_seconds = time();
                        $jwtHeader = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
                        $jwtClaimSet = json_encode([
                                'iss' => $serviceAccount['client_email'], // Issuer
                                'sub' => $serviceAccount['client_email'], // Subject
                                'aud' => 'https://oauth2.googleapis.com/token', // Audience
                                'iat' => $now_seconds, // Issued at
                                'exp' => $now_seconds + 3600, // Expiration (1 hour)
                                'scope' => 'https://www.googleapis.com/auth/datastore', // Scope
                        ]);
                
                        // Encode the header and claims
                        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtHeader));
                        $base64UrlClaimSet = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtClaimSet));
                
                        // Create the signature using the private key
                        $signatureInput = $base64UrlHeader . '.' . $base64UrlClaimSet;
                        openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'SHA256');
                        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                
                        // Create the complete JWT
                        $jwt = $base64UrlHeader . '.' . $base64UrlClaimSet . '.' . $base64UrlSignature;
                
                        // Get the OAuth 2.0 token using the JWT
                        $tokenUrl = 'https://oauth2.googleapis.com/token';
                        $postFields = http_build_query([
                                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                                'assertion' => $jwt,
                        ]);
                
                        // Send cURL request to get the access token
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                        $tokenResponse = curl_exec($ch);
                
                        if ($tokenResponse === false) {
                                throw new Exception('Error obtaining access token: ' . curl_error($ch));
                        }
                
                        $tokenData = json_decode($tokenResponse, true);
                        if (!isset($tokenData['access_token'])) {
                                throw new Exception('Access token not found in the response.');
                        }
                        $accessToken = $tokenData['access_token'];
                        curl_close($ch);
                
                        // Query Firestore to fetch all documents from the collection
                        $collectionUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collectionName}";
                        
                        $response = Http::withToken($accessToken)
                                ->acceptJson()
                                ->get($collectionUrl);
                
                        if ($response->successful()) {
                                $documents = $response->json();
                                //dd($documents['documents']); // Display the Firestore documents

                                foreach($documents['documents'] as $d)
                                {
                                        

                                        $driver=User::where('user_type','D')->where('id',$d['fields']['user_id']['stringValue'])->first();

                                        if(isset($driver))
                                        {
                                                

                                                        if (isset($d['fields']['latitude']['doubleValue']) && isset($d['fields']['longitude']['doubleValue'])) {

                                                                if ($d['fields']['latitude']['doubleValue'] != null || $d['fields']['longitude']['doubleValue'] != null) {
                                                
                                                                        $drivers[] = array('user_name' => $driver->name, 'availability' => ($d['fields']['availability']['stringValue']??'-'),
                                                                                'user_id' => $driver->id);

                                                                }
                                                        }
                                        }

                                        //dump($driver);
                                }

                        } else {
                                throw new Exception('Error fetching documents from Firestore: ' . $response->body());
                        }
                } catch (Exception $e) {
                        echo 'Error: ' . $e->getMessage();
                }


                 //$index['details'] = $drivers;
                
         if(isset($drivers))
                {
                        $index['details'] = $drivers;   
                }
                else
                {
                        $index['details'] =null;
                }

        
                return view('driver_maps', $index);

        }

                

        }


        public function track_driver($id) {

                if(Hyvikk::api('firebase_url') != NULL)
                {

                try {
                        // Firestore and Google API configurations
                        $serviceAccountKeyFile = storage_path('firebase/'.Hyvikk::api('firebase_url'));
                        
                        
                        // Load the service account credentials
                        if (!file_exists($serviceAccountKeyFile)) {
                                throw new Exception('Service account key file not found.');
                        }
                        $serviceAccount = json_decode(file_get_contents($serviceAccountKeyFile), true);

                        $projectId = $serviceAccount['project_id'];
                        $collectionName = 'User_Locations';

                
                        if (!isset($serviceAccount['client_email'], $serviceAccount['private_key'])) {
                                throw new Exception('Invalid service account configuration.');
                        }
                
                        // Create JWT
                        $now_seconds = time();
                        $jwtHeader = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
                        $jwtClaimSet = json_encode([
                                'iss' => $serviceAccount['client_email'], // Issuer
                                'sub' => $serviceAccount['client_email'], // Subject
                                'aud' => 'https://oauth2.googleapis.com/token', // Audience
                                'iat' => $now_seconds, // Issued at
                                'exp' => $now_seconds + 3600, // Expiration (1 hour)
                                'scope' => 'https://www.googleapis.com/auth/datastore', // Scope
                        ]);
                
                        // Encode the header and claims
                        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtHeader));
                        $base64UrlClaimSet = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtClaimSet));
                
                        // Create the signature using the private key
                        $signatureInput = $base64UrlHeader . '.' . $base64UrlClaimSet;
                        openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'SHA256');
                        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                
                        // Create the complete JWT
                        $jwt = $base64UrlHeader . '.' . $base64UrlClaimSet . '.' . $base64UrlSignature;
                
                        // Get the OAuth 2.0 token using the JWT
                        $tokenUrl = 'https://oauth2.googleapis.com/token';
                        $postFields = http_build_query([
                                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                                'assertion' => $jwt,
                        ]);
                
                        // Send cURL request to get the access token
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                        $tokenResponse = curl_exec($ch);
                
                        if ($tokenResponse === false) {
                                throw new Exception('Error obtaining access token: ' . curl_error($ch));
                        }
                
                        $tokenData = json_decode($tokenResponse, true);
                        if (!isset($tokenData['access_token'])) {
                                throw new Exception('Access token not found in the response.');
                        }
                        $accessToken = $tokenData['access_token'];
                        curl_close($ch);
                
                        // Query Firestore to fetch all documents from the collection
                        $collectionUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collectionName}";
                        
                        $response = Http::withToken($accessToken)
                                ->acceptJson()
                                ->get($collectionUrl);
                
                        if ($response->successful()) {
                                $documents = $response->json();
                                //dd($documents['documents']); // Display the Firestore documents

                                foreach($documents['documents'] as $d)
                                {
                                        if($d['fields']['user_id']['stringValue'] ==$id)
                                        {
                                                $driver=User::where('user_type','D')->where('id',$d['fields']['user_id']['stringValue'])->first();

                                                if(isset($driver))
                                                {
                                                                if (isset($d['fields']['latitude']['doubleValue']) && isset($d['fields']['longitude']['doubleValue'])) {
        
                                                                        if ($d['fields']['latitude']['doubleValue'] != null || $d['fields']['longitude']['doubleValue'] != null) {
                                                        
                                                                                // $drivers[] = array('user_name' => $driver->name, 'availability' => ($d['fields']['availability']['stringValue']??'-'),
                                                                                //      'user_id' => $driver->id);


                                                                                                        if (isset($d['fields']['availability']['stringValue']) && $d['fields']['availability']['stringValue'] == "1") {

                                                                                                                $icon = "online.png";

                                                                                                                $status = "Online";

                                                                                                        } else {

                                                                                                                $icon = "offline.png";

                                                                                                                $status = "Offline";

                                                                                                        }


                                                                                        $index['driver'] = array("id" => $driver->id, "name" => $driver->name, 
                                                                                        "position" => ["lat" => $d['fields']['latitude']['doubleValue'], "long" => $d['fields']['longitude']['doubleValue']], 
                                                                                        "icon" => $icon, 'status' => $status);
        
                                                                        }
                                                                }
                                                }
                                        }
                                        // else
                                        // {
                                        //      $driver=User::where('user_type','D')->where('id',$d['fields']['user_id']['stringValue'])->first();

                                        //      if(isset($driver))
                                        //      {
                                        //                      if (isset($d['fields']['latitude']['doubleValue']) && isset($d['fields']['longitude']['doubleValue'])) {
        
                                        //                              if ($d['fields']['latitude']['doubleValue'] != null || $d['fields']['longitude']['doubleValue'] != null) {
                                                        
                                        //                                      $drivers[] = array('user_name' => $driver->name, 'availability' => ($d['fields']['availability']['stringValue']??'-'),
                                        //                                              'user_id' => $driver->id);

                                                                                        
        
                                        //                              }
                                        //                      }
                                        //      }
                                        // }


                                }

                        } else {
                                throw new Exception('Error fetching documents from Firestore: ' . $response->body());
                        }
                } catch (Exception $e) {
                        echo 'Error: ' . $e->getMessage();
                }



                //  $index['details'] = $drivers;

        

                // return view('track_driver', $index);

                return response()->json($index);

        }

        }






        public function markers() {

if(Hyvikk::api('firebase_url') != NULL)
                {
                try {

        
                        
                        $serviceAccountKeyFile =storage_path('firebase/'.Hyvikk::api('firebase_url'));
                
                        // Load the service account credentials
                        if (!file_exists($serviceAccountKeyFile)) {
                                throw new Exception('Service account key file not found.');
                        }
                        $serviceAccount = json_decode(file_get_contents($serviceAccountKeyFile), true);

                        $projectId = $serviceAccount['project_id'];
                                $collectionName = 'User_Locations';
                
                        if (!isset($serviceAccount['client_email'], $serviceAccount['private_key'])) {
                                throw new Exception('Invalid service account configuration.');
                        }
                
                        // Create JWT
                        $now_seconds = time();
                        $jwtHeader = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
                        $jwtClaimSet = json_encode([
                                'iss' => $serviceAccount['client_email'], // Issuer
                                'sub' => $serviceAccount['client_email'], // Subject
                                'aud' => 'https://oauth2.googleapis.com/token', // Audience
                                'iat' => $now_seconds, // Issued at
                                'exp' => $now_seconds + 3600, // Expiration (1 hour)
                                'scope' => 'https://www.googleapis.com/auth/datastore', // Scope
                        ]);
                
                        // Encode the header and claims
                        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtHeader));
                        $base64UrlClaimSet = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtClaimSet));
                
                        // Create the signature using the private key
                        $signatureInput = $base64UrlHeader . '.' . $base64UrlClaimSet;
                        openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'SHA256');
                        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                
                        // Create the complete JWT
                        $jwt = $base64UrlHeader . '.' . $base64UrlClaimSet . '.' . $base64UrlSignature;
                
                        // Get the OAuth 2.0 token using the JWT
                        $tokenUrl = 'https://oauth2.googleapis.com/token';
                        $postFields = http_build_query([
                                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                                'assertion' => $jwt,
                        ]);
                
                        // Send cURL request to get the access token
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                        $tokenResponse = curl_exec($ch);
                
                        if ($tokenResponse === false) {
                                throw new Exception('Error obtaining access token: ' . curl_error($ch));
                        }
                
                        $tokenData = json_decode($tokenResponse, true);
                        if (!isset($tokenData['access_token'])) {
                                throw new Exception('Access token not found in the response.');
                        }
                        $accessToken = $tokenData['access_token'];
                        curl_close($ch);
                
                        // Query Firestore to fetch all documents from the collection
                        $collectionUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collectionName}";
                        
                        $response = Http::withToken($accessToken)
                                ->acceptJson()
                                ->get($collectionUrl);
                
                        if ($response->successful()) {
                                $documents = $response->json();
                                //dd($documents['documents']); // Display the Firestore documents

                                foreach($documents['documents'] as $d)
                                {
                                        
                                                $driver=User::where('user_type','D')->where('id',$d['fields']['user_id']['stringValue'])->first();

                                                if(isset($driver))
                                                {
                                                                if (isset($d['fields']['latitude']['doubleValue']) && isset($d['fields']['longitude']['doubleValue'])) {
        
                                                                        if ($d['fields']['latitude']['doubleValue'] != null || $d['fields']['longitude']['doubleValue'] != null) {
                                                        
                                                                                $drivers[] = array('user_name' => $driver->name, 'availability' => ($d['fields']['availability']['stringValue']??'-'),
                                                                                        'user_id' => $driver->id);


                                                                                                        if (isset($d['fields']['availability']['stringValue']) && $d['fields']['availability']['stringValue'] == "1") {

                                                                                                                $icon = "online.png";

                                                                                                                $status = "Online";

                                                                                                        } else {

                                                                                                                $icon = "offline.png";

                                                                                                                $status = "Offline";

                                                                                                        }


                                                                                        $markers[] = array("id" => $driver->id, "name" => $driver->name, 
                                                                                        "position" => ["lat" => $d['fields']['latitude']['doubleValue'], "long" => $d['fields']['longitude']['doubleValue']], 
                                                                                        "icon" => $icon, 'status' => $status);
        
                                                                        }
                                                                }
                                                
                                        }
                                        


                                }

                        } else {
                                throw new Exception('Error fetching documents from Firestore: ' . $response->body());
                        }
                } catch (Exception $e) {
                        echo 'Error: ' . $e->getMessage();
                }


      

                return json_encode($markers);


        }


        }



        //temp

        public function markers_filter($id) {

                // $data = Firebase::get('/User_Locations/');



                //$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);

                $database = app('firebase.database');

                $details = $database

                        ->getReference('/User_Locations')

                        ->orderByChild('user_type')

                        ->equalTo('D')

                        ->getValue();



                //$details = json_decode($data, true);



                // dd($details);

                $markers = array();

                foreach ($details as $d) {

                        if (isset($d['latitude']) && isset($d['longitude'])) {

                                if ($d['latitude'] != null || $d['longitude'] != null) {

                                        if ($d['availability'] == "1") {

                                                $icon = "online.png";

                                                $status = "Online";

                                        } else {

                                                $icon = "offline.png";

                                                $status = "Offline";

                                        }

                                        if ($id == 1) {

                                                if ($d['availability'] == "1") {

                                                        $markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);

                                                }



                                        }if ($id == 0) {

                                                if ($d['availability'] == "0") {

                                                        $markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);

                                                }



                                        }if ($id == 2) {

                                                $markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);

                                        }



                                }

                        }

                }

                return json_encode($markers);



        }



        // marker with status selection in dropdown

        public function track_markers($id) {

                //$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);



                $database = app('firebase.database');

                $details = $database

                        ->getReference('/User_Locations')

                        ->orderByChild('user_type')

                        ->equalTo('D')

                        ->getValue();



                //$details = json_decode($data, true);



                // dd($details);

                $markers = array();

                foreach ($details as $d) {

                        if (isset($d['latitude']) && isset($d['longitude'])) {

                                if ($d['latitude'] != null || $d['longitude'] != null) {

                                        if ($d['availability'] == "1") {

                                                $icon = "online.png";

                                                $status = "Online";

                                        } else {

                                                $icon = "offline.png";

                                                $status = "Offline";

                                        }

                                        if ($id == 1) {

                                                if ($d['availability'] == "1") {

                                                        $markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);

                                                }



                                        }if ($id == 0) {

                                                if ($d['availability'] == "0") {

                                                        $markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);

                                                }



                                        }if ($id == 2) {

                                                $markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);

                                        }

                                        // //appending $new in our array

                                        // array_unshift($arr, $new);

                                        // //now make it unique.

                                        // $final = array_unique($arr);



                                }

                        }

                }

                return json_encode($markers);

        }



        // view of single driver tracking

        

        public function single_driver($id) {

                if(Hyvikk::api('firebase_url') != NULL)
                { 

                try {
                        
                        $serviceAccountKeyFile = storage_path('firebase/'.Hyvikk::api('firebase_url'));
                
                        // Load the service account credentials
                        if (!file_exists($serviceAccountKeyFile)) {
                                throw new Exception('Service account key file not found.');
                        }
                        $serviceAccount = json_decode(file_get_contents($serviceAccountKeyFile), true);

                        $projectId = $serviceAccount['project_id'];
                                $collectionName = 'User_Locations';

                
                        if (!isset($serviceAccount['client_email'], $serviceAccount['private_key'])) {
                                throw new Exception('Invalid service account configuration.');
                        }
                
                        // Create JWT
                        $now_seconds = time();
                        $jwtHeader = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
                        $jwtClaimSet = json_encode([
                                'iss' => $serviceAccount['client_email'], // Issuer
                                'sub' => $serviceAccount['client_email'], // Subject
                                'aud' => 'https://oauth2.googleapis.com/token', // Audience
                                'iat' => $now_seconds, // Issued at
                                'exp' => $now_seconds + 3600, // Expiration (1 hour)
                                'scope' => 'https://www.googleapis.com/auth/datastore', // Scope
                        ]);
                
                        // Encode the header and claims
                        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtHeader));
                        $base64UrlClaimSet = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtClaimSet));
                
                        // Create the signature using the private key
                        $signatureInput = $base64UrlHeader . '.' . $base64UrlClaimSet;
                        openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'SHA256');
                        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                
                        // Create the complete JWT
                        $jwt = $base64UrlHeader . '.' . $base64UrlClaimSet . '.' . $base64UrlSignature;
                
                        // Get the OAuth 2.0 token using the JWT
                        $tokenUrl = 'https://oauth2.googleapis.com/token';
                        $postFields = http_build_query([
                                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                                'assertion' => $jwt,
                        ]);
                
                        // Send cURL request to get the access token
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                        $tokenResponse = curl_exec($ch);
                
                        if ($tokenResponse === false) {
                                throw new Exception('Error obtaining access token: ' . curl_error($ch));
                        }
                
                        $tokenData = json_decode($tokenResponse, true);
                        if (!isset($tokenData['access_token'])) {
                                throw new Exception('Access token not found in the response.');
                        }
                        $accessToken = $tokenData['access_token'];
                        curl_close($ch);
                
                        // Query Firestore to fetch all documents from the collection
                        $collectionUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collectionName}";
                        
                        $response = Http::withToken($accessToken)
                                ->acceptJson()
                                ->get($collectionUrl);
                
                        if ($response->successful()) {
                                $documents = $response->json();
                                //dd($documents['documents']); // Display the Firestore documents

                                foreach($documents['documents'] as $d)
                                {
                                        if($d['fields']['user_id']['stringValue'] ==$id)
                                        {
                                                $driver=User::where('user_type','D')->where('id',$d['fields']['user_id']['stringValue'])->first();

                                                if(isset($driver))
                                                {
                                                                if (isset($d['fields']['latitude']['doubleValue']) && isset($d['fields']['longitude']['doubleValue'])) {
        
                                                                        if ($d['fields']['latitude']['doubleValue'] != null || $d['fields']['longitude']['doubleValue'] != null) {
                                                        
                                                                        


                                                                                                        if (isset($d['fields']['availability']['stringValue']) && $d['fields']['availability']['stringValue'] == "1") {

                                                                                                                $icon = "online.png";

                                                                                                                $status = "Online";

                                                                                                        } else {

                                                                                                                $icon = "offline.png";

                                                                                                                $status = "Offline";

                                                                                                        }


                                                                                        // $index['driver'] = array("id" => $driver->id, "name" => $driver->name, 
                                                                                        // "position" => ["lat" => $d['fields']['latitude']['doubleValue'], "long" => $d['fields']['longitude']['doubleValue']], 
                                                                                        // "icon" => $icon, 'status' => $status);

                                                                                        $driver = [array("id" => $driver->id, "name" => $driver->name, 
                                                                                        "position" => ["lat" => $d['fields']['latitude']['doubleValue'], "long" => $d['fields']['longitude']['doubleValue']], 
                                                                                        "icon" => $icon, 'status' => $status)];
        
                                                                        }
                                                                }
                                                }
                                        }
                                        


                                }

                        } else {
                                throw new Exception('Error fetching documents from Firestore: ' . $response->body());
                        }
                } catch (Exception $e) {
                        echo 'Error: ' . $e->getMessage();
                }

                return json_encode($driver);

                }
        }


        public function getFirebaseTokenByEmail(Request $request)
        {

                

                $email = $request->input('email');

    if (Hyvikk::api('firebase_url') != NULL) {
        $firebase = (new \Kreait\Firebase\Factory)
            ->withServiceAccount(storage_path('firebase/'.Hyvikk::api('firebase_url')))
            ->createAuth();

        try {
            // Step 1: Get Firebase user by email
            $user = $firebase->getUserByEmail($email);

            // Step 2: Generate custom token for the user's UID
            $customToken = $firebase->createCustomToken($user->uid);

            return response()->json([
                'token' => $customToken->toString(),
                'uid' => $user->uid,
                'email' => $user->email,
            ]);

        } catch (UserNotFound $e) {
            return response()->json(['error' => 'User not found for email: ' . $email], 404);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    return response()->json(['error' => 'Firebase config missing'], 500);
        }


        public function get_driver_info($id)
        {
                $user = User::where('id',$id)->first();

                if(isset($user))
                {
                        return response()->json(['status'=>100,'data'=>$user]);
                }
                else
                {
                        return response()->json(['status'=>200,'data'=>$user]);
                }
        }


   public function get_availability_status(Request $request)
    {
        $ids = $request->input('driverIds'); // array of IDs

        $statuses = User::whereIn('id', $ids)
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'availability' => $user->is_available,
                ];
            });

        return response()->json($statuses);
    }

}
