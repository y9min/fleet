
@extends('layouts.app')

@section('page_title')
PCOFlow | Dashboard
@endsection

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
  /* Wide revenue card */
  .revenue-card {
    background: var(--light-bg);
    color: var(--dark-bg);
    border-radius: var(--border-radius);
    padding: 1.75rem;
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(126, 214, 223, 0.2);
  }
  .revenue-card h3 {
    margin: 0 0 0.75rem 0;
    font-weight: 700;
  }
  .revenue-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }
  .revenue-item {
    background: transparent;
    border: 1px solid rgba(126, 214, 223, 0.2);
    border-radius: 12px;
    padding: 1rem 1.25rem;
  }
  .revenue-label { color: #6c757d; text-transform: uppercase; letter-spacing: .5px; font-size: .85rem; }
  .revenue-amount { font-size: 2rem; font-weight: 800; color: var(--dark-bg); }
  @media (max-width: 768px) {
    .revenue-grid { grid-template-columns: 1fr; }
    .revenue-amount { font-size: 1.8rem; }
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
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(126, 214, 223, 0.3);
  }

  .card-title {
    color: var(--dark-bg);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
    width: 100%;
    text-align: center;
  }

  .action-btn {
    background: #6B7280;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    flex: 1;
    min-width: 0;
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

  .action-buttons-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 1.5rem;
    padding: 0 1rem;
    justify-content: center;
    align-items: center;
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
  
  /* Skeleton loader styles */
  .stat-loading {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: 8px;
    display: inline-block;
    min-width: 80px;
    height: 2.5rem;
  }
  
  .stat-loading-small {
    min-width: 40px;
    height: 1.5rem;
  }
  
  @keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }
  
  .stat-number.loading {
    line-height: 2.5rem;
  }
</style>
@endsection


@section('content')

<section class="content">
  <div class="container-fluid">
    

    @if(Auth::user()->email !== 'yamzahmed@hotmail.com')
    <!-- Statistics Cards - Hidden for Yamz -->
    <div class="stats-container">
      <div class="row d-flex no-gutters">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon vehicles">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
              </svg>
            </div>
            <h3 class="stat-number" data-stat="total_vehicles">{{ $total_vehicles ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.vehicles')</p>
            <a href="{{url('admin/vehicles')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100">
            <div class="stat-icon drivers">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
              </svg>
            </div>
            <h3 class="stat-number" data-stat="total_drivers">{{ $total_drivers ?? 0 }}</h3>
            <p class="stat-label">@lang('fleet.drivers')</p>
            <a href="{{url('admin/drivers')}}" class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100" style="cursor: pointer;" onclick="window.location.href='{{url('admin/onboarding')}}'">
            <div class="stat-icon onboarding">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
            </div>
            <h3 class="stat-number" data-stat="onboarding_pending">{{ $onboarding_pending ?? 0 }}</h3>
            <p class="stat-label">Driver Onboarding</p>
            <div class="stat-link">
              Manage <i class="fa fa-arrow-right"></i>
            </div>
            @if(($onboarding_pending ?? 0) > 0)
              <div style="position: absolute; top: 10px; right: 10px;">
                <span class="badge badge-warning">{{ $onboarding_pending }} Pending</span>
              </div>
            @endif
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100" style="cursor: pointer;" onclick="window.location.href='{{url('admin/vehicle-inspection')}}'">
            <div class="stat-icon inspection">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M16.862 4.487a5.5 5.5 0 00-7.41 6.596L3 17.5V21h3.5l6.452-6.452a5.5 5.5 0 006.596-7.41l-3.752 3.752a2.5 2.5 0 01-3.536-3.536l3.752-3.752z"/>
              </svg>
            </div>
            <h3 class="stat-number" data-stat="pending_inspections">{{ $pending_inspections ?? 0 }}</h3>
            <p class="stat-label">Vehicle Inspections</p>
            <div class="stat-link">
              Manage <i class="fa fa-arrow-right"></i>
            </div>
            @if(($upcoming_mots ?? 0) > 0)
              <div style="position: absolute; top: 10px; right: 10px;">
                <span class="badge badge-warning">{{ $upcoming_mots }} {{ ($upcoming_mots == 1) ? 'upcoming MOT' : 'upcoming MOTs' }}</span>
              </div>
            @endif
          </div>
        </div>
        
      </div>
    </div>

    <!-- Second Row with Onboarding Card - Hidden for Yamz -->
    <div class="stats-container">
      <div class="row d-flex no-gutters">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100" style="cursor: pointer;" onclick="window.location.href='{{url('admin/fines')}}'">
            <div class="stat-icon fines">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
              </svg>
            </div>
            <h3 class="stat-number" data-stat="total_fines">{{ $total_fines ?? 0 }}</h3>
            <p class="stat-label">Fines & Penalties</p>
            <div class="stat-link">
              Manage <i class="fa fa-arrow-right"></i>
            </div>
            @if(($pending_fines ?? 0) > 0)
              <div style="position: absolute; top: 10px; right: 10px;">
                <span class="badge badge-warning">{{ $pending_fines }} Pending</span>
              </div>
            @endif
          </div>
        </div>
        
        
        
        <!-- Expected Revenue card in same row as Fines -->
        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-6 col-12 d-flex">
          <div class="revenue-card w-100">
            <h3>Expected Revenue</h3>
            <div class="revenue-grid">
              <div class="revenue-item">
                <div class="revenue-label">Weekly</div>
                <div class="revenue-amount" data-stat="expected_weekly_revenue">£{{ number_format($expected_weekly_revenue ?? 0, 2) }}</div>
              </div>
              <div class="revenue-item">
                <div class="revenue-label">Monthly</div>
                <div class="revenue-amount" data-stat="expected_monthly_revenue">£{{ number_format($expected_monthly_revenue ?? 0, 2) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    @if(Auth::user()->email === 'yamzahmed@hotmail.com')
    <!-- Yamz Dashboard: Only Admin User Statistics -->
    <div class="stats-container">
      <div class="row d-flex no-gutters">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100" style="cursor: pointer;" onclick="window.location.href='{{url('admin/yamz/companies')}}'">
            <div class="stat-icon companies">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
              </svg>
            </div>
            <h3 class="stat-number">{{ $total_companies ?? 0 }}</h3>
            <p class="stat-label">Companies</p>
            <div class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100" style="cursor: pointer;" onclick="window.location.href='{{url('admin/yamz/all-users')}}'">
            <div class="stat-icon super-admins">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
              </svg>
            </div>
            <h3 class="stat-number">{{ $total_super_admins ?? 0 }}</h3>
            <p class="stat-label">Super Admins</p>
            <div class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100" style="cursor: pointer;" onclick="window.location.href='{{url('admin/yamz/all-users')}}'">
            <div class="stat-icon office-admins">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
              </svg>
            </div>
            <h3 class="stat-number">{{ $total_office_admins ?? 0 }}</h3>
            <p class="stat-label">Office Admins</p>
            <div class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 d-flex">
          <div class="stat-card w-100" style="cursor: pointer;" onclick="window.location.href='{{url('admin/yamz/all-users')}}'">
            <div class="stat-icon drivers">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 4H6c-1.1 0-2 .9-2 2v12c0 1.1 .9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-2V2c0-.6-.4-1-1-1s-1 .4-1 1v2h-4V2c0-.6-.4-1-1-1s-1 .4-1 1v2zM6 6h12v12H6V6zm2 6c0-1.1 .9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm8 6c0 1.1-.9 2-2 2h-8c-1.1 0-2-.9-2-2v-1c0-1.1 .9-2 2-2h8c1.1 0 2 .9 2 2v1z"/>
              </svg>
            </div>
            <h3 class="stat-number">{{ $total_drivers ?? 0 }}</h3>
            <p class="stat-label">Drivers</p>
            <div class="stat-link">
              View All <i class="fa fa-arrow-right"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    @if(Auth::user()->email !== 'yamzahmed@hotmail.com')
    <!-- Action Cards - Hidden for Yamz -->
    <div class="action-cards">
      <div class="row">
        <div class="col-md-12">
          <div class="action-card">
            <h3 class="card-title">
              <i class="fa fa-plus-circle"></i> Quick Actions
            </h3>
            <div class="action-buttons-grid">
              <a href="{{url('admin/vehicles/create')}}" class="action-btn">
                <i class="fa fa-plus"></i> Add Vehicle
              </a>
              <a href="{{url('admin/drivers/create')}}" class="action-btn">
                <i class="fa fa-plus"></i> Add Driver
              </a>
              <a href="{{url('admin/fines/create')}}" class="action-btn">
                <i class="fa fa-plus"></i> New Fine/Penalty
              </a>
              <a href="{{url('admin/invitations/create')}}" class="action-btn">
                <i class="fa fa-plus"></i> New Pickup Invitation
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

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
    
    // AJAX loading for dashboard stats
    (function() {
        const statsUrl = '{{ route("admin.dashboard.stats") }}';
        
        fetch(statsUrl)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Update each stat with data-stat attribute
                document.querySelectorAll('[data-stat]').forEach(el => {
                    const statName = el.getAttribute('data-stat');
                    if (data[statName] !== undefined) {
                        // Handle currency formatting for revenue stats
                        if (statName.includes('revenue')) {
                            el.textContent = '£' + parseFloat(data[statName]).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        } else {
                            el.textContent = data[statName];
                        }
                    }
                });
            })
            .catch(error => {
                console.warn('Failed to load dashboard stats:', error);
                // Fallback to server-rendered values
            });
    })();
});
</script>
@endsection
