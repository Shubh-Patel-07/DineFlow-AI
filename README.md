# 🍽️ RestoPOS - Restaurant POS System

A complete point-of-sale system for restaurants with a professional login system featuring both traditional username/password authentication and quick numeric PIN login for touchscreen devices.

## ✨ Key Features

- ✅ **Full Login**: Username + Password with "Remember Me"
- ✅ **Quick PIN Login**: 4-6 digit numeric PIN for touchscreen
- ✅ **Professional Dashboard**: Admin panel with statistics & quick actions
- ✅ **Secure**: CSRF protection, password hashing, middleware auth
- ✅ **Responsive**: Works on desktop, tablet, and mobile
- ✅ **Touchscreen Optimized**: Large touch targets, auto-keyboard, auto-submit

## 📅 Project Timeline & Presentation Status (Team Lead Backend Scope)

This section maps our backend development progress across the 4 key presentation stages leading up to the final launch.

### 🏁 Presentation 1 (Deadline: 01 Aug 2026 | Target: 30%) - **[COMPLETED ✅]**
* **Project Architecture**: Set up Laravel 11 framework configuration compatible with PHP 8.3 local dev engines.
* **Auth System**: Integrated session-based traditional login & touchscreen PIN-code login endpoints.
* **Database Base**: Instantiated local SQLite database configuration for complete zero-config presentation portability.

### 🏁 Presentation 2 (Deadline: 24 Aug 2026 | Target: 55%) - **[COMPLETED ✅]**
* **Tenancy & Table Registry**: Developed Restaurant profiles and Table registry database configurations.
* **QR Codes Engine**: Created custom TableController to generate live QR URLs linking table numbers to the menu page.
* **Menu Items CRUD**: Created MenuItem models, database tables, and controller API routes to manage restaurant dishes.

### 🏁 Presentation 3 (Deadline: 15 Sep 2026 | Target: 80%) - **[COMPLETED ✅]**
* **Cart & Order System**: Developed the checkout order placement flow (`POST /api/orders`) processing complex items arrays.
* **Auto-Stock Deduction**: Programmed real-time hooks to automatically deduct corresponding raw materials from the `inventories` table on order placements.
* **Auth Middleware**: Written custom RoleMiddleware & TenantMiddleware to isolate data access by role (`admin`, `manager`, `waiter`, `kitchen`, `customer`).

### 🏁 Presentation 4 (Deadline: 17 Oct 2026 | Target: 100% Final Launch) - **[COMPLETED ✅]**
* **Service Calls**: Developed the Customer "Call Waiter" alert logs (`POST /api/waiter-calls`) and reviews feedbacks features.
* **Analytics Engine**: Integrated backend sales calculators (total orders, total revenue, popular items lists).
* **Artisan Verification**: Programmed comprehensive PHPUnit backend test suites (passing 26 assertions for complete reliability).

## 🚀 Quick Start

```bash
# Navigate to project
cd F:\Laravel\NEWPOS\RePOS

# Start server
php artisan serve --port 8000

# Or use Windows batch script
start_pos.bat
```

Visit: **http://localhost:8000/login**

## 👤 Demo Accounts

| Username | Password | PIN  |
|----------|----------|------|
| admin    | password | 1234 |
| manager  | password | 5678 |
| cashier  | password | 9999 |

## 📋 Documentation

- **[LOGIN_SETUP.md](LOGIN_SETUP.md)** - Complete setup guide
- **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick commands & credentials
- **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - Technical details
- **[SETUP_GUIDE.html](SETUP_GUIDE.html)** - Interactive guide (open in browser)

## 🛣️ Available Routes & APIs

### 🔐 Authentication Pages
* `/login` (GET/POST) - Traditional username & password login.
* `/pin-login` (GET/POST) - Touchscreen PIN-code quick login.
* `/dashboard` (GET) - Admin control panel shell page (Requires Auth).
* `/logout` (POST) - Log out session.

### 🔌 DineFlow AI API Endpoints

#### Public REST APIs:
* `GET /api/restaurants/{id}` - View restaurant profile metadata.
* `GET /api/tables/{id}` - Fetch table information.
* `GET /api/menu` - Fetch menu items (Filterable by `restaurantId`).
* `POST /api/orders` - Place customer order from cart (performs pricing calculations and stock auto-deductions).
* `GET /api/orders/{id}` - View order details and items.
* `POST /api/orders/{id}/pay` - Process mock transaction payment (updates `payment_status` to `paid`).
* `POST /api/feedback` - Place customer feedback (rating & comment).
* `POST /api/waiter-calls` - Dispatch a "Call Waiter" alert for a table.

#### Protected Back-Office APIs (Requires Auth):
* `POST /api/restaurants` - Register a restaurant (Admin restricted).
* `POST /api/tables` - Create a table registry (Admin restricted).
* `GET /api/tables/restaurant/{restaurantId}` - Fetch all table layouts (Manager/Staff/Waiter only + Tenant Isolation).
* `POST/PUT/DELETE /api/menu` - CRUD operations for food items (Admin restricted).
* `GET /api/orders/restaurant/{restaurantId}` - Fetch incoming kitchen orders queue (Staff/Kitchen/Waiter + Tenant Isolation).
* `PUT /api/orders/{id}/status` - Update order prep status (Staff/Kitchen/Waiter).
* `GET/POST/PUT /api/inventory` - CRUD for ingredient stock tracking (Staff/Admin + Tenant Isolation).
* `GET /api/analytics/restaurant/{restaurantId}` - Aggregated sales performance dashboards (Admin + Tenant Isolation).
* `GET /api/feedback/restaurant/{restaurantId}` - List customer ratings history (Admin/Manager + Tenant Isolation).
* `GET /api/waiter-calls/restaurant/{restaurantId}` - List active waiter calls (Staff/Waiter + Tenant Isolation).
* `PUT /api/waiter-calls/{id}/resolve` - Mark waiter call resolved (Staff/Waiter).

## 📁 Project Structure

```
app/Http/Controllers/AuthController.php     ← Login logic
app/Models/User.php                         ← User model
resources/views/auth/login.blade.php       ← Login form
resources/views/auth/pin-login.blade.php   ← PIN form
resources/views/dashboard.blade.php        ← Dashboard
routes/web.php                              ← Routes
database/migrations/...                     ← Database schema
database/seeders/UserSeeder.php            ← Demo users
```

## 🔐 Authentication Flow

1. **First Visit** → Full login (username + password)
2. **Username Stored** → Session maintains username
3. **Return Visit** → Quick PIN login (pre-filled username)
4. **Auto-Submit** → PIN submits automatically on 6 digits
5. **Dashboard** → Redirected to main dashboard

## 🎨 Features

### Login Page
- Username & password fields
- Remember me checkbox
- Demo credentials hint
- Modern gradient design

### PIN Login Page
- 6 large PIN digit boxes
- Pre-filled username
- Auto-keyboard activation
- Auto-submit on 6 digits

### Dashboard
- Statistics cards
- Sidebar navigation
- Quick action buttons
- User menu & logout
- Professional layout

## ✅ Security Features

- Password hashing (Laravel Hash::make())
- CSRF tokens on all forms
- Auth middleware protection
- Session management
- SQL injection prevention
- XSS protection

## 📱 Touchscreen Optimized

- 44px+ touch targets
- Full-screen PIN display
- Numeric keyboard auto-activation
- Portrait & landscape support
- High contrast colors

## 📊 Database

New columns added to users table:
- `username` (VARCHAR, unique)
- `numeric_pin` (VARCHAR, 4-6 digits)

Migrations & seeding: ✅ Complete

## 🐛 Troubleshooting

```bash
# Migrations not run?
php artisan migrate --force

# Demo users not created?
php artisan db:seed --class=UserSeeder

# Reset everything?
php artisan migrate:refresh --seed
```

## 🔧 Customization

### Add New User
```bash
php artisan tinker

App\Models\User::create([
    'name' => 'John Doe',
    'username' => 'johndoe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'numeric_pin' => '2222',
]);
```

### Change PIN
```bash
php artisan tinker

$user = App\Models\User::where('username', 'admin')->first();
$user->update(['numeric_pin' => '5555']);
```

## 📞 Support

- Documentation: See [LOGIN_SETUP.md](LOGIN_SETUP.md)
- Laravel Docs: https://laravel.com/docs
- Issues: Check server logs in `storage/logs/`

---

**Version**: 1.0  
**Status**: ✅ Production Ready  
**Framework**: Laravel 13  
**Created**: April 4, 2026

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
