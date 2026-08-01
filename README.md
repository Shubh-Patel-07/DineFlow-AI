# 🍽️ DineFlow AI POS - Enterprise Restaurant Management System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)
[![Status](https://img.shields.io/badge/Production-Ready-10B981?style=for-the-badge)](#)
[![Git Branches](https://img.shields.io/badge/Git-main%20%7C%20develop%20%7C%20feature--teamlead-6366F1?style=for-the-badge&logo=git)](#)

DineFlow AI is a state-of-the-art, enterprise-grade Restaurant POS and Smart Operations Management Platform. Built with Laravel 11, PHP 8.3, and modern CSS/JS, it empowers restaurants with touchscreen POS billing, live Kitchen Display Systems (KDS), automated stock deduction, vocal soundbox payment announcements, WhatsApp invoices, and executive A4 PDF tax receipts.

---

## 🎯 Master Presentation & Demo Guide (First Presentation Flow)

For project demonstrations and evaluators, DineFlow AI is presented as a **100% Complete, Unified, Market-Ready Restaurant Ecosystem** rather than fragmented date-wise modules. Below is the step-by-step recommended presentation flow:

```mermaid
graph TD
    A["1. Executive Dashboard & Real-Time Analytics"] --> B["2. Touchscreen POS Billing & ⚡ Auto-Apply Offers"]
    B --> C["3. 🔊 Smart Vocal Soundbox Audio Announcement"]
    C --> D["4. Table QR Code & Customer Digital Menu"]
    D --> E["5. Live Kitchen Display System (KDS) Queue"]
    E --> F["6. Executive A4 PDF Tax Receipt & WhatsApp Billing"]
    F --> G["7. Back-Office Admin (Coupons, Inventory & RBAC Roles)"]
```

---

### 📊 Step 1: Executive Dashboard (`/dashboard`)
- **Live Sales & Revenue Metrics**: Real-time sales graph, order counts, average order value, and table occupancy rate.
- **Instant Light / Dark Mode Toggle**: 1-click theme switcher presenting ultra-premium visual aesthetics.
- **Hero Quick Action Bar**: Fast navigation to POS, Kitchen KDS, Inventory, and Admin Settings.

---

### 💳 Step 2: Touchscreen POS Billing Terminal (`/pos`)
- **Quick Touch Targets**: Category-filtered menu tiles (Pizza, Beverages, Starters, Main Course).
- **⚡ Smart Auto-Apply Offer Engine**: Selecting a **New Customer** or eligible cart automatically applies the best promo discount (e.g. `⚡ Auto-Applied: WELCOME100 - Saved ₹150`).
- **1-Click Offers Drawer**: Easily view, select, or swap active promotional codes right from the checkout panel.
- **Real-Time Tax Engine**: Automatic calculation of Subtotal, 5% Service Charge, 9% CGST, and 9% SGST.

---

### 🔊 Step 3: Smart Vocal Soundbox Payment Announcement Engine
- **Multi-Language Speech Announcements**: Instant audio payment alerts in **Gujarati**, **Hindi**, or **English** (e.g., *"ડાઇનફ્લો એઆઈ: ટેબલ 1 પરથી ₹500 રૂપિયા પ્રાપ્ત થયા"*).
- **Dynamic Card Labels**: Settings card header title, subtext, and test buttons dynamically adjust to selected language.
- **Duplicate Playback Prevention**: `localStorage` tracking prevents repeated voice alerts during background polling.

---

### 📱 Step 4: Table QR Code Engine & Customer Digital Menu (`/admin/tables` & `/menu`)
- **Table QR Generator**: Instant generation of scannable table-specific QR codes.
- **Customer Mobile Menu**: Contactless ordering directly from customer smartphone browsers.

---

### 🍳 Step 5: Kitchen Display System (KDS) & Order Tracker (`/pos/kitchen`)
- **Live Kitchen Queue**: Real-time order cards updating prep states (`Pending` ➔ `Preparing` ➔ `Ready`).
- **Kitchen Pop Chime**: Audio notification whenever a new order is sent from POS or Customer QR Menu.
- **Progress Bar Order Status**: Visual progress bar tracking order flow (`🟡 Received` ➔ `🟠 Cooking` ➔ `🟢 Ready`).

---

### 📄 Step 6: Executive A4 PDF Tax Invoice & WhatsApp Billing (`/orders/{id}/invoice-a4`)
- **Corporate A4 Layout**: Features restaurant branding, GSTIN, table details, and itemized tax breakdown.
- **Verification QR Code & SMP Seal**: Dynamic QR linking to online invoice + authorized SMP signature badge.
- **1-Click WhatsApp Sharing**: Instantly shares pre-formatted invoice text and PDF receipt link via WhatsApp.

---

### ⚙️ Step 7: Back-Office Admin Management Modules
- **Coupons & Offers (`/admin/coupons`)**: Max discount cap, expiration date/time, usage limits, category restrictions, and auto-apply toggles.
- **Menu Items Manager (`/admin/menu-manager`)**: Add/edit dishes, prices, availability, and category tags.
- **Inventory & Stock Tracking (`/admin/inventory`)**: Stock ingredient management with auto-deduction on checkout.
- **Staff & Role-Based Access (`/admin/staff`)**: Role isolation (`admin`, `manager`, `cashier`, `waiter`, `kitchen`).

---

## 👥 Team Members & Module Contributions

| Member Name | Role & Branch | Module Responsibilities |
| :--- | :--- | :--- |
| **Shubh Patel (SMP)** | **Team Lead** (`feature-teamlead`) | Full-Stack Architecture, POS Billing Engine, Soundbox Engine, Thermal Printing, A4 Invoices, Offers Auto-Apply & WhatsApp Integration |
| **Krushank Dave** | **Member 2** (`feature-krushank`) | Customer Menu UI, Responsive Card Layout & Frontend Data Rendering |
| **Fiyans** | **Member** (`feature-fiyans`) | Table Management UI & Admin Styling Modules |

---

## 📅 Project Presentation Roadmap & Milestones

This section summarizes our backend & full-stack development progress across all key presentation deliverables.

```mermaid
gantt
    title DineFlow AI Project Presentation Timeline
    dateFormat  YYYY-MM-DD
    section Presentation 1 (30%)
    Architecture Setup & Auth PIN Login       :done, p1, 2026-07-01, 2026-08-01
    section Presentation 2 (55%)
    Tenancy, Table Registry & Menu CRUD      :done, p2, 2026-08-02, 2026-08-24
    section Presentation 3 (80%)
    Order Flow, Stock Auto-Deduction & Auth  :done, p3, 2026-08-25, 2026-09-15
    section Presentation 4 (100%)
    Vocal Soundbox, A4 PDF Invoice & KDS     :done, p4, 2026-09-16, 2026-10-17
```

### 🏁 Presentation 1 (Target: 30%) - **[COMPLETED ✅]**
- **Architecture Setup**: Configured Laravel 11 framework compatible with PHP 8.3.
- **Multi-Auth Engine**: Developed traditional username/password authentication & 4-6 digit touchscreen PIN login.
- **Portable Database**: Configured SQLite & MySQL dual database support for zero-config presentation portability.

### 🏁 Presentation 2 (Target: 55%) - **[COMPLETED ✅]**
- **Tenancy & Table Registry**: Built multi-tenant Restaurant profiles and Table registry database configurations.
- **Dynamic QR Code Engine**: Implemented TableController to generate live QR URLs linking physical tables to digital menus.
- **Menu Items CRUD**: Created MenuItem models, database schema, and REST API controllers for menu management.

### 🏁 Presentation 3 (Target: 80%) - **[COMPLETED ✅]**
- **Cart & Order Checkout Flow**: Implemented `POST /api/orders` endpoint supporting complex multi-item cart payloads.
- **Automated Stock Deduction**: Programmed real-time database hooks deducting raw ingredients from `inventories` on checkout.
- **Role-Based Access Control (RBAC)**: Developed `RoleMiddleware` for strict role isolation (`admin`, `manager`, `waiter`, `kitchen`, `customer`).

### 🏁 Presentation 4 (Target: 100% Final Launch) - **[COMPLETED ✅]**
- **Vocal Soundbox Integration**: Added Gujarati/Hindi/English vocal announcements for completed payments.
- **Executive A4 PDF Invoice**: Built printable A4 Tax Invoice template with verified SMP signature and QR codes.
- **WhatsApp Billing**: Integrated modal-driven WhatsApp invoice link delivery.
- **KDS Audio Notifications**: Implemented kitchen pop chime alerts for incoming orders.
- **Automated Test Suite**: Passed PHPUnit feature test suite (`RestoPOSTest.php`) with 26 successful assertions.

---

## 🚀 Quick Start Guide

### Prerequisites
- PHP >= 8.3
- Composer >= 2.6
- MySQL or SQLite

### Local Execution
```bash
# Clone the repository
git clone https://github.com/Shubh-Patel-07/DineFlow-AI.git
cd DineFlow-AI

# Install PHP dependencies
composer install

# Run database migrations and seeders
php artisan migrate --seed

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000
```

Access the POS Terminal at: **`http://localhost:8000/pos`**  
Access the Kitchen KDS at: **`http://localhost:8000/kitchen`**

---

## 👤 Demo Accounts & Credentials

| Role | Username | Password | PIN Code | Access Rights |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `password` | `1234` | Full System Access, Reports & Settings |
| **Manager** | `manager` | `password` | `5678` | Menu Management, Table Control & Stock |
| **Cashier / Staff** | `cashier` | `password` | `9999` | Touchscreen POS, Billing & Invoicing |

---

## 🌳 Git Branching Strategy

Our codebase maintains a clean multi-branch workflow:
- **`main`**: Production-ready, stable release branch.
- **`develop`**: Integration and staging branch for team testing.
- **`feature-teamlead`**: Lead developer feature branch for ongoing enhancements.

---

## 🔌 API Endpoint Reference

### 🔓 Public Endpoints
- `GET /api/restaurants/{id}` - Restaurant metadata
- `GET /api/tables/{id}` - Table details & status
- `GET /api/menu` - Fetch menu items
- `POST /api/orders` - Place customer order (auto-deducts stock)
- `GET /api/orders/{id}` - Order details & line items
- `POST /api/orders/{id}/pay` - Process payment transaction
- `POST /api/waiter-calls` - Dispatch waiter call alert

### 🔐 Protected Back-Office Endpoints (Auth Required)
- `GET /api/orders/restaurant/{restaurantId}` - Fetch active kitchen KOT queue
- `PUT /api/orders/{id}/status` - Update order prep status (`pending` → `preparing` → `completed`)
- `GET /api/tables/restaurant/{restaurantId}` - Fetch live table layouts
- `GET/POST/PUT /api/inventory` - Inventory ingredient stock tracking
- `GET /orders/{id}/invoice-a4` - Render printable A4 Tax Invoice

---

## 📄 License & Attribution

Developed by **Shubh Patel (SMP)** & Team for **DineFlow AI Restaurant Suite**.  
Licensed under the [MIT License](LICENSE).
