// DineFlow AI Mobile App API Configuration
class ApiConfig {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  static const String webBaseUrl = 'http://127.0.0.1:8000';

  static const String loginUrl = '$webBaseUrl/login';
  static const String pinLoginUrl = '$webBaseUrl/pin-login';
  static const String logoutUrl = '$webBaseUrl/logout';

  static const String menuUrl = '$baseUrl/menu';
  static const String ordersUrl = '$baseUrl/orders';
  static const String autoApplyCouponUrl = '$baseUrl/coupons/auto-apply';
  static const String validateCouponUrl = '$baseUrl/coupons/validate';
  static const String waiterCallsUrl = '$baseUrl/waiter-calls';
  static const String feedbackUrl = '$baseUrl/feedback';
  static const String reservationsUrl = '$baseUrl/reservations';

  static const String tablesUrl = '$baseUrl/tables';
  static const String expensesUrl = '$baseUrl/expenses';
  static const String staffUrl = '$baseUrl/staff';
  static const String settingsUrl = '$baseUrl/restaurants';
  static const String analyticsUrl = '$baseUrl/analytics';
}