# DineFlow AI — Real Restaurant Pilot Setup Guide

This guide outlines the complete 20-step operational walkthrough to onboard a real restaurant partner for a free pilot.

---

## Part 1: Tenant Registration & Initial Configuration

1. **Self-Serve Registration**:
   - Navigate to `/register`.
   - Enter Restaurant Name, Owner Name, Business Email, Mobile Number, and Password.
   - Accept Terms of Service and click **Start Free Pilot**.
   - Automatic provisioning assigns the **Free Plan** (10 tables, 5 staff, 100 dishes, 500 monthly orders).

2. **Complete 8-Step Onboarding**:
   - Access `/onboarding`.
   - **Step 1**: Business profile & address details.
   - **Step 2**: Legal Tax & GSTIN configuration (GST %, Service Charge %).
   - **Step 3**: Primary currency selection (INR ₹).
   - **Step 4**: Table count & layout structure (e.g. T1–T10).
   - **Step 5**: Initial menu items and food categories.
   - **Step 6**: Initial staff creation (Cashier, Chef, Waiter).
   - **Step 7**: Gujarati / Hindi / English Soundbox voice preference.
   - **Step 8**: Go to Dashboard (`/dashboard`).

3. **Upload Brand Logo**:
   - Go to **System Settings** (`/admin/settings`).
   - Upload high-resolution restaurant logo (PNG/JPEG).

---

## Part 2: Catalog, Staff & Dining Setup

4. **Setup Menu Categories & Dishes**:
   - Open **Menu Items** (`/admin/menu-manager`).
   - Add items under *Starters*, *Main Course*, *Breads*, *Desserts*, and *Beverages*.
   - Mark Veg / Non-Veg badges and upload dish photos.

5. **Generate Table QR Standees**:
   - Open **QR Generator** (`/admin/qr-generator`).
   - Export high-resolution QR codes for each physical table.
   - Print standees and position them on dining tables.

6. **Create Staff PIN Logins**:
   - Open **Staff & Roles** (`/admin/staff`).
   - Generate unique numeric 4-digit PINs for:
     - Floor Manager (PIN e.g. `1002`)
     - Cashier (PIN e.g. `1003`)
     - Head Chef (PIN e.g. `1004`)
     - Waiter (PIN e.g. `1005`)

7. **Create Pilot Promotional Coupons**:
   - Open **Offers & Deals** (`/admin/coupons`).
   - Create coupon code `LAUNCH20` (20% OFF, min ₹300 order, ₹100 cap).
   - Toggle **Active** state.

8. **Initialize Inventory Stock**:
   - Open **Inventory Stock** (`/admin/inventory`).
   - Add primary ingredients (Paneer, Dairy, Oil, Flour, Spices) with unit costs and low-stock alert thresholds.

---

## Part 3: Operational Hardware & Live Workflow

9. **Billing Counter POS Station**:
   - Open `/pos` on the cashier laptop or tablet.
   - Test table occupancy grid and item quick-add.

10. **Kitchen KDS Screen**:
    - Open `/kitchen` on a dedicated tablet or wall-mounted monitor in the kitchen.
    - Enable sound alerts for incoming order notifications.

11. **Payment QR & Soundbox**:
    - Verify UPI VPA is configured in **System Settings**.
    - Test soundbox real-time audio playback upon order payment.

12. **Thermal Receipt Printer Setup**:
    - Select 58mm or 80mm thermal receipt in browser print dialog.

13. **Conduct Test Order (Customer Mobile)**:
    - Scan table QR code using a smartphone.
    - Add dishes to cart, specify cooking notes, apply `LAUNCH20` coupon, and submit order.

14. **Verify POS & Kitchen Live Notification**:
    - Ensure order card immediately reflects on KDS screen.
    - Chef accepts and transitions to *Preparing* ➔ *Ready*.

15. **Settle Payment & Print Receipt**:
    - Cashier marks order as Paid via Cash or UPI in POS.
    - Print thermal customer receipt.

---

## Part 4: Pilot Daily Wrap-up & Support

16. **End-of-Day Z-Report**:
    - Access `/z-report` to review daily sales, category breakdowns, and tax totals.

17. **Record Daily Operational Expenses**:
    - Log ingredient and utility purchases in `/admin/expenses`.

18. **Review Customer Feedback**:
    - Check ratings and customer reviews in `/admin/feedback`.

19. **Review Sales Analytics**:
    - Monitor top-selling dishes in `/admin/sales-report`.

20. **Go-Live Readiness Confirmed**:
    - Restaurant is now operational on DineFlow AI Free Pilot!
