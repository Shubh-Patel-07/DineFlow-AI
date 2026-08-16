# 📡 DineFlow AI - REST API Specification & Endpoint Documentation

## 1. Authentication & Security Headers

All JSON API requests should include:
```http
Accept: application/json
Content-Type: application/json
X-CSRF-TOKEN: <token> (for browser session requests)
```

---

## 2. API Endpoints

### 2.1 Menu Management (`/api/menu`)

#### `GET /api/menu`
Retrieve active restaurant menu items.

**Query Parameters:**
- `restaurantId` (string, optional): Default `'1'`
- `category` (string, optional): Filter by category
- `diet` (string, optional): `'veg'` or `'nonveg'`

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Paneer Butter Masala",
      "category": "Mains",
      "price": "240.00",
      "is_available": 1,
      "is_veg": 1,
      "image": "https://images.unsplash.com/photo-1631452180519-c014fe946bc7",
      "description": "Rich cottage cheese cubes cooked in butter tomato gravy."
    }
  ]
}
```

#### `POST /api/menu`
Create a new menu item (Admin/Manager role required).

**Request Body:**
```json
{
  "restaurant_id": "1",
  "name": "Paneer Tikka Starter",
  "category": "Starters",
  "price": 220.00,
  "is_veg": true,
  "is_available": true,
  "image": "https://images.unsplash.com/photo-1599488615731-7e5c2823ff28",
  "description": "Chargrilled marinated cottage cheese with capsicum."
}
```

---

### 2.2 Orders & Checkout (`/api/orders`)

#### `POST /api/orders`
Place a new digital order from customer menu or POS.

**Request Body:**
```json
{
  "restaurant_id": "1",
  "table_id": "3",
  "payment_method": "upi",
  "special_instructions": "Less spicy, extra green chutney",
  "items": [
    {
      "menu_item_id": 1,
      "name": "Paneer Butter Masala",
      "price": 240.00,
      "quantity": 2
    },
    {
      "menu_item_id": 3,
      "name": "Garlic Naan",
      "price": 60.00,
      "quantity": 3
    }
  ]
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Order placed successfully",
  "data": {
    "id": 1042,
    "table_id": "3",
    "subtotal": 660.00,
    "tax_amount": 33.00,
    "service_charge": 33.00,
    "total_amount": 726.00,
    "status": "pending",
    "payment_status": "unpaid",
    "created_at": "2026-08-23T04:15:00.000000Z"
  }
}
```

#### `GET /api/orders/{id}/status`
Real-time status tracking for customer live tracker.

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1042,
    "status": "preparing",
    "table_id": "3",
    "total_amount": 726.00,
    "elapsed_minutes": 6
  }
}
```

---

### 2.3 Waiter Calling Service (`/api/waiter-calls`)

#### `POST /api/waiter-calls`
Trigger immediate waiter assistance bell for a table.

**Request Body:**
```json
{
  "restaurant_id": "1",
  "table_id": "5",
  "request_type": "water"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Waiter call sent successfully to staff terminal."
}
```

---

### 2.4 Coupons & Discounts (`/api/coupons`)

#### `POST /api/coupons/validate`
Validate promo code and calculate discount.

**Request Body:**
```json
{
  "code": "FEAST20",
  "subtotal": 850.00,
  "restaurant_id": "1"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "valid": true,
  "discount_amount": 170.00,
  "discount_type": "percentage",
  "message": "Coupon 'FEAST20' applied successfully! (20% OFF)"
}
```
