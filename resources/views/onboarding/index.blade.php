<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Onboarding Wizard — DineFlow AI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary: #6366F1;
            --primary-glow: rgba(99, 102, 241, 0.3);
            --primary-dark: #4338CA;
            --success: #10B981;
            --bg: #090D1A;
            --card: #131A35;
            --border: rgba(255, 255, 255, 0.08);
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .topbar {
            height: 68px;
            background: rgba(19, 26, 53, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366F1, #818CF8);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #fff;
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
        }

        .restaurant-badge {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #A5B4FC;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Main Container */
        .wizard-container {
            max-width: 860px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        /* Progress Steps */
        .steps-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 36px;
            position: relative;
        }

        .steps-nav::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 30px;
            right: 30px;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 0;
        }

        .step-indicator {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1E293B;
            border: 2px solid rgba(255, 255, 255, 0.15);
            color: #94A3B8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .step-indicator.active .step-circle {
            background: var(--primary);
            border-color: #818CF8;
            color: #fff;
            box-shadow: 0 0 16px var(--primary-glow);
        }

        .step-indicator.completed .step-circle {
            background: #10B981;
            border-color: #34D399;
            color: #fff;
        }

        .step-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
            text-align: center;
            white-space: nowrap;
        }

        .step-indicator.active .step-label {
            color: #fff;
        }

        /* Wizard Card */
        .wizard-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .step-pane {
            display: none;
        }

        .step-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pane-header {
            margin-bottom: 28px;
        }

        .pane-title {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .pane-desc {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #CBD5E1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Buttons footer */
        .wizard-footer {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-secondary {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: #CBD5E1;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-primary {
            padding: 12px 28px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 12px;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 16px var(--primary-glow);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.45);
        }

        .sample-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .sample-card {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .usage-stat-box {
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.25);
            border-radius: 14px;
            padding: 20px;
            margin: 20px 0;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            text-align: center;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
        }

        .stat-label {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
            text-transform: uppercase;
        }

        @media (max-width: 768px) {
            .steps-nav { display: none; }
            .form-row { grid-template-columns: 1fr; }
            .usage-stat-box { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="topbar">
        <a href="/" class="brand-logo">
            <div class="brand-icon"><i class="fa-solid fa-fire-burner"></i></div>
            <div class="brand-name">DineFlow AI</div>
        </a>

        <div class="restaurant-badge">
            <i class="fa-solid fa-store"></i>
            <span>{{ $restaurant->name }} (Free Plan)</span>
        </div>
    </header>

    <!-- Main Wizard -->
    <main class="wizard-container">
        <!-- Progress Bar -->
        <div style="margin-bottom: 24px; background: rgba(15,23,42,0.6); padding: 14px 20px; border-radius: 16px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                    <span style="font-weight: 700; color: #A5B4FC;" id="progressStepTitle">Step 1 of 8: Restaurant Profile</span>
                    <span style="font-weight: 800; color: #34D399;" id="progressPercentage">12.5% Completed</span>
                </div>
                <div style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 6px; overflow: hidden;">
                    <div id="onboardingProgressBar" style="height: 100%; width: 12.5%; background: linear-gradient(90deg, #6366F1, #34D399); transition: width 0.3s ease;"></div>
                </div>
            </div>
        </div>

        <!-- 8-Step Navigation -->
        <div class="steps-nav" id="stepsNav">
            <div class="step-indicator active" onclick="jumpToStep(1)">
                <div class="step-circle">1</div>
                <div class="step-label">Profile</div>
            </div>
            <div class="step-indicator" onclick="jumpToStep(2)">
                <div class="step-circle">2</div>
                <div class="step-label">Branding</div>
            </div>
            <div class="step-indicator" onclick="jumpToStep(3)">
                <div class="step-circle">3</div>
                <div class="step-label">Tax & GST</div>
            </div>
            <div class="step-indicator" onclick="jumpToStep(4)">
                <div class="step-circle">4</div>
                <div class="step-label">Tables</div>
            </div>
            <div class="step-indicator" onclick="jumpToStep(5)">
                <div class="step-circle">5</div>
                <div class="step-label">Menu</div>
            </div>
            <div class="step-indicator" onclick="jumpToStep(6)">
                <div class="step-circle">6</div>
                <div class="step-label">Staff & PINs</div>
            </div>
            <div class="step-indicator" onclick="jumpToStep(7)">
                <div class="step-circle">7</div>
                <div class="step-label">QR Codes</div>
            </div>
            <div class="step-indicator" onclick="jumpToStep(8)">
                <div class="step-circle">8</div>
                <div class="step-label">Finish</div>
            </div>
        </div>

        <div class="wizard-card">
            <!-- STEP 1: Restaurant Profile -->
            <div class="step-pane active" id="step1">
                <div class="pane-header">
                    <h2 class="pane-title">Step 1: Restaurant Profile</h2>
                    <p class="pane-desc">Confirm your restaurant's legal name, phone number, and physical dining address.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Restaurant Legal Name</label>
                    <input type="text" id="ob_name" class="form-input" value="{{ $restaurant->name }}">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Business Phone</label>
                        <input type="tel" id="ob_phone" class="form-input" value="{{ $restaurant->phone }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Primary Currency</label>
                        <input type="text" class="form-input" value="INR (₹)" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Physical Address</label>
                    <input type="text" id="ob_address" class="form-input" placeholder="e.g. 102 Gourmet Street, Near City Mall" value="{{ $restaurant->address === 'Please update address in settings' ? '' : $restaurant->address }}">
                </div>
            </div>

            <!-- STEP 2: Branding & Logo -->
            <div class="step-pane" id="step2">
                <div class="pane-header">
                    <h2 class="pane-title">Step 2: Logo & Branding</h2>
                    <p class="pane-desc">Add a logo image URL to display on your digital QR menus and thermal receipts.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Logo Image URL</label>
                    <input type="url" id="ob_logo" class="form-input" placeholder="https://your-domain.com/logo.png" value="{{ $restaurant->logo }}">
                    <span style="font-size:12px; color:var(--text-secondary); margin-top:6px; display:block;">Leave blank to use default DineFlow AI clean typography branding.</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Receipt Footer Note</label>
                    <input type="text" id="ob_receipt_footer" class="form-input" placeholder="e.g. Thank you for dining with us! Visit again." value="{{ $restaurant->settings['receipt_footer'] ?? 'Thank you for dining with us!' }}">
                </div>
            </div>

            <!-- STEP 3: GST & Tax -->
            <div class="step-pane" id="step3">
                <div class="pane-header">
                    <h2 class="pane-title">Step 3: GST & Tax Compliance</h2>
                    <p class="pane-desc">Set your GSTIN identification and default tax rate (standard restaurant GST is 5.0%).</p>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">GSTIN (Optional)</label>
                        <input type="text" id="ob_gstin" class="form-input" placeholder="e.g. 24AAAAA0000A1Z5" value="{{ $restaurant->gstin }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">GST Rate (%)</label>
                        <input type="number" id="ob_gst_rate" class="form-input" step="0.5" value="{{ $restaurant->gst_rate ?? 5.0 }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Service Charge (%)</label>
                    <input type="number" id="ob_service_charge" class="form-input" step="0.5" value="{{ $restaurant->service_charge ?? 5.0 }}">
                </div>
            </div>

            <!-- STEP 4: Tables -->
            <div class="step-pane" id="step4">
                <div class="pane-header">
                    <h2 class="pane-title">Step 4: Dining Tables Setup</h2>
                    <p class="pane-desc">We can automatically generate your first batch of dining tables.</p>
                </div>

                <div style="background:rgba(15,23,42,0.6); padding:20px; border-radius:14px; border:1px solid var(--border);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <div>
                            <div style="font-weight:700; font-size:15px;">Quick Generate Tables</div>
                            <div style="font-size:12px; color:var(--text-secondary);">Generates T-1 through T-6 with ready QR codes.</div>
                        </div>
                        <button type="button" onclick="generateSampleTables()" class="btn-secondary" style="color:#818CF8; border-color:#6366F1;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Generate 6 Tables
                        </button>
                    </div>
                    <div id="tableListStatus" style="font-size:13px; color:#34D399; font-weight:600;">
                        {{ $tablesCount > 0 ? "✓ {$tablesCount} tables currently active" : "No tables created yet. Click Auto-Generate above." }}
                    </div>
                </div>
            </div>

            <!-- STEP 5: Initial Menu Items -->
            <div class="step-pane" id="step5">
                <div class="pane-header">
                    <h2 class="pane-title">Step 5: Menu Items Starter Pack</h2>
                    <p class="pane-desc">Load a popular starter menu template or add your own dishes.</p>
                </div>

                <div style="background:rgba(15,23,42,0.6); padding:20px; border-radius:14px; border:1px solid var(--border);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <div>
                            <div style="font-weight:700; font-size:15px;">Load Starter Menu Items</div>
                            <div style="font-size:12px; color:var(--text-secondary);">Adds 5 top-selling dishes (Paneer Masala, Biryani, Naan, Spring Rolls, Drinks).</div>
                        </div>
                        <button type="button" onclick="loadSampleMenuItems()" class="btn-secondary" style="color:#818CF8; border-color:#6366F1;">
                            <i class="fa-solid fa-utensils"></i> Load Starter Dishes
                        </button>
                    </div>
                    <div id="menuListStatus" style="font-size:13px; color:#34D399; font-weight:600;">
                        {{ $menuItemsCount > 0 ? "✓ {$menuItemsCount} dishes currently active in your menu" : "No dishes created yet. Click Load Starter Dishes above." }}
                    </div>
                </div>
            </div>

            <!-- STEP 6: Staff Credentials -->
            <div class="step-pane" id="step6">
                <div class="pane-header">
                    <h2 class="pane-title">Step 6: Staff Credentials & Account Access</h2>
                    <p class="pane-desc">Your Administrator owner account uses your Email and Password for secure logins.</p>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div style="background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.3); border-radius:14px; padding:18px;">
                        <div style="font-size:11px; font-weight:700; color:#A5B4FC; text-transform:uppercase;">Owner Administrator</div>
                        <div style="font-size:16px; font-weight:800; color:#fff; margin:6px 0;">{{ $user->name }}</div>
                        <div style="font-size:13px; color:var(--text-secondary);">Login Email: <strong style="color:#fff;">{{ $user->email }}</strong></div>
                        <div style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Security: <strong style="color:#34D399; font-size:13px;">Password Protected</strong></div>
                    </div>

                    <div style="background:rgba(15,23,42,0.6); border:1px solid var(--border); border-radius:14px; padding:18px;">
                        <div style="font-weight:700; font-size:14px; margin-bottom:4px;">Need additional staff?</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-bottom:12px;">You can easily add Waiters, Cashiers, and Kitchen Chefs anytime from the Staff & Roles manager.</div>
                        <span style="font-size:11px; color:#A5B4FC; font-weight:600;"><i class="fa-solid fa-circle-check"></i> 5 Staff included in Free Plan</span>
                    </div>
                </div>
            </div>

            <!-- STEP 7: QR Codes -->
            <div class="step-pane" id="step7">
                <div class="pane-header">
                    <h2 class="pane-title">Step 7: Table QR Code Preview</h2>
                    <p class="pane-desc">Customers can scan these table QR codes to view your digital menu and order seamlessly.</p>
                </div>

                <div style="text-align:center; padding:24px; background:rgba(15,23,42,0.5); border-radius:14px; border:1px solid var(--border);">
                    <img id="obQrPreview" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url('/customer/menu?restaurantId=' . $restaurant->id . '&tableId=1')) }}" style="border-radius:12px; padding:8px; background:#fff; margin-bottom:12px;" alt="Table QR">
                    <div style="font-weight:700; font-size:15px;">Table #1 Digital QR Code</div>
                    <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Ready to print and stick on table tops!</div>
                </div>
            </div>

            <!-- STEP 8: Finish & Launch -->
            <div class="step-pane" id="step8">
                <div class="pane-header" style="text-align:center;">
                    <div style="width:64px; height:64px; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; color:#34D399; font-size:28px;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h2 class="pane-title">Your Restaurant is Ready to Launch!</h2>
                    <p class="pane-desc">Your multi-tenant DineFlow AI workspace has been configured under the Free Plan.</p>
                </div>

                <div class="usage-stat-box">
                    <div>
                        <div class="stat-value">10</div>
                        <div class="stat-label">Max Tables</div>
                    </div>
                    <div>
                        <div class="stat-value">5</div>
                        <div class="stat-label">Staff Logins</div>
                    </div>
                    <div>
                        <div class="stat-value">100</div>
                        <div class="stat-label">Menu Items</div>
                    </div>
                    <div>
                        <div class="stat-value">500</div>
                        <div class="stat-label">Monthly Orders</div>
                    </div>
                </div>

                <div style="text-align:center; margin-top:20px;">
                    <button type="button" onclick="finishOnboarding()" class="btn-primary" style="margin:0 auto; padding:16px 36px; font-size:16px;">
                        <span>Launch Restaurant Command Center</span>
                        <i class="fa-solid fa-rocket"></i>
                    </button>
                </div>
            </div>

            <!-- Wizard Footer Navigation -->
            <div class="wizard-footer">
                <button type="button" id="btnPrev" onclick="prevStep()" class="btn-secondary" style="display:none;">
                    <i class="fa-solid fa-arrow-left"></i> Previous
                </button>
                <div></div>
                <button type="button" id="btnNext" onclick="nextStep()" class="btn-primary">
                    <span>Continue</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </main>

    <script>
        let currentStep = 1;
        const totalSteps = 8;
        const restaurantId = "{{ $restaurant->id }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const stepTitles = [
            'Restaurant Profile',
            'Branding & Logo',
            'Tax & GST Settings',
            'Dining Tables Setup',
            'Menu Items Starter Pack',
            'Staff Credentials & PINs',
            'Table QR Code Preview',
            'Finish & Launch'
        ];

        document.addEventListener('DOMContentLoaded', () => {
            const savedStep = parseInt(localStorage.getItem('dineflow_onboarding_step') || '1');
            if (savedStep >= 1 && savedStep <= totalSteps) {
                currentStep = savedStep;
            }
            updateStepView();
        });

        function jumpToStep(step) {
            if (step < 1 || step > totalSteps) return;
            currentStep = step;
            updateStepView();
        }

        function nextStep() {
            if (currentStep === 1) saveProfileData();
            if (currentStep === 2) saveBrandingData();
            if (currentStep === 3) saveTaxData();

            if (currentStep < totalSteps) {
                currentStep++;
                updateStepView();
            } else {
                finishOnboarding();
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateStepView();
            }
        }

        function updateStepView() {
            localStorage.setItem('dineflow_onboarding_step', currentStep);

            // Update progress bar
            const pct = Math.round((currentStep / totalSteps) * 100);
            const titleElem = document.getElementById('progressStepTitle');
            const pctElem = document.getElementById('progressPercentage');
            const barElem = document.getElementById('onboardingProgressBar');
            if (titleElem) titleElem.textContent = `Step ${currentStep} of ${totalSteps}: ${stepTitles[currentStep - 1] || ''}`;
            if (pctElem) pctElem.textContent = `${pct}% Completed`;
            if (barElem) barElem.style.width = `${pct}%`;

            // Update step panes
            document.querySelectorAll('.step-pane').forEach((p, idx) => {
                p.classList.toggle('active', idx + 1 === currentStep);
            });

            // Update step indicators
            document.querySelectorAll('.step-indicator').forEach((ind, idx) => {
                const s = idx + 1;
                ind.classList.toggle('active', s === currentStep);
                ind.classList.toggle('completed', s < currentStep);
            });

            // Update buttons
            document.getElementById('btnPrev').style.display = currentStep > 1 ? 'inline-flex' : 'none';
            const btnNext = document.getElementById('btnNext');
            if (currentStep === totalSteps) {
                btnNext.style.display = 'none';
            } else {
                btnNext.style.display = 'inline-flex';
                btnNext.querySelector('span').textContent = currentStep === totalSteps - 1 ? 'Review & Launch' : 'Continue';
            }
        }

        async function saveProfileData() {
            const name = document.getElementById('ob_name')?.value;
            const phone = document.getElementById('ob_phone')?.value;
            const address = document.getElementById('ob_address')?.value;

            await fetch(`/api/restaurants/${restaurantId}/settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ name, phone, address })
            });
        }

        async function saveBrandingData() {
            const logo = document.getElementById('ob_logo')?.value;
            const footer = document.getElementById('ob_receipt_footer')?.value;

            await fetch(`/api/restaurants/${restaurantId}/settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ logo, settings: { receipt_footer: footer } })
            });
        }

        async function saveTaxData() {
            const gstin = document.getElementById('ob_gstin')?.value;
            const gst_rate = parseFloat(document.getElementById('ob_gst_rate')?.value) || 5.0;
            const service_charge = parseFloat(document.getElementById('ob_service_charge')?.value) || 5.0;

            await fetch(`/api/restaurants/${restaurantId}/settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ gstin, gst_rate, service_charge })
            });
        }

        async function generateSampleTables() {
            const statusElem = document.getElementById('tableListStatus');
            statusElem.textContent = 'Generating tables...';
            try {
                for (let i = 1; i <= 6; i++) {
                    await fetch('/api/tables', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ table_number: `T-${i}`, restaurant_id: restaurantId })
                    });
                }
                statusElem.textContent = '✓ 6 Dining tables created successfully with live QR codes!';
            } catch (err) {
                statusElem.textContent = 'Tables already initialized.';
            }
        }

        async function loadSampleMenuItems() {
            const statusElem = document.getElementById('menuListStatus');
            statusElem.textContent = 'Loading dishes...';
            const dishes = [
                { name: 'Paneer Butter Masala', category: 'Mains', price: 240, is_veg: true },
                { name: 'Chicken Biryani', category: 'Rice', price: 320, is_veg: false },
                { name: 'Butter Garlic Naan', category: 'Breads', price: 60, is_veg: true },
                { name: 'Crispy Veg Spring Rolls', category: 'Starters', price: 180, is_veg: true },
                { name: 'Fresh Mango Lassi', category: 'Drinks', price: 120, is_veg: true }
            ];

            try {
                for (const dish of dishes) {
                    await fetch('/api/menu', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ ...dish, restaurant_id: restaurantId })
                    });
                }
                statusElem.textContent = '✓ 5 Starter dishes loaded into your menu!';
            } catch (err) {
                statusElem.textContent = 'Menu items initialized.';
            }
        }

        async function finishOnboarding() {
            const res = await fetch('/api/onboarding/complete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = data.redirect || '/dashboard';
            } else {
                window.location.href = '/dashboard';
            }
        }
    </script>
</body>
</html>
