# RestoPOS Quick Reference

## 🚀 Quick Start Commands

```bash
# Navigate to project
cd F:\Laravel\NEWPOS\RePOS

# Run migrations (already done)
php artisan migrate

# Seed demo users (already done)
php artisan db:seed

# Start development server
php artisan serve --port 8000

# In another terminal, compile assets (optional)
npm run dev
```

## 📍 Important URLs

| URL | Purpose |
|-----|---------|
| http://localhost:8000/login | Full login page |
| http://localhost:8000/pin-login | PIN login page |
| http://localhost:8000/dashboard | Main dashboard |
| http://localhost:8000/logout | Logout |

## 👤 Demo Login Credentials

### Method 1: Full Login (Username + Password)
```
URL: http://localhost:8000/login

Option A:
Username: admin
Password: password

Option B:
Username: manager
Password: password

Option C:
Username: cashier
Password: password
```

### Method 2: Quick PIN Login
```
URL: http://localhost:8000/pin-login

After logging in once, use PIN only:

Admin PIN: 1234
Manager PIN: 5678
Cashier PIN: 9999
```

## 🔧 File Locations

| File | Purpose |
|------|---------|
| `app/Http/Controllers/AuthController.php` | Login logic |
| `app/Models/User.php` | User model |
| `resources/views/auth/login.blade.php` | Login page |
| `resources/views/auth/pin-login.blade.php` | PIN page |
| `resources/views/dashboard.blade.php` | Dashboard |
| `routes/web.php` | All routes |
| `database/seeders/UserSeeder.php` | Demo users |
| `database/migrations/2026_04_04_000000_add_numeric_pin_to_users.php` | Migration |

## 💾 Database

The `users` table has these new fields:
- `username` - Login username (unique)
- `numeric_pin` - 4-6 digit PIN for quick login

## 🎯 Key Features

✅ Two login methods
- Full: username + password
- Quick: PIN only (after first login)

✅ Secure password hashing with Laravel Hash

✅ Session-based username storage

✅ Responsive design (desktop & mobile)

✅ Touchscreen optimized UI

✅ Protected dashboard routes

✅ User dropdown menu

✅ Logout functionality

## 🔐 Security

- ✓ CSRF tokens on all forms
- ✓ Password hashing with Laravel Hash::make()
- ✓ Auth middleware on protected routes
- ✓ Guest middleware on login pages
- ✓ Session invalidation on logout
- ✓ Numeric PIN stored securely

## 📱 Touchscreen Features

- Large 44px+ touch targets
- 6 pin digit input boxes
- Numeric keyboard auto-activation
- Auto-submit on 6 digits
- Full screen PIN display
- Responsive portrait/landscape

## 🛠️ Customization

### Add New User
```bash
php artisan tinker

App\Models\User::create([
    'name' => 'Your Name',
    'username' => 'yourusername',
    'email' => 'you@example.com',
    'password' => Hash::make('yourpassword'),
    'numeric_pin' => '1111',
]);
```

### Change User PIN
```bash
php artisan tinker

$user = App\Models\User::where('username', 'admin')->first();
$user->update(['numeric_pin' => '5555']);
```

### Update User Password
```bash
php artisan tinker

$user = App\Models\User::where('username', 'admin')->first();
$user->update(['password' => Hash::make('newpassword')]);
```

## 📊 Dashboard Features

- Statistics cards (orders, revenue, customers, time)
- Quick action buttons
- User profile menu
- Sidebar navigation
- Recent orders section
- Responsive layout
- Dark theme ready

## 🎨 UI Colors

| Color | Hex | Usage |
|-------|-----|-------|
| Primary | #4F46E5 | Buttons, links |
| Success | #10B981 | Positive actions |
| Danger | #EF4444 | Errors |
| Warning | #F59E0B | Warnings |
| Background | #F8FAFC | Page background |
| Card | #FFFFFF | Card backgrounds |

## 🔄 Login Flow Diagram

```
Start
  ↓
No Auth? → Go to /login
  ↓
Username & Password Login
  ↓
Valid? → Yes → Store username in session → Redirect to /dashboard
  ↓
         No → Show error, stay on /login
  ↓
Return visit → Go to /pin-login
  ↓
Username + PIN
  ↓
Valid? → Yes → Auto-login → Redirect to /dashboard
  ↓
         No → Show error, stay on /pin-login
```

## 📝 Important Notes

1. **First Time**: Users must use username + password
2. **Username Storage**: Saved in session for quick access
3. **PIN Required**: Must be 4-6 digits
4. **Auto-Submit**: PIN auto-submits on 6 digits
5. **Session**: Expires after inactivity
6. **Database**: Uses SQLite by default (check .env)

## 🐛 Debug Mode

Check if debug is enabled in `.env`:
```
APP_DEBUG=true
```

## 📞 Need Help?

1. Check `LOGIN_SETUP.md` for detailed documentation
2. Review `Laravel` official docs at laravel.com
3. Check Chrome DevTools for JavaScript errors
4. Review server logs: `storage/logs/`

---

**Version**: 1.0
**Created**: April 4, 2026
**Framework**: Laravel 13
