import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/menu_item.dart';
import '../models/order.dart';

class ApiService {
  // Fetch Menu Items from Laravel REST API
  static Future<List<MenuItemModel>> fetchMenu() async {
    try {
      final response = await http.get(Uri.parse(ApiConfig.menuUrl));
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['data'] != null) {
          final List itemsJson = data['data'];
          return itemsJson.map((json) => MenuItemModel.fromJson(json)).toList();
        }
      }
    } catch (e) {
      // Fallback offline mock menu items
    }
    return [
      MenuItemModel(id: 1, name: 'Paneer Butter Masala', description: 'Rich gravy cottage cheese', price: 260.0, category: 'Main Course', image: ''),
      MenuItemModel(id: 2, name: 'Margherita Pizza 10"', description: 'Fresh mozzarella & basil', price: 320.0, category: 'Pizza', image: ''),
      MenuItemModel(id: 3, name: 'Cold Coffee Frappe', description: 'Chilled espresso shake', price: 140.0, category: 'Beverages', image: ''),
      MenuItemModel(id: 4, name: 'Crispy Veg Spring Roll', description: 'Stuffed fried rolls', price: 180.0, category: 'Starters', image: ''),
      MenuItemModel(id: 5, name: 'Butter Garlic Naan', description: 'Tandoori bread with garlic', price: 60.0, category: 'Main Course', image: ''),
      MenuItemModel(id: 6, name: 'Sizzling Chocolate Brownie', description: 'Hot brownie with vanilla ice cream', price: 210.0, category: 'Desserts', image: ''),
    ];
  }

  // Auto-Apply Best Coupon via Laravel API
  static Future<Map<String, dynamic>?> checkAutoApplyCoupon({
    required double orderAmount,
    required bool isNewCustomer,
    String orderType = 'all',
  }) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.autoApplyCouponUrl),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({
          'order_amount': orderAmount,
          'is_new_customer': isNewCustomer,
          'order_type': orderType,
        }),
      );
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['data'] != null) {
          return data['data'];
        }
      }
    } catch (_) {}
    
    // Fallback new customer auto-apply
    if (isNewCustomer && orderAmount >= 200) {
      final discount = (orderAmount * 0.15).clamp(0, 150).toDouble();
      return {
        'coupon': {'code': 'WELCOME100', 'name': '15% New Customer Offer'},
        'discount': discount,
        'final_amount': orderAmount - discount,
      };
    }
    return null;
  }

  // Place Order from Mobile App
  static Future<bool> placeOrder({
    required int restaurantId,
    required int tableId,
    required List<MenuItemModel> cartItems,
    required double totalAmount,
    String? couponCode,
  }) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.ordersUrl),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({
          'restaurant_id': restaurantId,
          'table_id': tableId,
          'total_amount': totalAmount,
          'coupon_code': couponCode,
          'items': cartItems.where((i) => i.qty > 0).map((i) => i.toJson()).toList(),
        }),
      );
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (_) {
      return true; // Simulate local success
    }
  }

  // Fetch Live Kitchen KOT Queue
  static Future<List<OrderModel>> fetchKitchenOrders() async {
    try {
      final response = await http.get(Uri.parse('${ApiConfig.baseUrl}/orders/restaurant/1'));
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['data'] != null) {
          final List ordersJson = data['data'];
          return ordersJson.map((json) => OrderModel.fromJson(json)).toList();
        }
      }
    } catch (_) {}
    return [];
  }

  // Update Kitchen Order Status
  static Future<bool> updateOrderStatus(int orderId, String newStatus) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/orders/$orderId/status'),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({'status': newStatus}),
      );
      return response.statusCode == 200;
    } catch (_) {
      return true;
    }
  }
}
