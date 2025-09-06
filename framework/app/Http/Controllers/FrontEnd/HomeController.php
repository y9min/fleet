<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Mail\VehicleBooked;
use App\Model\Address;
use App\Model\Bookings;
use App\Model\CompanyServicesModel;
use App\Model\Hyvikk;
use App\Model\MessageModel;
use App\Model\PasswordResetModel;
use App\Model\TeamModel;
use App\Model\Testimonial;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use Auth;
use Edujugon\PushNotification\PushNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as Login;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use session;
use Illuminate\Http\Response;
use DB;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

use App\Traits\FirebasePassword;

class HomeController extends Controller {

        use FirebasePassword;

        public function __construct() {
                if (file_exists(storage_path('installed'))) {
                        app()->setLocale(Hyvikk::frontend('language'));
                }
        }
        public function save_ratings(Request $request)
        {
         
                $data = DB::table('reviews')->where('booking_id', $request->booking_id)->first();

                if (!$data) {
                        // Insert new review
                        DB::table('reviews')->insert([
                                'user_id'=>Auth::user()->id,
                                'booking_id' => $request->booking_id,
                                'driver_id' => $request->driver_id,
                                'ratings' => $request->rating,
                                'created_at' => now(), 
                                'updated_at' => now(),
                        ]);
                        
                        return response()->json(['status'=>100,'message' => 'Rating saved successfully']);
                } else {
                        return response()->json(['status'=>200,'message' => 'Rating already exists for this booking']);
                }
        }

        public function test800()
        {
                $firebaseApiKey = "AIzaSyC22mWJ8HvrbhUZ2_MLr1fr5x3Sytj6IQs"; // Replace with your Firebase Web API Key

                // Step 1: Authenticate User and Get ID Token
                $signInUrl = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$firebaseApiKey";
                $signInResponse = Http::post($signInUrl, [
                        'email' => 'harsh100@gmail.com',
                        'password' =>'12345612',
                        'returnSecureToken' => true,
                ]);

        
                //dd($signInResponse->json()['idToken']);

                if ($signInResponse->failed()) {
                        return response()->json(['error' => 'Invalid email or password'], 401);
                }
        
                $idToken = $signInResponse->json()['idToken']; // Extract ID Token

                 // Step 2: Update Password Using ID Token
                 $updateUrl = "https://identitytoolkit.googleapis.com/v1/accounts:update?key=$firebaseApiKey";
                 $updateResponse = Http::post($updateUrl, [
                         'idToken' => $idToken,
                         'password' => '12345678',
                         'returnSecureToken' => true,
                 ]);
        
         
                 if ($updateResponse->failed()) {
                         return response()->json(['error' => 'Failed to update password'], 400);
                 }
         
                 return response()->json(['message' => 'Password updated successfully']);

        }




        public function show_document()
        {
                $serviceAccountFile = storage_path('firebase/'.Hyvikk::api('firebase_url'));

                $serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);

                dd($serviceAccount['']);
        }





        public function get_free_vehicle(Request $request) {




                if($request->status == "book_later")
                {
                        $date=date('Y-m-d', strtotime($request->date)).' '.$request->time;
                }
                else
                {
                        $date=date('Y-m-d H:i:s');
                }

                
                $from_date = $date;


                if(isset($request->booking_type)  && $request->booking_type == 'return_way')
                {
                        $to_date = date('Y-m-d', strtotime($request->return_pickup_date)).' '.$request->return_pickup_time;
                }
                else
                {
                        if($request->status == "book_now")
                        {
                

                                $date=date('Y-m-d H:i:s');

                                $to_date = $date;
                        }
                        else
                        {
                                $date=date('Y-m-d H:i:s');

                                $to_date = date('Y-m-d', strtotime($request->date)).' '.$request->time;
                        }
                        
                
                }


                $vehicleInterval = Hyvikk::get('vehicle_interval').' MINUTE';
                $condition = " and type_id = '" . $request->type_id . "'";
                
                
                if (!Auth::user() || Auth::user()->group_id == null || Auth::user()->user_type == "S") {
                                // $q = "select id from vehicles where in_service=1 and deleted_at is null  and  id not in(select vehicle_id from bookings where  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
                                
                        
                                
                                $q = "SELECT id
                                FROM vehicles
                                WHERE in_service = 1" . $condition . "
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
                                // $q = "select id from vehicles where in_service=1 and deleted_at is null and group_id=" . Auth::user()->group_id . " and  id not in(select vehicle_id from bookings where  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
        
                                


                                $q = "SELECT id
                                FROM vehicles
                                WHERE in_service = 1" . $condition . "
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
                        
                   
                        $new = array();
                        // foreach ($d as $ro) {
                        //      $vhc = VehicleModel::find($ro->id);
                        //      $text = $vhc->make_name . "-" . $vhc->model_name . "-" . $vhc->license_plate;
                        //      $luggage=$vhc->luggage??'';
                        //      array_push($new, array("id" => $ro->id, "model_name" => $text,"luggage"=>$luggage));
                        // }

                        foreach ($d as $ro) {
                $vhc = VehicleModel::find($ro->id);
    
                if(Hyvikk::get('fare_mode') == "price_wise")
                {
                    if($vhc && $vhc->getMeta('price') != 0 )
                    {
                        $text = $vhc->make_name . "-" . $vhc->model_name . "-" . $vhc->license_plate;
                        $luggage=$vhc->luggage??'';
                        array_push($new, array("id" => $ro->id, "model_name" => $text,"luggage"=>$luggage));
                    }
    
                }
               else if(Hyvikk::get('fare_mode') == "type_wise")
               {
                    $text = $vhc->make_name . "-" . $vhc->model_name . "-" . $vhc->license_plate;
                    $luggage=$vhc->luggage??'';
                    array_push($new, array("id" => $ro->id, "model_name" => $text,"luggage"=>$luggage));
                }
    
                
            }


        
                        $r = $new;
                 
                 
                        if(count($r) > 0)
                        {
                   
                                return response()->json(['status' => 100, 'data' => $r]);
                        }
                        else {
                                        return response()->json(['status' => 200]);
                        }
        
        }




        public function edit_profile_post(Request $request) {

                
                $validator = Validator::make($request->all(), [
                        'full_name' => [
                                'required',
                                function ($attribute, $value, $fail) {
                                        if (!strpos($value, ' ')) {
                                                return $fail('The '.$attribute.' must contain both first name and last name.');
                                        }
                                },
                        ],
                        'gender' => 'required|integer',
                        'phone' => 'required|numeric',
                        'email' => 'required|email|unique:users,email,' . Auth::user()->id,
                        
                ]);
        
                
                

                if ($validator->fails()) {
                        return response()->json([
                                'error' => $validator->errors(),
                        ]);
                } else {
                        $arr = explode(" ", $request->full_name);
        
                        $user = User::find(Auth::user()->id);
                        $user->name = $arr[0] . " " . $arr[1];
                        $user->user_type = "C";
                        $user->api_token = str_random(60);
                        $user->first_name = $arr[0];
                        $user->last_name = $arr[1];
                        $user->email = $request->email;
                        $user->mobno = $request->phone;
                        $user->gender = $request->gender;
        
                        // if ($request->hasFile('image') && isset($request->image)) { 
                        //      $file = $request->file('image');
                        //      $destinationPath = './uploads';
                        
                        //      if (isset($user->profile)) {
                        //              $oldImagePath = $user->profile;
                        //              if (file_exists($oldImagePath)) {
                        //                      unlink($oldImagePath);
                        //              }
                        //      }
                        
                        //      if (!file_exists($destinationPath)) {
                        //              mkdir($destinationPath, 0777, true); 
                        //      }
                        
                        //      // Generate a random name for the file
                        //      $randomFileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        
                        //      $file->move($destinationPath, $randomFileName);
                        //      $user->profile_pic = $destinationPath . '/' . $randomFileName;
                        // }


                        if(isset($request->image) && $request->image != null)
                        {
                                
                                $destinationImage='./uploads';
                                $official_profile=$request->image;
                                $offical_img_path=uniqid().$official_profile->getClientOriginalName();
                                $official_profile->move($destinationImage, $offical_img_path);
                                $user->profile_pic=$offical_img_path;
                        }
                        
        
                        $user->save();
                        return response()->json(['status' => 100]);
                }
        }
        
        
        public function redirect($method, $booking_id) {
                $booking = Bookings::find($booking_id);
                try {
                        if ($method == "cash") {
                                return redirect('cash/' . $booking_id);
                        }
                        if ($method == "stripe") {
                                return redirect('stripe/' . $booking_id);
                        }
                        if ($method == "razorpay") {
                                return redirect('razorpay/' . $booking_id);
                        }
                        if ($method == "paystack") {
                                return redirect('paystack/' . $booking_id);
                        }
                } catch (Exception $e) {
                        return redirect()->back()->withErrors(['error' => 'Payment redirection failed.']);
                }
        }
        public function redirect_payment(Request $request) {
                $validation = Validator::make($request->all(), [
                        'booking_id' => 'required',
                        'method' => 'required',
                ]);
                $errors = $validation->errors();
                if (count($errors) > 0) {
                        return redirect()->back()->withErrors(['error' => 'Something went wrong, please try again later!']);
                } else {
                        // dd($request->all());
                        $booking = Bookings::find($request->booking_id);
                        if ($booking->receipt) {
                                if ($request->method == "cash") {
                                        return redirect('cash/' . $request->booking_id);
                                }
                                if ($request->method == "stripe") {
                                        return redirect('stripe/' . $request->booking_id);
                                }
                                if ($request->method == "razorpay") {
                                        return redirect('razorpay/' . $request->booking_id);
                                }
                                if ($request->method == "paystack") {
                                        return redirect('paystack/' . $request->booking_id);
                                }
                        } else {
                                return redirect()->back()->withErrors(['error' => 'Booking receipt not generated, try after generation of booking receipt.']);
                        }
                }
        }
        public function index() {
        
                $data['testimonial'] = Testimonial::get();
                $data['vehicle'] = VehicleModel::get();
                $data['company_services'] = CompanyServicesModel::get();
                $data['vehicle_type'] = VehicleTypeModel::get();
                $data['token']=null;
        
                return view('frontend.home', $data);
        }

        public function get_ratings(Request $request)
        {
                
                $totalRatingsData =DB::table('bookings as b')
                        ->join('reviews as r', 'r.booking_id', '=', 'b.id')
                        ->where('b.vehicle_id', $request->vid)
                        ->selectRaw('SUM(r.ratings) AS total_rating, COUNT(r.ratings) AS total_records, SUM(r.ratings) / COUNT(r.ratings) AS average_rating')
                        ->first();

                $averageRating = $totalRatingsData->average_rating;

                return response()->json(['avg'=>($averageRating??0)]);
        }

        public function forget_email($token)
        {
                $data['testimonial'] = Testimonial::get();
                $data['vehicle'] = VehicleModel::get();
                $data['company_services'] = CompanyServicesModel::get();
                $data['vehicle_type'] = VehicleTypeModel::get();
                $data['token']=$token;
                return view('customer_dashboard.forgot_password', $data);
        }
        public function contact() {
                return view('frontend.contact');
        }
        public function about() {
                $data['team'] = TeamModel::get();
                return view('frontend.about', $data);
        }
        public function booking_history($id) {
                
                if (Auth::user()->id == $id) {
                        $data['bookings'] = Bookings::where('customer_id', $id)->latest()->limit(5)->get();
                } else {
                        $data['bookings'] = [];
                }
                return view('frontend.booking_history', $data);
        }

        public function load_bookinghistory(Request $request)
        {
                if (Auth::check()) {
                        $perPage = 5;
                        $currentPage = $request->query('page', 1);
                        $bookings = Bookings::where('customer_id', Auth::user()->id)->orderBy('id','Desc')->paginate($perPage, ['*'], 'page', $currentPage);
        
                        if ($bookings->isEmpty()) {
                                return response()->json(['status' => 100, 'message' => 'No data found'], 200);
                        }
        
                        $view = view('frontend.booking_history_tbl', ['bookings' => $bookings])->render();
        
                        return response()->json(['status' => 200, 'data' => $view]);
                } else {
                        return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);
                }
        }
        


        public function user_logout(Request $request) {
                $user = Login::user();
                $user->login_status = 0;
                $user->save();
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/');
        }
        public function user_login(Request $request) {
                // DEBUG: Log login attempt
                \Log::info("HomeController user_login called", [
                    "email" => $request->email,
                    "session_id" => session()->getId(),
                    "is_ajax" => $request->expectsJson() || $request->ajax(),
                    "auth_check_before" => Auth::guard("web")->check()
                ]);


                $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
  
        if ($validator->fails()) {
                    // Check if this is an AJAX request
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                                "error" => $validator->errors(),
                        ], 422);
                    } else {
                        // Standard form submission - redirect back with errors
                        return back()->withErrors($validator)->withInput();
                    }
        }
                else
                {
                        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
                                $user = Auth::guard('web')->user();
                                if ($user->user_type == "C") {
                                        $user->login_status = 1;
                                                // DEBUG: Log successful authentication
                                                \Log::info("Authentication successful", [
                                                    "user_id" => $user->id,
                                                    "user_type" => $user->user_type,
                                                    "session_id" => session()->getId(),
                                                    "auth_check_after" => Auth::guard("web")->check()
                                                ]);
                                        $user->save();
                                        // return redirect('/');
                                        // Regenerate session for security (both AJAX and form)
                                        $request->session()->regenerate();
                                        $request->session()->regenerateToken();
                                        
                                        // Check if this is an AJAX request
                                        if ($request->expectsJson() || $request->ajax()) {
                                                return response()->json(['status'=>100]);
                                        } else {
                                                // Standard form submission - redirect to dashboard
                                                return redirect('/dashboard');
                                        }
                                } else {
                                        Auth::logout();
                                        
                                        // Check if this is an AJAX request
                                        if ($request->expectsJson() || $request->ajax()) {
                                                return response()->json(['status'=>200]);
                                        } else {
                                                // Standard form submission - redirect back with error
                                                return back()->withErrors(["error" => "This login is for customers only. Please use the admin login."])->withInput();
                                        }
                                        // return back()->withErrors(["error" => "Invalid login credentials or customer not verified."], 'login')->withInput();
                                }
                        } else {
                                // Check if this is an AJAX request
                                if ($request->expectsJson() || $request->ajax()) {
                                        return response()->json(['status'=>300]);
                                } else {
                                        // Standard form submission - redirect back with error
                                        return back()->withErrors(["error" => "Invalid email or password. Please try again."])->withInput();
                                }
                                // return back()->withErrors(["error" => "Invalid login credentials"], 'login')->withInput();
                        }

                }
        }
        public function forgot() {
                return view('frontend.auth.forgot_password');
        }
        public function forgot_password(Request $request) {
                $request->validate(['email' => 'required|email']);
                $response = Password::sendResetLink(
                        $request->only('email')
                );
                if ($response == Password::RESET_LINK_SENT) {
                        return back()->with(['success' => 'Email Sent Successfully...']);
                } else {
                        return back()->with(['error' => 'User Email Not Valid Please Enter Valid Email.'])->withInput();
                }
        }
        public function customer_register(Request $request) {
                //dd($request->all());
                $validator = Validator::make($request->all(), [
                        'first_name' =>'required',
                        'last_name'=>'required',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required|min:6|max:16',
                        'confirm_password' => 'required|same:password',
                        'gender' => 'required|integer',
                        'phone' => 'required|numeric',
                        'agree'=>'required'
                ]);
                        
                
                if ($validator->fails()) {
                        // return back()->withErrors($validator, 'register')->withInput();
                        return response()->json([
                                'error' => $validator->errors(),
                        ]);
                }

                $name=$request->first_name.' '.$request->last_name;
                
                $id = User::create([
                        "name" => $name,
                        "email" => $request->email,
                        "password" => bcrypt($request->password),
                        "user_type" => "C",
                        "api_token" => str_random(60),
                ])->id;
                
                $user = User::find($id);
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->address = $request->address;
                $user->mobno = $request->phone;
                $user->gender = $request->gender;
                if(isset($request->address))
                {
                        $user->address=$request->address;
                }
                $user->save();
                
                return response()->json(['status'=>100]);
                // return back()->with('success', 'You are registered Successfully! please login here.');
        }
        public function send_enquiry(Request $request) {
                 //dd($request->all());
                $message = MessageModel::create([
                        "name" => $request->name,
                        "email" => $request->email,
                        "message" => $request->message,
                ]);

                if($message)
                {
                        return redirect()->back()->with('success1', 'Your message has been sent successfully!');
                }
                else
                {
                        return redirect()->back()->with('error1', 'Your message has not been sent successfully!');
                }

        }
        public function book(Request $request) {

                        


                $rules = [];
                if($request->radio1 == "book_now")
                {
                        $rules['pickup_address']='required';
                        $rules['dropoff_address']='required|different:pickup_address';
                        $rules['vehicle_type']='required';
                        $rules['vehicle']='required';
                        $rules['no_of_person']='required|integer';

                        if(isset($request->booking_type) && $request->booking_type  == "return_way")
                        {
                                $rules['return_pickup_date']='required';
                                $rules['return_pickup_time']='required';


                        }
                }
                else
                {
                        $rules['pickup_address']='required';
                        $rules['dropoff_address']='required|different:pickup_address';
                        $rules['vehicle_type']='required';
                        $rules['vehicle']='required';
                        $rules['no_of_person']='required|integer';
                        $rules['pickup_date'] = 'required|date_format:d-m-Y|after:today';
                        $rules['pickup_time']='required';

                        if(isset($request->booking_type) && $request->booking_type  == "return_way")
                        {
                                $rules['return_pickup_date']='required';
                                $rules['return_pickup_time']='required';

                        

                        }

                }
                $validator = Validator::make($request->all(), $rules);
        
                if ($validator->fails()) {
                        return response()->json([
                                'error' => $validator->errors(),
                        ]);
                }
                else
                {

                $max_seats = VehicleTypeModel::find($request->get('vehicle_type'))->seats;
                if ($request->get("no_of_person") > $max_seats) {
                        // return back()->withErrors(["error" => ])->withInput();
                
                        return response()->json(['status'=>500,'message'=>"Number of Travellers exceed seating capity of the vehicle | Seats Available : " . $max_seats . ""]);
                }
                if (Auth::user() && Auth::user()->user_type == 'C') {
                        
                        
                        if ($request->radio1 == "book_now") {
                                
                                
                                        $booking_time = Hyvikk::frontend('booking_time');
                                        $id = Bookings::create(['customer_id' => Auth::user()->id,
                                                'pickup_addr' => $request->pickup_address,
                                                'dest_addr' => $request->dropoff_address,
                                                'travellers' => $request->no_of_person,
                                                'note' => $request->note,
                                                'pickup' => date('Y-m-d H:i:s', strtotime('+' . $booking_time . ' hours')),
                                                'vehicle_id'=>$request->vehicle
                                        ])->id;

                                


                                        $booking = Bookings::find($id);
                                        $booking->journey_date = date('d-m-Y');
                                        $booking->journey_time = date('H:i:s');
                                        $booking->accept_status = 0; 
                                        $booking->ride_status = "Pending";
                                        $booking->booking_type = 0;
                                        $booking->vehicle_typeid = $request->vehicle_type;
                                        
                                        

                                        $key1 = (Hyvikk::api('api_key') ?? '-');
        
                                        $pickupAddress1 = urlencode($request->pickup_address);
                                        $dropoffAddress1 = urlencode($request->dropoff_address);
                                        
                                        $url1 = "https://maps.googleapis.com/maps/api/directions/json?origin={$pickupAddress1}&destination={$dropoffAddress1}&key={$key1}";
                                        
                                        $ch1 = curl_init();
                                        curl_setopt($ch1, CURLOPT_URL, $url1);
                                        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                                        
                                        // Turn off SSL certificate verification
                                        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
                                        curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
                                        
                                        $response1 = curl_exec($ch1);
                                        curl_close($ch1);
                                        
                                        $dataFetch = json_decode($response1, true);
                                        
                                        if ($dataFetch['status'] === 'OK') {
                                                $totalTimeInSeconds = $dataFetch['routes'][0]['legs'][0]['duration']['value'];
                                                $hours = floor($totalTimeInSeconds / 3600);
                                                $minutes = floor(($totalTimeInSeconds % 3600) / 60);
                                                $seconds = $totalTimeInSeconds % 60;
                                                $totalTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        
                                                $booking->total_kms = explode(" ", str_replace(",", "", $dataFetch['routes'][0]['legs'][0]['distance']['text']))[0];

                                                $booking->total_time=$totalTime;
                                } else {
                                                $totalTime = "00:00:00";
                                                $booking->total_time=$totalTime;
                                                $booking->total_kms=0;
                                        }

                                        $booking->save();


                                        if(isset($request->booking_type) && $request->booking_type  == "return_way")
                                        {
                                                        $booking_time = Hyvikk::frontend('booking_time');
                                                        $ids = Bookings::create(['customer_id' => Auth::user()->id,
                                                                'pickup_addr' => $request->dropoff_address,
                                                                'dest_addr' => $request->pickup_address,
                                                                'travellers' => $request->no_of_person,
                                                                'note' => $request->note,
                                                                'pickup' => date('Y-m-d', strtotime($request->return_pickup_date)) . " " . $request->return_pickup_time,
                                                                'vehicle_id'=>$request->vehicle
                                                        ])->id;

                                        


                                                $bookings = Bookings::find($ids);
                                                $bookings->journey_date = date('Y-m-d', strtotime($request->return_pickup_date));
                                                $bookings->journey_time = $request->return_pickup_time;
                                                $bookings->accept_status = 0; 
                                                $bookings->ride_status = "Pending";
                                                $bookings->vehicle_typeid = $request->vehicle_type;

                                                $bookings->booking_type = 0;

                                                $bookings->return_flag=1;

                                                $bookings->parent_booking_id=$booking->id;


                                                $key2 = (Hyvikk::api('api_key') ?? '-');
                        
                                                $pickupAddress2 = urlencode($request->pickup_address);
                                                $dropoffAddress2 = urlencode($request->dropoff_address);
                                                
                                                $url2 = "https://maps.googleapis.com/maps/api/directions/json?origin={$pickupAddress2}&destination={$dropoffAddress2}&key={$key2}";
                                                
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
                                                        $totalTime2 = sprintf('%02d:%02d:%02d', $hours2, $minutes2, $seconds2);
                
                                                        $bookings->total_kms = explode(" ", str_replace(",", "", $dataFetch2['routes'][0]['legs'][0]['distance']['text']))[0];

                                                        $bookings->total_time=$totalTime2;


                
                                                } else {
                                                        $totalTime2 = "00:00:00";
                                                        $bookings->total_time=$totalTime2;
                                                        $bookings->total_kms=0;
                                                }

                                                $bookings->save();
                                        }




                                        Address::updateOrCreate(['customer_id' => Auth::user()->id, 'address' => $request->pickup_address]);
                                        Address::updateOrCreate(['customer_id' => Auth::user()->id, 'address' => $request->dropoff_address]);
                                        $this->book_now_notification($booking->id, $booking->vehicle_typeid);
                                        if (Hyvikk::email_msg('email') == 1) {

                                                 try{
                                                Mail::to($booking->customer->email)->send(new VehicleBooked($booking));

                                                }
                                                catch (\Throwable $e) {
                                                                
                                                }
                                        }
                                //}
                        } else {

                                
                                        $id = Bookings::create(['customer_id' => Auth::user()->id,
                                                'pickup_addr' => $request->pickup_address,
                                                'dest_addr' => $request->dropoff_address,
                                                'travellers' => $request->no_of_person,
                                                'note' => $request->note,
                                                'pickup' => date('Y-m-d', strtotime($request->pickup_date)) . " " .$request->pickup_time,
                                                'vehicle_id'=>$request->vehicle
                                        ])->id;

                        
                                        $booking = Bookings::find($id);
                                        $booking->journey_date = $request->pickup_date;
                                        $booking->journey_time = $request->pickup_time;
                                        $booking->booking_type = 1;
                                        $booking->accept_status = 0; //0=yet to accept, 1= accept
                                        $booking->ride_status = "Pending";
                                        $booking->vehicle_typeid = $request->vehicle_type;

                                        $key1 = (Hyvikk::api('api_key') ?? '-');
        
                                        $pickupAddress1 = urlencode($request->pickup_address);
                                        $dropoffAddress1 = urlencode($request->dropoff_address);
                                        
                                        $url1 = "https://maps.googleapis.com/maps/api/directions/json?origin={$pickupAddress1}&destination={$dropoffAddress1}&key={$key1}";
                                        
                                        $ch1 = curl_init();
                                        curl_setopt($ch1, CURLOPT_URL, $url1);
                                        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                                        
                                        // Turn off SSL certificate verification
                                        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
                                        curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
                                        
                                        $response1 = curl_exec($ch1);
                                        curl_close($ch1);
                                        
                                        $dataFetch = json_decode($response1, true);
                                        
                                        if ($dataFetch['status'] === 'OK') {
                                                $totalTimeInSeconds = $dataFetch['routes'][0]['legs'][0]['duration']['value'];
                                                $hours = floor($totalTimeInSeconds / 3600);
                                                $minutes = floor(($totalTimeInSeconds % 3600) / 60);
                                                $seconds = $totalTimeInSeconds % 60;
                                                $totalTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        
                                                $booking->total_kms = explode(" ", str_replace(",", "", $dataFetch['routes'][0]['legs'][0]['distance']['text']))[0];

                                                $booking->total_time=$totalTime;
        
                                        } else {
                                                $totalTime = "00:00:00";
                                                $booking->total_time=$totalTime;
                                                $booking->total_kms=0;
                                        }
                                        
                                        $booking->save();


                                        if(isset($request->booking_type) && $request->booking_type  == "return_way")
                                        {
                                                $ids = Bookings::create(['customer_id' => Auth::user()->id,
                                                'pickup_addr' => $request->dropoff_address,
                                                'dest_addr' => $request->pickup_address,
                                                'travellers' => $request->no_of_person,
                                                'note' => $request->note,
                                                'pickup' => date('Y-m-d', strtotime($request->return_pickup_date)) . " " . $request->return_pickup_time,
                                                'vehicle_id'=>$request->vehicle
                                                ])->id;

                        
                                                $bookings = Bookings::find($ids);
                                                $bookings->journey_date = date('Y-m-d', strtotime($request->return_pickup_date));
                                                $bookings->journey_time = $request->return_pickup_time;
                                                $bookings->accept_status = 0; //0=yet to accept, 1= accept
                                                $bookings->ride_status = "Pending";
                                                $bookings->vehicle_typeid = $request->vehicle_type;
                                        
                                                $bookings->booking_type = 1;

                                                $bookings->return_flag=1;

                                                $bookings->parent_booking_id=$booking->id;

                                                $key2 = (Hyvikk::api('api_key') ?? '-');
                        
                                                $pickupAddress2 = urlencode($request->pickup_address);
                                                $dropoffAddress2 = urlencode($request->dropoff_address);
                                                
                                                $url2 = "https://maps.googleapis.com/maps/api/directions/json?origin={$pickupAddress2}&destination={$dropoffAddress2}&key={$key2}";
                                                
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
                                                        $totalTime2 = sprintf('%02d:%02d:%02d', $hours2, $minutes2, $seconds2);
                
                                                        $bookings->total_kms = explode(" ", str_replace(",", "", $dataFetch2['routes'][0]['legs'][0]['distance']['text']))[0];

                                                        $bookings->total_time=$totalTime2;


                
                                                } else {
                                                        $totalTime2 = "00:00:00";
                                                        $bookings->total_time=$totalTime2;
                                                        $bookings->total_kms=0;
                                                }

                                                $bookings->save();
                                        }


                                        Address::updateOrCreate(['customer_id' => Auth::user()->id, 'address' => $request->pickup_address]);
                                        Address::updateOrCreate(['customer_id' => Auth::user()->id, 'address' => $request->dropoff_address]);
                                        $this->book_later_notification($booking->id, $booking->vehicle_typeid);
                                        if (Hyvikk::email_msg('email') == 1) {
                                                         try{
                                                                Mail::to($booking->customer->email)->send(new VehicleBooked($booking));
                                                        }
                                                        catch (\Throwable $e) {
                                                        }
                                        }
                                // }
                        }
                        try {

                                if (isset($request->method) && Hyvikk::frontend('admin_approval') == 0) {
                                        // fare calc
                                        $key = Hyvikk::api('api_key');

                                        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . str_replace(" ", "", $booking->pickup_addr) . "&destination=" . str_replace(" ", "", $booking->dest_addr) . "&mode=driving&units=metric&sensor=false&key=" . $key;
                                
                                        $ch = curl_init();
                                        curl_setopt($ch, CURLOPT_URL, $url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        $data = curl_exec($ch);
                                        
                                        $response = json_decode($data, true);
                
                                        if ($response['status'] == "OK") {

                                                $vehicle=VehicleModel::where('id',$request->vehicle)->first();

                                                

                                                $v_type = VehicleTypeModel::find($request->vehicle_type);
                                                $type = strtolower(str_replace(" ", "", $v_type->vehicletype));
                                                $fare_details = array();
                                                $total_kms = explode(" ", str_replace(",", "", $response['routes'][0]['legs'][0]['distance']['text']))[0];


                                if(Hyvikk::get('fare_mode') == "type_wise")
                {
                    $base_fare = Hyvikk::fare($type . '_base_fare');
                    $km_base = Hyvikk::fare($type . '_base_km');
                    $std_fare = Hyvikk::fare($type . '_std_fare');
                    $base_km = Hyvikk::fare($type . '_base_km');
    
                    if ($total_kms <= $km_base) {
                        $total_fare = $base_fare;
    
                    } else {
                        $total_fare = $base_fare + (($total_kms - $km_base) * $std_fare);
                    }
                    // calculate tax charges
                    $count = 0;
                    if (Hyvikk::get('tax_charge') != "null") {
                        $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                        foreach ($taxes as $key => $val) {
                            $count = $count + $val;
                        }
                    }
                    $total_fare = round($total_fare, 2);
                    $tax_total = round((($total_fare * $count) / 100) + $total_fare, 2);
                    $total_tax_percent = $count;
                    $total_tax_charge_rs = round(($total_fare * $count) / 100, 2);
    
                    $arr=[
                        'total_fare'=>$total_fare,
                        'total_tax_percent'=>$total_tax_percent,
                        'total_tax_charge_rs'=>$total_tax_charge_rs,
                        'tax_total'=>$tax_total
                    ];


                }
                else if(Hyvikk::get('fare_mode') == "price_wise")
                {

                                        if($vehicle->getMeta('price') != '0')
                    {
    
                        $total_fare=$vehicle->getMeta('price');
    
                        $count = 0;
                        if (Hyvikk::get('tax_charge') != "null") {
                            $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                            foreach ($taxes as $key => $val) {
                                $count = $count + $val;
                            }
                        }
                        $total_fare = round($total_fare, 2);
                        $tax_total = round((($total_fare * $count) / 100) + $total_fare, 2);
                        $total_tax_percent = $count;
                        $total_tax_charge_rs = round(($total_fare * $count) / 100, 2);
        
                        $arr=[
                            'total_fare'=>$total_fare,
                            'total_tax_percent'=>$total_tax_percent,
                            'total_tax_charge_rs'=>$total_tax_charge_rs,
                            'tax_total'=>$tax_total
                        ];


                    }
                }

                                
                                                $booking->setMeta([
                                                        'customerId' => Auth::id(),
                                                        'day' => 1,
                                                        'mileage' => $total_kms,
                                                        'waiting_time' => 0,
                                                        'date' => date('Y-m-d'),
                                                        'total' => round($arr['total_fare'], 2),
                                                        'total_kms' => $total_kms,
                                                        'tax_total' => round($arr['tax_total'], 2),
                                                        'total_tax_percent' => round($arr['total_tax_percent'], 2),
                                                        'total_tax_charge_rs' => round($arr['total_tax_charge_rs'], 2),
                                                        
                                                ]);
                                                $booking->save();

                                        if(isset($request->booking_type) && $request->booking_type  == "return_way")
                                        {
                                                $bookings->setMeta([
                                                        'customerId' => Auth::id(),
                                                        'day' => 1,
                                                        'mileage' => $total_kms,
                                                        'waiting_time' => 0,
                                                        'date' => date('Y-m-d'),
                                                        'total' => round($arr['total_fare'], 2),
                                                        'total_kms' => $total_kms,
                                                        'tax_total' => round($arr['tax_total'], 2),
                                                        'total_tax_percent' => round($arr['total_tax_percent'], 2),
                                                        'total_tax_charge_rs' => round($arr['total_tax_charge_rs'], 2),
                                                        
                                                ]);
                                                $bookings->save();
                                        }
                                        
                                                return response()->json(['status'=>100,'method'=>$request->method,'booking_id'=>$booking->id]);

                                        } else {
                                                //return back()->withErrors(['error' => 'Your Booking Request has been Submitted Successfully, but payment has failed.']);
                                                return response()->json(['status'=>200,'message'=>'Your Request has been Submitted Successfully']);
                                        }
                                }
                        } catch (Exception $e) {
                                return response()->json(['status'=>200,'message'=>'Your Request has been Submitted Successfully']);
                        }
                        // return back()->with('success', 'Your Request has been Submitted Successfully.');
                        return response()->json(['status'=>300,'message'=>'Your Request has been Submitted Successfully.']);

                } else {
                        // return redirect("/#login")->withErrors(["error" => "Please Login Fleet Manager"], 'login');
                
                        return response()->json(['status'=>400,'message'=>'Please Login Fleet Manager']);
                }

         }
        }
        public function send_reset_link(Request $request) {
                $validator = Validator::make($request->all(), [
            'email' => 'required|email',
                ]);

        if ($validator->fails()) {
            return response()->json([
                                'error' => $validator->errors(),
                        ]);
        } else {

                        $user = User::where('email', $request->email)->get()->toArray();
                        if (!empty($user) && $user[0]['user_type'] == "C") {
                                $this->validateEmail($request);
                                $email = $request->email;
                                $token = Str::random(60);
                                PasswordResetModel::where('email', $email)->delete();
                                PasswordResetModel::create(['email' => $email, 'token' => Hash::make($token), 'created_at' => date('Y-m-d H:i:s')]);
                                
                                try{            
                                Mail::to($email)->send(new ForgotPassword($email, $token));
                                } catch (\Throwable $e) {

                                }
                                return response()->json(['status' => 100, 'message' => "We have e-mailed your password reset link!"]);
                        } else {
                                return response()->json(['status' => 100, 'message' => "Please Enter Valid Email Address..."]);
                        }

                }
        }


        public function reset_password_email(Request $request)
    {
                $validator = Validator::make($request->all(), [
                        'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
                ]);

        if ($validator->fails()) {
            return response()->json([
                                'error' => $validator->errors(),
                        ]);
        } else {
            $response = $this->broker()->reset(
                $this->credentials($request), function ($user, $password) {
                    $this->resetPassword($user, $password);
                }
            );

            if ($response == Password::PASSWORD_RESET) {
             
                                return response()->json(['status' => 100, 'message' => __($response)]);
            } else {
                                return response()->json(['status' => 200, 'message' => __($response)]);
            }

        }
    }
        protected function validateEmail(Request $request) {
                $this->validate($request, ['email' => 'required|email']);
        }
        public function reset($token) {
                $data['token'] = $token;
                $data['email'] = $_GET['email'];
                return view('frontend.auth.reset', $data);
        }
        public function reset_password(Request $request) {

                
                $validator = Validator::make($request->all(), [
                        'password' => 'required',
                        'new_password' => 'required|same:confirm_password|min:6',
                ]);
                
                if ($validator->fails()) {
                        return response()->json([
                                'error' => $validator->errors(),
                        ]);
                }
                else
                {       
                        if (Auth::check()) {
                                $user = Auth::user();
                                if (password_verify($request->password, $user->password)) {
                                        
                                        $user->password = bcrypt($request->new_password);
                                        if ($user->save()) {

                                                $this->newpassword($user->email,$request->new_password);

                                                return response()->json(['status' => 200, 'message' => 'Password updated successfully']);
                                        } else {
                                                return response()->json(['status' => 500, 'message' => 'Failed to update password']);
                                        }
                                } else {
                                        return response()->json(['status' => 401, 'message' => 'Invalid current password']);
                                }
                        } else {
                                return response()->json(['status' => 403, 'message' => 'Unauthorized']);
                        }
                }
        }





        
        public function broker() {
                return Password::broker();
        }
        protected function credentials(Request $request) {
                return $request->only(
                        'email', 'password', 'password_confirmation', 'token'
                );
        }
        protected function resetPassword($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                if($user->save())
        {
            $this->newpassword($user->email,$password);
        }
        }
        // book now notification
        public function book_now_notification($id, $type_id) {
                $booking = Bookings::find($id);
                $data['success'] = 1;
                $data['key'] = "book_now_notification";
                $data['message'] = 'Data Received.';
                $data['title'] = "New Ride Request (Book Now)";
                $data['description'] = "Do you want to Accept it ?";
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['data'] = array(
                        // 'success' => 1,
                        // 'key' => "book_now_notification",
                        // 'message' => 'Data Received.',
                        // 'title' => "New Ride Request (Book Now)",
                        // 'description' => "Do you want to Accept it ?",
                        // 'timestamp' => date('Y-m-d H:i:s'),
                        'riderequest_info' => array(
                                'booking_type' => 'Book now notification',
                                'user_id' => $booking->customer_id,
                                'booking_id' => $booking->id,
                                'source_address' => $booking->pickup_addr,
                                'dest_address' => $booking->dest_addr,
                                'book_date' => date('Y-m-d'),
                                'book_time' => date('H:i:s'),
                                'journey_date' => date('d-m-Y'),
                                'journey_time' => date('H:i:s'),
                                'accept_status' => $booking->accept_status));
                if ($type_id == null) {
                        $vehicles = VehicleModel::get()->pluck('id')->toArray();
                } else {
                        $vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
                }
                $drivers = User::where('user_type', 'D')->get();
                if (Hyvikk::api('is_on_ven_app') == 1) {
                        $vendors = User::where('user_type', 'O')->get();
                        //dd($vendors);
                        foreach ($vendors as $v) {

                                if ($v->fcm_id != null) {

                                        $push = new PushNotification('fcm');

                                        // $push->setMessage($data)
                                        //     ->setApiKey(env('server_key'))
                                        //     ->setDevicesToken([$v->fcm_id])
                                        //     ->send();
                                        $push->setMessage($data)

                                                ->setApiKey(env('vendor_server_key'))

                                                ->setDevicesToken([$v->fcm_id]);

                                        $push = $push->send();
                                        // $feedback = $push->getFeedback();
                                        // // dd($feedback);
                                        // if ($feedback->success == 1) {

                                        //     $success = 1;

                                        // } else {

                                        //     $success = 0;

                                        // }
                                }

                        }
                }

                foreach ($drivers as $d) {
                        if (in_array($d->vehicle_id, $vehicles)) {
                                if ($d->fcm_id != null && $d->is_available == 1 && $d->is_on != 1) {
                                        // PushNotification::app('appNameAndroid')
                                        //     ->to($d->fcm_id)
                                        //     ->send($data);
                                        $push = new PushNotification('fcm');
                                        $push->setMessage($data)
                                                ->setApiKey(env('server_key'))
                                                ->setDevicesToken([$d->fcm_id])
                                                ->send();
                                }
                        }
                }
        }
        // book later notification
        public function book_later_notification($id, $type_id) {
                $booking = Bookings::find($id);
                $data['success'] = 1;
                $data['key'] = "book_later_notification";
                $data['message'] = 'Data Received.';
                $data['title'] = "New Ride Request (Book Later)";
                $data['description'] = "Do you want to Accept it ?";
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['data'] = array(
                        // 'success' => 1,
                        // 'key' => "book_later_notification",
                        // 'message' => 'Data Received.',
                        // 'title' => "New Ride Request (Book Later)",
                        // 'description' => "Do you want to Accept it ?",
                        // 'timestamp' => date('Y-m-d H:i:s'),
                        'riderequest_info' => array('user_id' => $booking->customer_id,
                                'booking_type' => 'Book later notification',
                                'booking_id' => $booking->id,
                                'source_address' => $booking->pickup_addr,
                                'dest_address' => $booking->dest_addr,
                                'book_date' => date('Y-m-d'),
                                'book_time' => date('H:i:s'),
                                'journey_date' => $booking->journey_date,
                                'journey_time' => $booking->journey_time,
                                'accept_status' => $booking->accept_status));
                if ($type_id == null) {
                        $vehicles = VehicleModel::get()->pluck('id')->toArray();
                } else {
                        $vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
                }
                $drivers = User::where('user_type', 'D')->get();
                if (Hyvikk::api('is_on_ven_app') == 1) {
                        $vendors = User::where('user_type', 'O')->get();
                        foreach ($vendors as $v) {

                                if ($v->fcm_id != null) {

                                        // PushNotification::app('appNameAndroid')
                                        //     ->to($v->fcm_id)
                                        //     ->send($vata);

                                        $push = new PushNotification('fcm');
                                        $push->setMessage($data)
                                                ->setApiKey(env('vendor_server_key'))
                                                ->setDevicesToken([$v->fcm_id])
                                                ->send();
                                }

                        }
                }

                foreach ($drivers as $d) {
                        if (in_array($d->vehicle_id, $vehicles)) {
                                // echo $d->vehicle_id . " " . $d->id . "<br>";
                                if ($d->fcm_id != null) {
                                        // PushNotification::app('appNameAndroid')
                                        //     ->to($d->fcm_id)
                                        //     ->send($data);
                                        $push = new PushNotification('fcm');
                                        $push->setMessage($data)
                                                ->setApiKey(env('server_key'))
                                                ->setDevicesToken([$d->fcm_id])
                                                ->send();
                                }
                        }
                }
        }
}
