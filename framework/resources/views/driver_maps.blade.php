@extends("layouts.app")

@section('extra_css')

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

@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.maps')</li>
@endsection
@section('content')

<div id="loadingOverlay" class="d-none">
    <div class="loading-overlay-content">
        <div class="loader"></div>
    </div>
  </div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header d-flex">

        <div class="col-md-6">
            <h3 class="card-title">
              @lang('fleet.maps')
            </h3>
        </div>

        <div class="col-md-4 d-track-name d-none">
          <h5>Driver Name :<span class="show-name"></span></h5>
        </div>
        
        <div class="col-md-2 d-flex justify-content-end">
          <div class="r-button d-none">
              <a href="{{url('/admin/driver-maps')}}" class="btn btn-success">Back To All</a>
          </div>
         
        </div>


        
      </div>

      <div class="card-body">
        <div class="row">
      	<div style="width: 100%; height: 400px;" id="map_canvas"></div>
          <input type="hidden" name="lat" id="lat" value="">
          <input type="hidden" name="long" id="long" value="">
        </div>
        <div class="row table-responsive" style="margin-top: 10px;">

          <table class="table display" id="data_table">
            <thead class="thead-inverse">
              <tr>
                <th>@lang('fleet.driver')</th>
                <th>@lang('fleet.status')</th>
                <th>@lang('fleet.track')</th>
              </tr>
            </thead>
              
            <tbody id="details">
             
            @if(isset($details))
              @foreach($details as $d)
              <tr id="row-{{$d['user_id']}}" data-driver_id="{{$d['user_id']}}">
                <td>{{$d["user_name"]}}</td>
                <td>
                  @if($d['availability'] == "1")
                    <span class="text-success">@lang('fleet.online')</span>
                  @else
                    <span class="text-danger">@lang('fleet.offline')</span>
                  @endif
                </td>
                <td>
                  <button class="btn btn-info track-driver" type="button" data-id="{{$d['user_id']}}">@lang('fleet.track')</button>
                </td>
              </tr>
              @endforeach
              @else
                  <tr><td colspan="3" class="text-center">No active users found</td></tr>
              @endif

               
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')

<script src="https://maps.googleapis.com/maps/api/js?key={{Hyvikk::api('api_key')}}"></script>


@php
    $path = storage_path('firebase/' . Hyvikk::api('firebase_url'));
    $fileData = file_get_contents($path); // read file
@endphp

<script>
    var projectData = @json(json_decode($fileData, true)); 
  

    var project_id=projectData['project_id'];
</script>

<script>
const BASE_URL_MAP = "{{ url('/') }}";
</script>

<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
import {
  getFirestore,
  collection,
  onSnapshot
} from "https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore.js";
import {
  getAuth,
  signInWithCustomToken,
  onAuthStateChanged
} from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";



const firebaseConfig = {
  apiKey: "{{Hyvikk::api('firebase_web_key')}}",
  authDomain: project_id+".firebaseapp.com",
  projectId: project_id,
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const auth = getAuth(app);
const userEmail = "{{ Auth::user()->email }}";

let map;
let markerStore = {};
let currentlyTrackedUserId = null;
let unsubscribeSnapshot = null;
let availabilityInterval = null;

// Firebase Sign-In
const url_track = BASE_URL_MAP + "/admin/firebase/token-by-email?email=" + encodeURIComponent(userEmail);
fetch(url_track)
  .then(res => res.json())
  .then(data => {
    if (data.token) return signInWithCustomToken(auth, data.token);
    else throw new Error(data.error || 'Token not received');
  })
  .then(() => console.log("✅ Signed in with Firebase custom token"))
  .catch((error) => console.error("❌ Sign-in Error:", error.message));

onAuthStateChanged(auth, (user) => {
  if (user) console.log("🧑‍💻 Logged in UID:", user.uid);
});

window.onload = function () {
  getMarkers();
  setTimeout(() => {
    availabilityInterval = setInterval(check_availability1, 5000);
  }, 3000);
};

// Initialize Map + Load Markers
function getMarkers() {
  markerStore = {};
  const myLatlng = new google.maps.LatLng(20.0, 0.0);
  map = new google.maps.Map(document.getElementById("map_canvas"), {
    zoom: 2,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
  });

  $.get(BASE_URL_MAP + '/admin/markers', {}, function (res) {
    const bounds = new google.maps.LatLngBounds();
    const infowindow = new google.maps.InfoWindow();

    res.forEach(driver => {
      const latLng = new google.maps.LatLng(driver.position.lat, driver.position.long);
      const color = driver.status == 'Online' ? 'text-success' : 'text-danger';

      $('#details tbody').append(`
        <tr id="row-${driver.id}" data-driver_id="${driver.id}">
          <td>${driver.name}</td>
          <td><span class="${color}">${driver.status}</span></td>
          <td><button class="btn btn-info track-driver" type="button" data-id="${driver.id}">@lang('fleet.track')</button></td>
        </tr>`);

      bounds.extend(latLng);

      const marker = new google.maps.Marker({
        position: latLng,
        map: map,
        title: `${driver.name} (${driver.status})`,
        icon: BASE_URL_MAP + '/assets/images/' + driver.icon
      });

      marker.iconName = driver.icon;

      google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(driver.name);
        infowindow.open(map, marker);
      });

      markerStore[driver.id] = marker;
    });

    if (res.length === 1) {
      map.setCenter(res[0].position);
      map.setZoom(13);
    } else if (res.length > 1) {
      map.fitBounds(bounds);
    }
  }, "json");
}

// Update Markers + Table
function updateMapAndTable(users) {
  users.forEach(user => {
    const latLng = new google.maps.LatLng(user.position.lat, user.position.long);
    const isTracked = user.id == currentlyTrackedUserId;
    const iconSize = isTracked ? new google.maps.Size(60, 60) : new google.maps.Size(32, 32);
    const iconPath = BASE_URL_MAP + '/assets/images/' + user.icon;

    if (markerStore[user.id]) {
      const marker = markerStore[user.id];
      marker.setPosition(latLng);
      marker.setIcon({ url: iconPath, scaledSize: iconSize });
      marker.setTitle(`${user.name} (${user.status})`);
      marker.iconName = user.icon;
    } else {
      const marker = new google.maps.Marker({
        position: latLng,
        title: `${user.name} (${user.status})`,
        map: map,
        icon: { url: iconPath, scaledSize: iconSize }
      });

      const infowindow = new google.maps.InfoWindow();
      marker.addListener('click', () => {
        infowindow.setContent(user.name);
        infowindow.open(map, marker);
      });

      marker.iconName = user.icon;
      markerStore[user.id] = marker;
    }

    if (isTracked) {
      map.setCenter(latLng);
      //map.setZoom(15);
    }

    const rowId = `row-${user.id}`;
    const rowHTML = `
      <tr id="${rowId}" data-driver_id="${user.id}">
        <td>${user.name}</td>
        <td><span class="${user.status === 'Online' ? 'text-success' : 'text-danger'}">${user.status}</span></td>
        <td>
          <button class="btn ${isTracked ? 'btn-success' : 'btn-info'} track-driver" type="button" data-id="${user.id}">
            @lang('fleet.track')
          </button>
        </td>
      </tr>`;

    if ($(`#${rowId}`).length > 0) {
      $(`#details #${rowId}`).replaceWith(rowHTML);
    } else {
      $('#details').append(rowHTML);
    }
  });
}
$(document).on("click", ".track-driver", function (e) {
  e.preventDefault();
  const user_id = $(this).data('id');
  if (currentlyTrackedUserId === user_id) return;

  if (availabilityInterval !== null) {
    clearInterval(availabilityInterval);
    availabilityInterval = null;
    console.log("⛔ Auto checker stopped");
  }

  if (unsubscribeSnapshot) {
    unsubscribeSnapshot();
    unsubscribeSnapshot = null;
  }

  currentlyTrackedUserId = user_id;

  // Remove all other markers from map except tracked one
  for (const id in markerStore) {
    if (id != user_id) {
      markerStore[id].setMap(null); // Hide from map
    }
  }

  $(".track-driver").removeClass('btn-success').addClass('btn-info');
  $(this).removeClass('btn-info').addClass('btn-success');
  $(".d-track-name, .r-button").removeClass('d-none');

  listenToUserSnapshot(user_id);

  $.ajax({
    url: BASE_URL_MAP + `/admin/track-driver/${user_id}`,
    type: 'GET',
    beforeSend: function () {
      $("#loadingOverlay").removeClass('d-none');
    },
    success: function (res) {
      $("#loadingOverlay").addClass('d-none');
      const user = res.driver;
      const latLng = new google.maps.LatLng(user.position.lat, user.position.long);
      const iconPath = BASE_URL_MAP + `/assets/images/${user.icon}`;

      const markerOptions = {
        url: iconPath,
        scaledSize: new google.maps.Size(60, 60)
      };

      if (markerStore[user.id]) {
        const marker = markerStore[user.id];
        marker.setPosition(latLng);
        marker.setIcon(markerOptions);
        marker.setTitle(`${user.name} (${user.status})`);
        marker.setMap(map); // Re-show tracked marker if hidden
      } else {
        const marker = new google.maps.Marker({
          position: latLng,
          map: map,
          title: `${user.name} (${user.status})`,
          icon: markerOptions
        });

        const infowindow = new google.maps.InfoWindow();
        marker.addListener('click', () => {
          infowindow.setContent(user.name);
          infowindow.open(map, marker);
        });

        marker.iconName = user.icon;
        markerStore[user.id] = marker;
      }

      $(".show-name").text(user.name);
      map.setCenter(latLng);
      //map.setZoom(15);
      updateMapAndTable([user]);
    },
    error: function () {
      $("#loadingOverlay").addClass('d-none');
    }
  });
});


// Track Button Click
// $(document).on("click", ".track-driver", function (e) {
//   e.preventDefault();
//   const user_id = $(this).data('id');
//   if (currentlyTrackedUserId === user_id) return;

//   if (availabilityInterval !== null) {
//     clearInterval(availabilityInterval);
//     availabilityInterval = null;
//     console.log("⛔ Auto checker stopped");
//   }

//   if (unsubscribeSnapshot) {
//     unsubscribeSnapshot();
//     unsubscribeSnapshot = null;
//   }

//   currentlyTrackedUserId = user_id;
//   $(".track-driver").removeClass('btn-success').addClass('btn-info');
//   $(this).removeClass('btn-info').addClass('btn-success');
//   $(".d-track-name, .r-button").removeClass('d-none');

//   for (const id in markerStore) {
//     markerStore[id].setIcon({
//       url: BASE_URL_MAP + '/assets/images/' + markerStore[id].iconName,
//       scaledSize: new google.maps.Size(32, 32)
//     });
//   }

//   listenToUserSnapshot(user_id);

//   $.ajax({
//     url: BASE_URL_MAP + `/admin/track-driver/${user_id}`,
//     type: 'GET',
//     beforeSend: function () {
//       $("#loadingOverlay").removeClass('d-none');
//     },
//     success: function (res) {
//       $("#loadingOverlay").addClass('d-none');
//       const user = res.driver;
//       const latLng = new google.maps.LatLng(user.position.lat, user.position.long);
//       const iconPath = BASE_URL_MAP + `/assets/images/${user.icon}`;

//       const markerOptions = {
//         url: iconPath,
//         scaledSize: new google.maps.Size(60, 60)
//       };

//       if (markerStore[user.id]) {
//         const marker = markerStore[user.id];
//         marker.setPosition(latLng);
//         marker.setIcon(markerOptions);
//         marker.setTitle(`${user.name} (${user.status})`);
//       } else {
//         const marker = new google.maps.Marker({
//           position: latLng,
//           map: map,
//           title: `${user.name} (${user.status})`,
//           icon: markerOptions
//         });

//         const infowindow = new google.maps.InfoWindow();
//         marker.addListener('click', () => {
//           infowindow.setContent(user.name);
//           infowindow.open(map, marker);
//         });

//         marker.iconName = user.icon;
//         markerStore[user.id] = marker;
//       }

//       $(".show-name").text(user.name);
//       map.setCenter(latLng);
//       //map.setZoom(15);
//       updateMapAndTable([user]);
//     },
//     error: function () {
//       $("#loadingOverlay").addClass('d-none');
//     }
//   });
// });

// Firebase Live Tracking
function listenToUserSnapshot(userId) {
  if (unsubscribeSnapshot) unsubscribeSnapshot();
  const q = collection(db, "User_Locations");

  unsubscribeSnapshot = onSnapshot(q, (snapshot) => {
    snapshot.docChanges().forEach(change => {
      const data = change.doc.data();
      if (change.type === "modified" && data.user_id == userId) {
        $.ajax({
          url: BASE_URL_MAP + `/admin/get-user-info/${userId}`,
          type: 'GET',
          dataType: 'json',
          success: function (response) {
            if (response.status == 100) {
              const user = {
                id: response.data.id,
                name: response.data.name || 'Unknown',
                status: data.availability == 1 ? 'Online' : 'Offline',
                icon: data.availability == 1 ? 'online.png' : 'offline.png',
                position: {
                  lat: data.latitude || 0,
                  long: data.longitude || 0
                }
              };
              updateMapAndTable([user]);
            }
          }
        });
      }
    });
  });
}

// 🔁 Periodic Availability Checker
function check_availability1() {
  console.log("🔁 Running availability check...");
  let driverIds = [];

  $('#details tr').each(function () {
    let id = $(this).data('driver_id');
    if (id !== undefined) {
      driverIds.push(id);
    }
  });

  if (driverIds.length === 0) return;

  $.ajax({
    url: BASE_URL_MAP + `/admin/get-availability-status`,
    type: "post",
    data: { driverIds },
    success: function (data) {
      data.forEach(driver => {
        let row = $('#row-' + driver.user_id);
        let statusCell = row.find('td:nth-child(2)');
        let icon = driver.availability == 1 ? 'online.png' : 'offline.png';

        if (driver.availability == 1) {
          statusCell.html('<span class="text-success">Online</span>');
        } else {
          statusCell.html('<span class="text-danger">Offline</span>');
        }

        if (markerStore[driver.user_id]) {
          markerStore[driver.user_id].setIcon({
            url: BASE_URL_MAP + '/assets/images/' + icon,
            scaledSize: new google.maps.Size(32, 32)
          });
          markerStore[driver.user_id].iconName = icon;
        }
      });
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
    }
  });
}
</script>








@endsection