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
