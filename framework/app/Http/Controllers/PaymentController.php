<?php

/*
@copyright

Fleet Manager v7.0

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers;

use App\Model\BookingPaymentsModel;
use App\Model\Bookings;
use App\Model\User;
use App\Model\Hyvikk as ModelHyvikk;
use Exception;
use Hyvikk;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Model\IncomeModel;
use App\Model\BookingIncome;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;

class PaymentController extends Controller {

	// public function redirect_payment(Request $request) {
	//     if ($request->method == "cash") {
	//         return redirect('cash/' . $request->booking_id);
	//     }
	//     if ($request->method == "stripe") {
	//         return redirect('stripe/' . $request->booking_id);
	//     }
	//     if ($request->method == "razorpay") {
	//         return redirect('razorpay/' . $request->booking_id);
	//     }
	// }

	public function paystack($booking_id) {
		try {
			# in test mode only ZAR currency will support.
			$is_in_testing = strpos(Hyvikk::payment('paystack_secret'), 'test') !== false ? 1 : 0;

			$booking = Bookings::find($booking_id);
			$booking->load('customer');
			$url = "https://api.paystack.co/transaction/initialize";
			$fields = [
				'email' => ($booking->customer->email) ?? "customer1@gmail.com",
				'amount' => $booking->tax_total * 100,
				'currency' => $is_in_testing ? 'ZAR' : Hyvikk::payment('currency_code'),
				'callback_url' => url('paystack-success'),
				'metadata' => [
					'booking_id' => $booking->id,
				],
			];
			$fields_string = http_build_query($fields);
			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Bearer " . Hyvikk::payment('paystack_secret'),
				"Cache-Control: no-cache",
			));

			//So that curl_exec returns the contents of the cURL; rather than echoing it
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			//execute post
			$result = curl_exec($ch);

			$err = curl_error($ch);
			curl_close($ch);

			if ($err) {
				$error_msg = $err;
				return view('payments.payment_failed', compact('error_msg'));
			} elseif ($result) {
				$authorization_data = json_decode($result);
				// dd($authorization_data);
				$authorization_url = $authorization_data->data->authorization_url;
				header("Location: " . $authorization_url);
				exit();
			}
		} catch (\Exception $e) {
			$error_msg = $e->getMessage();
			return view('payments.payment_failed', compact('error_msg'));

		}
	}

	public function paystack_callback(Request $request) {
		try {
			$reference = $request->reference;

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . ModelHyvikk::payment('paystack_secret'),
					"Cache-Control: no-cache",
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);

			if ($err) {
				$error_msg = $err;
				return view('payments.payment_failed', compact('error_msg'));
			} else {
				//echo $response;
				if ($response) {
					$result = json_decode($response);
					// dd($result);
					$booking_id = $result->data->metadata->booking_id;
					$status = $result->data->gateway_response;
					$transaction_id = $result->data->id;
					$payment_status = $result->data->status;
					if ($status == "Successful") {
						$booking = Bookings::find($booking_id);
						$booking->payment = 1;
						$booking->payment_method = "paystack";
						if ($booking->vehicle_id) {
							$booking->status = 1;
							$booking->ride_status = "Completed";
						}
						$booking->save();

						BookingPaymentsModel::create([
							'method' => 'paystack',
							'booking_id' => $booking_id,
							'amount' => $booking->tax_total,
							'payment_details' => json_encode($result->data),
							'transaction_id' => $transaction_id,
							'payment_status' => $payment_status,
						]);

						$data['amount'] = $booking->tax_total;
						return view('payments.payment_success', $data);
						return redirect(url('transaction'));
					} else {
						$error_msg = $status;
						return view('payments.payment_failed', compact('error_msg'));
					}
				}
			}
		} catch (\Exception $e) {
			$error_msg = $e->getMessage();
			return view('payments.payment_failed', compact('error_msg'));
		}
	}

	public function transaction() {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.paystack.co/transaction",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . Hyvikk::payment('paystack_secret'),
				"Cache-Control: no-cache",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$data = json_decode($response);
			dd($data->data);
		}
	}

	public function cash($booking_id) {
		$booking = Bookings::find($booking_id);
		$booking->payment = 1;
		$booking->payment_method = "cash";
		if ($booking->vehicle_id) {
			$booking->status = 0;
			$booking->ride_status = "Pending";
		}

		$booking->save();

		BookingPaymentsModel::create(['method' => 'cash', 'booking_id' => $booking_id, 'amount' => $booking->tax_total, 'payment_details' => null, 'transaction_id' => null, 'payment_status' => "succeeded"]);

		$total_tax=$booking->tax_total;

		

		$bookings=Bookings::select("bookings.*")
                ->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                ->where('bookings_meta.key', 'parent_booking_id')
                ->where('bookings_meta.value', $booking->id)
                ->first();

		

		if(isset($bookings))
		{
			$bookings->payment = 1;
			$bookings->payment_method = "cash";
			if ($bookings->vehicle_id) {
				$bookings->status = 0;
				$bookings->ride_status = "Pending";
			}

			$bookings->save();

			BookingPaymentsModel::create(['method' => 'cash', 'booking_id' => $bookings->id, 'amount' => $bookings->tax_total, 'payment_details' => null, 'transaction_id' => null, 'payment_status' => "succeeded"]);

			$total_tax+=$bookings->tax_total;
		}
		
		$data['amount'] = ($total_tax??0);
		
		return view('payments.payment_success', $data);
	}
	
	
	// public function stripe($booking_id) {
	// try {
	// 		$booking = Bookings::find($booking_id);
	// 		\Stripe\Stripe::setApiKey(Hyvikk::payment('stripe_secret_key'));
	// 		$methods = ['card'];
	// 		if (Hyvikk::payment('currency_code') == "EUR") {
	// 			$methods = ['card', 'ideal'];
	// 		}
	// 		$session = \Stripe\Checkout\Session::create([
	// 			'payment_method_types' => $methods,
	// 			'line_items' => [[
	// 				'name' => 'Booking',
	// 				'amount' => $booking->tax_total * 100,
	// 				'currency' => strtolower(Hyvikk::payment('currency_code')),
	// 				'quantity' => 1,
	// 			]],
	// 			'payment_intent_data' => [
	// 				'capture_method' => 'automatic',
	// 			],
	// 			'success_url' => url('stripe-success') . "?session_id={CHECKOUT_SESSION_ID}&booking_id=" . $booking_id,
	// 			'cancel_url' => url('stripe-cancel'),
	// 		]);
	// 		$session_id = $session['id'];
	// 		return view('payments.stripe', compact('session_id'));
	// 	} catch (\Stripe\Exception\CardException $e) {
	// 		// Since it's a decline, \Stripe\Exception\CardException will be caught
	// 		$error_msg = $e->getError()->message;
	// 		return view('payments.payment_failed', compact('error_msg'));

	// 	} catch (\Stripe\Exception\RateLimitException $e) {
	// 		// Too many requests made to the API too quickly
	// 		$error_msg = $e->getError()->message;
	// 		return view('payments.payment_failed', compact('error_msg'));

	// 	} catch (\Stripe\Exception\InvalidRequestException $e) {
	// 		// Invalid parameters were supplied to Stripe's API
	// 		$error_msg = $e->getError()->message;
	// 		return view('payments.payment_failed', compact('error_msg'));

	// 	} catch (\Stripe\Exception\AuthenticationException $e) {
	// 		// Authentication with Stripe's API failed
	// 		// (maybe you changed API keys recently)
	// 		$error_msg = $e->getError()->message;
	// 		return view('payments.payment_failed', compact('error_msg'));

	// 	} catch (\Stripe\Exception\ApiConnectionException $e) {
	// 		// Network communication with Stripe failed
	// 		$error_msg = $e->getError()->message;
	// 		return view('payments.payment_failed', compact('error_msg'));

	// 	} catch (\Stripe\Exception\ApiErrorException $e) {
	// 		// Display a very generic error to the user, and maybe send
	// 		// yourself an email
	// 		$error_msg = $e->getError()->message;
	// 		return view('payments.payment_failed', compact('error_msg'));

	// 	} catch (Exception $e) {
	// 		// Something else happened, completely unrelated to Stripe
	// 		$error_msg = $e->getError()->message;
	// 		return view('payments.payment_failed', compact('error_msg'));
	// 	}
	// }

	// public function stripe($booking_id) {
	// 	try {
	// 			$booking = Bookings::find($booking_id);
	// 			\Stripe\Stripe::setApiKey(Hyvikk::payment('stripe_secret_key'));
	// 			$methods = ['card'];
	// 			if (Hyvikk::payment('currency_code') == "EUR") {
	// 				$methods = ['card', 'ideal'];
	// 			}
	// 			$user = User::find($booking->customer_id);
	
	// 			$address = $user->address;

	// 			if (!empty($address)) {
	// 				$lines = explode("\n", $address);

	// 				if (isset($lines[0])) {
	// 					$streetAddress = $lines[0];
	// 				} else {
	// 					$streetAddress = "Test";
	// 				}

	// 				if (isset($lines[1])) {
	// 					list($city, $statePostalCode) = explode(', ', $lines[1]);
	// 					list($state, $postalCode) = explode(' ', $statePostalCode);
	// 				} else {
	// 					$city = $state = $postalCode = "Test";
	// 				}

	// 			} 

	// 			$customer = \Stripe\Customer::create([
	// 				'name' => ($user->name??"Test"),
	// 				'address' => [
	// 					'line1' => ($streetAddress??"Test"),
	// 					'city' => ($city??"Test"),
	// 					'state' => ($state??"Test"),
	// 					'postal_code' => ($postalCode??"Test"),
	// 					'country' => 'TR' 
	// 				],
	// 			]);
				
			
	// 			// Create checkout session
	// 			$session = \Stripe\Checkout\Session::create([
	// 				'payment_method_types' => $methods,
	// 				'line_items' => [[
	// 					'price_data' => [
	// 						'currency' => strtolower(Hyvikk::payment('currency_code')),
	// 						'unit_amount' => $booking->tax_total * 100,
	// 						'product_data' => [
	// 							'name' => 'Booking',
	// 						],
	// 					],
	// 					'quantity' => 1,
	// 				]],
	// 				'customer' => $customer->id, 
	// 				'mode' => 'payment',
	// 				'payment_intent_data' => [
	// 					'capture_method' => 'automatic',
	// 				],
	// 				'success_url' => url('stripe-success') ."?session_id={CHECKOUT_SESSION_ID}&booking_id=" . $booking_id,
	// 				'cancel_url' => url('stripe-cancel') ."?session_id={CHECKOUT_SESSION_ID}&booking_id=" . $booking_id,
	// 			]);
		
	// 			$session_id = $session['id'];
	// 			return view('payments.stripe', compact('session_id'));
	// 		} catch (\Stripe\Exception\CardException $e) {
	// 			// Since it's a decline, \Stripe\Exception\CardException will be caught
	// 			$error_msg = $e->getError()->message;
	// 			return view('payments.payment_failed', compact('error_msg'));
	
	// 		} catch (\Stripe\Exception\RateLimitException $e) {
	// 			// Too many requests made to the API too quickly
	// 			$error_msg = $e->getError()->message;
	// 			return view('payments.payment_failed', compact('error_msg'));
	
	// 		} catch (\Stripe\Exception\InvalidRequestException $e) {
	// 			// Invalid parameters were supplied to Stripe's API
	// 			$error_msg = $e->getError()->message;
	// 			return view('payments.payment_failed', compact('error_msg'));
	
	// 		} catch (\Stripe\Exception\AuthenticationException $e) {
	// 			// Authentication with Stripe's API failed
	// 			// (maybe you changed API keys recently)
	// 			$error_msg = $e->getError()->message;
	// 			return view('payments.payment_failed', compact('error_msg'));
	
	// 		} catch (\Stripe\Exception\ApiConnectionException $e) {
	// 			// Network communication with Stripe failed
	// 			$error_msg = $e->getError()->message;
	// 			return view('payments.payment_failed', compact('error_msg'));
	
	// 		} catch (\Stripe\Exception\ApiErrorException $e) {
	// 			// Display a very generic error to the user, and maybe send
	// 			// yourself an email
	// 			$error_msg = $e->getError()->message;
	// 			return view('payments.payment_failed', compact('error_msg'));
	
	// 		} catch (Exception $e) {
	// 			// Something else happened, completely unrelated to Stripe
	// 			$error_msg = $e->getError()->message;
	// 			return view('payments.payment_failed', compact('error_msg'));
	// 		}
	// 	}

	public function stripe($booking_id) {
		try {
			$booking = Bookings::find($booking_id);

			$total_amount=$booking->tax_total;

			$bookings=Bookings::select("bookings.*")
                ->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                ->where('bookings_meta.key', 'parent_booking_id')
                ->where('bookings_meta.value', $booking->id)
                ->first();

			

			if(isset($bookings))
			{
					$total_amount+=$booking->tax_total;
			}

			\Stripe\Stripe::setApiKey(Hyvikk::payment('stripe_secret_key'));
			$methods = ['card'];
			
			if (Hyvikk::payment('currency_code') == "EUR") {
				$methods = ['card', 'ideal'];
			}
			
			$user = User::find($booking->customer_id);
			$address = $user->address;
	
			if (!empty($address)) {
				$lines = explode("\n", $address);
				$streetAddress = $lines[0] ?? "Test";
	
				if (isset($lines[1])) {
					list($city, $statePostalCode) = explode(', ', $lines[1]);
					list($state, $postalCode) = explode(' ', $statePostalCode);
				} else {
					$city = $state = $postalCode = "Test";
				}
			} else {
				$streetAddress = $city = $state = $postalCode = "Test";
			}
	
			$customer = \Stripe\Customer::create([
				'name' => $user->name ?? "Test",
				'address' => [
					'line1' => $streetAddress,
					'city' => $city,
					'state' => $state,
					'postal_code' => $postalCode,
					'country' => 'TR'
				],
			]);
	
			// Create checkout session
			$session = \Stripe\Checkout\Session::create([
				'payment_method_types' => $methods,
				'line_items' => [[
					'price_data' => [
						'currency' => strtolower(Hyvikk::payment('currency_code')),
						'unit_amount' => $total_amount * 100,
						'product_data' => [
							'name' => 'Booking',
						],
					],
					'quantity' => 1,
				]],
				'customer' => $customer->id, 
				'mode' => 'payment',
				'payment_intent_data' => [
					'capture_method' => 'automatic',
				],
				'success_url' => url('stripe-success') . "?session_id={CHECKOUT_SESSION_ID}&booking_id=" . $booking_id,
				'cancel_url' => url('stripe-cancel') . "?session_id={CHECKOUT_SESSION_ID}&booking_id=" . $booking_id,
			]);
	
			$session_id = $session['id'];
			return view('payments.stripe', compact('session_id'));
	
		} 
		catch (\Stripe\Exception\CardException $e) {
			// Since it's a decline, \Stripe\Exception\CardException will be caught
			$error_msg = $e->getError()->message;
			return view('payments.payment_failed', compact('error_msg'));
	
		} catch (\Stripe\Exception\RateLimitException $e) {
			// Too many requests made to the API too quickly
			$error_msg = $e->getError()->message;
			return view('payments.payment_failed', compact('error_msg'));
	
		} catch (\Stripe\Exception\InvalidRequestException $e) {
			// Invalid parameters were supplied to Stripe's API
			$error_msg = $e->getError()->message;
			return view('payments.payment_failed', compact('error_msg'));
	
		} catch (\Stripe\Exception\AuthenticationException $e) {
			// Authentication with Stripe's API failed (maybe you changed API keys recently)
			$error_msg = $e->getError()->message;
			return view('payments.payment_failed', compact('error_msg'));
	
		} catch (\Stripe\Exception\ApiConnectionException $e) {
			// Network communication with Stripe failed
			$error_msg = $e->getError()->message;
			return view('payments.payment_failed', compact('error_msg'));
	
		} catch (\Stripe\Exception\ApiErrorException $e) {
			// Display a very generic error to the user
			$error_msg = $e->getError()->message;
			return view('payments.payment_failed', compact('error_msg'));
	
		} 
		catch (Exception $e) {
			$error_msg = $e->getMessage(); 
			return view('payments.payment_failed', compact('error_msg'));
		}
	}
	

		public function stripe_success() {
		
			$booking_id = $_GET['booking_id'];
			\Stripe\Stripe::setApiKey(Hyvikk::payment('stripe_secret_key'));
			$payment_data = \Stripe\Checkout\Session::retrieve(
				$_GET['session_id']
			);
			// dd($payment_data);
			$payment_int = \Stripe\PaymentIntent::retrieve(
				$payment_data['payment_intent']
			);

			
			$paymentMode=Null;
			$paymentMethod = \Stripe\PaymentMethod::retrieve($payment_int['payment_method'],[]);
		
			if(isset($paymentMethod))
			{
				$paymentMode=$paymentMethod['card']['funding'].' '.$paymentMethod['type'];
			}
			
			// dd($payment_int->charges->data[0]->payment_method_details->ideal);
			// dd($payment_int['charges']['data'][0]['id']);
	
			/*
				if ($payment_int->charges->data[0]->payment_method_details->type == "card") {
				$payment_method_details = array(
				'card' => array(
				'brand' => $payment_int->charges->data[0]->payment_method_details->card->brand,
				'country' => $payment_int->charges->data[0]->payment_method_details->card->country,
				'exp_month' => $payment_int->charges->data[0]->payment_method_details->card->exp_month,
				'exp_year' => $payment_int->charges->data[0]->payment_method_details->card->exp_year,
				'fingerprint' => $payment_int->charges->data[0]->payment_method_details->card->fingerprint,
				'funding' => $payment_int->charges->data[0]->payment_method_details->card->funding,
				'installments' => $payment_int->charges->data[0]->payment_method_details->card->installments,
				'last4' => $payment_int->charges->data[0]->payment_method_details->card->last4,
				'network' => $payment_int->charges->data[0]->payment_method_details->card->network,
				'three_d_secure' => $payment_int->charges->data[0]->payment_method_details->card->three_d_secure,
				'wallet' => $payment_int->charges->data[0]->payment_method_details->card->wallet,
				),
				);
				}
				if ($payment_int->charges->data[0]->payment_method_details->type == "ideal") {
				$payment_method_details = array(
				"bank" => $payment_int->charges->data[0]->payment_method_details->ideal->bank,
				"bic" => $payment_int->charges->data[0]->payment_method_details->ideal->bic,
				"iban_last4" => $payment_int->charges->data[0]->payment_method_details->ideal->iban_last4,
				"verified_name" => $payment_int->charges->data[0]->payment_method_details->ideal->verified_name,
				);
				}
	*/
			$info = array(
				'session_id' => $_GET['session_id']??'-',
				'payment_intent_id' => $payment_int['id']??'-',
			);
	
		
			$booking = Bookings::find($booking_id);
		
			BookingPaymentsModel::create(['method' => $paymentMode, 'booking_id' => $booking_id??'-', 'amount' => $booking->tax_total??'-', 'payment_details' => json_encode($info), 'transaction_id' =>  ($payment_int['id']??'-'), 'payment_status' => ($payment_int['status']??'-')]);

			$booking->payment = 1;
			$booking->payment_method = $paymentMode;
			if ($booking->vehicle_id) {
				$booking->status = 0;
				$booking->ride_status = "Pending";
			}
			
			
			// if ($booking->driver->driver_commision != null) {
			// 	$commision = $booking->driver->driver_commision;
			// 	$amnt = $commision;
			// 	if ($booking->driver->driver_commision_type == 'percent') {
			// 		$amnt = ($booking->total * $commision) / 100;
			// 	}
			// 	// $driver_amount = round($booking->total - $amnt, 2);
			// 	$booking->driver_amount = $amnt;
			// 	$booking->driver_commision = $booking->driver->driver_commision;
			// 	$booking->driver_commision_type = $booking->driver->driver_commision_type;
			// 	$booking->save();
			// }
			$booking->save();
	
			$id = IncomeModel::create([
				"vehicle_id" => $booking->vehicle_id,
				// "amount" => $request->get('total'),
				"amount" => $booking->tax_total,
				"driver_amount" => $booking->driver_amount ?? $booking->tax_total,
				"user_id" => $booking->customer_id,
				"date" => $booking->date,
				"mileage" => $booking->mileage,
				"income_cat" => "Booking",
				"income_id" => $booking->id,
				"tax_percent" => $booking->tax_percent,
				"tax_charge_rs" => $booking->tax_charge_rs,
			])->id;
	
			BookingIncome::create(['booking_id' => $booking->id, "income_id" => $id]);
			$xx = Bookings::whereId($booking->id)->first();
			// $xx->status = 1;
			$xx->receipt = 1;
			$xx->save();
	
			if (Hyvikk::email_msg('email') == 1) {
				try{
				Mail::to($booking->customer->email)->send(new CustomerInvoice($booking));
				}
                catch (\Throwable $e) {
                }
			}

			$tax_total_amt=$booking->tax_total;

			$bookings=Bookings::select("bookings.*")
                ->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                ->where('bookings_meta.key', 'parent_booking_id')
                ->where('bookings_meta.value', $booking->id)
                ->first();

			

			if(isset($bookings))
			{
				BookingPaymentsModel::create(['method' => $paymentMode, 'booking_id' => $bookings->id??'-', 'amount' => $bookings->tax_total??'-', 'payment_details' => json_encode($info), 'transaction_id' =>  ($payment_int['id']??'-'), 'payment_status' => ($payment_int['status']??'-')]);

				$bookings->payment = 1;
				$bookings->payment_method = $paymentMode;
				if ($bookings->vehicle_id) {
					$bookings->status = 0;
					$bookings->ride_status = "Pending";
				}
				

				$bookings->save();
		
				$ids = IncomeModel::create([
					"vehicle_id" => $bookings->vehicle_id,
					// "amount" => $request->get('total'),
					"amount" => $bookings->tax_total,
					"driver_amount" => $bookings->driver_amount ?? $bookings->tax_total,
					"user_id" => $bookings->customer_id,
					"date" => $bookings->date,
					"mileage" => $bookings->mileage,
					"income_cat" => "Booking",
					"income_id" => $bookings->id,
					"tax_percent" => $bookings->tax_percent,
					"tax_charge_rs" => $bookings->tax_charge_rs,
				])->id;
		
				BookingIncome::create(['booking_id' => $bookings->id, "income_id" => $ids]);
				$xxs = Bookings::whereId($bookings->id)->first();
				// $xx->status = 1;
				$xxs->receipt = 1;
				$xxs->save();
		
				if (Hyvikk::email_msg('email') == 1) {
					try{
					Mail::to($bookings->customer->email)->send(new CustomerInvoice($bookings));
					}
					catch (\Throwable $e) {
					}
				}

				$tax_total_amt+=$bookings->tax_total;
			}
		





			// dd($payment_int['charges']['data']);
			$data['amount'] = $tax_total_amt;
			return view('payments.payment_success', $data);
		}

	// public function stripe_cancel() {
		
	// 	$error_msg = "You have cancelled payment transaction successfully!";

	// 	$booking_id = $_GET['booking_id'];

	// 	\Stripe\Stripe::setApiKey(Hyvikk::payment('stripe_secret_key'));
	// 	$payment_data = \Stripe\Checkout\Session::retrieve(
	// 		$_GET['session_id']
	// 	);
		
	// 	$payment_int = \Stripe\PaymentIntent::retrieve(
	// 		$payment_data['payment_intent']
	// 	);

		
	// 	$paymentMode=Null;
	// 	$paymentMethod = \Stripe\PaymentMethod::retrieve($payment_int['payment_method'],[]);
	
	// 	if(isset($paymentMethod))
	// 	{
	// 		$paymentMode=$paymentMethod['card']['funding'].' '.$paymentMethod['type'];
	// 	}

	// 	$info = array(
	// 		'session_id' => $_GET['session_id']??'-',
	// 		'payment_intent_id' => $payment_int['id']??'-',
	// 	);

	// 	$booking = Bookings::find($booking_id);
		
	// 	BookingPaymentsModel::create(['method' => $paymentMode, 'booking_id' => $booking_id??'-', 'amount' => $booking->tax_total??'-', 'payment_details' => json_encode($info), 'transaction_id' =>  ($payment_int['id']??'-'), 'payment_status' => ($payment_int['status']??'-')]);
	// 	$booking->payment =0;
	// 	$booking->payment_method = $paymentMode;
	// 	if ($booking->vehicle_id) {
	// 		$booking->status = 0;
	// 		$booking->ride_status = "Pending";
	// 	}
	// 	$booking->save();


	// 	return view('payments.payment_failed', compact('error_msg'));
	// }


	public function stripe_cancel() {

	$error_msg = "You have cancelled the payment transaction successfully!";
	
		// Check if booking_id and session_id are set
		if (!isset($_GET['booking_id']) || !isset($_GET['session_id'])) {
			return view('payments.payment_failed', compact('error_msg'));
		}
	
		$booking_id = $_GET['booking_id'];
		$session_id = $_GET['session_id'];
	
		// Set Stripe API Key
		Stripe::setApiKey(Hyvikk::payment('stripe_secret_key'));
	
		// Retrieve Checkout Session
		try {
			$payment_data = Session::retrieve($session_id);
		} catch (\Exception $e) {
			return view('payments.payment_failed', compact('error_msg'));
		}
	
		// Retrieve Payment Intent (if available)
		$payment_int = null;
		if (!empty($payment_data['payment_intent'])) {
			try {
				$payment_int = PaymentIntent::retrieve($payment_data['payment_intent']);
			} catch (\Exception $e) {
				$payment_int = null;
			}
		}
	
		// Retrieve Payment Method if Payment Intent exists
		$paymentMode = null;
		if ($payment_int && !empty($payment_int['payment_method'])) {
			try {
				$paymentMethod = PaymentMethod::retrieve($payment_int['payment_method']);
				if ($paymentMethod) {
					$paymentMode = $paymentMethod['card']['funding'] . ' ' . $paymentMethod['type'];
				}
			} catch (\Exception $e) {
				$paymentMode = "Unknown";
			}
		}
	
		// Store Payment Info
		$info = [
			'session_id' => $session_id,
			'payment_intent_id' => $payment_int['id'] ?? '-',
		];
	
		// Retrieve Booking
		$booking = Bookings::find($booking_id);
		if ($booking) {
			BookingPaymentsModel::create([
				'method' => $paymentMode,
				'booking_id' => $booking_id ?? '-',
				'amount' => $booking->tax_total ?? '-',
				'payment_details' => json_encode($info),
				'transaction_id' => $payment_int['id'] ?? '-',
				'payment_status' => $payment_int['status'] ?? 'canceled',
			]);
	
			// Update Booking Status
			$booking->payment = 0;
			$booking->payment_method = $paymentMode;
			if ($booking->vehicle_id) {
				$booking->status = 0;
				$booking->ride_status = "Pending";
			}
			$booking->save();
		}

		
			$bookings=Bookings::select("bookings.*")
                ->join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                ->where('bookings_meta.key', 'parent_booking_id')
                ->where('bookings_meta.value', $booking->id)
                ->first();

			

			if(isset($bookings))
			{
				BookingPaymentsModel::create([
					'method' => $paymentMode,
					'booking_id' => $bookings->id ?? '-',
					'amount' => $bookings->tax_total ?? '-',
					'payment_details' => json_encode($info),
					'transaction_id' => $payment_int['id'] ?? '-',
					'payment_status' => $payment_int['status'] ?? 'canceled',
				]);
		
				// Update Booking Status
				$bookings->payment = 0;
				$bookings->payment_method = $paymentMode;
				if ($bookings->vehicle_id) {
					$bookings->status = 0;
					$bookings->ride_status = "Pending";
				}
				$bookings->save();
			}

	
		return view('payments.payment_failed', compact('error_msg'));
	}

	public function razorpay($booking_id) {
		try {
			$booking = Bookings::find($booking_id);
			$receipt_no = time() . "_" . date('Y_m_d') . "_" . $booking_id;
			$api = new Api(Hyvikk::payment('razorpay_key'), Hyvikk::payment('razorpay_secret'));
			$order = $api->order->create(array('receipt' => $receipt_no, 'amount' => $booking->tax_total * 100, 'currency' => Hyvikk::payment('currency_code'), 'payment_capture' => 1));
			// dd($order);
			$data['order_id'] = $order['id'];
			$data['amount'] = $booking->tax_total * 100;
			$data['booking_id'] = $booking_id;
			return view('payments.razorpay_form', $data);
		} catch (Exception $e) {
			$error_msg = $e->getMessage();
			return view('payments.payment_failed', compact('error_msg'));
		}
	}

	public function razorpay_success(Request $request) {
		if ($request->error) {
			$error_msg = $request->error['description'];
			return view('payments.payment_failed', compact('error_msg'));
		} else {
			$api = new Api(Hyvikk::payment('razorpay_key'), Hyvikk::payment('razorpay_secret'));
			$payment = $api->payment->fetch($request->razorpay_payment_id);
			// dd($payment);
			// $order = $api->order->fetch($request->razorpay_order_id);
			// dd($order);
			$payment_info = array(
				"razorpay_payment_id" => $payment->id,
				"razorpay_order_id" => $request->razorpay_order_id,
				"razorpay_signature" => $request->razorpay_signature,
				"entity" => $payment->entity,
				"amount" => $payment->amount,
				"currency" => $payment->currency,
				"status" => $payment->status,
				"order_id" => $payment->order_id,
				"invoice_id" => $payment->invoice_id,
				"international" => $payment->international,
				"method" => $payment->method,
				"amount_refunded" => $payment->amount_refunded,
				"refund_status" => $payment->refund_status,
				"captured" => $payment->captured,
				"description" => $payment->description,
				"card_id" => $payment->card_id,
				"bank" => $payment->bank,
				"wallet" => $payment->wallet,
				"vpa" => $payment->vpa,
				"email" => $payment->email,
				"contact" => $payment->contact,
				"fee" => $payment->fee,
				"tax" => $payment->tax,
				"error_code" => $payment->error_code,
				"error_description" => $payment->error_description,
				"created_at" => $payment->created_at,
			);
			$booking_id = $_GET['booking_id'];
			$booking = Bookings::find($booking_id);
			BookingPaymentsModel::create(['method' => 'razorpay', 'booking_id' => $booking_id, 'amount' => $booking->tax_total, 'payment_details' => json_encode($payment_info), 'transaction_id' => $payment['id'], 'payment_status' => "succeeded"]);
			$booking->payment = 1;
			$booking->payment_method = "razorpay";
			if ($booking->vehicle_id) {
				$booking->status = 1;
				$booking->ride_status = "Completed";
			}
			$booking->save();
			$data['amount'] = $booking->tax_total;
			return view('payments.payment_success', $data);
		}
	}

	public function razorpay_failed() {
		$error_msg = "You have cancelled payment transaction successfully!";
		return view('payments.payment_failed', compact('error_msg'));
	}

}
