import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/menu_item.dart';
import '../models/order.dart';
import '../models/expense_model.dart';
import '../models/user_model.dart';
import '../models/table_model.dart';
import '../models/restaurant_settings_model.dart';
import 'cache_service.dart';

class ApiService {
  static const Map<String, String> defaultHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  // --- MENU ---
  static Future<List<MenuItemModel>> fetchMenu([String restaurantId = '1']) async {
    try {
      final response = await http.get(Uri.parse('${ApiConfig.menuUrl}/restaurant/$restaurantId'), headers: defaultHeaders);
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['data'] != null) {
          final List itemsJson = data['data'];
          final items = itemsJson.map((json) => MenuItemModel.fromJson(json)).toList();
          await CacheService.cacheMenu(items);
          return items;
        }
      }
    } catch (_) {}

    final cached = await CacheService.getCachedMenu();
    if (cached != null && cached.isNotEmpty) return cached;

    return [
      MenuItemModel(id: 1, name: 'Paneer Butter Masala', description: 'Rich gravy cottage cheese', price: 240.0, category: 'Main Course', image: ''),
      MenuItemModel(id: 2, name: 'Margherita Pizza 10"', description: 'Fresh mozzarella & basil', price: 320.0, category: 'Pizza', image: ''),
      MenuItemModel(id: 3, name: 'Cold Coffee Frappe', description: 'Chilled espresso shake', price: 140.0, category: 'Beverages', image: ''),
      MenuItemModel(id: 4, name: 'Crispy Veg Spring Roll', description: 'Stuffed fried rolls', price: 180.0, category: 'Starters', image: ''),
      MenuItemModel(id: 5, name: 'Butter Garlic Naan', description: 'Tandoori bread with garlic', price: 60.0, category: 'Main Course', image: ''),
      MenuItemModel(id: 6, name: 'Gulab Jamun (2 Pcs)', description: 'Traditional hot dessert', price: 80.0, category: 'Desserts', image: ''),
    ];
  }

  // --- ORDERS ---
  static Future<Map<String, dynamic>> placeOrder({
    required String restaurantId,
    required String tableId,
    required List<MenuItemModel> cartItems,
    String? customerName,
    String? customerPhone,
    String? cookingNotes,
    String orderType = 'dine_in',
    String? couponCode,
  }) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.ordersUrl),
        headers: defaultHeaders,
        body: json.encode({
          'restaurant_id': restaurantId,
          'table_id': tableId,
          'customer_name': customerName ?? 'Mobile Guest',
          'customer_phone': customerPhone ?? '',
          'order_type': orderType,
          'coupon_code': couponCode,
          'notes': cookingNotes,
          'items': cartItems.where((i) => i.qty > 0).map((i) => {
            'menu_item_id': i.id,
            'quantity': i.qty,
            'price': i.price,
            'notes': i.cookingNote ?? '',
          }).toList(),
        }),
      );

      final data = json.decode(response.body);
      if (response.statusCode == 201 || response.statusCode == 200) {
        return {'success': true, 'data': data['data']};
      }
      return {'success': false, 'message': data['message'] ?? 'Failed to place order'};
    } catch (e) {
      return {'success': false, 'message': 'Network error. Please try again.'};
    }
  }

  static Future<List<OrderModel>> fetchKitchenOrders([String restaurantId = '1']) async {
    try {
      final response = await http.get(Uri.parse('${ApiConfig.baseUrl}/orders/restaurant/$restaurantId'), headers: defaultHeaders);
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

  static Future<bool> updateOrderStatus(int orderId, String newStatus) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/orders/$orderId/status'),
        headers: defaultHeaders,
        body: json.encode({'status': newStatus}),
      );
      return response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  static Future<bool> payOrder(int orderId, String paymentMethod, double amount) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/orders/$orderId/pay'),
        headers: defaultHeaders,
        body: json.encode({
          'payment_method': paymentMethod,
          'amount_paid': amount,
        }),
      );
      return response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  // --- TABLES ---
  static Future<List<TableModel>> fetchTables([String restaurantId = '1']) async {
    try {
      final response = await http.get(Uri.parse('${ApiConfig.tablesUrl}/restaurant/$restaurantId'), headers: defaultHeaders);
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['data'] != null) {
          final List list = data['data'];
          return list.map((json) => TableModel.fromJson(json)).toList();
        }
      }
    } catch (_) {}
    return List.generate(8, (i) => TableModel(id: i + 1, tableNumber: 'T-0${i + 1}', capacity: 4, status: i % 3 == 0 ? 'occupied' : 'available'));
  }

  // --- EXPENSES ---
  static Future<List<ExpenseModel>> fetchExpenses([String restaurantId = '1']) async {
    try {
      final response = await http.get(Uri.parse('${ApiConfig.expensesUrl}/restaurant/$restaurantId'), headers: defaultHeaders);
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['data'] != null) {
          final List list = data['data'];
          return list.map((json) => ExpenseModel.fromJson(json)).toList();
        }
      }
    } catch (_) {}
    return [];
  }

  static Future<bool> addExpense(ExpenseModel expense, [String restaurantId = '1']) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.expensesUrl),
        headers: defaultHeaders,
        body: json.encode({
          'restaurant_id': restaurantId,
          'title': expense.title,
          'amount': expense.amount,
          'category': expense.category,
          'payment_method': expense.paymentMethod,
          'date': expense.date,
          'notes': expense.notes ?? '',
        }),
      );
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  static Future<bool> deleteExpense(int id) async {
    try {
      final response = await http.delete(Uri.parse('${ApiConfig.expensesUrl}/$id'), headers: defaultHeaders);
      return response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  // --- STAFF ---
  static Future<List<UserModel>> fetchStaff([String restaurantId = '1']) async {
    try {
      final response = await http.get(Uri.parse('${ApiConfig.staffUrl}/restaurant/$restaurantId'), headers: defaultHeaders);
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['data'] != null) {
          final List list = data['data'];
          return list.map((json) => UserModel.fromJson(json)).toList();
        }
      }
    } catch (_) {}
    return [];
  }

  static Future<bool> addStaff(String name, String role, String pin, [String restaurantId = '1']) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.staffUrl),
        headers: defaultHeaders,
        body: json.encode({
          'restaurant_id': restaurantId,
          'name': name,
          'role': role,
          'numeric_pin': pin,
        }),
      );
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  static Future<bool> deleteStaff(int id) async {
    try {
      final response = await http.delete(Uri.parse('${ApiConfig.staffUrl}/$id'), headers: defaultHeaders);
      return response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  // --- SETTINGS ---
  static Future<RestaurantSettingsModel?> fetchSettings([String restaurantId = '1']) async {
    try {
      final response = await http.get(Uri.parse('${ApiConfig.settingsUrl}/$restaurantId/settings'), headers: defaultHeaders);
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['data'] != null) {
          final settings = RestaurantSettingsModel.fromJson(data['data']);
          await CacheService.cacheSettings(settings);
          return settings;
        }
      }
    } catch (_) {}
    return CacheService.getCachedSettings();
  }

  // --- WAITER CALL ---
  static Future<bool> callWaiter(String tableId, String requestType, [String restaurantId = '1']) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.waiterCallsUrl),
        headers: defaultHeaders,
        body: json.encode({
          'restaurant_id': restaurantId,
          'table_id': tableId,
          'request_type': requestType,
        }),
      );
      return response.statusCode == 200 || response.statusCode == 201;
    } catch (_) {
      return false;
    }
  }
}