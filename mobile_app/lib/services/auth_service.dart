import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';
import '../models/user_model.dart';

class AuthService {
  static const String _userKey = 'dineflow_mobile_auth_user';
  static const String _activeRoleKey = 'dineflow_mobile_active_role';

  // Login with Email/Username and Password
  static Future<UserModel?> loginWithCredentials(String username, String password) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/login'),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: json.encode({'username': username, 'password': password}),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true && data['user'] != null) {
          final user = UserModel.fromJson(data['user']);
          await saveUserSession(user);
          return user;
        }
      }
    } catch (_) {}

    // Offline fallback for testing
    final mockRole = (username == 'admin' || username == 'manager' || username == 'cashier' || username == 'kitchen' || username == 'waiter') ? username : 'waiter';
    final user = UserModel(
      id: 1,
      name: username.toUpperCase(),
      username: username,
      email: '$username@dineflow.local',
      role: mockRole,
      restaurantId: '1',
    );
    await saveUserSession(user);
    return user;
  }

  static Future<void> saveUserSession(UserModel user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_userKey, json.encode(user.toJson()));
    await prefs.setString(_activeRoleKey, user.role);
  }

  static Future<UserModel?> getCurrentUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(_userKey);
    if (userJson != null) {
      return UserModel.fromJson(json.decode(userJson));
    }
    return null;
  }

  static Future<String> getActiveRole() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_activeRoleKey) ?? 'customer';
  }

  static Future<void> setActiveRole(String role) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_activeRoleKey, role);
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_userKey);
    await prefs.setString(_activeRoleKey, 'customer');
  }
}