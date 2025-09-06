@extends('layouts.app')

@section('extra_css')
    <style>
        .content-header {
            display: none;
        }

        .container {
            padding: 20px;
        }

        .header-bg {
            background-color: #fff;
            border-bottom: 1px solid #e0e0e0;
        }

        .card,
        .card-container {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            padding: 20px;
            background-color: #fff;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .card:hover,
        .card-container:hover {
            transform: translateY(-3px);
        }

        .card {
            /* display: flex;
            justify-content: space-around;
            flex-direction: row;
            align-items: center;
            flex-wrap: wrap;
            word-wrap: break-word; */
        }

        .addr-class {
            display: flex;
            flex-direction: column;
            gap: 10px;
            word-break: break-word;
        }

        .dot {
            min-height: 12px;
            min-width: 12px;
            /* width: 100%; */
            /* height: 100%; */
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            border: 2px solid #fff;
        }

        .dot-green {
            background-color: #28a745;
        }

        .dot-red {
            background-color: #dc3545;

        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .info-item {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .info-item .label {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .info-item .value {
            font-weight: bold;
            font-size: 1rem;
            color: #343a40;
        }

        .fare-total {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-top: 15px;
        }

        .payment-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .customer-profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .customer-profile .avatar {
            width: 50px;
            height: 50px;
            background-color: #ffc107;
            color: #212529;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .customer-profile .name {
            font-weight: bold;
            font-size: 1.1rem;
        }

        @media (max-width: 992px) {
            .card {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .addr-class {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3 header-bg p-3 rounded">
            <h4 class="mb-0">
                
               <a href="{{url('admin/my_bookings')}}">
          
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>

            </a>

                <span class="ms-2">{{ $data->ride_status ?? '' }} @lang('fleet.Ride')</span>
            </h4>
            <span class="text-muted">@lang('fleet.Booking_ID'): #{{ $data->id ?? '' }}</span>
        </div>

        <div class="content">
            <div class="card">
                <div class="row">
                    <div class="col-md-4 col-12 my-2">
                        <div class="addr-class">
                            <h5 class="card-title">@lang('fleet.Source')</h5>
                            <div class="d-flex align-items-start">
                                <span class="dot dot-green mt-1"></span>
                                <p class="mb-0"><strong class="d-block">{{$data->pickup_addr}}</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 my-2">
                        <div class="addr-class">
                            <h5 class="card-title">@lang('fleet.Destination')</h5>
                            <div class="d-flex align-items-start">
                                <span class="dot dot-red mt-1"></span>
                                <p class="mb-0"><strong class="d-block">{{$data->dest_addr}}</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 my-2">
                        <div class="addr-class">
                            <h5 class="card-title">@lang('fleet.journey_date')</h5>
                            <div class="d-flex align-items-start">
                                <span class="dot dot-red mt-1"></span>
                                <p class="mb-0"><strong class="d-block">{{  date("d-F-Y", strtotime($data->journey_date)).' at '. date("h:i A", strtotime($data->journey_time)) }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

            <div class="info-grid">
                <div class="info-item card">
                    <div class="label">@lang('fleet.total_time')</div>
                    <div class="value">  
                        @if ($data->getMeta('total_time'))
                                @php
                                    $timeString = $data->getMeta('total_time');
                                    [$hours, $minutes, $seconds] = explode(':', $timeString);
                                    $totalMinutes = $hours * 60 + $minutes + round($seconds / 60);
                                @endphp
                                {{ $totalMinutes }} Mins
                            @else
                                ---
                            @endif
                        </div>
                </div>
                <div class="info-item card">
                    <div class="label">@lang('fleet.total_kms')</div>
                    <div class="value">{{ $data->getMeta('total_kms') ? $data->getMeta('total_kms') . ' Kms' : 'NA' }}</div>
                </div>
                <div class="info-item card">
                    <div class="label">@lang('fleet.Started_On')</div>
                    <div class="value">{{ $data->getMeta('ridestart_timestamp') ? date('d M Y \a\t h:i A', strtotime($data->getMeta('ridestart_timestamp'))) : 'NA' }}</div>
                </div>
                
                <div class="info-item card">
                    <div class="label">@lang('fleet.Completed_On')</div>
                    <div class="value">{{ $data->getMeta('rideend_timestamp') ? date('d M Y \a\t h:i A', strtotime($data->getMeta('rideend_timestamp'))) : 'NA' }}</div>
                </div>
            </div>

            <div class="card-container">
                <small class="text-muted">@lang('fleet.Booked_On')</small>
                <p class="mb-0"><strong>{{ $data->created_at ? date('d M Y \a\t h:i A', strtotime($data->created_at)) : 'NA' }}</strong></p>
            </div>

            <div class="card-container">
                <div class="customer-profile">
                    <div class="name">{{$data->customer->name??''}}</div>
                    <div class="avatar">

                        @php
                            $cus = \App\Model\User::find($data->customer_id);
                            $cusimg = $cus?->getMeta('profile_pic');
                            $custmerprofile = !empty($cusimg) 
                                ? url('uploads/'.$cusimg) 
                                : url('assets/images/no-user.jpg');
                        @endphp

                        <img src="{{ $custmerprofile }}" alt="Customer Profile" width="50" height="50" style="border-radius:50px;50px;">



                        {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3z" />
                            <path fill-rule="evenodd" d="M8 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
                        </svg> --}}
                    </div>
                </div>
            </div>

         {{-- <div class="card-container">
            <h6>@lang('fleet.fare_details')</h6>
            <div class="card-details d-flex justify-content-between">
                <span>@lang('fleet.amount'):</span>
                <span>{{ Hyvikk::get('currency') }} {{ $data->total ?? 0 }}</span>
            </div>
            <div class="card-details d-flex justify-content-between">
                <span>@lang('fleet.total_tax') (%) :</span>
                <span>{{ $data->total_tax_percent ?? 0 }} %</span>
            </div>
            <hr class="my-2">
            <div class="card-details d-flex justify-content-between">
                <span>@lang('fleet.total') @lang('fleet.tax_charge')</span>
                <span><strong>{{ Hyvikk::get('currency') }} {{ $data->total_tax_charge_rs ?? 0 }}</strong></span>
            </div>
            <div class="fare-total">
                <span>@lang('fleet.total'):</span>
                <span>{{ Hyvikk::get('currency') }} {{ $data->tax_total ?? 0 }}</span>
            </div>
        </div> --}}


            {{-- <div class="card-container">
                <h6>Payment Mode</h6>
                <div class="payment-info mb-2">
                    <span>credit card</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                        class="bi bi-credit-card-fill" viewBox="0 0 16 16">
                        <path
                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4zm0 3v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1z" />
                    </svg>
                </div>
                <small class="text-muted">Transaction ID</small>
                <p class="mb-0 text-muted"><strong>#pi_3RBdYSJZCLpS50gYaM55</strong></p>
            </div> --}}
        </div>
    </div>
@endsection


@section('script')

 @if(Session::get('success'))
  <script>
    new PNotify({
        title: 'Success!',
        text: '{{ Session::get('success') }}',
        type: 'success'
      });
      </script>
  @endif



@endsection