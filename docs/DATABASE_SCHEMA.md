# 🗄️ DineFlow AI - Database Schema & Data Dictionary

## 1. Entity-Relationship Overview

The DineFlow AI database is designed around a relational core supporting multi-tenancy, transactional consistency, and auditability.

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│   restaurants   │◀──────│      users      │       │     coupons     │
└─────────────────┘       └─────────────────┘       └─────────────────┘
         │                         │                         │
         │ (1:N)                   │ (1:N)                   │ (1:N)
         ▼                         ▼                         ▼
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     tables      │◀──────│     orders      │──────▶│   order_items   │
└─────────────────┘       └─────────────────┘       └─────────────────┘
         │                         │                         │
         │                         ▼                         ▼
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│  waiter_calls   │       │    customers    │       │   menu_items    │
└─────────────────┘       └─────────────────┘       └─────────────────┘
```

---

## 2. Table Specifications

### 2.1 `restaurants`
| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | VARCHAR(36) | NO | Primary key (UUID / string) |
| `name` | VARCHAR(191) | NO | Restaurant business name |
| `tagline` | VARCHAR(255) | YES | Marketing tagline |
| `address` | TEXT | YES | Physical address |
| `phone` | VARCHAR(30) | YES | Official contact phone |
| `email` | VARCHAR(191) | YES | Official contact email |
| `gst_number` | VARCHAR(50) | YES | Tax registration identifier |
| `tax_percentage` | DECIMAL(5,2) | NO | Default GST rate (5.00%) |
| `service_charge_percentage` | DECIMAL(5,2) | NO | Service charge rate (5.00%) |
| `is_active` | BOOLEAN | NO | Restaurant tenant active status |
| `created_at`, `updated_at` | TIMESTAMP | YES | Standard timestamps |

### 2.2 `menu_items`
| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | Primary key (Auto-increment) |
| `restaurant_id` | VARCHAR(36) | NO | Multi-tenant foreign key |
| `name` | VARCHAR(191) | NO | Item display name |
| `description` | TEXT | YES | Detailed item description |
| `category` | VARCHAR(100) | NO | Category (e.g. Mains, Breads, Drinks) |
| `price` | DECIMAL(10,2) | NO | Base price in INR |
| `image` | VARCHAR(500) | YES | Food thumbnail URL / asset path |
| `is_available` | BOOLEAN | NO | In stock / Available flag |
| `is_veg` | BOOLEAN | NO | Vegetarian indicator |
| `rating` | DECIMAL(3,2) | YES | Customer rating average (e.g. 4.8) |
| `created_at`, `updated_at` | TIMESTAMP | YES | Timestamps |

### 2.3 `orders`
| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | Primary key (Order number) |
| `restaurant_id` | VARCHAR(36) | NO | Tenant scope |
| `table_id` | VARCHAR(36) | NO | Dining table identifier |
| `customer_id` | BIGINT UNSIGNED | YES | Optional loyalty customer relation |
| `order_type` | ENUM | NO | `dine_in`, `takeaway`, `delivery` |
| `status` | ENUM | NO | `pending`, `preparing`, `ready`, `served`, `completed`, `cancelled` |
| `payment_status` | ENUM | NO | `unpaid`, `paid`, `refunded` |
| `payment_method` | ENUM | NO | `cash`, `upi`, `card`, `split` |
| `subtotal` | DECIMAL(10,2) | NO | Sum of item lines before tax |
| `discount_amount` | DECIMAL(10,2) | NO | Applied coupon deduction |
| `tax_amount` | DECIMAL(10,2) | NO | GST tax total (5%) |
| `service_charge` | DECIMAL(10,2) | NO | Service charge total (5%) |
| `total_amount` | DECIMAL(10,2) | NO | Net payable grand total |
| `special_instructions` | TEXT | YES | Chef cooking notes |
| `created_at`, `updated_at` | TIMESTAMP | YES | Timestamps |

### 2.4 `order_items`
| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | Primary key |
| `order_id` | BIGINT UNSIGNED | NO | Foreign key references `orders.id` |
| `menu_item_id` | BIGINT UNSIGNED | YES | Foreign key references `menu_items.id` |
| `name` | VARCHAR(191) | NO | Snapshot item name |
| `price` | DECIMAL(10,2) | NO | Snapshot unit price |
| `quantity` | INT UNSIGNED | NO | Ordered quantity |
| `created_at`, `updated_at` | TIMESTAMP | YES | Timestamps |

### 2.5 `tables`
| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | VARCHAR(36) | NO | Primary key |
| `restaurant_id` | VARCHAR(36) | NO | Tenant scope |
| `table_number` | VARCHAR(50) | NO | Display label (e.g. `T-01`) |
| `capacity` | INT UNSIGNED | NO | Seat capacity (e.g. 4) |
| `section` | VARCHAR(100) | YES | Area name (e.g. `AC Hall`) |
| `status` | ENUM | NO | `available`, `occupied`, `reserved` |
| `qr_code_url` | VARCHAR(500) | YES | Customer menu access link |
