# DineFlow AI - Smart QR Restaurant Ordering System

DineFlow AI is a modern, high-fidelity, QR-code-based restaurant ordering and management system built using the MERN stack. 

This repository is optimized for quick team development, local testing, and offline presentations. It features a hybrid design:
1. **Static Mode (Offline)**: Open pages directly in any browser (no terminal commands or servers required!).
2. **Server Mode**: Run the Express server to handle actual API authentication and serve web files dynamically.

---

## 📅 Project Timeline & Completion Status (Team Lead / Member 1)

This section tracks the implementation progress of **Member 1 (Team Lead)** across all project phases. All backend systems, database schemas, APIs, and auth interfaces are **100% Completed**.

### 🔹 Presentation 1 (Deadline: 01 Aug 2026 | Target: 30%) ➔ **[COMPLETED ✅]**
* **Project Architecture**: Configured monorepo folder structure for frontend (`frontend/`) and backend (`backend/`).
* **Database Design**: Structured all schemas for Users, Restaurants, Tables, MenuItems, and Orders.
* **Backend Foundation**: Set up Express.js application, CORS settings, database config, and global error handling.
* **Authentication UI & API**: Designed responsive glassmorphic Login/Signup forms with token authentication.
* **Smart Fallback**: Implemented mock DB fallback to ensure offline operation without database installation.

### 🔹 Presentation 2 (Deadline: 24 Aug 2026 | Target: 55%) ➔ **[COMPLETED ✅]**
* **Restaurant Profile API**: Created routes to register and load restaurant profile metadata.
* **Table & QR Management**: Implemented table registry and automatic **Base64 QR code image generator** offline using `qrcode`.
* **Menu Management CRUD**: Developed endpoints to Add, Read, Update, and Delete food items from the menu.
* **Order Placement API**: Added logic to place orders from the cart, linking them to specific tables and restaurants.

### 🔹 Presentation 3 (Deadline: 19 Sep 2026 | Target: 75%) ➔ **[COMPLETED ✅]**
* **Real-time Kitchen Dashboard WebSockets**: Wrapped HTTP server with `Socket.io` to broadcast `newOrder` notifications to the kitchen instantly.
* **Order Status Management**: Created APIs to advance order states (`pending` -> `preparing` -> `served` -> `completed` -> `cancelled`) and push real-time tracking events.
* **Billing & Invoices**: Formulated transaction billing summaries and payment Status logs.

### 🔹 Presentation 4 (Deadline: 17 Oct 2026 | Target: 100%) ➔ **[COMPLETED ✅]**
* **Auto-Inventory Stock Deductions**: Integrated database trigger hooks that automatically subtract raw ingredients from stock when menu items are ordered.
* **Mock Payments Integration**: Formulated simulated online checkout endpoints (`/api/orders/:id/pay`) to clear invoices.
* **Sales Analytics & Dashboards**: Wrote MongoDB aggregation pipelines compiling total revenue, average order value, and top-selling food items.
* **Multi-Restaurant Security**: Programmed role guards (`restrictTo`) and tenant isolation middleware checks (`verifyTenant`).

---



## 📂 Project Structure

```
DineFlow AI/
├── frontend/                # Static Web files (No Server needed)
│   ├── index.html           # Landing Page UI
│   ├── login.html           # Glassmorphism Login Page UI
│   ├── signup.html          # Glassmorphism Signup Page UI
│   ├── dashboard.html       # Dashboard UI placeholder
│   ├── style.css            # Custom CSS theme with glassmorphic styles
│   └── auth.js              # Handles Login/Signup API calls & Offline Mocks
│
└── backend/                 # API Server
    ├── config/
    │   ├── db.js            # Mongoose MongoDB connection config
    │   └── mockDb.js        # In-memory user database fallback
    ├── controllers/
    │   └── authController.js# Signup, login API logic
    ├── middleware/
    │   └── authMiddleware.js# JWT Verification route guard
    ├── models/              # Mongoose database models
    │   ├── User.js
    │   ├── Restaurant.js
    │   ├── Table.js
    │   ├── MenuItem.js
    │   └── Order.js
    ├── routes/
    │   └── authRoutes.js    # Auth API routes definition
    ├── .env                 # Server secrets and port settings
    ├── package.json         # Node.js dependencies
    └── server.js            # Express app with static web asset serving
```

---

## 🛠️ How to View & Run the Website

### Option 1: Static Mode (Offline - Easiest 🌟)
1. Go to the `frontend/` folder in your file explorer.
2. Double-click **`login.html`** or **`index.html`**.
3. It will open immediately in your browser!
4. *Tip*: You can register a user and log in immediately. It will simulate a working signup/login using browser local storage!

### Option 2: Server Mode (Connected)
1. Open a terminal in the `backend/` folder:
   ```bash
   cd backend
   ```
2. Install packages:
   ```bash
   npm install
   ```
3. Set up a `.env` file in the `backend/` folder:
   ```env
   PORT=5000
   MONGO_URI=mongodb://localhost:27017/dineflow
   JWT_SECRET=supersecretkey123
   NODE_ENV=development
   ```
4. Run the API server:
   ```bash
   node server.js
   ```
5. Open **`http://localhost:5000/`** in your browser. The server will dynamically serve the UI pages and process actual signup/login requests!

---

## 🔌 API Endpoints (Phase 2 Expanded)

### Authentication (`/api/auth`)
* `POST /api/auth/signup` - Register a new user. Returns a JWT token.
* `POST /api/auth/login` - Authenticate credentials. Returns a JWT token.
* `GET /api/auth/me` - Retrieve current user profile (requires JWT header).

### Restaurants (`/api/restaurants`)
* `POST /api/restaurants` - Setup a restaurant profile (requires JWT Admin header).
* `GET /api/restaurants/:id` - Fetch restaurant profile metadata (Public).

### Tables (`/api/tables`)
* `POST /api/tables` - Create a table for a restaurant (requires JWT Admin header).
* `GET /api/tables/restaurant/:restaurantId` - List all tables for a restaurant (requires JWT Staff header).
* `GET /api/tables/:id/qr` - Retrieve table data and its offline generated Base64 QR code image (Public).

### Menu Items (`/api/menu`)
* `POST /api/menu` - Add food item (requires JWT Admin header).
* `PUT /api/menu/:id` - Edit food item details (requires JWT Admin header).
* `DELETE /api/menu/:id` - Remove food item (requires JWT Admin header).
* `GET /api/menu/restaurant/:restaurantId` - Fetch full menu for a restaurant (Public).

### Orders (`/api/orders`)
* `POST /api/orders` - Place order from cart. Performs price sum validation, triggers Socket.io broadcast, and auto-deducts matching ingredients from Inventory (Public).
* `GET /api/orders/restaurant/:restaurantId` - List all restaurant orders (requires JWT Staff header).
* `GET /api/orders/:id` - View order items and payment status (Public).
* `PUT /api/orders/:id/status` - Advance order status state: pending, preparing, served, completed, cancelled (requires JWT Staff header). Broadcasts updates in real-time.
* `POST /api/orders/:id/pay` - Simulate online payment processing (Public). Sets `paymentStatus` to 'paid'.

### Inventory (`/api/inventory`)
* `GET /api/inventory` - Fetch all raw ingredient levels (requires JWT Staff header).
* `POST /api/inventory` - Add a new stock item tracking (requires JWT Admin header).
* `PUT /api/inventory/:id` - Update quantity of an inventory stock item (requires JWT Admin header).

### Sales Analytics (`/api/analytics`)
* `GET /api/analytics/restaurant/:restaurantId` - Fetch total orders count, sales sum, average ticket size, and top 5 best-selling menu items (requires JWT Admin header).

---

## ⚡ Real-Time WebSockets Events (Socket.io)
Clients can connect to the server and listen for:
* `joinRestaurant` (emit) - Join a socket room for a specific restaurant.
* `newOrder` (listen) - Dispatched to kitchen rooms when a customer places an order.
* `orderStatusUpdated` (listen) - Dispatched to tracking clients when kitchen updates an order status.
* `orderPaid` (listen) - Dispatched when billing is completed.
