
@extends('layouts.app')

@section('extra_css')
<style>
  :root {
    --primary-color: #7ED6DF;
    --dark-bg: #032127;
    --light-bg: #F7F7F7;
    --card-shadow: 0 4px 8px rgba(3,33,39,0.1);
    --border-radius: 15px;
    --transition: all 0.3s ease;
  }

  .content-header {
    background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary-color) 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: var(--border-radius);
  }

  .content-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
  }

  .breadcrumb {
    background: transparent;
    margin: 0;
    padding: 0;
  }

  .breadcrumb-item a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: var(--transition);
  }

  .breadcrumb-item a:hover {
    color: white;
  }

  .breadcrumb-item.active {
    color: white;
  }

  .stats-container {
    margin-bottom: 2rem;
  }

  .stat-card {
    background: var(--light-bg);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    border: 1px solid rgba(126, 214, 223, 0.2);
    height: 100%;
    position: relative;
    overflow: hidden;
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color) 0%, var(--dark-bg) 100%);
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(126, 214, 223, 0.3);
  }

  .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 1rem;
  }

  .stat-icon.vehicles { background: linear-gradient(45deg, var(--primary-color), var(--dark-bg)); }
  .stat-icon.drivers { background: linear-gradient(45deg, var(--dark-bg), var(--primary-color)); }
  .stat-icon.customers { background: linear-gradient(45deg, var(--primary-color), #5BA4B0); }
  .stat-icon.bookings { background: linear-gradient(45deg, var(--dark-bg), #5BA4B0); }

  .stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark-bg);
    margin: 0;
    line-height: 1;
  }

  .stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0.5rem 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .stat-link {
    background: linear-gradient(to right, #80D7DF, #BDEFCC);
    color: white;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    padding: 8px 16px;
    border-radius: 50px;
    transition: all 0.3s ease;
    display: inline-block;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  .stat-link:hover {
    background: #B7ECCE;
    color: #032127;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  }

  .action-cards {
    margin-top: 2rem;
  }

  .action-card {
    background: var(--light-bg);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    height: 100%;
    border: 1px solid rgba(126, 214, 223, 0.2);
  }

  .action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(126, 214, 223, 0.3);
  }

  .card-title {
    color: var(--dark-bg);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
  }

  .action-btn {
    background: linear-gradient(to right, #80D7DF, #BDEFCC);
    color: white;
    border: none;
    padding: 15px 35px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    margin: 0.25rem;
    font-size: 0.9rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  }

  .action-btn:hover {
    background: #B7ECCE;
    color: #032127;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
  }

  .action-btn i {
    font-size: 1rem;
  }

  .welcome-section {
    background: linear-gradient(135deg, var(--light-bg) 0%, #ffffff 100%);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    border-left: 4px solid var(--primary-color);
    border: 1px solid rgba(126, 214, 223, 0.2);
  }

  .welcome-title {
    color: var(--dark-bg);
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .welcome-text {
    color: #6c757d;
    margin-bottom: 0.5rem;
  }

  .last-login {
    color: #6c757d;
    font-size: 0.85rem;
    font-style: italic;
  }

  .container-fluid {
    max-width: 1200px;
    margin: 0 auto;
  }

  @media (max-width: 768px) {
    .content-header h1 {
      font-size: 2rem;
    }
    
    .stat-number {
      font-size: 2rem;
    }
    
    .stat-card {
      margin-bottom: 1rem;
    }
    
    .action-card {
      margin-bottom: 1rem;
    }
    
    .action-btn {
      width: 100%;
      justify-content: center;
      margin-bottom: 0.5rem;
    }
  }

  /* Ensure stats stay in one row on all screen sizes */
  .stats-container .row {
    flex-wrap: nowrap !important;
    display: flex !important;
  }
  
  .stats-container .col-xl-3,
  .stats-container .col-lg-3,
  .stats-container .col-md-3,
  .stats-container .col-sm-3 {
    flex: 1 !important;
    max-width: 25% !important;
    min-width: 0 !important;
  }
  
  /* Responsive adjustments for very small screens */
  @media (max-width: 576px) {
    .stats-container .col-xl-3,
    .stats-container .col-lg-3,
    .stats-container .col-md-3,
    .stats-container .col-sm-3 {
      flex: 1 !important;
      max-width: 25% !important;
      padding: 0 5px !important;
    }
    
    .stat-card {
      padding: 1rem !important;
    }
    
    .stat-number {
      font-size: 1.8rem !important;
    }
    
    .stat-icon {
      width: 45px !important;
      height: 45px !important;
    }
  }
</style>
@endsection

@section('heading')
@lang('fleet.dashboard')
@endsection

@section('breadcrumb')
@lang('fleet.dashboard')
@endsection

@section('content')

<section class="content">
  <div class="container-fluid">
    
    <!-- Welcome Section -->
    <div class="welcome-section">
      <h2 class="welcome-title">Welcome to Fleet Manager</h2>
      <p class="welcome-text">Manage your fleet operations efficiently with our comprehensive dashboard.</p>
      <p class="last-login">Last login: {{ Auth::user()->updated_at->format('M d, Y H:i A') }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
      <div class="row d-flex no-gutters">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-3 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon vehicles">
              <i class="fa fa-car"></i>
            </div>
            <h3 class="stat-number">{{ $total_vehicles ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.vehicles')</p>
            <a href="{{url('admin/vehicles')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-3 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon drivers">
              <i class="fa fa-id-card"></i>
            </div>
            <h3 class="stat-number">{{ $total_drivers ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.drivers')</p>
            <a href="{{url('admin/drivers')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-3 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon customers">
              <i class="fa fa-users"></i>
            </div>
            <h3 class="stat-number">{{ $total_customers ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.customers')</p>
            <a href="{{url('admin/customers')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-3 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon bookings">
              <i class="fa fa-address-book"></i>
            </div>
            <h3 class="stat-number">{{ $total_bookings ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.bookings')</p>
            <a href="{{url('admin/bookings')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Cards -->
    <div class="action-cards">
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
            $('.stat-card, .action-card').hover(
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
        const cards = document.querySelectorAll('.stat-card, .action-card');
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
