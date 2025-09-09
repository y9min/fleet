@extends('layouts.app')

@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  .dropdown-menu>li>a {
    color: #212529 !important;
  }

  /* Simple dashboard styling */
  .dashboard-welcome {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .dashboard-welcome h2 {
    color: #2c3e50;
    font-size: 24px;
    margin: 0;
    font-weight: 500;
  }

  .simple-info-box {
    background: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    transition: transform 0.2s ease;
  }

  .simple-info-box:hover {
    transform: translateY(-2px);
  }

  .simple-info-box .icon {
    font-size: 32px;
    color: #6c757d;
    margin-bottom: 10px;
  }

  .simple-info-box .title {
    font-size: 14px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
  }

  .simple-info-box .number {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
  }

  .btn-simple {
    background: #6c757d !important;
    border: none !important;
    color: white !important;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
  }

  .btn-simple:hover {
    background: #5a6268 !important;
    color: white !important;
  }

  /* Updated styles for action cards */
  .action-card {
      background-color: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 20px;
  }

  .action-card .card-title {
      font-size: 18px;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #e0e0e0;
  }

  .action-btn {
      background: #6c757d;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 500;
      font-size: 14px;
      margin: 5px;
      transition: background 0.2s ease;
      display: inline-block;
  }

  .action-btn:hover {
      background: #5a6268;
      color: white;
      text-decoration: none;
  }

  .action-btn i {
      margin-right: 8px;
  }
</style>
@endsection
@section('breadcrumb')
@endsection
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="dashboard-welcome">
      <h2>Welcome to PCO Flow Fleet Manager</h2>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="simple-info-box">
              <div class="icon">
                <i class="fa fa-users"></i>
              </div>
              <div class="title">@lang('fleet.customers')</div>
              <div class="number">{{$customers}}</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="simple-info-box">
              <div class="icon">
                <i class="fa fa-car"></i>
              </div>
              <div class="title">@lang('fleet.vehicles')</div>
              <div class="number">{{$vehicles}}</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="simple-info-box">
              <div class="icon">
                <i class="fa fa-calendar-check-o"></i>
              </div>
              <div class="title">@lang('fleet.bookings')</div>
              <div class="number">{{$bookings}}</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="simple-info-box">
              <div class="icon">
                <i class="fa fa-money"></i>
              </div>
              <div class="title">@lang('fleet.income')</div>
              <div class="number">{{Hyvikk::get('currency')}} {{$income}}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="action-card">
      <h3 class="card-title">
        <i class="fa fa-plus-circle"></i> Quick Actions
      </h3>
      <div class="d-flex flex-wrap">
        <a href="{{url('admin/vehicles/create')}}" class="action-btn">
          <i class="fa fa-plus"></i> Add Vehicle
        </a>
        <a href="{{url('admin/drivers/create')}}" class="action-btn">
          <i class="fa fa-plus"></i> Add Driver
        </a>
        <a href="{{url('admin/customers/create')}}" class="action-btn">
          <i class="fa fa-plus"></i> Add Customer
        </a>
        <a href="{{url('admin/bookings/create')}}" class="action-btn">
          <i class="fa fa-plus"></i> New Booking
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="action-card">
      <h3 class="card-title">
        <i class="fa fa-chart-line"></i> Reports & Analytics
      </h3>
      <div class="d-flex flex-wrap">
        <a href="{{url('admin/reports/monthly')}}" class="action-btn">
          <i class="fa fa-chart-bar"></i> Monthly Report
        </a>
        <a href="{{url('admin/reports/yearly')}}" class="action-btn">
          <i class="fa fa-chart-line"></i> Yearly Report
        </a>
        <a href="{{url('admin/reports/fuel')}}" class="action-btn">
          <i class="fa fa-gas-pump"></i> Fuel Report
        </a>
        <a href="{{url('admin/reports/vehicle')}}" class="action-btn">
          <i class="fa fa-car"></i> Vehicle Report
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.incomeByMonth')</h3>
        <div class="card-tools">
          <button class="btn btn-simple btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="card-body">
        <canvas id="incomeChart" style="height: 230px; width: 100%;"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.bookingByMonth')</h3>
        <div class="card-tools">
          <button class="btn btn-simple btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="card-body">
        <canvas id="bookingChart" style="height: 230px; width: 100%;"></canvas>
      </div>
    </div>
  </div>
</div>

</div>
</section>
@endsection

@section('script')
<script>
// Wait for jQuery to be available
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is available
    if (typeof $ !== 'undefined') {
        $(function() {
            // Dashboard initialization
            console.log('Fleet Manager Dashboard loaded successfully');

            // Add smooth hover effects
            $('.simple-info-box, .action-card').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );

            // Add click analytics for action buttons
            $('.action-btn').click(function() {
                console.log('Action clicked:', $(this).text().trim());
            });
        });
    } else {
        console.log('jQuery not available yet, using vanilla JS for hover effects');
        // Fallback to vanilla JavaScript if jQuery isn't loaded
        const cards = document.querySelectorAll('.simple-info-box, .action-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('shadow-lg');
            });
            card.addEventListener('mouseleave', function() {
                this.classList.remove('shadow-lg');
            });
        });
    }
});
</script>
@endsection