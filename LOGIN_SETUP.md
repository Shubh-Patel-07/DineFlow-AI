# RestoPOS Login System - Setup Guide

## 🎯 Overview

This is a complete POS login system for a restaurant application with two authentication methods:
1. **Full Login**: Username + Password
2. **Quick PIN Login**: Username + 4-6 digit numeric PIN (for touchscreen quick access)

## ✨ Features

- ✅ Secure username/password authentication
- ✅ Quick numeric PIN login for touchscreen devices
- ✅ Username persistence in session for fast switching
- ✅ Beautiful, responsive UI optimized for touchscreen
- ✅ Protected dashboard with role management
- ✅ Automatic redirect based on auth status
- ✅ Session management and logout

## 🚀 Getting Started

### 1. Database Migration & Seeding

The system adds a `numeric_pin` column to the users table. Run these commands:

```bash
# Run migrations
php artisan migrate --force

# Seed demo users
php artisan db:seed
```

### 2. Demo Credentials

Three demo users are automatically created:

| Username | Password | PIN  | Role    |
|----------|----------|------|---------|
| admin    | password | 1234 | Admin   |
| manager  | password | 5678 | Manager |
| cashier  | password | 9999 | Cashier |

## 🔐 Authentication Flow

### Full Login (Username + Password)
```
1. User navigates to /login
2. Enters username and password
3. System validates credentials
4. Stores username in session for quick access
5. Redirects to dashboard
```

### Quick PIN Login (Username + PIN)
```
1. User navigates to /pin-login
2. System pre-fills username from session
3. User enters 4-6 digit PIN
4. System validates PIN
5. Auto-submits when PIN reaches 6 digits
6. Redirects to dashboard
```

## 📋 Routes

| Route | Method | Purpose |
|-------|--------|---------|
| `/login` | GET | Show login form |
| `/login` | POST | Process login |
| `/pin-login` | GET | Show PIN login form |
| `/pin-login` | POST | Process PIN login |
| `/dashboard` | GET | Main dashboard (protected) |
| `/logout` | POST | Logout user |
| `/switch-user` | GET | Switch user (clears session) |

## 📁 Project Structure

```
app/
  ├── Http/Controllers/
  │   └── AuthController.php          # Authentication logic
  └── Models/
      └── User.php                     # User model (updated)

database/
  ├── migrations/
  │   └── 2026_04_04_000000_add_numeric_pin_to_users.php  # Migration
  └── seeders/
      ├── UserSeeder.php               # Demo users
      └── DatabaseSeeder.php           # Updated

resources/views/
  ├── auth/
  │   ├── login.blade.php              # Full login page
  │   └── pin-login.blade.php          # PIN login page
  └── dashboard.blade.php              # Main dashboard

routes/
  └── web.php                           # Updated routes
```

## 🎨 UI Features

### Login Page
- Clean, modern design
- Email/password fields
- Remember me checkbox
- Demo credentials hint
- "Quick PIN Login" button (visible after first login)
- Responsive design for all devices

### PIN Login Page
- Large PIN digit display (6 boxes)
- Touchscreen-optimized numeric keyboard
- Pre-filled username display
- Auto-submit on 6 digits
- Back button to full login
- Interactive feedback

### Dashboard
- Professional admin panel design
- Sidebar with navigation menu
- Statistics cards (orders, revenue, customers, avg time)
- Quick action buttons
- User menu with profile & logout
- Responsive layout
- Recent orders section

## 🛠️ Configuration

### Adding New Users

You can create new users via Tinker:

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'John Doe',
    'username' => 'johndoe',
    'email' => 'john@example.com',
    'password' => Hash::make('securepassword'),
    'numeric_pin' => '1111',
]);
```

### Changing User PIN

To update a user's PIN:

```php
$user = App\Models\User::where('username', 'admin')->first();
$user->update(['numeric_pin' => '5555']);
```

## 🔒 Security Considerations

1. **Passwords**: Always hashed using Laravel's `Hash::make()`
2. **Session**: Uses Laravel's built-in session management
3. **CSRF**: All forms include CSRF tokens
4. **Auth Middleware**: Protected routes require authentication
5. **Guest Middleware**: Auth pages require unauthenticated state

## 🎯 Usage Examples

### Test Full Login
1. Go to http://localhost:8000/login
2. Enter username: `admin`
3. Enter password: `password`
4. Click "Login"
5. You'll see "Quick PIN Login" button appear

### Test PIN Login
1. After first login, go to http://localhost:8000/pin-login
2. Username is pre-filled
3. Enter PIN: `1234`
4. Auto-submits and logs you in
5. Or manually click "Login" button

## 📱 Touchscreen Optimization

- Large touch targets (minimum 44px)
- Full-screen PIN input boxes
- Numeric keyboard activation
- Auto-focus on PIN field
- Haptic feedback ready
- Portrait and landscape support

## 🚀 Running the Application

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite for CSS/JS compilation
npm run dev
```

Then visit: http://localhost:8000

## 📝 Database Schema

### Users Table (Updated)
```sql
CREATE TABLE users (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  username VARCHAR(255) UNIQUE,          -- NEW
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255),
  numeric_pin VARCHAR(10),                -- NEW: 4-6 digits
  remember_token VARCHAR(100),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

## 🔄 Workflow for Touchscreen Kiosk

For a touchscreen restaurant kiosk:

1. **First Time**: User logs in with username/password
2. **Subsequent Times**: User quickly enters PIN on touchscreen
3. **Multiple Users**: Each can have their own PIN
4. **Fast Service**: 6-digit PIN login takes ~2 seconds

## 🐛 Troubleshooting

### PIN Login Not Appearing
- Clear your browser cache
- Make sure you've logged in once with username/password
- Check that session data is being stored

### Migration Failed
- Ensure database connection is correct
- Check that the users table exists
- Run `php artisan migrate:refresh` if needed (WARNING: clears data)

### Users Not Created
- Verify seeder ran successfully
- Check: `php artisan db:seed --class=UserSeeder`
- Verify `app/Models/User.php` has correct Fillable attributes

## 📞 Support

For issues or questions about the POS system, refer to Laravel documentation:
- Authentication: https://laravel.com/docs/authentication
- Sessions: https://laravel.com/docs/session
- Middleware: https://laravel.com/docs/middleware

---

**Version**: 1.0  
**Last Updated**: April 4, 2026  
**Framework**: Laravel 13
