<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <!-- Meta Tags -->
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Shubh Patel">
  <title>DineFlow AI – Next-Gen Restaurant Cloud POS, QR Ordering & Enterprise ERP</title>
  <meta name="description" content="DineFlow AI is India's most advanced restaurant cloud POS and operating system. Instant QR table ordering, lightning-fast 3-second billing, real-time KDS, silent ESC/POS thermal printing, and recipe inventory ERP.">

  <!-- Open Graph / SEO -->
  <meta property="og:title" content="DineFlow AI – Modern Restaurant POS, QR Ordering & Kitchen Management">
  <meta property="og:description" content="Complete restaurant operating system. Streamline QR table ordering, fast POS billing, real-time KDS, silent thermal printing, and staff payroll.">
  <meta property="og:image" content="{{ asset('images/dineflow_banner.jpg') }}">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url('/') }}">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="robots" content="index, follow">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" href="/favicon.png">
  <link rel="shortcut icon" href="/favicon.ico">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="/vendor/posing/css/plugins/bootstrap.min.css">
  <link rel="stylesheet" href="/vendor/posing/css/plugins/slick.css">
  <link rel="stylesheet" href="/vendor/posing/css/plugins/animate.css">
  <link rel="stylesheet" href="/vendor/posing/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Custom DineFlow Design Refinements -->
  <style>
    :root {
      --df-primary: #6366F1;
      --df-primary-dark: #4F46E5;
      --df-secondary: #52C8C4;
      --df-accent: #EC4899;
      --df-dark: #0F172A;
      --df-slate: #1E293B;
      --df-whatsapp: #25D366;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      color: #334155;
      background-color: #FAFAFC;
      overflow-x: hidden;
    }

    /* Fixed & Polished Navigation Header */
    .cs-site_header.cs-style1 {
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
      background-color: #ffffff;
      z-index: 1000;
    }
    .cs-site_header.cs-sticky-active {
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.09);
      background-color: #ffffff;
    }
    .cs-site_header.cs-style1 .cs-main_header_in {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      flex-wrap: nowrap !important;
      height: 80px;
    }
    .cs-site_branding img {
      max-height: 44px;
      width: auto;
      object-fit: contain;
    }
    
    /* Clean Desktop Nav Links with no wrapping */
    .cs-nav .cs-nav_list {
      display: flex !important;
      align-items: center !important;
      flex-wrap: nowrap !important;
      gap: 22px !important;
      margin: 0 !important;
      padding: 0 !important;
      list-style: none !important;
    }
    .cs-nav .cs-nav_list > li {
      margin: 0 !important;
      padding: 0 !important;
      white-space: nowrap !important;
    }
    .cs-nav .cs-nav_list > li > a {
      font-weight: 600 !important;
      font-size: 14.5px !important;
      color: #1E293B !important;
      padding: 8px 2px !important;
      transition: color 0.2s ease !important;
      text-decoration: none !important;
    }
    .cs-nav .cs-nav_list > li > a:hover {
      color: #6366F1 !important;
    }

    .cs-toolbox {
      display: flex !important;
      align-items: center !important;
      flex-shrink: 0 !important;
      white-space: nowrap !important;
      gap: 16px !important;
    }
    .cs-toolbox .cs-btn span {
      padding: 9px 20px !important;
      font-size: 14px !important;
    }

    /* Hero Section: 2-Column Clean Grid (NO OVERLAPPING) */
    .df-hero-section {
      position: relative;
      padding: 50px 0 90px 0 !important;
      min-height: auto !important;
      display: block !important;
      overflow: hidden;
      background-color: #F8FAFC;
    }

    /* Reset Posing template absolute position on hero image */
    .df-hero-section .cs-hero_img {
      position: relative !important;
      top: auto !important;
      left: auto !important;
      transform: none !important;
      width: 100% !important;
      height: auto !important;
      margin: 0 !important;
      display: block !important;
    }

    .df-hero-heading {
      font-size: 50px !important;
      line-height: 1.15 !important;
      font-weight: 800 !important;
      color: #0F172A !important;
      margin-bottom: 18px !important;
      letter-spacing: -0.5px;
    }

    .df-hero-sub {
      font-size: 16.5px !important;
      line-height: 1.65 !important;
      color: #475569 !important;
      max-width: 530px !important;
      margin-bottom: 28px !important;
    }

    /* Badge & Micro Elements */
    .df-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(99, 102, 241, 0.08);
      color: #4F46E5;
      border: 1px solid rgba(99, 102, 241, 0.2);
      border-radius: 9999px;
      padding: 6px 16px;
      font-size: 12.5px;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 16px;
    }

    .df-badge-whatsapp {
      background: #25D366;
      color: #ffffff;
      box-shadow: 0 4px 14px rgba(37, 211, 102, 0.35);
    }
    .df-badge-whatsapp:hover {
      background: #128C7E;
      color: #ffffff;
    }

    /* Hero Banner Frame */
    .df-hero-mockup-wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
      max-width: 600px;
      border-radius: 20px;
      padding: 8px;
      background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(240,244,255,0.85));
      box-shadow: 0 25px 60px -15px rgba(99, 102, 241, 0.28);
      border: 1px solid rgba(99, 102, 241, 0.2);
    }
    .df-hero-mockup-img {
      display: block;
      width: 100%;
      height: auto;
      border-radius: 14px;
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    }

    /* Floating Metric Badges on Mockup */
    .df-stat-pill {
      position: absolute;
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(99, 102, 241, 0.2);
      padding: 8px 14px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.14);
      display: flex;
      align-items: center;
      gap: 10px;
      z-index: 5;
    }
    .df-stat-pill.pill-1 {
      bottom: -15px;
      left: 15px;
    }
    .df-stat-pill.pill-2 {
      top: 15px;
      right: 15px;
    }
    .df-stat-pill-icon {
      width: 36px;
      height: 36px;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
    }

    /* Primary Accent Buttons */
    .cs-btn {
      background-color: #6366F1;
      border-color: #6366F1;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .cs-btn:before {
      border-color: #6366F1;
    }
    .cs-btn.cs-color1 {
      background-color: #52C8C4;
      border-color: #52C8C4;
    }
    .cs-btn.cs-btn-outline {
      background-color: transparent;
      border: 2px solid #6366F1;
      color: #6366F1;
    }
    .cs-btn.cs-btn-outline:hover {
      background-color: #6366F1;
      color: #ffffff;
    }
    .cs-btn.cs-btn-outline:before, .cs-btn.cs-btn-outline:after {
      display: none;
    }

    /* Pricing Card Glow on Featured */
    .cs-pricing_table.featured-plan {
      border: 2px solid #6366F1;
      position: relative;
      transform: translateY(-8px);
      box-shadow: 0 20px 45px rgba(99, 102, 241, 0.2);
    }
    .cs-pricing_featured_badge {
      position: absolute;
      top: -14px;
      left: 50%;
      transform: translateX(-50%);
      background: linear-gradient(135deg, #6366F1, #4F46E5);
      color: #ffffff;
      font-size: 12px;
      font-weight: 700;
      padding: 4px 16px;
      border-radius: 9999px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Hardware Thermal Badge */
    .df-printer-card {
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid #E2E8F0;
      padding: 24px;
      transition: all 0.3s ease;
      height: 100%;
    }
    .df-printer-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
      border-color: #6366F1;
    }

    @media (max-width: 1200px) {
      .df-hero-heading {
        font-size: 42px !important;
      }
      .cs-nav .cs-nav_list {
        gap: 14px !important;
      }
    }

    @media (max-width: 991px) {
      .df-stat-pill {
        display: none;
      }
      .cs-pricing_table.featured-plan {
        transform: none;
      }
      .df-hero-heading {
        font-size: 36px !important;
      }
    }
  </style>
</head>

<body>
  <!-- Preloader -->
  <div class="cs-preloader cs-white_bg cs-center">
    <div class="cs-preloader_in">
      <img src="/favicon.svg" alt="DineFlow AI Logo" style="width: 50px; height: 50px;">
    </div>
  </div>

  <!-- Start Header Section -->
  <header class="cs-site_header cs-style1 cs-sticky-header cs-primary_color cs-white_bg">
    <div class="cs-main_header">
      <div class="container-fluid px-xl-5 px-lg-4 px-3">
        <div class="cs-main_header_in">
          <!-- Left Logo -->
          <div class="cs-main_header_left">
            <a class="cs-site_branding cs-accent_color" href="/">
              <img src="/images/dineflow_logo.png" alt="DineFlow AI">
            </a>
          </div>

          <!-- Center Nav -->
          <div class="cs-main_header_center">
            <div class="cs-nav">
              <ul class="cs-nav_list">
                <li><a href="#features" class="cs-smoth_scroll">Features</a></li>
                <li><a href="#operations" class="cs-smoth_scroll">Operations</a></li>
                <li><a href="#hardware" class="cs-smoth_scroll">Hardware</a></li>
                <li><a href="#formats" class="cs-smoth_scroll">Formats</a></li>
                <li><a href="#pricing" class="cs-smoth_scroll">Pricing</a></li>
                <li><a href="#faq" class="cs-smoth_scroll">FAQ</a></li>
                <li><a href="#contact" class="cs-smoth_scroll">Contact</a></li>
              </ul>
            </div>
          </div>

          <!-- Right Toolbox -->
          <div class="cs-main_header_right">
            <div class="cs-toolbox">
              <a href="/demo" class="cs-link d-none d-md-inline-flex align-items-center text-decoration-none" style="color: #6366F1; font-weight: 700;">
                <i class="fa-solid fa-play-circle me-1"></i> Live Demo
              </a>
              <a href="{{ route('login') }}" class="cs-link text-decoration-none" style="font-weight: 600; color: #1E293B;">
                Sign In
              </a>
              <a href="{{ route('register') }}" class="cs-btn cs-color1 text-decoration-none">
                <span>Start Free Trial</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <!-- End Header Section -->

  <!-- Start Hero Section (Rock-Solid 2-Column Grid) -->
  <div id="home">
    <div class="cs-height_80 cs-height_lg_80"></div>
    <section class="cs-hero cs-style1 cs-bg df-hero-section" data-src="/vendor/posing/img/hero_bg.svg">
      <div class="container py-lg-4">
        <div class="row align-items-center g-4 g-xl-5">
          <!-- Hero Content (Left Column) -->
          <div class="col-lg-6 cs-hero_text">
            <div class="df-badge">
              <i class="fa-solid fa-sparkles"></i> 100% Cloud + Offline Local Sync
            </div>
            <h1 class="df-hero-heading">
              Next-Gen Restaurant <br>
              <span style="color: #6366F1;">Cloud POS &amp; ERP</span>
            </h1>
            <div class="df-hero-sub">
              Accelerate table turnover with 3-second billing, contactless QR table ordering, real-time Kitchen Display (KDS), silent ESC/POS thermal printing, and recipe inventory management.
            </div>

            <div class="cs-btn_group d-flex align-items-center flex-wrap" style="gap: 12px;">
              <a href="{{ route('register') }}" class="cs-btn text-decoration-none">
                <span>Start 14-Day Free Trial</span>
              </a>
              <a href="/demo" class="cs-btn cs-btn-outline text-decoration-none">
                <span><i class="fa-solid fa-desktop me-2"></i> Explore Sandbox Demo</span>
              </a>
              <a href="/buy-source-code" class="cs-btn cs-color1 text-decoration-none">
                <span><i class="fa-solid fa-code me-2"></i> Buy Source Code</span>
              </a>
            </div>

            <!-- Trust Badges -->
            <div class="mt-4 pt-2 d-flex align-items-center flex-wrap" style="gap: 18px; font-size: 13px; color: #64748B; font-weight: 600;">
              <span><i class="fa-solid fa-circle-check text-success me-1"></i> No Credit Card Required</span>
              <span><i class="fa-solid fa-circle-check text-success me-1"></i> GST Ready (GSTR-1 &amp; 3B)</span>
              <span><i class="fa-solid fa-circle-check text-success me-1"></i> Windows, Mac, iPad &amp; Android</span>
            </div>
          </div>

          <!-- Hero Graphic (Right Column) -->
          <div class="col-lg-6 text-center">
            <div class="cs-hero_img">
              <div class="df-hero-mockup-wrapper wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s">
                <img src="/images/dineflow_banner.jpg" alt="DineFlow AI Restaurant Cloud POS & ERP" class="df-hero-mockup-img">
                
                <!-- Floating Stat Pill 1 -->
                <div class="df-stat-pill pill-1">
                  <div class="df-stat-pill-icon" style="background: rgba(99, 102, 241, 0.15); color: #6366F1;">
                    <i class="fa-solid fa-bolt"></i>
                  </div>
                  <div class="text-start">
                    <div style="font-size: 11px; color: #64748B; font-weight: 600;">Fast Checkout</div>
                    <div style="font-size: 14px; font-weight: 800; color: #0F172A;">3-Sec Billing Speed</div>
                  </div>
                </div>

                <!-- Floating Stat Pill 2 -->
                <div class="df-stat-pill pill-2">
                  <div class="df-stat-pill-icon" style="background: rgba(82, 200, 196, 0.15); color: #0D9488;">
                    <i class="fa-solid fa-print"></i>
                  </div>
                  <div class="text-start">
                    <div style="font-size: 11px; color: #64748B; font-weight: 600;">Silent Printing</div>
                    <div style="font-size: 14px; font-weight: 800; color: #0F172A;">Direct ESC/POS Cut</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Floating Background Shapes -->
        <div class="cs-hero_shapes">
          <div class="cs-shape cs-shape_position1">
            <img src="/vendor/posing/img/shape/shape_1.svg" alt="Shape">
          </div>
          <div class="cs-shape cs-shape_position2">
            <img src="/vendor/posing/img/shape/shape_2.svg" alt="Shape">
          </div>
          <div class="cs-shape cs-shape_position3">
            <img src="/vendor/posing/img/shape/shape_3.svg" alt="Shape">
          </div>
          <div class="cs-shape cs-shape_position4">
            <img src="/vendor/posing/img/shape/shape_4.svg" alt="Shape">
          </div>
          <div class="cs-shape cs-shape_position5">
            <img src="/vendor/posing/img/shape/shape_5.svg" alt="Shape">
          </div>
          <div class="cs-shape cs-shape_position6">
            <img src="/vendor/posing/img/shape/shape_6.svg" alt="Shape">
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- End Hero Section -->

  <!-- Start Main Feature Pillars -->
  <section class="cs-bg" data-src="/vendor/posing/img/feature_bg.svg">
    <div class="cs-height_95 cs-height_lg_70"></div>
    <div class="container">
      <div class="cs-seciton_heading cs-style1 text-center">
        <div class="cs-section_subtitle wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">Engineered For High Volume</div>
        <div class="cs-height_10 cs-height_lg_10"></div>
        <h3 class="cs-section_title">Core Pillars of DineFlow AI</h3>
      </div>
      <div class="cs-height_50 cs-height_lg_40"></div>
      
      <div class="row">
        <!-- 01 Fast POS -->
        <div class="col-md-6 col-xl-3">
          <div class="cs-height_25 cs-height_lg_0"></div>
          <div class="cs-iconbox cs-style1">
            <div class="cs-iconbox_icon cs-center">
              <img src="/vendor/posing/img/icons/icon_box_1.svg" alt="Fast POS Billing">     
            </div>
            <div class="cs-iconbox_in">
              <div class="cs-iconbox_number cs-primary_font">01</div>
              <h3 class="cs-iconbox_title">3-Sec POS Billing</h3>
              <div class="cs-iconbox_subtitle">Touchscreen-optimized POS, instant table billing, split checks, token generators, and UPI QR codes.</div>
            </div>
          </div>
          <div class="cs-height_25 cs-height_lg_25"></div>
        </div>

        <!-- 02 QR Table Ordering -->
        <div class="col-md-6 col-xl-3">
          <div class="cs-iconbox cs-style1">
            <div class="cs-iconbox_icon cs-center">
              <img src="/vendor/posing/img/icons/icon_box_2.svg" alt="QR Digital Ordering">               
            </div>
            <div class="cs-iconbox_in">
              <div class="cs-iconbox_number cs-primary_font">02</div>
              <h3 class="cs-iconbox_title">QR Table Ordering</h3>
              <div class="cs-iconbox_subtitle">Zero app download required. Diners scan table QR, browse rich visual menus, customize modifiers, and place orders.</div>
            </div>
          </div>
          <div class="cs-height_25 cs-height_lg_25"></div>
        </div>

        <!-- 03 Live KDS -->
        <div class="col-md-6 col-xl-3">
          <div class="cs-height_25 cs-height_lg_0"></div>
          <div class="cs-iconbox cs-style1">
            <div class="cs-iconbox_icon cs-center">
              <img src="/vendor/posing/img/icons/icon_box_3.svg" alt="Kitchen Display System">   
            </div>
            <div class="cs-iconbox_in">
              <div class="cs-iconbox_number cs-primary_font">03</div>
              <h3 class="cs-iconbox_title">Real-Time KDS</h3>
              <div class="cs-iconbox_subtitle">Color-coded prep timers, station routing, and one-tap bump bar for head chefs to eliminate paper KOT confusion.</div>
            </div>
          </div>
          <div class="cs-height_25 cs-height_lg_25"></div>
        </div>

        <!-- 04 Silent Printing -->
        <div class="col-md-6 col-xl-3">
          <div class="cs-iconbox cs-style1">
            <div class="cs-iconbox_icon cs-center">
              <img src="/vendor/posing/img/icons/icon_box_4.svg" alt="Silent Thermal Printing"> 
            </div>
            <div class="cs-iconbox_in">
              <div class="cs-iconbox_number cs-primary_font">04</div>
              <h3 class="cs-iconbox_title">Silent ESC/POS Print</h3>
              <div class="cs-iconbox_subtitle">Direct background thermal printing via USB, Bluetooth, or LAN with auto-cut and cash drawer trigger without popups.</div>
            </div>
          </div>
          <div class="cs-height_25 cs-height_lg_25"></div>
        </div>
      </div>
      <div class="cs-height_75 cs-height_lg_45"></div>
    </div>
  </section>
  <!-- End Main Feature Pillars -->

  <!-- Start About / Operations Showcase -->
  <section id="operations" class="cs-gradient_bg_1">
    <div class="cs-height_100 cs-height_lg_70"></div>
    <div class="container">
      <div class="row align-items-center flex-column-reverse-lg">
        <div class="col-xl-6 wow fadeInLeft" data-wow-duration="1s" data-wow-delay="0.3s">
          <div class="cs-left_full_width cs-space110">
            <div class="cs-left_sided_img">
              <img src="/vendor/posing/img/about_img_1.png" alt="DineFlow AI Restaurant Operations">
            </div>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="cs-height_0 cs-height_lg_40"></div>
          <div class="cs-seciton_heading cs-style1">
            <div class="cs-section_subtitle">End-To-End Restaurant ERP</div>
            <div class="cs-height_10 cs-height_lg_10"></div>
            <h3 class="cs-section_title">Control Every Detail of Your Kitchen &amp; Floor</h3>
          </div>
          <div class="cs-height_20 cs-height_lg_20"></div>
          <p>
            DineFlow AI bridges the gap between customer experience and kitchen execution. Every transaction triggers automated stock deductions, real-time table state updates, and precise staff audit trails.
          </p>
          <div class="cs-height_15 cs-height_lg_15"></div>
          <div class="cs-list_1_wrap">
            <ul class="cs-list cs-style1 cs-mp0">
              <li>
                <span class="cs-list_icon">
                  <img src="/vendor/posing/img/icons/tick.svg" alt="Tick">                      
                </span>
                <div class="cs-list_right">
                  <h3>Interactive Floor &amp; Table Management</h3>
                  <p>Visual floor plan with real-time dining timers, table merging, seat transfers, and reservation scheduling.</p>
                </div>
              </li>
              <li>
                <span class="cs-list_icon">
                  <img src="/vendor/posing/img/icons/tick.svg" alt="Tick">                      
                </span>
                <div class="cs-list_right">
                  <h3>Recipe Costing &amp; Auto-Depletion</h3>
                  <p>Link raw ingredients to menu items. When a burger sells, the exact grams of cheese, patty, and buns are deducted automatically.</p>
                </div>
              </li>
              <li>
                <span class="cs-list_icon">
                  <img src="/vendor/posing/img/icons/tick.svg" alt="Tick">                      
                </span>
                <div class="cs-list_right">
                  <h3>Shift Controls &amp; Cash Drawer Reconciliation</h3>
                  <p>Manager PIN verification, petty cash entries, blind end-of-shift cash counts, and Day-End Z-reports.</p>
                </div>
              </li>
            </ul>
            <div class="cs-list_img wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">
              <img src="/vendor/posing/img/about_img_2.png" alt="POS Floor & Kitchen Management">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="cs-height_100 cs-height_lg_70"></div>
    <div class="cs-height_135 cs-height_lg_0"></div>
  </section>
  <!-- End About / Operations Showcase -->

  <!-- Start Fun Fact Counters -->
  <div class="container-fluid">
    <div class="cs-funfact_1_wrap cs-type1">
      <div class="cs-funfact cs-style1">
        <div class="cs-funfact_icon cs-center"><img src="/vendor/posing/img/icons/funfact_icon_1.svg" alt="Restaurants"></div>
        <div class="cs-funfact_right">
          <div class="cs-funfact_number cs-primary_font"><span data-count-to="500" class="odometer">500</span>+</div>
          <h2 class="cs-funfact_title">Active Restaurants</h2>
        </div>
      </div>
      <div class="cs-funfact cs-style1">
        <div class="cs-funfact_icon cs-center"><img src="/vendor/posing/img/icons/funfact_icon_2.svg" alt="Orders"></div>
        <div class="cs-funfact_right">
          <div class="cs-funfact_number cs-primary_font"><span data-count-to="1200" class="odometer">1200</span>k+</div>
          <h2 class="cs-funfact_title">Orders Processed</h2>
        </div>
      </div>
      <div class="cs-funfact cs-style1">
        <div class="cs-funfact_icon cs-center"><img src="/vendor/posing/img/icons/funfact_icon_3.svg" alt="Rating"></div>
        <div class="cs-funfact_right">
          <div class="cs-funfact_number cs-primary_font"><span data-count-to="99" class="odometer">99</span>.99%</div>
          <h2 class="cs-funfact_title">Cloud &amp; Offline Uptime</h2>
        </div>
      </div>
      <div class="cs-funfact cs-style1">
        <div class="cs-funfact_icon cs-center"><img src="/vendor/posing/img/icons/funfact_icon_4.svg" alt="Rating"></div>
        <div class="cs-funfact_right">
          <div class="cs-funfact_number cs-primary_font"><span data-count-to="5" class="odometer">5</span>.0★</div>
          <h2 class="cs-funfact_title">Restaurateur Rating</h2>
        </div>
      </div>
    </div>
  </div>
  <!-- End Fun Fact Counters -->

  <!-- Start All Features Grid -->
  <section class="cs-bg" data-src="/vendor/posing/img/feature_bg.svg" id="features">
    <div class="cs-height_135 cs-height_lg_0"></div>
    <div>
      <div class="cs-height_95 cs-height_lg_70"></div>
      <div class="container">
        <div class="cs-seciton_heading cs-style1 text-center">
          <div class="cs-section_subtitle wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">Enterprise Capabilities</div>
          <div class="cs-height_10 cs-height_lg_10"></div>
          <h3 class="cs-section_title">Everything Required to Run a Food Empire</h3>
        </div>
        <div class="cs-height_50 cs-height_lg_40"></div>
        <div class="row">
          <!-- Feat 1 -->
          <div class="col-xl-4 col-md-6">
            <div class="cs-iconbox cs-style1 cs-type1">
              <div class="cs-iconbox_icon cs-center">
                <img src="/vendor/posing/img/icons/icon_box_5.svg" alt="Menu Modifiers & Combos">               
              </div>
              <div class="cs-iconbox_in">
                <h3 class="cs-iconbox_title">Menu Modifiers &amp; Combos</h3>
                <div class="cs-iconbox_subtitle">Configure multi-variant sizes, optional add-ons, mandatory selections, crust options, and spice level choices.</div>
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>
          </div>

          <!-- Feat 2 -->
          <div class="col-xl-4 col-md-6">
            <div class="cs-iconbox cs-style1 cs-type1">
              <div class="cs-iconbox_icon cs-center">
                <img src="/vendor/posing/img/icons/icon_box_6.svg" alt="Procurement & Vendors">
              </div>
              <div class="cs-iconbox_in">
                <h3 class="cs-iconbox_title">Procurement &amp; Vendor ERP</h3>
                <div class="cs-iconbox_subtitle">Generate purchase orders (PO), track goods received (GRN), calculate food wastage, and automate vendor payments.</div>
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>
          </div>

          <!-- Feat 3 -->
          <div class="col-xl-4 col-md-6">
            <div class="cs-iconbox cs-style1 cs-type1">
              <div class="cs-iconbox_icon cs-center">
                <img src="/vendor/posing/img/icons/icon_box_7.svg" alt="Staff Attendance & Payroll">                          
              </div>
              <div class="cs-iconbox_in">
                <h3 class="cs-iconbox_title">Staff Attendance &amp; Payroll</h3>
                <div class="cs-iconbox_subtitle">Role-based security (Cashier, Waiter, Captain, Manager), biometric/PIN check-in, shift history, and salary slips.</div>
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>
          </div>

          <!-- Feat 4 -->
          <div class="col-xl-4 col-md-6">
            <div class="cs-iconbox cs-style1 cs-type1">
              <div class="cs-iconbox_icon cs-center">
                <img src="/vendor/posing/img/icons/icon_box_8.svg" alt="Customer CRM & Loyalty">
              </div>
              <div class="cs-iconbox_in">
                <h3 class="cs-iconbox_title">Customer CRM &amp; Loyalty</h3>
                <div class="cs-iconbox_subtitle">Maintain guest dining history, favorite items, reward loyalty points, and send automated promotional WhatsApp messages.</div>
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>
          </div>

          <!-- Feat 5 -->
          <div class="col-xl-4 col-md-6">
            <div class="cs-iconbox cs-style1 cs-type1">
              <div class="cs-iconbox_icon cs-center">
                <img src="/vendor/posing/img/icons/icon_box_9.svg" alt="GST & Tax Invoicing"> 
              </div>
              <div class="cs-iconbox_in">
                <h3 class="cs-iconbox_title">GST Tax Ready (India)</h3>
                <div class="cs-iconbox_subtitle">Compliant with Indian GST laws: CGST, SGST, IGST calculations, SAC codes, B2B tax invoices, and one-click GSTR reports.</div>
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>
          </div>

          <!-- Feat 6 -->
          <div class="col-xl-4 col-md-6">
            <div class="cs-iconbox cs-style1 cs-type1">
              <div class="cs-iconbox_icon cs-center">
                <img src="/vendor/posing/img/icons/icon_box_10.svg" alt="Multi-Branch Central Cloud"> 
              </div>
              <div class="cs-iconbox_in">
                <h3 class="cs-iconbox_title">Multi-Branch Chain Sync</h3>
                <div class="cs-iconbox_subtitle">Control 1 to 100+ restaurant outlets from a single master dashboard. Synchronize centralized menus and view consolidated revenues.</div>
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>
          </div>
        </div>
      </div>
      <div class="cs-height_75 cs-height_lg_45"></div>
    </div>
  </section>
  <!-- End All Features Grid -->

  <!-- Start Silent Thermal Printing Hardware Section -->
  <section id="hardware" class="cs-gradient_bg_1">
    <div class="cs-height_95 cs-height_lg_70"></div>
    <div class="container">
      <div class="cs-seciton_heading cs-style1 text-center">
        <div class="cs-section_subtitle wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">Hardware &amp; Printing</div>
        <div class="cs-height_10 cs-height_lg_10"></div>
        <h3 class="cs-section_title">Direct Silent ESC/POS Thermal Printing</h3>
        <p class="cs-section_desc text-muted mx-auto" style="max-width: 650px;">
          Say goodbye to annoying Windows browser print dialogs. DineFlow AI prints directly to kitchen &amp; billing printers in milliseconds.
        </p>
      </div>
      <div class="cs-height_50 cs-height_lg_40"></div>

      <div class="row align-items-center">
        <div class="col-lg-5 mb-4 mb-lg-0 wow fadeInLeft" data-wow-duration="1s" data-wow-delay="0.3s">
          <div style="background: #ffffff; border-radius: 20px; padding: 24px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08); border: 1px solid #E2E8F0;">
            <img src="/images/pos_thermal_printer.jpg" alt="Thermal Receipt Printer" style="border-radius: 12px; width: 100%; height: auto;">
            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
              <span class="badge bg-success px-3 py-2" style="font-size: 13px;"><i class="fa-solid fa-check me-1"></i> Tested &amp; Certified</span>
              <span class="text-muted fw-bold" style="font-size: 13px;">58mm &amp; 80mm ESC/POS</span>
            </div>
          </div>
        </div>

        <div class="col-lg-7 wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.4s">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="df-printer-card">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(99, 102, 241, 0.1); color: #6366F1; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-usb"></i>
                  </div>
                  <h4 class="m-0 fs-6 fw-bold">USB &amp; RawBT Silent Cut</h4>
                </div>
                <p class="text-muted small m-0">Zero browser prompts. Single-click print fires immediate cut command to high-speed thermal heads.</p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="df-printer-card">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(82, 200, 196, 0.1); color: #0D9488; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-network-wired"></i>
                  </div>
                  <h4 class="m-0 fs-6 fw-bold">LAN &amp; Wi-Fi Kitchen Routing</h4>
                </div>
                <p class="text-muted small m-0">Route bar items to Bar Printer (Port 9100) and food orders to Kitchen Printer automatically.</p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="df-printer-card">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(236, 72, 153, 0.1); color: #EC4899; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-brands fa-bluetooth-b"></i>
                  </div>
                  <h4 class="m-0 fs-6 fw-bold">Bluetooth Mobile Printing</h4>
                </div>
                <p class="text-muted small m-0">Captains and delivery staff can print receipts on-the-go using compact handheld Bluetooth printers.</p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="df-printer-card">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245, 158, 11, 0.1); color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-cash-register"></i>
                  </div>
                  <h4 class="m-0 fs-6 fw-bold">RJ11 Cash Drawer Kick</h4>
                </div>
                <p class="text-muted small m-0">Sends electronic 24V pulse to automatically pop open the physical cash drawer upon bill settlement.</p>
              </div>
            </div>
          </div>
          
          <div class="mt-4 p-3 rounded-3" style="background: #ffffff; border-left: 4px solid #6366F1;">
            <small class="text-dark fw-bold">Compatible Printer Brands:</small>
            <div class="d-flex flex-wrap gap-3 mt-1 text-muted" style="font-size: 13px;">
              <span>• EPSON TM-T82 / T88</span>
              <span>• TVS RP-3160 / 3200</span>
              <span>• NGX Technologies</span>
              <span>• Posiflex</span>
              <span>• Sunmi T2 / V2</span>
              <span>• Citizen &amp; Xprinter</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="cs-height_95 cs-height_lg_70"></div>
  </section>
  <!-- End Silent Thermal Printing Hardware Section -->

  <!-- Start Restaurant Formats Section -->
  <section class="cs-bg" data-src="/vendor/posing/img/feature_bg.svg" id="formats">
    <div class="cs-height_95 cs-height_lg_70"></div>
    <div class="container">
      <div class="cs-seciton_heading cs-style1 text-center">
        <div class="cs-section_subtitle wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">Universal Architecture</div>
        <div class="cs-height_10 cs-height_lg_10"></div>
        <h3 class="cs-section_title">Tailored For Every Food &amp; Beverage Model</h3>
      </div>
      <div class="cs-height_50 cs-height_lg_40"></div>
      
      <div class="row align-items-center flex-column-reverse-lg">
        <div class="col-xl-6">
          <div class="cs-left_full_width cs-space40">
            <div class="cs-left_sided_img">
              <img src="/vendor/posing/img/retail-store.png" alt="DineFlow AI Restaurant Types">
            </div>
          </div>
        </div>
        
        <div class="col-xl-6">
          <div class="cs-height_25 cs-height_lg_40"></div>
          <!-- Row 1 -->
          <div class="row wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s">
            <div class="col-lg-11 offset-lg-1">
              <div class="row">
                <div class="col-md-6">
                  <div class="cs-iconbox cs-style2 text-center">
                    <div class="cs-iconbox_icon"><img src="/vendor/posing/img/icons/retail_icon_1.svg" alt="Fine Dine"></div>
                    <h2 class="cs-iconbox_title">Fine Dine &amp; Casual</h2>
                    <small class="text-muted d-block mt-1">Table reservations, captain tablets &amp; multi-course KOTs.</small>
                  </div>
                  <div class="cs-height_25 cs-height_lg_25"></div>
                </div>
                <div class="col-md-6">
                  <div class="cs-iconbox cs-style2 text-center">
                    <div class="cs-iconbox_icon"><img src="/vendor/posing/img/icons/retail_icon_2.svg" alt="QSR"></div>
                    <h2 class="cs-iconbox_title">QSR &amp; Fast Food</h2>
                    <small class="text-muted d-block mt-1">High-velocity counter billing, token screens &amp; combos.</small>
                  </div>
                  <div class="cs-height_25 cs-height_lg_25"></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Row 2 -->
          <div class="row wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.4s">
            <div class="col-lg-11">
              <div class="row">
                <div class="col-md-6">
                  <div class="cs-iconbox cs-style2 text-center">
                    <div class="cs-iconbox_icon"><img src="/vendor/posing/img/icons/retail_icon_6.svg" alt="Cafe & Bakery"></div>
                    <h2 class="cs-iconbox_title">Cafes &amp; Bakeries</h2>
                    <small class="text-muted d-block mt-1">Milk/syrup barista modifiers, advance cake pre-orders.</small>
                  </div>
                  <div class="cs-height_25 cs-height_lg_25"></div>
                </div>
                <div class="col-md-6">
                  <div class="cs-iconbox cs-style2 text-center">
                    <div class="cs-iconbox_icon"><img src="/vendor/posing/img/icons/retail_icon_3.svg" alt="Cloud Kitchen"></div>
                    <h2 class="cs-iconbox_title">Cloud Kitchens</h2>
                    <small class="text-muted d-block mt-1">Single kitchen cooking for multiple virtual food brands.</small>
                  </div>
                  <div class="cs-height_25 cs-height_lg_25"></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Row 3 -->
          <div class="row wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.5s">
            <div class="col-lg-11 offset-lg-1">
              <div class="row">
                <div class="col-md-6">
                  <div class="cs-iconbox cs-style2 text-center">
                    <div class="cs-iconbox_icon"><img src="/vendor/posing/img/icons/retail_icon_4.svg" alt="Bars & Pubs"></div>
                    <h2 class="cs-iconbox_title">Bars &amp; Breweries</h2>
                    <small class="text-muted d-block mt-1">Running guest bar tabs, liquor peg stock &amp; happy hours.</small>
                  </div>
                  <div class="cs-height_25 cs-height_lg_25"></div>
                </div>
                <div class="col-md-6">
                  <div class="cs-iconbox cs-style2 text-center">
                    <div class="cs-iconbox_icon"><img src="/vendor/posing/img/icons/retail_icon_5.svg" alt="Food Trucks"></div>
                    <h2 class="cs-iconbox_title">Food Trucks &amp; Kiosks</h2>
                    <small class="text-muted d-block mt-1">Runs 100% on battery tablets and mobile thermal printers.</small>
                  </div>
                  <div class="cs-height_25 cs-height_lg_25"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="cs-height_75 cs-height_lg_70"></div>
  </section>
  <!-- End Restaurant Formats Section -->

  <!-- Start Price Section -->
  <section class="cs-gradient_bg_1" id="pricing">
    <div class="cs-height_95 cs-height_lg_70"></div>
    <div class="container">
      <div class="cs-seciton_heading cs-style1 text-center">
        <div class="cs-section_subtitle wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s">Transparent Investment</div>
        <div class="cs-height_10 cs-height_lg_10"></div>
        <h3 class="cs-section_title">Simple Plans That Grow With Your Business</h3>
      </div>
      <div class="cs-height_50 cs-height_lg_40"></div>
      
      <div class="row align-items-stretch">
        <!-- Starter Plan -->
        <div class="col-lg-4 mb-4 mb-lg-0">
          <div class="cs-pricing_table cs-style1 h-100 d-flex flex-column justify-content-between">
            <div class="cs-pricing_head">
              <div class="cs-pricing_heading">
                <div class="cs-pricing_icon cs-center"><img src="/vendor/posing/img/icons/pricing_icon_1.svg" alt="Starter Plan"></div>
                <h2 class="cs-pricing_title cs-m0">Starter Plan</h2>
              </div>
              <div class="cs-price cs-primary_font">
                <b class="cs-accent_color">₹999</b>
                <span>/ month</span>
              </div>
              <div class="cs-pricing_lable">Ideal For Single Outlets &amp; Cafes</div>
            </div>
            
            <div class="cs-pricing_body flex-grow-1">
              <ul class="cs-mp0 cs-pricing_feature">
                <li><i class="fa-solid fa-check text-success me-2"></i> 1 Active POS Billing Counter</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Unlimited Orders &amp; Menu Items</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Dynamic QR Table Ordering</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Up to 15 Dining Tables</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Standard Daily Sales &amp; GST Reports</li>
                <li class="cs-inactive"><i class="fa-solid fa-xmark text-muted me-2"></i> Multi-Station KDS Screens</li>
                <li class="cs-inactive"><i class="fa-solid fa-xmark text-muted me-2"></i> Raw Material Recipe ERP</li>
              </ul>
            </div>
            
            <div class="cs-pricing_footer mt-4">
              <a href="{{ route('register') }}?plan=starter" class="cs-btn w-100 text-center" style="text-decoration: none;">
                <span>Start 14-Day Free Trial</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Restaurant Pro Suite (FEATURED) -->
        <div class="col-lg-4 mb-4 mb-lg-0">
          <div class="cs-pricing_table cs-style1 featured-plan h-100 d-flex flex-column justify-content-between">
            <span class="cs-pricing_featured_badge"><i class="fa-solid fa-fire me-1"></i> Most Popular</span>
            
            <div class="cs-pricing_head">
              <div class="cs-pricing_heading">
                <div class="cs-pricing_icon cs-center"><img src="/vendor/posing/img/icons/pricing_icon_2.svg" alt="Restaurant Pro"></div>
                <h2 class="cs-pricing_title cs-m0">Restaurant Pro</h2>
              </div>
              <div class="cs-price cs-primary_font">
                <b style="color: #6366F1;">₹2,499</b>
                <span>/ month</span>
              </div>
              <div class="cs-pricing_lable" style="background: rgba(99, 102, 241, 0.1); color: #4F46E5;">Full Kitchen &amp; ERP Automation</div>
            </div>
            
            <div class="cs-pricing_body flex-grow-1">
              <ul class="cs-mp0 cs-pricing_feature">
                <li><i class="fa-solid fa-check text-success me-2"></i> <strong>3 POS Billing Counters</strong></li>
                <li><i class="fa-solid fa-check text-success me-2"></i> <strong>Real-time Kitchen Display (KDS)</strong></li>
                <li><i class="fa-solid fa-check text-success me-2"></i> <strong>Silent ESC/POS Thermal Printing</strong></li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Unlimited Tables &amp; Floor Map Editor</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Recipe Costing &amp; Auto-Depletion ERP</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Shift Controls &amp; Petty Cash Management</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Staff Attendance, Biometrics &amp; Payroll</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> WhatsApp Bill &amp; Loyalty Automation</li>
              </ul>
            </div>
            
            <div class="cs-pricing_footer mt-4">
              <a href="{{ route('register') }}?plan=pro" class="cs-btn w-100 text-center" style="background-color: #6366F1; text-decoration: none;">
                <span>Start Free Trial</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Enterprise / Full Source Code -->
        <div class="col-lg-4">
          <div class="cs-pricing_table cs-style1 h-100 d-flex flex-column justify-content-between">
            <div class="cs-pricing_head">
              <div class="cs-pricing_heading">
                <div class="cs-pricing_icon cs-center"><img src="/vendor/posing/img/icons/pricing_icon_3.svg" alt="Source Code License"></div>
                <h2 class="cs-pricing_title cs-m0">Lifetime License</h2>
              </div>
              <div class="cs-price cs-primary_font">
                <b class="cs-accent_color">₹49,999</b>
                <span>/ One-Time</span>
              </div>
              <div class="cs-pricing_lable">100% Commercial Source Code Ownership</div>
            </div>
            
            <div class="cs-pricing_body flex-grow-1">
              <ul class="cs-mp0 cs-pricing_feature">
                <li><i class="fa-solid fa-check text-success me-2"></i> <strong>100% Unencrypted Laravel 11 Code</strong></li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Self-Host On Your Own AWS / DigitalOcean</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> <strong>Zero Monthly Subscription Fees Forever</strong></li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Unlimited Restaurant Outlets &amp; Brands</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Full Database Schemas &amp; Migrations</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> White-Labeling &amp; Custom Rebranding Rights</li>
                <li><i class="fa-solid fa-check text-success me-2"></i> Direct Deployment Assistance by Shubh Patel</li>
              </ul>
            </div>
            
            <div class="cs-pricing_footer mt-4">
              <a href="/buy-source-code" class="cs-btn cs-color1 w-100 text-center" style="text-decoration: none;">
                <span>Acquire Full Source Code</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="cs-height_95 cs-height_lg_70"></div>
  </section>
  <!-- End Price Section -->

  <!-- Start Testimonials Section -->
  <section class="cs-bg" data-src="/vendor/posing/img/feature_bg.svg">
    <div class="cs-height_95 cs-height_lg_70"></div>
    <div class="container">
      <div class="cs-seciton_heading cs-style1 text-center">
        <div class="cs-section_subtitle wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s">Client Reviews</div>
        <div class="cs-height_10 cs-height_lg_10"></div>
        <h3 class="cs-section_title">Loved by Restaurant Owners Across India</h3>
      </div>
      <div class="cs-height_50 cs-height_lg_40"></div>
      
      <div class="cs-slider cs-style1 cs-gap-24">
        <div class="cs-slider_container" data-autoplay="0" data-loop="1" data-speed="600" data-fade-slide="0" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="1" data-md-slides="2" data-lg-slides="3" data-add-slides="3">
          <div class="cs-slider_wrapper">
            <!-- Review 1 -->
            <div class="cs-slide">
              <div class="cs-testimonial cs-style1">
                <div class="cs-testimonial_text">"DineFlow AI completely eliminated our weekend dinner rush chaos. QR ordering cut waiter delays by 70%, and the silent thermal printing never misses an order!"</div>
                <div class="cs-testimonial_meta">
                  <div class="cs-avatar">
                    <img src="/vendor/posing/img/avatar_1.png" alt="Chef Rajesh Patel">
                  </div>
                  <div class="cs-testimonial_meta_right">
                    <h3 class="cs-testimonial_title">Rajesh Patel</h3>
                    <div class="cs-testimonial_subtitle">Founder, Spice Symphony Fine Dine (Ahmedabad)</div>
                    <div class="cs-review" data-review="5">
                      <div class="cs-review_in"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Review 2 -->
            <div class="cs-slide">
              <div class="cs-testimonial cs-style1">
                <div class="cs-testimonial_text">"We run 3 cloud kitchen brands under one roof. The station-wise KDS and automatic ingredient depletion saved us over ₹45,000 every month in food wastage."</div>
                <div class="cs-testimonial_meta">
                  <div class="cs-avatar">
                    <img src="/vendor/posing/img/avatar_2.png" alt="Ananya Sharma">
                  </div>
                  <div class="cs-testimonial_meta_right">
                    <h3 class="cs-testimonial_title">Ananya Sharma</h3>
                    <div class="cs-testimonial_subtitle">Operations Head, CraveBox Cloud Kitchen (Mumbai)</div>
                    <div class="cs-review" data-review="5">
                      <div class="cs-review_in"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Review 3 -->
            <div class="cs-slide">
              <div class="cs-testimonial cs-style1">
                <div class="cs-testimonial_text">"We purchased the full Laravel source code and self-hosted on AWS. The codebase is remarkably clean, modern, and perfectly documented. Best investment for our brand."</div>
                <div class="cs-testimonial_meta">
                  <div class="cs-avatar">
                    <img src="/vendor/posing/img/avatar_3.png" alt="Vikram Malhotra">
                  </div>
                  <div class="cs-testimonial_meta_right">
                    <h3 class="cs-testimonial_title">Vikram Malhotra</h3>
                    <div class="cs-testimonial_subtitle">Director, Urban Cafe Chain (Bangalore)</div>
                    <div class="cs-review" data-review="5">
                      <div class="cs-review_in"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="cs-height_95 cs-height_lg_70"></div>
  </section>
  <!-- End Testimonials Section -->

  <!-- Start FAQ Section -->
  <section id="faq" class="cs-gradient_bg_1">
    <div class="cs-height_95 cs-height_lg_70"></div>
    <div class="cs-seciton_heading cs-style1 text-center">
      <div class="cs-section_subtitle wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s">Common Questions</div>
      <div class="cs-height_10 cs-height_lg_10"></div>
      <h3 class="cs-section_title">Frequently Asked Questions</h3>
    </div>
    <div class="cs-height_50 cs-height_lg_40"></div>
    <div class="container">
      <div class="row align-items-center flex-column-reverse-lg">
        <div class="col-xl-6 wow fadeInLeft" data-wow-duration="1s" data-wow-delay="0.4s">
          <div class="cs-left_full_width cs-space110">
            <div class="cs-left_sided_img">
              <img src="/vendor/posing/img/faq_img.png" alt="DineFlow AI FAQ">
            </div>
          </div>
          <div class="cs-height_0 cs-height_lg_40"></div>
        </div>
        <div class="col-xl-6">
          <div class="cs-accordians cs-style1 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.4s">
            <!-- Q1 -->
            <div class="cs-accordian cs-white_bg active">
              <div class="cs-accordian_head">
                <h2 class="cs-accordian_title"><span>Q1.</span> Do I need to purchase expensive proprietary POS hardware?</h2>
                <span class="cs-accordian_toggle">
                  <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C6.71573 0 0 6.71573 0 15C0 23.2843 6.71573 30 15 30C23.2843 30 30 23.2843 30 15C30 6.71573 23.2843 0 15 0ZM15 20L7.875 14.5587C7.46452 13.6689 8.23667 12.4769 9.21659 12.4876L15 17.5L20.7834 12.4876C21.7633 12.4769 22.5355 13.6689 22.125 14.5587L15 20Z" fill="currentColor"/>
                  </svg>                 
                </span>
              </div>
              <div class="cs-accordian-body">
                No! DineFlow AI operates seamlessly on any existing hardware — Windows PCs, MacBooks, iPads, Android tablets, touch monitors, or smartphones. You can plug in standard 58mm or 80mm ESC/POS USB or LAN thermal printers.
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>

            <!-- Q2 -->
            <div class="cs-accordian cs-white_bg">
              <div class="cs-accordian_head">
                <h2 class="cs-accordian_title"><span>Q2.</span> How does silent thermal receipt printing work without popups?</h2>
                <span class="cs-accordian_toggle">
                  <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C6.71573 0 0 6.71573 0 15C0 23.2843 6.71573 30 15 30C23.2843 30 30 23.2843 30 15C30 6.71573 23.2843 0 15 0ZM15 20L7.875 14.5587C7.46452 13.6689 8.23667 12.4769 9.21659 12.4876L15 17.5L20.7834 12.4876C21.7633 12.4769 22.5355 13.6689 22.125 14.5587L15 20Z" fill="currentColor"/>
                  </svg>                 
                </span>
              </div>
              <div class="cs-accordian-body">
                DineFlow AI connects directly to ESC/POS print streams via WebUSB, local network raw sockets (Port 9100), or Android RawBT drivers. It transmits raw hex bytes directly to your printer, triggering instant cutting and cash drawer ejection with zero browser dialog delays.
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>

            <!-- Q3 -->
            <div class="cs-accordian cs-white_bg">
              <div class="cs-accordian_head">
                <h2 class="cs-accordian_title"><span>Q3.</span> Do customers have to download an app to order via QR?</h2>
                <span class="cs-accordian_toggle">
                  <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C6.71573 0 0 6.71573 0 15C0 23.2843 6.71573 30 15 30C23.2843 30 30 23.2843 30 15C30 6.71573 23.2843 0 15 0ZM15 20L7.875 14.5587C7.46452 13.6689 8.23667 12.4769 9.21659 12.4876L15 17.5L20.7834 12.4876C21.7633 12.4769 22.5355 13.6689 22.125 14.5587L15 20Z" fill="currentColor"/>
                  </svg>                 
                </span>
              </div>
              <div class="cs-accordian-body">
                Zero app downloads or logins! Customers open their native phone camera, scan the QR code placed on their dining table, and a rich, interactive menu loads instantaneously in Safari, Chrome, or any mobile browser.
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>

            <!-- Q4 -->
            <div class="cs-accordian cs-white_bg">
              <div class="cs-accordian_head">
                <h2 class="cs-accordian_title"><span>Q4.</span> Is DineFlow AI compliant with Indian GST laws?</h2>
                <span class="cs-accordian_toggle">
                  <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C6.71573 0 0 6.71573 0 15C0 23.2843 6.71573 30 15 30C23.2843 30 30 23.2843 30 15C30 6.71573 23.2843 0 15 0ZM15 20L7.875 14.5587C7.46452 13.6689 8.23667 12.4769 9.21659 12.4876L15 17.5L20.7834 12.4876C21.7633 12.4769 22.5355 13.6689 22.125 14.5587L15 20Z" fill="currentColor"/>
                  </svg>                 
                </span>
              </div>
              <div class="cs-accordian-body">
                100% GST ready. Configure CGST, SGST, IGST rates (5%, 12%, 18%), service charges, round-offs, restaurant GSTIN, SAC codes, and generate one-click Excel exports for GSTR-1 and GSTR-3B filings.
              </div>
            </div>
            <div class="cs-height_25 cs-height_lg_25"></div>

            <!-- Q5 -->
            <div class="cs-accordian cs-white_bg">
              <div class="cs-accordian_head">
                <h2 class="cs-accordian_title"><span>Q5.</span> What is included with the Lifetime Source Code license?</h2>
                <span class="cs-accordian_toggle">
                  <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C6.71573 0 0 6.71573 0 15C0 23.2843 6.71573 30 15 30C23.2843 30 30 23.2843 30 15C30 6.71573 23.2843 0 15 0ZM15 20L7.875 14.5587C7.46452 13.6689 8.23667 12.4769 9.21659 12.4876L15 17.5L20.7834 12.4876C21.7633 12.4769 22.5355 13.6689 22.125 14.5587L15 20Z" fill="currentColor"/>
                  </svg>                 
                </span>
              </div>
              <div class="cs-accordian-body">
                You receive the complete, unencrypted, production-ready Laravel 11 + Blade + Alpine.js codebase, MySQL database schemas, and documentation. You can host on your own server, rebrand with your logo, and never pay monthly subscription fees or aggregator commissions again.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="cs-height_95 cs-height_lg_70"></div>
  </section>
  <!-- End FAQ Section -->

  <!-- Start Contact Section -->
  <section class="cs-bg" data-src="/vendor/posing/img/feature_bg.svg" id="contact">
    <div class="cs-height_95 cs-height_lg_70"></div>
    <div class="container">
      <div class="row">
        <!-- Contact Info Left -->
        <div class="col-xl-5 col-lg-8">
          <div class="cs-seciton_heading cs-style1">
            <div class="cs-section_subtitle wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s">Direct Contact</div>
            <div class="cs-height_10 cs-height_lg_10"></div>
            <h3 class="cs-section_title">Book a 1-on-1 Personalized Live Demo</h3>
          </div>
          <div class="cs-height_20 cs-height_lg_20"></div>
          <p>
            Experience how DineFlow AI transforms dining room efficiency, kitchen throughput, and inventory control. Our restaurant solutions team will answer all your questions.
          </p>
          <div class="cs-height_15 cs-height_lg_15"></div>
          
          <!-- Address -->
          <div class="cs-iconbox cs-style3">
            <div class="cs-iconbox_icon">
              <img src="/vendor/posing/img/icons/contact_icon_1.svg" alt="Location">
            </div>
            <div class="cs-iconbox_right">
              <h2 class="cs-iconbox_title">Headquarters</h2>
              <div class="cs-iconbox_subtitle">Gujarat &amp; Bangalore, India</div>
            </div>
          </div>
          <div class="cs-height_30 cs-height_lg_30"></div>

          <!-- Phone -->
          <div class="cs-iconbox cs-style3">
            <div class="cs-iconbox_icon">
              <img src="/vendor/posing/img/icons/contact_icon_2.svg" alt="Phone">
            </div>
            <div class="cs-iconbox_right">
              <h2 class="cs-iconbox_title">Direct Phone / WhatsApp</h2>
              <div class="cs-iconbox_subtitle">
                <a href="https://wa.me/917777938338?text=Hello%20Shubh%2C%20I%20want%20a%20DineFlow%20AI%20Demo" target="_blank" style="color: #6366F1; text-decoration: none; font-weight: 700;">
                  +91 77779 38338
                </a>
              </div>
            </div>
          </div>
          <div class="cs-height_30 cs-height_lg_30"></div>

          <!-- Email -->
          <div class="cs-iconbox cs-style3">
            <div class="cs-iconbox_icon">
              <img src="/vendor/posing/img/icons/contact_icon_3.svg" alt="Email">
            </div>
            <div class="cs-iconbox_right">
              <h2 class="cs-iconbox_title">Official Support Email</h2>
              <div class="cs-iconbox_subtitle">support@dineflowai.com</div>
            </div>
          </div>
        </div>

        <!-- Contact Form Right -->
        <div class="col-xl-6 offset-xl-1">
          <div class="cs-height_40 cs-height_lg_40"></div>
          
          <form action="{{ route('contact.submit') }}" method="POST" class="cs-contact_form" style="background: #ffffff; padding: 40px; border-radius: 20px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08); border: 1px solid #E2E8F0;">
            @csrf
            <!-- Anti-bot Honeypot -->
            <input type="text" name="website_hp" style="display:none;" tabindex="-1" autocomplete="off">
            <input type="hidden" name="source" value="landing_page_lead">

            <h2 class="cs-contact_form_title mb-2" style="font-size: 24px; font-weight: 800; color: #0F172A;">Request Demo &amp; Pricing</h2>
            <p class="text-muted small mb-4">Fill in your restaurant details and we will reach out within 30 minutes.</p>

            @if(session('success'))
              <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
              </div>
            @endif

            @if(isset($errors) && $errors->any())
              <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                <div>{{ $errors->first() }}</div>
              </div>
            @endif

            <div class="row">
              <div class="col-lg-6 mb-3">
                <label class="form-label small fw-bold text-dark">Your Name *</label>
                <input type="text" name="name" class="cs-form_field" placeholder="e.g. Shubh Patel" value="{{ old('name') }}" required>
              </div>
              <div class="col-lg-6 mb-3">
                <label class="form-label small fw-bold text-dark">Restaurant Name *</label>
                <input type="text" name="restaurant_name" class="cs-form_field" placeholder="e.g. Royal Dine &amp; Cafe" value="{{ old('restaurant_name') }}" required>
              </div>
              <div class="col-lg-6 mb-3">
                <label class="form-label small fw-bold text-dark">Phone / WhatsApp Number *</label>
                <input type="text" name="phone" class="cs-form_field" placeholder="e.g. +91 98765 43210" value="{{ old('phone') }}" required>
              </div>
              <div class="col-lg-6 mb-3">
                <label class="form-label small fw-bold text-dark">Email Address</label>
                <input type="email" name="email" class="cs-form_field" placeholder="e.g. contact@restaurant.com" value="{{ old('email') }}">
              </div>
              <div class="col-lg-12 mb-3">
                <label class="form-label small fw-bold text-dark">City &amp; Outlets</label>
                <input type="text" name="city" class="cs-form_field" placeholder="e.g. Ahmedabad (2 Outlets)" value="{{ old('city') }}">
              </div>
              <div class="col-lg-12 mb-4">
                <label class="form-label small fw-bold text-dark">Tell Us About Your Requirements</label>
                <textarea name="message" cols="30" rows="3" class="cs-form_field" placeholder="Mention any specific needs: QR Ordering, KDS, Thermal Printers, Multi-branch, etc.">{{ old('message') }}</textarea>
              </div>
              <div class="col-lg-12">
                <button type="submit" class="cs-btn cs-size_md w-100" style="background-color: #6366F1;">
                  <span>Book Free Live Demo</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="cs-height_95 cs-height_lg_70"></div>
  </section>
  <!-- End Contact Section -->

  <!-- Start Footer -->
  <footer class="cs-footer" style="background-color: #0F172A; color: #94A3B8;">
    <div class="cs-height_75 cs-height_lg_70"></div>
    <div class="container">
      <div class="row">
        <!-- Brand Info -->
        <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
          <div class="cs-footer_item">
            <div class="cs-footer_widget_text">
              <a href="/">
                <img src="/images/dineflow_logo.png" alt="DineFlow AI" style="max-height: 48px; width: auto; filter: brightness(0) invert(1);">
              </a>
              <p class="mt-3" style="color: #94A3B8; font-size: 14px; line-height: 1.7;">
                Next-generation restaurant cloud POS, contactless QR ordering, real-time KDS, and raw material recipe costing ERP. Engineered for reliability, speed, and profitability.
              </p>
            </div>
            <div class="cs-height_20 cs-height_lg_20"></div>
            <div class="d-flex align-items-center gap-3">
              <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20AI" target="_blank" class="df-badge df-badge-whatsapp text-decoration-none">
                <i class="fa-brands fa-whatsapp fs-5"></i> Chat on WhatsApp
              </a>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
          <div class="cs-footer_item widget_nav_menu">
            <h2 class="cs-widget_title text-white" style="font-size: 17px; font-weight: 700;">Platform</h2>
            <ul class="menu list-unstyled p-0 m-0" style="font-size: 14px; line-height: 2.2;">
              <li><a href="#features" class="text-decoration-none" style="color: #94A3B8;">Core Features</a></li>
              <li><a href="#operations" class="text-decoration-none" style="color: #94A3B8;">Kitchen Operations</a></li>
              <li><a href="#hardware" class="text-decoration-none" style="color: #94A3B8;">Thermal Printing</a></li>
              <li><a href="/demo" class="text-decoration-none" style="color: #94A3B8;">Interactive Sandbox</a></li>
              <li><a href="/buy-source-code" class="text-decoration-none" style="color: #F472B6; font-weight: 700;">Buy Source Code</a></li>
            </ul>
          </div>
        </div>

        <!-- Solutions -->
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
          <div class="cs-footer_item widget_nav_menu">
            <h2 class="cs-widget_title text-white" style="font-size: 17px; font-weight: 700;">Restaurant Models</h2>
            <ul class="menu list-unstyled p-0 m-0" style="font-size: 14px; line-height: 2.2;">
              <li><a href="#formats" class="text-decoration-none" style="color: #94A3B8;">Fine Dine &amp; Casual</a></li>
              <li><a href="#formats" class="text-decoration-none" style="color: #94A3B8;">QSR &amp; Fast Food Counters</a></li>
              <li><a href="#formats" class="text-decoration-none" style="color: #94A3B8;">Cafes &amp; Bakeries</a></li>
              <li><a href="#formats" class="text-decoration-none" style="color: #94A3B8;">Cloud Kitchens &amp; Multi-Brands</a></li>
              <li><a href="#formats" class="text-decoration-none" style="color: #94A3B8;">Bars, Pubs &amp; Breweries</a></li>
            </ul>
          </div>
        </div>

        <!-- Account & Legal -->
        <div class="col-lg-3 col-md-6">
          <div class="cs-footer_item widget_nav_menu">
            <h2 class="cs-widget_title text-white" style="font-size: 17px; font-weight: 700;">Account &amp; Legal</h2>
            <ul class="menu list-unstyled p-0 m-0" style="font-size: 14px; line-height: 2.2;">
              <li><a href="{{ route('login') }}" class="text-decoration-none" style="color: #94A3B8;">Restaurant Login</a></li>
              <li><a href="{{ route('register') }}" class="text-decoration-none" style="color: #94A3B8;">Start Free Trial</a></li>
              <li><a href="{{ route('auth.pin-login') }}" class="text-decoration-none" style="color: #94A3B8;">Staff Quick PIN Login</a></li>
              <li><a href="/terms" class="text-decoration-none" style="color: #94A3B8;">Terms of Service</a></li>
              <li><a href="/privacy" class="text-decoration-none" style="color: #94A3B8;">Privacy Policy</a></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Copyright Sub-Footer -->
      <div class="pt-4 mt-5 border-top d-flex justify-content-between align-items-center flex-wrap" style="border-color: rgba(255,255,255,0.1) !important; font-size: 13px;">
        <div style="color: #64748B;">
          &copy; {{ date('Y') }} <strong>DineFlow AI</strong>. Developed &amp; Owned by <strong>Shubh Patel</strong>. All Rights Reserved.
        </div>
        <div style="color: #64748B;">
          Engineered for Ultra-High Performance Restaurant Cloud Operations
        </div>
      </div>
    </div>
    <div class="cs-height_50 cs-height_lg_40"></div>
  </footer>
  <!-- End Footer -->

  <!-- JavaScript Plugins -->
  <script src="/vendor/posing/js/plugins/jquery-3.6.0.min.js"></script>
  <script src="/vendor/posing/js/plugins/jquery.slick.min.js"></script>
  <script src="/vendor/posing/js/plugins/jquery.counter.min.js"></script>
  <script src="/vendor/posing/js/plugins/wow.min.js"></script>
  <script src="/vendor/posing/js/main.js"></script>
</body>
</html>
