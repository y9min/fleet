@extends('frontend.layouts.app')

@section('title') 
    <title>Streamline Your Business, One Click at a Time | {{ Hyvikk::get('app_name') }}</title>
@endsection

@section('css')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --dark-bg: #1a1a2e;
        --accent-color: #4facfe;
        --text-light: #f8f9fa;
        --card-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        overflow-x: hidden;
    }

    /* Hero Section */
    .hero-modern {
        background: var(--primary-gradient);
        min-height: 100vh;
        display: flex;
        align-items: center;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
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
        color: #667eea;
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
        color: #667eea;
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
        background: #f8f9fa;
    }

    .brand-logos img {
        height: 40px;
        opacity: 0.6;
        transition: opacity 0.3s ease;
    }

    .brand-logos img:hover {
        opacity: 1;
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
        color: #48bb78;
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
                    <h1 class="hero-title">Streamline Your Business, One Click at a Time</h1>
                    <p class="hero-subtitle">Powerful fleet management platform that transforms how you manage vehicles, drivers, and operations with intelligent automation and real-time insights.</p>
                    <a href="#features" class="btn-hero">Get Started Today</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-preview">
                    <img src="{{ asset('assets/images/dashboard-preview.png') }}" alt="Fleet Manager Dashboard" class="img-fluid">
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
        <h2 class="section-title">Why Choose Fleet Manager</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Real-time Analytics</h3>
                    <p class="feature-description">Get comprehensive insights into your fleet operations with real-time tracking, performance metrics, and detailed reporting.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Mobile Optimization</h3>
                    <p class="feature-description">Access your fleet management system anywhere, anytime with our fully responsive mobile-optimized platform.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Advanced Security</h3>
                    <p class="feature-description">Enterprise-grade security with encrypted data transmission, secure authentication, and compliance with industry standards.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3 class="feature-title">Route Optimization</h3>
                    <p class="feature-description">Intelligent route planning and optimization to reduce fuel costs, improve efficiency, and enhance customer satisfaction.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3 class="feature-title">Payment Integration</h3>
                    <p class="feature-description">Seamless payment processing with multiple payment gateways, automated billing, and financial reporting.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3 class="feature-title">API Integration</h3>
                    <p class="feature-description">Flexible API integration capabilities to connect with your existing systems and third-party applications.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="testimonial-section">
    <div class="container">
        <h2 class="section-title">See How Fleet Manager is Transforming Businesses</h2>
        <div class="testimonial-card">
            <p class="testimonial-text">"Fleet Manager has revolutionized our operations. The real-time tracking and automated reporting have saved us countless hours and significantly improved our efficiency."</p>
            <div class="testimonial-author">
                <img src="{{ asset('assets/images/testimonial-avatar.jpg') }}" alt="Sarah Johnson" class="author-avatar">
                <div class="author-info">
                    <h4>Sarah Johnson</h4>
                    <p>Operations Director, TransportCo</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="pricing-section">
    <div class="container">
        <h2 class="section-title">Flexible Plans Tailored for Every Business</h2>
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card">
                    <h3 class="plan-name">Starter</h3>
                    <div class="plan-price">$29</div>
                    <p class="plan-period">per month</p>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Up to 10 vehicles</li>
                        <li><i class="fas fa-check"></i> Basic reporting</li>
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
                        <li><i class="fas fa-check"></i> Up to 50 vehicles</li>
                        <li><i class="fas fa-check"></i> Advanced analytics</li>
                        <li><i class="fas fa-check"></i> Priority support</li>
                        <li><i class="fas fa-check"></i> API integration</li>
                        <li><i class="fas fa-check"></i> Custom reporting</li>
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
                        <li><i class="fas fa-check"></i> Unlimited vehicles</li>
                        <li><i class="fas fa-check"></i> White-label solution</li>
                        <li><i class="fas fa-check"></i> 24/7 dedicated support</li>
                        <li><i class="fas fa-check"></i> Custom integrations</li>
                        <li><i class="fas fa-check"></i> On-premise deployment</li>
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
        <h2 class="cta-title">Ready to Transform Your Fleet?</h2>
        <p class="cta-subtitle">Join thousands of businesses already using Fleet Manager to streamline their operations.</p>
        <a href="{{ route('frontend.contact') }}" class="btn-hero">Start Your Free Trial</a>
    </div>
</section>

@endsection