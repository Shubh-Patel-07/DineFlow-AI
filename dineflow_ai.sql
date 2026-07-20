-- DineFlow AI MySQL Database Export for phpMyAdmin
-- Created for College Presentation / Viva

CREATE DATABASE IF NOT EXISTS `dineflow_ai` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dineflow_ai`;

-- --------------------------------------------------------
-- Table structure and data for `users`
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `username`, `numeric_pin`, `role`, `restaurant_id`) VALUES ('1', 'Admin User', 'admin@dineflow.com', NULL, '$2y$12$0tMj0902Tl6lu4fcZznG0eMVEvQplJhnfMuJgBJKnaz2kPl0T4b2W', NULL, '2026-07-19 18:36:34', '2026-07-19 18:36:34', 'admin', '1234', 'admin', '1');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `username`, `numeric_pin`, `role`, `restaurant_id`) VALUES ('2', 'Manager', 'manager@dineflow.com', NULL, '$2y$12$180PZZGqUacjI8Yyw9kE6uSVBgwiMK4W4YAtsNTEp6Hlr9LWbkNGK', NULL, '2026-07-19 18:36:34', '2026-07-19 18:36:34', 'manager', '5678', 'manager', '1');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `username`, `numeric_pin`, `role`, `restaurant_id`) VALUES ('3', 'Cashier', 'cashier@dineflow.com', NULL, '$2y$12$009FCSA5wOqIKiXM1nekaOENHapJZBDkNbE2AoqUUd2WOYfKYAT8u', NULL, '2026-07-19 18:36:34', '2026-07-19 18:36:34', 'cashier', '9999', 'cashier', '1');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `username`, `numeric_pin`, `role`, `restaurant_id`) VALUES ('4', 'Head Chef', 'chef@dineflow.com', NULL, '$2y$12$nbFORNmkHpWxHCNd1wz6buTiuTYbdL6errDs9WnpwQRaqyncVCYo2', NULL, '2026-07-19 18:36:34', '2026-07-19 18:36:34', 'kitchen', '2580', 'kitchen', '1');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `username`, `numeric_pin`, `role`, `restaurant_id`) VALUES ('5', 'Waiter John', 'waiter@dineflow.com', NULL, '$2y$12$KtHd/.qL8jdAhMy09innt.wwdITuGMsbk/joYWD4JTELebA5m1m2m', NULL, '2026-07-19 18:36:35', '2026-07-19 18:36:35', 'waiter', '3690', 'waiter', '1');

-- --------------------------------------------------------
-- Table structure and data for `restaurants`
-- --------------------------------------------------------

INSERT INTO `restaurants` (`id`, `name`, `address`, `phone`, `logo`, `created_at`, `updated_at`) VALUES ('1', 'Ultimate DineFlow AI POS House', '456 Gourmet Boulevard', '555-0199', '', '2026-07-19 18:36:35', '2026-07-19 18:36:35');

-- --------------------------------------------------------
-- Table structure and data for `tables`
-- --------------------------------------------------------

INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('1', '1', 'T-01', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D1', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('2', '1', 'T-02', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D2', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('3', '1', 'T-03', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D3', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('4', '1', 'T-04', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D4', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('5', '1', 'T-05', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D5', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('6', '1', 'T-06', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D6', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('7', '1', 'T-07', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D7', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('8', '1', 'T-08', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D8', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('9', '1', 'T-09', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D9', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('10', '1', 'T-10', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D10', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('11', '1', 'T-11', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D11', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code_url`, `status`, `created_at`, `updated_at`) VALUES ('12', '1', 'T-12', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http%3A%2F%2F192.168.1.4%3A8000%2Fcustomer%2Fmenu%3FrestaurantId%3D1%26tableId%3D12', 'active', '2026-07-19 18:36:35', '2026-07-19 18:36:35');

-- --------------------------------------------------------
-- Table structure and data for `menu_items`
-- --------------------------------------------------------

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('1', '1', 'Paneer Butter Masala', 'Paneer Butter Masala served fresh.', '240', 'Mains', 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('2', '1', 'Chicken Tikka Masala', 'Chicken Tikka Masala served fresh.', '320', 'Mains', 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=500&auto=format&fit=crop&q=60', '1', '0', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('3', '1', 'Garlic Naan', 'Garlic Naan served fresh.', '60', 'Breads', 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('4', '1', 'Veg Spring Rolls', 'Veg Spring Rolls served fresh.', '180', 'Starters', 'https://images.unsplash.com/photo-1544025162-d76694265947?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('5', '1', 'Chicken Biryani', 'Chicken Biryani served fresh.', '380', 'Rice', 'https://images.unsplash.com/photo-1633945274405-b6c8069047b0?w=500&auto=format&fit=crop&q=60', '1', '0', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('6', '1', 'Mango Lassi', 'Mango Lassi served fresh.', '120', 'Beverages', 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('7', '1', 'Gulab Jamun', 'Gulab Jamun served fresh.', '80', 'Desserts', 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('8', '1', 'Dal Tadka', 'Dal Tadka served fresh.', '180', 'Mains', 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('9', '1', 'Fish Fry', 'Fish Fry served fresh.', '280', 'Starters', 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=500&auto=format&fit=crop&q=60', '1', '0', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('10', '1', 'Laccha Paratha', 'Laccha Paratha served fresh.', '70', 'Breads', 'https://images.unsplash.com/photo-1627834377411-8da5f4f09de8?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('11', '1', 'Veg Biryani', 'Veg Biryani served fresh.', '280', 'Rice', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');
INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `is_veg`, `created_at`, `updated_at`) VALUES ('12', '1', 'Cold Coffee', 'Cold Coffee served fresh.', '140', 'Beverages', 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=500&auto=format&fit=crop&q=60', '1', '1', '2026-07-19 18:36:35', '2026-07-19 18:36:35');

-- --------------------------------------------------------
-- Table structure and data for `orders`
-- --------------------------------------------------------

INSERT INTO `orders` (`id`, `restaurant_id`, `table_id`, `total_amount`, `status`, `payment_status`, `payment_method`, `special_instructions`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '3500', 'completed', 'paid', 'cash', '', '2026-07-19 18:37:54', '2026-07-19 18:41:44');
INSERT INTO `orders` (`id`, `restaurant_id`, `table_id`, `total_amount`, `status`, `payment_status`, `payment_method`, `special_instructions`, `created_at`, `updated_at`) VALUES ('2', '1', '1', '4970', 'completed', 'paid', 'cash', '', '2026-07-19 18:41:35', '2026-07-19 19:39:50');
INSERT INTO `orders` (`id`, `restaurant_id`, `table_id`, `total_amount`, `status`, `payment_status`, `payment_method`, `special_instructions`, `created_at`, `updated_at`) VALUES ('3', '1', '1', '1170', 'completed', 'paid', 'cash', '', '2026-07-19 19:47:31', '2026-07-19 21:34:28');
INSERT INTO `orders` (`id`, `restaurant_id`, `table_id`, `total_amount`, `status`, `payment_status`, `payment_method`, `special_instructions`, `created_at`, `updated_at`) VALUES ('4', '1', '1', '870', 'completed', 'paid', 'cash', '', '2026-07-19 19:51:52', '2026-07-19 21:34:52');
INSERT INTO `orders` (`id`, `restaurant_id`, `table_id`, `total_amount`, `status`, `payment_status`, `payment_method`, `special_instructions`, `created_at`, `updated_at`) VALUES ('5', '1', '1', '2390', 'completed', 'paid', 'cash', '', '2026-07-19 19:54:53', '2026-07-19 21:34:45');
INSERT INTO `orders` (`id`, `restaurant_id`, `table_id`, `total_amount`, `status`, `payment_status`, `payment_method`, `special_instructions`, `created_at`, `updated_at`) VALUES ('6', '1', '1', '1250', 'completed', 'paid', 'cash', '', '2026-07-19 19:56:53', '2026-07-19 21:34:41');
INSERT INTO `orders` (`id`, `restaurant_id`, `table_id`, `total_amount`, `status`, `payment_status`, `payment_method`, `special_instructions`, `created_at`, `updated_at`) VALUES ('7', '1', '1', '3710', 'completed', 'paid', 'cash', '', '2026-07-19 19:59:10', '2026-07-19 21:34:36');
INSERT INTO `orders` (`id`, `restaurant_id`, `table_id`, `total_amount`, `status`, `payment_status`, `payment_method`, `special_instructions`, `created_at`, `updated_at`) VALUES ('8', '1', '7', '910', 'completed', 'paid', 'cash', '', '2026-07-19 19:59:53', '2026-07-19 21:34:32');

-- --------------------------------------------------------
-- Table structure and data for `order_items`
-- --------------------------------------------------------

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('1', '1', '3', 'Garlic Naan', '1', '60', '2026-07-19 18:37:54', '2026-07-19 18:37:54');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('2', '1', '4', 'Veg Spring Rolls', '3', '180', '2026-07-19 18:37:54', '2026-07-19 18:38:09');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('3', '1', '5', 'Chicken Biryani', '3', '380', '2026-07-19 18:37:54', '2026-07-19 18:38:09');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('4', '1', '6', 'Mango Lassi', '3', '120', '2026-07-19 18:37:54', '2026-07-19 18:38:18');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('5', '1', '10', 'Laccha Paratha', '2', '70', '2026-07-19 18:37:54', '2026-07-19 18:38:09');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('6', '1', '11', 'Veg Biryani', '3', '280', '2026-07-19 18:37:54', '2026-07-19 18:38:18');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('7', '1', '12', 'Cold Coffee', '3', '140', '2026-07-19 18:37:54', '2026-07-19 18:38:18');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('8', '2', '3', 'Garlic Naan', '3', '60', '2026-07-19 18:41:35', '2026-07-19 18:42:08');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('9', '2', '4', 'Veg Spring Rolls', '3', '180', '2026-07-19 18:41:35', '2026-07-19 18:42:08');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('10', '2', '5', 'Chicken Biryani', '3', '380', '2026-07-19 18:41:35', '2026-07-19 18:42:08');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('11', '2', '6', 'Mango Lassi', '1', '120', '2026-07-19 18:41:35', '2026-07-19 18:41:35');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('12', '2', '9', 'Fish Fry', '4', '280', '2026-07-19 18:41:35', '2026-07-19 18:42:08');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('13', '2', '10', 'Laccha Paratha', '3', '70', '2026-07-19 18:41:35', '2026-07-19 18:42:08');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('14', '2', '11', 'Veg Biryani', '3', '280', '2026-07-19 18:41:35', '2026-07-19 18:42:08');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('15', '2', '12', 'Cold Coffee', '1', '140', '2026-07-19 18:41:35', '2026-07-19 18:41:35');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('16', '2', '2', 'Chicken Tikka Masala', '1', '320', '2026-07-19 18:41:51', '2026-07-19 18:41:51');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('17', '2', '8', 'Dal Tadka', '2', '180', '2026-07-19 18:41:51', '2026-07-19 18:42:08');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('18', '3', '4', 'Veg Spring Rolls', '1', '180', '2026-07-19 19:47:31', '2026-07-19 19:47:31');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('19', '3', '5', 'Chicken Biryani', '1', '380', '2026-07-19 19:47:31', '2026-07-19 19:47:31');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('20', '3', '6', 'Mango Lassi', '1', '120', '2026-07-19 19:47:31', '2026-07-19 19:47:31');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('21', '3', '10', 'Laccha Paratha', '1', '70', '2026-07-19 19:47:31', '2026-07-19 19:47:31');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('22', '3', '11', 'Veg Biryani', '1', '280', '2026-07-19 19:47:31', '2026-07-19 19:47:31');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('23', '3', '12', 'Cold Coffee', '1', '140', '2026-07-19 19:47:31', '2026-07-19 19:47:31');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('24', '4', '3', 'Garlic Naan', '1', '60', '2026-07-19 19:51:52', '2026-07-19 19:51:52');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('25', '4', '4', 'Veg Spring Rolls', '1', '180', '2026-07-19 19:51:52', '2026-07-19 19:51:52');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('26', '4', '9', 'Fish Fry', '1', '280', '2026-07-19 19:51:52', '2026-07-19 19:51:52');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('27', '4', '10', 'Laccha Paratha', '1', '70', '2026-07-19 19:51:52', '2026-07-19 19:51:52');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('28', '4', '11', 'Veg Biryani', '1', '280', '2026-07-19 19:51:52', '2026-07-19 19:51:52');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('29', '5', '2', 'Chicken Tikka Masala', '1', '320', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('30', '5', '3', 'Garlic Naan', '1', '60', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('31', '5', '4', 'Veg Spring Rolls', '1', '180', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('32', '5', '5', 'Chicken Biryani', '2', '380', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('33', '5', '6', 'Mango Lassi', '1', '120', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('34', '5', '8', 'Dal Tadka', '1', '180', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('35', '5', '9', 'Fish Fry', '1', '280', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('36', '5', '10', 'Laccha Paratha', '1', '70', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('37', '5', '11', 'Veg Biryani', '1', '280', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('38', '5', '12', 'Cold Coffee', '1', '140', '2026-07-19 19:54:53', '2026-07-19 19:54:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('39', '6', '3', 'Garlic Naan', '1', '60', '2026-07-19 19:56:53', '2026-07-19 19:56:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('40', '6', '4', 'Veg Spring Rolls', '1', '180', '2026-07-19 19:56:53', '2026-07-19 19:56:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('41', '6', '5', 'Chicken Biryani', '1', '380', '2026-07-19 19:56:53', '2026-07-19 19:56:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('42', '6', '9', 'Fish Fry', '1', '280', '2026-07-19 19:56:53', '2026-07-19 19:56:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('43', '6', '10', 'Laccha Paratha', '1', '70', '2026-07-19 19:56:53', '2026-07-19 19:56:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('44', '6', '11', 'Veg Biryani', '1', '280', '2026-07-19 19:56:53', '2026-07-19 19:56:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('45', '7', '5', 'Chicken Biryani', '3', '380', '2026-07-19 19:59:10', '2026-07-19 19:59:10');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('46', '7', '6', 'Mango Lassi', '3', '120', '2026-07-19 19:59:10', '2026-07-19 19:59:10');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('47', '7', '11', 'Veg Biryani', '3', '280', '2026-07-19 19:59:10', '2026-07-19 19:59:10');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('48', '7', '12', 'Cold Coffee', '2', '140', '2026-07-19 19:59:10', '2026-07-19 19:59:10');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('49', '7', '2', 'Chicken Tikka Masala', '1', '320', '2026-07-19 19:59:22', '2026-07-19 19:59:22');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('50', '7', '3', 'Garlic Naan', '1', '60', '2026-07-19 19:59:22', '2026-07-19 19:59:22');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('51', '7', '4', 'Veg Spring Rolls', '1', '180', '2026-07-19 19:59:22', '2026-07-19 19:59:22');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('52', '7', '8', 'Dal Tadka', '1', '180', '2026-07-19 19:59:22', '2026-07-19 19:59:22');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('53', '7', '9', 'Fish Fry', '1', '280', '2026-07-19 19:59:22', '2026-07-19 19:59:22');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('54', '7', '10', 'Laccha Paratha', '1', '70', '2026-07-19 19:59:22', '2026-07-19 19:59:22');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('55', '8', '4', 'Veg Spring Rolls', '1', '180', '2026-07-19 19:59:53', '2026-07-19 19:59:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('56', '8', '5', 'Chicken Biryani', '1', '380', '2026-07-19 19:59:53', '2026-07-19 19:59:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('57', '8', '10', 'Laccha Paratha', '1', '70', '2026-07-19 19:59:53', '2026-07-19 19:59:53');
INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES ('58', '8', '11', 'Veg Biryani', '1', '280', '2026-07-19 19:59:53', '2026-07-19 19:59:53');

-- --------------------------------------------------------
-- Table structure and data for `feedback`
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Table structure and data for `waiter_calls`
-- --------------------------------------------------------

INSERT INTO `waiter_calls` (`id`, `restaurant_id`, `table_id`, `status`, `created_at`, `updated_at`) VALUES ('1', '1', '1', 'resolved', '2026-07-19 19:53:14', '2026-07-19 19:53:27');
INSERT INTO `waiter_calls` (`id`, `restaurant_id`, `table_id`, `status`, `created_at`, `updated_at`) VALUES ('2', '1', '12', 'resolved', '2026-07-19 19:53:42', '2026-07-19 19:53:49');

