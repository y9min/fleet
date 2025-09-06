@extends('frontend.layouts.app')

@section('title') 
    <title>Streamline Your PCO Operations, One Click at a Time | PCO Flow</title>
@endsection

@section('css')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #7FD7E1 0%, #032127 100%);
        --dark-bg: #032127;
        --accent-color: #7FD7E1;
        --success-color: #B7ECCE;
        --text-light: #f8f9fa;
        --card-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        overflow-x: hidden;
    }

    /* Header and Hero Combined */
    .main-section-background {
        background: #032127;
        min-height: 100vh;
        color: white;
        position: relative;
        overflow: hidden;
    }


    /* Header Styles */
    .header {
        padding: 15px 0;
        position: relative;
        z-index: 10;
    }

    .navbar {
        padding: 0;
    }
    
    .navbar-brand img {
        width: 140px;
        height: auto;
    }

    .main-menubar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        position: relative;
    }
    
    .navbar-brand {
        position: absolute;
        left: 0;
    }

    .navbar-nav {
        display: flex;
        align-items: center;
        margin: 0;
        flex-direction: row;
    }

    .navbar-collapse {
        flex-grow: 0;
    }

    .navbar-nav .nav-item {
        margin: 0 15px;
    }

    .auth-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .navbar-nav .nav-link {
        color: white !important;
        font-weight: 500;
        margin: 0 10px;
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        color: var(--success-color) !important;
    }

    .signin-signup-btn .btn,
    .auth-buttons .btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 2px solid rgba(255,255,255,0.3);
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 500;
        margin-left: 10px;
        transition: all 0.3s ease;
        white-space: nowrap;
        min-width: 80px;
    }

    .signin-signup-btn .btn:hover {
        background: white;
        color: var(--dark-bg);
        border-color: white;
    }

    /* Hero Section */
    .hero-modern {
        padding: 80px 0 100px;
        display: flex;
        align-items: center;
        color: white;
        position: relative;
        z-index: 2;
    }


    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        margin-bottom: 2rem;
        max-width: 500px;
    }

    .btn-hero {
        background: white;
        color: #032127;
        padding: 15px 35px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .btn-hero:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        background: var(--success-color);
        color: #032127;
    }

    .dashboard-preview {
        position: relative;
        z-index: 2;
    }

    .dashboard-preview img {
        max-width: 100%;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
    }

    /* Brand Logos */
    .brand-logos {
        padding: 60px 0;
        background: #032127;
    }

    .brand-logos img {
        height: 40px;
        opacity: 0.4;
        transition: opacity 0.3s ease;
        filter: brightness(0) invert(1);
    }

    .brand-logos img:hover {
        opacity: 0.8;
    }

    /* Features Section */
    .features-section {
        padding: 100px 0;
        background: white;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 3rem;
        color: #2d3748;
    }

    .feature-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
    }

    .feature-card:hover {
        transform: translateY(-10px);
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-gradient);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .feature-icon i {
        color: white;
        font-size: 1.5rem;
    }

    .feature-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .feature-description {
        color: #718096;
        line-height: 1.6;
    }

    /* Testimonial Section */
    .testimonial-section {
        padding: 100px 0;
        background: var(--dark-bg);
        color: white;
        position: relative;
    }

    .testimonial-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
    }

    .testimonial-text {
        font-size: 1.25rem;
        line-height: 1.6;
        margin-bottom: 2rem;
        font-style: italic;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }

    .author-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .author-info h4 {
        margin: 0;
        font-weight: 600;
    }

    .author-info p {
        margin: 0;
        opacity: 0.8;
        font-size: 0.9rem;
    }

    /* Pricing Section */
    .pricing-section {
        padding: 100px 0;
        background: #f8f9fa;
    }

    .pricing-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: var(--card-shadow);
        text-align: center;
        position: relative;
        transition: transform 0.3s ease;
    }

    .pricing-card:hover {
        transform: translateY(-10px);
    }

    .pricing-card.featured {
        border: 3px solid var(--accent-color);
        transform: scale(1.05);
    }

    .pricing-card.featured::before {
        content: 'Most Popular';
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--accent-color);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .plan-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .plan-price {
        font-size: 3rem;
        font-weight: 700;
        color: var(--accent-color);
        margin-bottom: 0.5rem;
    }

    .plan-period {
        color: #718096;
        margin-bottom: 2rem;
    }

    .plan-features {
        list-style: none;
        padding: 0;
        margin-bottom: 2rem;
    }

    .plan-features li {
        padding: 0.5rem 0;
        color: #4a5568;
    }

    .plan-features li i {
        color: var(--success-color);
        margin-right: 0.5rem;
    }

    .btn-pricing {
        background: var(--primary-gradient);
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-pricing:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* CTA Section */
    .cta-section {
        padding: 100px 0;
        background: var(--primary-gradient);
        color: white;
        text-align: center;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .cta-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .dashboard-preview img {
            transform: none;
        }

        .navbar-brand img {
            width: 120px;
        }

        .navbar-toggler {
            border-color: rgba(255,255,255,0.3);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    }
</style>
@endsection

@section('content')

<!-- Hero Section -->
<section class="hero-modern">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">Streamline Your PCO Operations</h1>
                    <p class="hero-subtitle">Complete Private Hire Vehicle management platform designed for PCO operators. Manage drivers, bookings, vehicles, and compliance with intelligent automation and real-time insights.</p>
                    <a href="#features" class="btn-hero">Get Started Today</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-preview">
                    <img src="{{ asset('assets/images/dashboard-preview.png') }}" alt="PCO Flow Dashboard" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Brand Logos -->
<section class="brand-logos">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-6 col-md-2 text-center mb-3">
                <img src="{{ asset('assets/images/brand1.png') }}" alt="Brand 1" class="img-fluid">
            </div>
            <div class="col-6 col-md-2 text-center mb-3">
                <img src="{{ asset('assets/images/brand2.png') }}" alt="Brand 2" class="img-fluid">
            </div>
            <div class="col-6 col-md-2 text-center mb-3">
                <img src="{{ asset('assets/images/brand3.png') }}" alt="Brand 3" class="img-fluid">
            </div>
            <div class="col-6 col-md-2 text-center mb-3">
                <img src="{{ asset('assets/images/brand4.png') }}" alt="Brand 4" class="img-fluid">
            </div>
            <div class="col-6 col-md-2 text-center mb-3">
                <img src="{{ asset('assets/images/brand5.png') }}" alt="Brand 5" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section" id="features">
    <div class="container">
        <h2 class="section-title">Why Choose PCO Flow</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Driver Management</h3>
                    <p class="feature-description">Complete driver portal with license tracking, document management, and performance analytics designed for PCO operations.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Booking System</h3>
                    <p class="feature-description">Advanced booking management with customer portal, automated dispatch, and integrated payment processing for seamless operations.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Compliance Management</h3>
                    <p class="feature-description">Built-in TfL compliance tools, license monitoring, and automated reminders to keep your PCO operation fully compliant.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3 class="feature-title">Live Tracking</h3>
                    <p class="feature-description">Real-time vehicle tracking with telematics integration for monitoring driver behavior, vehicle location, and trip analytics.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3 class="feature-title">Financial Management</h3>
                    <p class="feature-description">Comprehensive invoicing, commission tracking, driver payments, and financial reporting tailored for PCO business models.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3 class="feature-title">Multi-Platform</h3>
                    <p class="feature-description">Native mobile apps for drivers and customers, web dashboard for operators, and API integration with third-party systems.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="testimonial-section">
    <div class="container">
        <h2 class="section-title">See How PCO Flow is Transforming Private Hire Operations</h2>
        <div class="testimonial-card">
            <p class="testimonial-text">"PCO Flow has completely transformed our private hire business. The driver management system and compliance tools have saved us hours of admin work while keeping us fully TfL compliant."</p>
            <div class="testimonial-author">
                <img src="{{ asset('assets/images/testimonial-avatar.jpg') }}" alt="Sarah Johnson" class="author-avatar">
                <div class="author-info">
                    <h4>Michael Thompson</h4>
                    <p>Fleet Manager, London Cabs Ltd</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="pricing-section">
    <div class="container">
        <h2 class="section-title">Flexible Plans Tailored for PCO Operators</h2>
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card">
                    <h3 class="plan-name">Starter</h3>
                    <div class="plan-price">$29</div>
                    <p class="plan-period">per month</p>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Up to 10 drivers</li>
                        <li><i class="fas fa-check"></i> Basic driver portal</li>
                        <li><i class="fas fa-check"></i> Email support</li>
                        <li><i class="fas fa-check"></i> Mobile app access</li>
                    </ul>
                    <button class="btn-pricing">Get Started</button>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card featured">
                    <h3 class="plan-name">Professional</h3>
                    <div class="plan-price">$79</div>
                    <p class="plan-period">per month</p>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Up to 50 drivers</li>
                        <li><i class="fas fa-check"></i> TfL compliance tools</li>
                        <li><i class="fas fa-check"></i> Priority support</li>
                        <li><i class="fas fa-check"></i> Payment processing</li>
                        <li><i class="fas fa-check"></i> Financial reporting</li>
                    </ul>
                    <button class="btn-pricing">Get Started</button>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card">
                    <h3 class="plan-name">Enterprise</h3>
                    <div class="plan-price">Custom</div>
                    <p class="plan-period">contact us</p>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Unlimited drivers</li>
                        <li><i class="fas fa-check"></i> White-label solution</li>
                        <li><i class="fas fa-check"></i> 24/7 dedicated support</li>
                        <li><i class="fas fa-check"></i> Custom integrations</li>
                        <li><i class="fas fa-check"></i> Multi-operator setup</li>
                    </ul>
                    <button class="btn-pricing">Contact Sales</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Ready to Transform Your PCO Operations?</h2>
        <p class="cta-subtitle">Join hundreds of PCO operators already using PCO Flow to streamline their private hire business.</p>
        <a href="{{ route('frontend.contact') }}" class="btn-hero">Start Your Free Trial</a>
    </div>
</section>

@endsection