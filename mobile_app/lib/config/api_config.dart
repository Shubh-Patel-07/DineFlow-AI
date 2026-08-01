// DineFlow AI Mobile App API Configuration
class ApiConfig {
  static const String baseUrl = 'http://10.0.2.2:8000/api'; // Android Emulator default / change to server IP
  
  static const String loginUrl = '$baseUrl/auth/login';
  static const String menuUrl = '$baseUrl/menu';
  static const String ordersUrl = '$baseUrl/orders';
  static const String autoApplyCouponUrl = '$baseUrl/coupons/auto-apply';
  static const String tablesUrl = '$baseUrl/tables';
  static const String analyticsUrl = '$baseUrl/analytics/sales/1';
}
