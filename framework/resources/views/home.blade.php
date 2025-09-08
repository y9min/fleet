
@extends('layouts.app')

@section('extra_css')
<style>
  .info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: .25rem;
    background-color: #fff;
    display: flex;
    margin-bottom: 1rem;
    min-height: 80px;
    padding: .5rem;
    position: relative;
    width: 100%;
  }
  
  .info-box .info-box-icon {
    border-radius: .25rem;
    align-items: center;
    display: flex;
    font-size: 1.875rem;
    justify-content: center;
    text-align: center;
    width: 70px;
  }
  
  .info-box .info-box-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.8;
    flex: 1;
    padding: 0 10px;
  }
  
  .info-box .info-box-number {
    display: block;
    margin-top: .25rem;
    font-weight: 700;
    font-size: 18px;
  }
  
  .info-box .info-box-text {
    display: block;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .bg-info {
    background-color: #17a2b8!important;
    color: #fff;
  }
  
  .bg-success {
    background-color: #28a745!important;
    color: #fff;
  }
  
  .bg-warning {
    background-color: #ffc107!important;
    color: #212529;
  }
  
  .bg-danger {
    background-color: #dc3545!important;
    color: #fff;
  }
</style>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">@lang('fleet.dashboard')</li>
@endsection

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">@lang('fleet.dashboard')</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('admin/')}}">@lang('fleet.home')</a></li>
          <li class="breadcrumb-item active">@lang('fleet.dashboard')</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-car"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">@lang('fleet.vehicles')</span>
            <span class="info-box-number">{{ $total_vehicles ?? 0 }}</span>
          </div>
          <div class="info-box-more">
            <a href="{{url('admin/vehicles')}}" class="btn btn-sm btn-primary">
              @lang('fleet.view') <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="info-box">
          <span class="info-box-icon bg-success">
            <i class="fa fa-id-card"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">@lang('fleet.drivers')</span>
            <span class="info-box-number">{{ $total_drivers ?? 0 }}</span>
          </div>
          <div class="info-box-more">
            <a href="{{url('admin/drivers')}}" class="btn btn-sm btn-success">
              @lang('fleet.view') <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="info-box">
          <span class="info-box-icon bg-warning">
            <i class="fa fa-users"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">@lang('fleet.customers')</span>
            <span class="info-box-number">{{ $total_customers ?? 0 }}</span>
          </div>
          <div class="info-box-more">
            <a href="{{url('admin/customers')}}" class="btn btn-sm btn-warning">
              @lang('fleet.view') <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="info-box">
          <span class="info-box-icon bg-danger">
            <i class="fa fa-address-book"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">@lang('fleet.bookings')</span>
            <span class="info-box-number">{{ $total_bookings ?? 0 }}</span>
          </div>
          <div class="info-box-more">
            <a href="{{url('admin/bookings')}}" class="btn btn-sm btn-danger">
              @lang('fleet.view') <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">@lang('fleet.quickActions')</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-6">
                <a href="{{url('admin/vehicles/create')}}" class="btn btn-primary btn-block">
                  <i class="fa fa-plus"></i> @lang('fleet.addVehicle')
                </a>
              </div>
              <div class="col-6">
                <a href="{{url('admin/drivers/create')}}" class="btn btn-success btn-block">
                  <i class="fa fa-plus"></i> @lang('fleet.addDriver')
                </a>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-6">
                <a href="{{url('admin/customers/create')}}" class="btn btn-warning btn-block">
                  <i class="fa fa-plus"></i> @lang('fleet.addCustomer')
                </a>
              </div>
              <div class="col-6">
                <a href="{{url('admin/bookings/create')}}" class="btn btn-danger btn-block">
                  <i class="fa fa-plus"></i> @lang('fleet.newBooking')
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">@lang('fleet.recentActivity')</h3>
          </div>
          <div class="card-body">
            <p>@lang('fleet.welcomeMessage')</p>
            <p><small class="text-muted">@lang('fleet.lastLogin'): {{ Auth::user()->updated_at->format('M d, Y H:i') }}</small></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('script')
<script>
$(document).ready(function() {
    try {
        // Dashboard initialization
        console.log('Dashboard loaded successfully');
        
        // Add error handling for any AJAX requests
        $(document).ajaxError(function(event, xhr, settings, error) {
            console.error('AJAX Error:', error);
        });
        
        // Prevent infinite loops in any polling mechanisms
        window.dashboardInitialized = true;
        
    } catch (error) {
        console.error('Dashboard initialization error:', error);
    }
});
</script>
@endsection
