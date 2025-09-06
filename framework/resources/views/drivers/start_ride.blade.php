@extends('layouts.app')

@section('extra_css')
<style>
    .content-header{
        display:none;
    }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            flex-direction: row;
            align-items: center;
            word-wrap: break-word;
        }
        .header-bg {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
        }
        .map-placeholder {
            height: 250px;
            background-color: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        .map-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .map-overlay {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: rgba(0,0,0,0.5);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
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
        .dot-green { background-color: #28a745; border: 2px solid #ffffff; }
        .dot-red { background-color: #dc3545; border: 2px solid #ffffff; }
        .text-muted-custom {
            color: #6c757d;
        }
        .btn-green {
            background-color: #28a745;
            color: white;
            border: none;
        }
        .addr-class{
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 40%;
              word-break: break-all;
            
        }
        .common-class{
            display: flex;
            gap: 10px;
            align-items: center;

        }

        @media screen and (max-width:992px){
            .card{
                    flex-direction: column;
                justify-content: start;
                align-items: start;
                gap: 20px;
            }
              .addr-class{
                width:100%;
              }
        
        }

  </style>


<style>

          #loadingOverlay {
    position: fixed;
    top: 0;
    left: 250px;
    width: calc(100% - 250px);
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0.5;
    background-color: #45454563;
  
}

.loading-overlay-content {
    text-align: center;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
}

.loader {
    border: 5px solid #F3F3F3; 
    border-top: 5px solid #3498DB;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

#loadingOverlay.visible {
   display: flex;
}

/* Media query for tablets and smaller devices */
@media (max-width: 768px) {
    #loadingOverlay {
        left: 0;
        width: 100%;
    }

    .loading-overlay-content {
        font-size: 0.9em;
    }

    .loader {
        width: 50px;
        height: 50px;
        border-width: 4px;
    }
}

/* Media query for phones */
@media (max-width: 480px) {
    .loading-overlay-content {
        font-size: 0.8em;
    }

    .loader {
        width: 40px;
        height: 40px;
        border-width: 3px;
    }
}
</style>

@endsection

@section('content')

<div id="loadingOverlay" class="d-none">
    <div class="loading-overlay-content">
        <div class="loader"></div>
    </div>
  </div>

<div class="container py-2">

    <div class="d-flex justify-content-between align-items-center mb-3 header-bg p-3 rounded">
        <h4 class="mb-0">
            
            <a href="{{url('admin/my_bookings')}}">
          
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>

            </a>

            <span class="ms-2">{{$data->ride_status??''}} @lang('fleet.Ride')</span>
        </h4>
        <span class="text-muted">@lang('fleet.Booking_ID'): #{{$data->id??''}}</span>
    </div>

    <div class="map-placeholder mb-4">
     
         <div class="map" style="width:100%;height:478px;border-radius: 10px;"></div>
        <div class="map-overlay">{{  date("d-F-Y", strtotime($data->journey_date)).' at '. date("h:i A", strtotime($data->journey_time)) }}</div>
    </div>

    <div class="card p-4 mb-4">
            
        <div class="addr-class">
            <h5 class="card-title">@lang('fleet.Source')</h5>
            <div class="d-flex align-items-start">
                <span class="dot dot-green mt-1"></span>
                <p class="mb-0">
                    <strong class="d-block">{{$data->pickup_addr}}</strong>
                </p>
            </div>
        </div>
        <div class="addr-class">
            <h5 class="card-title">@lang('fleet.Destination')</h5>
            <div class="d-flex align-items-start">
                <span class="dot dot-red mt-1"></span>
                <p class="mb-0">
                    <strong class="d-block">{{$data->dest_addr}}</strong>
                </p>
            </div>
        </div>

          

    </div>

    <div class="card p-4 mb-4 text-center">
        <div class="row" style="gap: 50px;">
            <div class="col border-end common-class">
                <p class="mb-0 text-muted-custom text-nowrap">@lang('fleet.total_time')</p>
                <h4 class="mb-0 text-nowrap"> 
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
                </h4>
            </div>
            <div class="col common-class">
                <p class="mb-0 text-muted-custom text-nowrap">@lang('fleet.total_kms')</p>
                <h4 class="mb-0 text-nowrap">{{ $data->getMeta('total_kms') ? $data->getMeta('total_kms') . ' Kms' : 'NA' }}</h4>
            </div>
        </div>
       
    </div>


    @if($data && $data->ride_status == "Ongoing")

    <div class="d-grid gap-2">
      
        <div class="">
         
             

             <div class="d-flex justify-content-center" style="gap: 100px;">


                 <a href="https://www.google.com/maps/dir/?api=1&origin={{ urlencode($data->pickup_addr) }}&destination={{ urlencode($data->dest_addr) }}" target="_blank" class="btn btn-lg btn-primary" style="width: 250px;">Track Ride</a>


                  <button type="button" id="startCompBtn" class="btn btn-lg btn-secondary" style="width: 250px;">
                    @lang('fleet.Destination_Reached')
                </button>
              

                
            </div>

          


        </div>
    </div>

    @endif

</div>




    

@endsection

@section('script')


<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('startCompBtn');
    if (!button) return;

    button.addEventListener('click', function () {
        $("#loadingOverlay").removeClass('d-none');

        if (!navigator.geolocation) {
            $("#loadingOverlay").addClass('d-none');
            new PNotify({ title: 'Error!', text: 'Geolocation not supported.', type: 'error' });
            return;
        }

        function successCallback(position) {
            const lat = position.coords.latitude;
            const long = position.coords.longitude;
            $("#loadingOverlay").addClass('d-none');
            console.log("Lat:", lat, "Long:", long);

            window.location.href = "{{ url('admin/complete-ride/' . $data->id) }}" 
                + "?lat=" + encodeURIComponent(lat) 
                + "&long=" + encodeURIComponent(long);
        }

        function errorCallback(error) {
            if (error.code === 2 || error.code === 3) {  
                // Retry fallback with low accuracy
                navigator.geolocation.getCurrentPosition(successCallback, finalErrorCallback, {
                    enableHighAccuracy: false,
                    timeout: 15000,
                    maximumAge: 60000
                });
            } else {
                finalErrorCallback(error);
            }
        }

        function finalErrorCallback(error) {
            $("#loadingOverlay").addClass('d-none');
            let message = "Please turn on your location to continue.";
            if (error.code === 1) message = "Permission denied. Please allow location access.";
            else if (error.code === 2) message = "Location unavailable. Try again.";
            else if (error.code === 3) message = "Location request timed out. Please try again.";

            new PNotify({ title: "Error!", text: message, type: "error" });
        }

        
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback, {
            enableHighAccuracy: true,
            timeout: 30000,  // More time for GPS fix
            maximumAge: 0
        });
    });
});
</script>



 @if(Session::get('success'))
  <script>
    new PNotify({
        title: 'Success!',
        text: '{{ Session::get('success') }}',
        type: 'success'
      });
      </script>
  @endif

 @if(Session::get('error'))
    <script>
        new PNotify({
            title: 'Error!',
            text: '{{ Session::get('error') }}',
            type: 'error' // or 'danger', depending on PNotify version
        });
    </script>
@endif


@if(Hyvikk::api('google_api') == "1")

<script>
    function initMap() {
        // Now google.maps is guaranteed to be defined

        var mapDivs = document.querySelectorAll('.map');
        mapDivs.forEach(function(mapDiv) {
            var map = new google.maps.Map(mapDiv, {
                zoom: 1,
                center: { lat: 0, lng: 0 }
            });

            var startAddress = "{{ $data->pickup_addr }}";
            var endAddress = "{{ $data->dest_addr }}";
            var geocoder = new google.maps.Geocoder();

            geocodeAddress(geocoder, map, startAddress, endAddress);
        });
    }

    function geocodeAddress(geocoder, map, startAddress, endAddress) {
        geocoder.geocode({ 'address': startAddress }, function(resultsStart, status) {
            if (status === 'OK') {
                var startLocation = resultsStart[0].geometry.location;

                geocoder.geocode({ 'address': endAddress }, function(resultsEnd, status) {
                    if (status === 'OK') {
                        var endLocation = resultsEnd[0].geometry.location;

                        var startMarker = new google.maps.Marker({
                            position: startLocation,
                            map: map,
                            title: 'Start',
                            icon: "{{ asset('assets/images/pickup.png') }}"
                        });

                        var endMarker = new google.maps.Marker({
                            position: endLocation,
                            map: map,
                            title: 'End',
                            icon: "{{ asset('assets/images/dropup.png') }}"
                        });

                        var bounds = new google.maps.LatLngBounds();
                        bounds.extend(startLocation);
                        bounds.extend(endLocation);
                        map.fitBounds(bounds);

                        displayRoute(map, startLocation, endLocation);

                    } else {
                        console.error("End address geocoding failed: " + status);
                    }
                });

            } else {
                console.error("Start address geocoding failed: " + status);
            }
        });
    }

    function displayRoute(map, startLocation, endLocation) {
        var directionsService = new google.maps.DirectionsService();
        var directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true
        });
        var request = {
            origin: startLocation,
            destination: endLocation,
            travelMode: 'DRIVING'
        };
        directionsService.route(request, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
            }
        });
    }
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&libraries=places&callback=initMap"
    async defer></script>


@endif


@endsection