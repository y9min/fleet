@extends('frontend.layouts.app')

@section('title') 
    <title>PCOFlow</title>
@endsection

@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@600&display=swap" rel="stylesheet">
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
        width: 112px;
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
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
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
        padding: 96px 0 64px;
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
        font-size: 4.5rem; /* ~72px */
        font-weight: 600;
        line-height: 1.1;
        letter-spacing: -0.5px;
        margin-bottom: 1.25rem;
    }
    .hero-title .mint {
        background: linear-gradient(90deg, #7FD7DF 0%, #BDEFCC 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
        font-size: 1rem;
        line-height: 1.6;
        opacity: 0.95;
        margin-bottom: 1.25rem;
        max-width: 480px;
        color:#EAF3F4;
    }

    .btn-hero {
        background: linear-gradient(90deg, #7FD7DF 0%, #BDEFCC 100%);
        color: #0F2A2C;
        padding: 10px 18px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 0 0 1px rgba(0,0,0,0.0);
    }

    .btn-hero:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        background: linear-gradient(90deg, #7FD7DF 0%, #BDEFCC 100%);
        color: #032127;
    }

    .dashboard-preview { position: relative; z-index:2; }
    .img-grey-box { width:100%; height: 440px; background:#D1D5DB; border-radius:16px; box-shadow: var(--card-shadow); }
    .img-hero-ss1 { width:2503px; max-width:100%; height:auto; border-radius:40px; box-shadow: var(--card-shadow); display:block; }
    .hero-image-wrap { display:flex; justify-content:center; margin-top:16px; }
    .hero-fixed { width:2503px; max-width:100%; margin:0 auto; position:relative; }
    .hero-right { position:absolute; top:0; right:0; width:954px; z-index:1000; }
    .hero-content { width:600px; }
    .hero-right .hero-subtitle { font-size:22.5px; line-height:27px; color:#EAF3F4; margin-bottom:16px; max-width:954px; }
    .subtitle-container { width:450px; max-width:100%; margin-left:auto; }
    @media (max-width: 1200px) {
        .hero-right { position:static; width:100%; max-width:954px; margin:12px auto 0 auto; padding:0 16px; }
        .hero-right .hero-subtitle { font-size:20px; line-height:28px; }
        .hero-image-wrap { justify-content:center !important; }
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

    /* Why choose section */
    .features-section { padding: 80px 0; background:#fff; }
    #why { margin-top:-80px; position:relative; z-index:3; }
    #why.features-section { padding-top: 40px; }
    .section-eyebrow { display:inline-flex; align-items:center; gap:8px; padding:6px 12px; border-radius:999px; background:#EFF6F8; color:#3B4B50; font-size:12px; font-weight:600; margin:0 auto 10px auto; }
    .section-title { font-size: 2.75rem; font-weight: 600; text-align:center; margin:0 0 6px 0; color:#102B2E; font-family: 'Source Sans Pro', sans-serif; }
    .section-subtitle { text-align:center; color:#6A7C80; font-size:14px; margin-bottom:28px; }
    .why-card { background:#E9F7F4; border-radius:16px; padding:22px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .why-card.mint { background:#DFF7E8; }
    .why-title { display:flex; align-items:center; gap:12px; font-size:28px; font-weight:800; color:#102B2E; margin:0 0 10px 0; }
    .why-title .dot { width:24px; height:24px; border-radius:50%; background:#0F2A2C; color:#AEEBCB; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; }
    .why-desc { color:#516569; font-size:14px; max-width:420px; }
    .why-shot { width:100%; height:260px; background:#D1D5DB; border-radius:8px; margin-top:14px; }
    .why-shot.small { height:210px; }

    /* Features grid (next section) */
    .features-eyebrow { display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:999px; background:#ECF4F6; color:#3B4B50; font-weight:700; font-size:14px; }
    .features-title { font-size:3rem; font-weight:600; color:#0F2A2C; margin:16px 0 28px 0; font-family: 'Source Sans Pro', sans-serif; }
    .feature-card-v2 { background:#FFFFFF; border:1px solid #E6ECEE; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.06); overflow:hidden; height:100%; display:flex; flex-direction:column; }
    .feature-shot { width:100%; height:260px; background:#F7F7F7; }
    .feature-shot.fines-shot { background-position: right bottom; background-size: 85% auto; background-repeat: no-repeat; }
    .feature-shot.onboarding-shot { background-position: left bottom, right bottom; background-size: 57.6% auto, 31.3632% auto; background-repeat: no-repeat, no-repeat; }
    .feature-shot.inspection-shot { background-position: right center; background-size: 90% auto; background-repeat: no-repeat; }
    .feature-body { padding:18px 18px 22px 18px; }
    .feature-name { font-size:18px; font-weight:600; color:#0F2A2C; margin:0 0 6px 0; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .feature-desc { font-size:14px; color:#566A6F; margin:0; }

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
        background: linear-gradient(to right, #80D7DF, #BDEFCC);
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

    .testimonial-section .section-title {
        color: white;
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
        background: linear-gradient(to right, #80D7DF, #BDEFCC);
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
    .cta-wrap { background:#032127; padding:80px 0; }
    .cta-card { max-width:760px; margin:0 auto; border-radius:16px; overflow:hidden; position:relative; }
    .cta-bg { width:100%; height:380px; background: url('{{ asset('assets/ss10.png') }}') center center / cover no-repeat; filter:none; }
    .cta-overlay { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:24px; background:linear-gradient(180deg, rgba(0,0,0,0.35) 0%, rgba(0,0,0,0.45) 100%); z-index:2; }
    .cta-logo { height:72px; margin-bottom:16px; width:auto; display:block; position:relative; z-index:3; }
    .cta-title { font-size:2.75rem; font-weight:600; color:#FFFFFF; margin:6px 0; line-height:1.2; font-family: 'Source Sans Pro', sans-serif; }
    .cta-subtitle { color:#E7ECEE; font-size:14px; margin-bottom:16px; }
    .cta-btn { background: linear-gradient(90deg, #7FD7DF 0%, #BDEFCC 100%); color:#0F2A2C; border:0; padding:10px 18px; border-radius:999px; font-weight:700; font-size:14px; text-decoration:none; display:inline-block; }
    .cta-btn:hover { text-decoration:none; }

    /* Footer Styles */
    footer {
        background: #F7F7F7 !important;
        color: #333 !important;
    }

    footer a {
        color: #333 !important;
    }

    /* Mobile Login Button Styling */
    .mobile-login-btn {
        background: linear-gradient(to right, #80D7DF, #BDEFCC) !important;
        color: white !important;
        border: none !important;
        padding: 8px 20px !important;
        border-radius: 25px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
    }

    .mobile-login-btn:hover {
        background: linear-gradient(to right, #BDEFCC, #80D7DF) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(0,0,0,0.2) !important;
    }

    .login-btn-res {
        display: flex !important;
        align-items: center !important;
        margin-right: 15px !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-modern { padding-top: 38px !important; }
        .hero-title { font-size: 2.5rem; }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .img-grey-box { height: 240px; }

        /* Prevent logo/nav overlap and keep layout tidy */
        .navbar-brand img { width: 140px !important; height: auto !important; }
        .main-menubar { justify-content: space-between; padding: 0 12px; }
        .res-collapse { margin-left: auto; display: flex; align-items: center; gap: 8px; }
        .navbar { padding: 0 0 8px 0; }
        .header { padding-top: 70px !important; }
        .main-section-background { overflow: visible !important; }

        /* Collapse nav to stacked panel so it doesn't collide with logo */
        .navbar-nav {
            position: static !important;
            left: auto !important;
            transform: none !important;
            flex-direction: column;
            background: var(--dark-bg);
            padding: 8px;
            margin-top: 6px;
            border-radius: 10px;
            width: 100%;
            gap: 3px; /* reduced spacing */
            max-height: 250px; /* constrain dropdown list */
            height: 250px;
            overflow-y: auto;
        }

        .navbar-nav .nav-item { margin: 1px 0; }

        /* Right fixed desktop auth buttons should not show on mobile */
        .signin-signup-btn { display: none !important; }

        .auth-buttons { margin-top: 10px; flex-direction: column; gap: 8px; width: 100%; }

        .signin-signup-btn .btn,
        .auth-buttons .btn { margin-left: 0; margin-bottom: 8px; width: 100%; text-align: center; }

        .navbar-toggler { border-color: rgba(255,255,255,0.3); }
        .navbar-toggler-icon,
        .custom-toggler.navbar-toggler-icon { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important; width: 1.8rem; height: 1.8rem; background-size: 100% 100%; }

        /* Mobile auth buttons match desktop thin white outline style */
        .mobile-login-btn,
        .mobile-signup-btn,
        .res-collapse .btn {
            background: transparent !important;
            color: #EAF3F4 !important;
            border: 1px solid rgba(234,243,244,0.25) !important;
            padding: 8px 14px !important;
            border-radius: 999px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            height: 36px !important;
            display: flex !important;
            align-items: center !important;
        }
        .mobile-login-btn { padding: 6px 16px !important; font-size: 13px !important; margin-right: 0 !important; }
        .login-btn-res { margin-right: 6px !important; }

        /* Ensure hero right content flows below image and button isn't clipped */
        .hero-right { position: static !important; width: 100% !important; margin-top: 12px !important; }
        .subtitle-container { width: 100% !important; margin-left: 0 !important; }
        .btn-hero { display: inline-block; max-width: 100%; white-space: nowrap; margin-bottom: 40px; }
        .hero-image-wrap { justify-content: center !important; }

        /* Toggler swaps burger to X when open */
        .navbar-toggler .close { display: none; }
        .navbar-toggler[aria-expanded="true"] .close { display: inline-flex; align-items: center; }
        .navbar-toggler .close { float: none !important; position: static !important; margin: 0 !important; }
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon,
        .navbar-toggler[aria-expanded="true"] .custom-toggler.navbar-toggler-icon { display: none !important; }

        /* Mobile burger menu container -> dropdown under toggler */
        .navbar { position: relative; }
        #navbarsExample09 { position: absolute; top: 100%; left: 12px; right: 12px; background: var(--dark-bg); padding: 8px; z-index: 20000; border: 1px solid rgba(234,243,244,0.25); border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.35); max-height: none; height: auto !important; overflow: visible; min-height: 0; --bs-collapse-transition: none; }
        #navbarsExample09.collapsing { height: auto !important; overflow: visible !important; width: calc(100% - 24px - 150px) !important; left: auto !important; right: 12px !important; -webkit-transition: none !important; transition: none !important; }
        #navbarsExample09.collapse { display: none; }
        #navbarsExample09.show { display: block; width: calc(100% - 24px - 150px); left: auto; right: 12px; }
        .mobile-menu-close { display: none !important; }
        #navbarsExample09 .navbar-nav { background: transparent; padding: 0; margin-top: 0; }
        #navbarsExample09 .navbar-nav .nav-link { padding: 4px 4px !important; }

        /* Driver Portal card: keep image inside and visible */
        #why .why-card.mint { overflow: hidden; }
        #why .why-card.mint img[alt="Driver Portal screenshot"] {
            position: static !important;
            width: 100% !important;
            height: auto !important;
            right: auto !important;
            top: auto !important;
        }
        #why .why-card.mint .why-desc { width: 100% !important; }
        #why .why-card.mint > div[style*="min-height"] { min-height: 0 !important; }

        /* CTA section sizing on small screens */
        .cta-bg { height: 280px; }
        .cta-title { font-size: 2rem; }
        .cta-subtitle { font-size: 13px; }
        .cta-btn { display: inline-block; max-width: 100%; white-space: nowrap; }
    }
</style>
@endsection

@section('content')

<!-- Hero Section -->
<section class="hero-modern">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="hero-fixed">
                    <div class="hero-content" style="text-align:left; position:relative; z-index:3;">
                        <h1 class="hero-title" style="margin-left:0;">Streamline Your<br> PCO Operations,<br><span class="mint">All In One Place</span></h1>
                    </div>
                    <div class="hero-image-wrap" style="justify-content:flex-start; position:relative; z-index:1;">
                        <img src="{{ asset('assets/ss1.png') }}" alt="Dashboard preview" class="img-hero-ss1">
                    </div>
                    <div class="hero-right">
                        <div class="subtitle-container">
                            <p class="hero-subtitle">Our platform simplifies PCO operations with a powerful dashboard, driver and vehicle management, streamlined onboarding, and real‑time insights.</p>
                            <a href="{{ url('contact') }}" class="btn-hero">Book a demo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Why choose section -->
<section class="features-section" id="why">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="section-eyebrow">Smart. Simple. Scaleable.</div>
        </div>
        <h2 class="section-title">Why choose PCO Flow</h2>
        <div class="section-subtitle">Empowering PCO operators with seamless fleet management<br>and real-time insights.</div>

        <div class="why-card mb-4" style="background:#ECFAFA; min-height:390px;">
            <div class="why-title" style="font-size:36px; font-family: 'Source Sans Pro', sans-serif; font-weight:400;"><span class="dot" style="background:#000; color:#000;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="12" cy="8" r="4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                    <path d="M4 20c0-4 4-6 8-6s8 2 8 6" stroke="#FFFFFF" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </span> Driver Management</div>
            <div class="row g-3">
                <div class="col-lg-4 col-md-5">
                    <div class="why-desc" style="font-size:18px;">Onboard, track, and manage drivers with ease. Store documents, monitor compliance, and keep profiles up to date automatically.</div>
                </div>
                <div class="col-lg-8 col-md-7 d-flex justify-content-end align-items-end" style="position:relative; min-height:390px;">
                    <img src="{{ asset('assets/ss2.png') }}" alt="Driver Management screenshot" style="position:absolute; bottom:-27px; right:-12px; width:100%; height:auto; display:block; border-radius:8px;">
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6" style="flex: 0 0 55%; max-width:55%;">
                <div class="why-card" style="display:flex; flex-direction:column; background:#C8F2F2; padding-bottom:0;">
                    <div class="why-title" style="font-size:36px; font-family: 'Source Sans Pro', sans-serif; font-weight:400;"><span class="dot" style="background:#000; color:#000;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#FFFFFF" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                        </svg>
                    </span> Vehicle Control</div>
                    <div class="why-desc mb-2" style="font-size:18px; max-width:100%;">Manage your entire fleet in one place. Organise vehicles by groups, track inspections, and log service history effortlessly.</div>
                    <img src="{{ asset('assets/ss3.png') }}" alt="Vehicle Control screenshot" style="margin-top:auto; width:100%; height:auto; display:block; border-radius:8px;">
                </div>
            </div>
            <div class="col-lg-6" style="flex: 0 0 45%; max-width:45%;">
                <div class="why-card mint" style="background:#BFF0CB; display:flex; flex-direction:column; padding-bottom:0; height:100%;">
                    <div class="why-title" style="font-size:36px; font-family: 'Source Sans Pro', sans-serif; font-weight:400;">
        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="margin-right:12px;">
            <circle cx="12" cy="12" r="12" fill="#000000"/>
            <g transform="translate(4,4) scale(0.6667)">
                <rect x="4" y="1.5" width="16" height="21" rx="3" fill="#FFFFFF"/>
                <rect x="7" y="5" width="10" height="12" rx="1.5" fill="#000000"/>
                <circle cx="12" cy="19" r="1" fill="#000000"/>
            </g>
        </svg>
                        Driver Portal
                    </div>
                    <div style="position:relative; flex:1; min-height:330px;">
                        <div class="why-desc mb-2" style="font-size:18px; width:260px; position:relative; z-index:2;">Give access to everything in one place. Allow drivers to view assigned vehicles, payments, service reminders, and fines — all at a glance.</div>
                        <img src="{{ asset('assets/ss4.png') }}" alt="Driver Portal screenshot" style="position:absolute; top:0; right:-22px; bottom:0; height:100%; width:auto; display:block; z-index:1;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features grid -->
<section class="features-section" id="features">
    <div class="container">
        <div class="features-eyebrow">Features</div>
        <h2 class="features-title">Built to streamline your operations and boost
            productivity</h2>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="feature-card-v2">
                    <div class="feature-shot fines-shot" style="background-image:url('{{ asset('assets/ss5.png') }}');"></div>
                    <div class="feature-body">
                        <div class="feature-name">Fines & Penalties Tracking</div>
                        <p class="feature-desc">Comprehensive system for managing traffic fines, penalties, and violations with automated notifications and status tracking.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="feature-card-v2">
                    <div class="feature-shot onboarding-shot" style="background-image:url('{{ asset('assets/ss8.png') }}'), url('{{ asset('assets/ss9.png') }}');"></div>
                    <div class="feature-body">
                        <div class="feature-name">Driver Onboarding System</div>
                        <p class="feature-desc">Streamlined driver registration with document uploads, personal information collection, and automated user account creation with status tracking.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="feature-card-v2">
                    <div class="feature-shot inspection-shot" style="background-image:url('{{ asset('assets/ss7.png') }}');"></div>
                    <div class="feature-body">
                        <div class="feature-name">Vehicle Inspection Management</div>
                        <p class="feature-desc">Centralised dashboard for tracking inspections, MOT expiries, vehicle status, and alerts across your entire fleet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA banner -->
<section class="cta-wrap">
    <div class="container">
        <div class="cta-card">
            <div class="cta-bg"></div>
                <div class="cta-overlay">
                    <img class="cta-logo" src="{{ asset('assets/pco-flow-logo-2.png') }}" alt="PCO Flow logo">
                <h2 class="cta-title">Plans That Scale With<br>Your Business</h2>
                <div class="cta-subtitle">Clear, straightforward pricing with no surprises.</div>
                <a href="{{ url('contact') }}" class="cta-btn">Book a demo</a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var collapseEl = document.getElementById('navbarsExample09');
    if (!collapseEl) return;
    var closeBtn = collapseEl.querySelector('.mobile-menu-close');
    var togglers = document.querySelectorAll('[data-bs-target="#navbarsExample09"]');

    function closeMenu() {
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                var instance = bootstrap.Collapse.getInstance(collapseEl);
                if (!instance) { instance = new bootstrap.Collapse(collapseEl, { toggle: false }); }
                instance.hide();
            }
        } catch(e) {}
        collapseEl.classList.remove('show');
        collapseEl.style.display = 'none';
        // No body scroll locking for dropdown
        // sync toggler button state
        togglers.forEach(function(btn){
            btn.classList.add('collapsed');
            btn.setAttribute('aria-expanded', 'false');
        });
    }
    var links = collapseEl.querySelectorAll('.nav-link');
    links.forEach(function(link) {
        link.addEventListener('click', function() {
            closeMenu();
        });
    });

    // Fallback toggle when Bootstrap JS isn't loaded
    togglers.forEach(function(btn){
        btn.addEventListener('click', function(e){
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) return; // let Bootstrap handle
            e.preventDefault();
            if (collapseEl.classList.contains('show')) {
                closeMenu();
            } else {
                collapseEl.classList.add('show');
                collapseEl.style.display = 'block';
                // No body scroll locking for dropdown
                btn.classList.remove('collapsed');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeMenu);
    }

    // Expose a global fallback so inline onclick can call it
    window.closeMobileMenu = closeMenu;

    // Also close on Escape key
    document.addEventListener('keydown', function(ev){
        if (ev.key === 'Escape' && collapseEl.classList.contains('show')) {
            closeMenu();
        }
    });
});
</script>
@endpush