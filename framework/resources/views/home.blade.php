
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


  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(126, 214, 223, 0.3);
  }

  .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #7FD7E1;
    background: transparent;
    margin-bottom: 1rem;
  }

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
    background: #6B7280;
    color: white;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
    padding: 8px 16px;
    border-radius: 6px;
    transition: all 0.3s ease;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .stat-link:hover {
    background: #4B5563;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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
    background: #6B7280;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    margin: 0.25rem;
    font-size: 0.9rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .action-btn:hover {
    background: #4B5563;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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

  /* Ensure stats stay in one row on desktop and tablet */
  .stats-container .row {
    display: flex !important;
  }
  
  /* Desktop: keep cards in row */
  @media (min-width: 769px) {
    .stats-container .row {
      flex-wrap: nowrap !important;
    }
    
    .stats-container .col-xl-3,
    .stats-container .col-lg-3,
    .stats-container .col-md-3,
    .stats-container .col-sm-3 {
      flex: 1 !important;
      max-width: 25% !important;
      min-width: 0 !important;
    }
  }
  
  /* Mobile: allow cards to stack in column */
  @media (max-width: 768px) {
    .stats-container .row {
      flex-wrap: wrap !important;
      flex-direction: column !important;
    }
    
    .stats-container .col-xl-3,
    .stats-container .col-lg-3,
    .stats-container .col-md-3,
    .stats-container .col-sm-3 {
      flex: 1 1 100% !important;
      max-width: 100% !important;
      width: 100% !important;
      margin-bottom: 1rem !important;
    }
    
    .stat-card {
      padding: 1.5rem !important;
      margin-bottom: 0 !important;
    }
    
    .stat-number {
      font-size: 2.2rem !important;
    }
    
    .stat-icon {
      width: 55px !important;
      height: 55px !important;
    }
  }
</style>
@endsection


@section('content')

<section class="content">
  <div class="container-fluid">
    

    <!-- Statistics Cards -->
    <div class="stats-container">
      <div class="row d-flex no-gutters">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon vehicles">
              <svg width="24" height="24" viewBox="0 0 512 512" fill="currentColor">
                <path d="M135.2 117.4L109.1 192H402.9l-26.1-74.6C372.3 104.6 360.2 96 346.6 96H165.4c-13.6 0-25.7 8.6-30.2 21.4zM109.1 224c-2.4 0-4.7.2-6.9.7L133.6 320h244.8l31.4-95.3c-2.2-.5-4.5-.7-6.9-.7H109.1zM160 384c0 17.7-14.3 32-32 32s-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32zM416 384c0 17.7-14.3 32-32 32s-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32zM48 192c0-26.5 21.5-48 48-48H112L138.1 69.4C151.3 41.5 179.5 24 210.4 24H301.6c30.9 0 59.1 17.5 72.3 45.4L400 144h16c26.5 0 48 21.5 48 48v160c0 26.5-21.5 48-48 48H400c0 53-43 96-96 96s-96-43-96-96H208c0 53-43 96-96 96s-96-43-96-96H0V192z"/>
              </svg>
            </div>
            <h3 class="stat-number">{{ $total_vehicles ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.vehicles')</p>
            <a href="{{url('admin/vehicles')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon drivers">
              <svg width="24" height="24" viewBox="0 0 448 512" fill="currentColor">
                <path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0S96 57.3 96 128s57.3 128 128 128zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
              </svg>
            </div>
            <h3 class="stat-number">{{ $total_drivers ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.drivers')</p>
            <a href="{{url('admin/drivers')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon customers">
              <svg width="24" height="24" viewBox="0 0 640 512" fill="currentColor">
                <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM609.3 512H471.4c5.4-9.4 8.6-20.3 8.6-32v-8c0-60.7-27.1-115.2-69.8-151.8c2.4-.1 4.7-.2 7.1-.2h61.4C567.8 320 640 392.2 640 481.3c0 17-13.8 30.7-30.7 30.7zM432 256c-31 0-59-12.6-79.3-32.9C372.4 196.5 384 163.6 384 128c0-26.8-6.6-52.1-18.3-74.3C384.3 40.1 407.2 32 432 32c61.9 0 112 50.1 112 112s-50.1 112-112 112z"/>
              </svg>
            </div>
            <h3 class="stat-number">{{ $total_customers ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.customers')</p>
            <a href="{{url('admin/customers')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon bookings">
              <svg width="24" height="24" viewBox="0 0 448 512" fill="currentColor">
                <path d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16zm16 48H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
              </svg>
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
