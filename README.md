# DineFlow AI - Smart QR Restaurant Ordering System

DineFlow AI is a modern, high-fidelity, QR-code-based restaurant ordering and management system built using the MERN stack. 

This repository is optimized for quick team development, local testing, and offline presentations. It features a hybrid design:
1. **Static Mode (Offline)**: Open pages directly in any browser (no terminal commands or servers required!).
2. **Server Mode**: Run the Express server to handle actual API authentication and serve web files dynamically.

---

## 📅 Presentation 1 Timeline Status
* **Target Completion**: 30%
* **Features Completed**:
  - project folder structure (monorepo frontend/backend split)
  - database schemas (User, Restaurant, Table, MenuItem, Order)
  - backend Express server with static file serving
  - authentication endpoints (Signup & Login APIs with Mock fallback)
  - glassmorphism login, signup & landing HTML UI
  - dynamic client side authentication logic (`frontend/auth.js`)

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
* `POST /api/orders` - Place order from cart. Performs price sum validation (Public).
* `GET /api/orders/restaurant/:restaurantId` - List all restaurant orders (requires JWT Staff header).
* `GET /api/orders/:id` - View order items and payment status (Public).
