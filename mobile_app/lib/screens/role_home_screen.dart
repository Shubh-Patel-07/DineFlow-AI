import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';
import 'customer_menu_screen.dart';
import 'pos_screen.dart';
import 'kds_screen.dart';
import 'expenses_screen.dart';
import 'staff_screen.dart';
import 'settings_screen.dart';
import 'analytics_screen.dart';
import 'login_screen.dart';

class RoleHomeScreen extends StatefulWidget {
  final UserModel? user;
  const RoleHomeScreen({super.key, this.user});

  @override
  State<RoleHomeScreen> createState() => _RoleHomeScreenState();
}

class _RoleHomeScreenState extends State<RoleHomeScreen> {
  int _selectedIndex = 0;

  @override
  Widget build(BuildContext context) {
    final role = widget.user?.role ?? 'customer';

    // Configure role screens
    List<Widget> screens = [];
    List<BottomNavigationBarItem> navItems = [];

    if (role == 'admin' || role == 'manager') {
      screens = [
        const OwnerAnalyticsScreen(),
        const WaiterPosScreen(),
        const KitchenKdsScreen(),
        const ExpensesScreen(),
        const StaffScreen(),
        const SettingsScreen(),
      ];
      navItems = const [
        BottomNavigationBarItem(icon: Icon(Icons.analytics_outlined), label: 'Analytics'),
        BottomNavigationBarItem(icon: Icon(Icons.point_of_sale), label: 'POS'),
        BottomNavigationBarItem(icon: Icon(Icons.restaurant), label: 'KDS'),
        BottomNavigationBarItem(icon: Icon(Icons.account_balance_wallet_outlined), label: 'Expenses'),
        BottomNavigationBarItem(icon: Icon(Icons.people_outline), label: 'Staff'),
        BottomNavigationBarItem(icon: Icon(Icons.settings_outlined), label: 'Settings'),
      ];
    } else if (role == 'cashier') {
      screens = [
        const WaiterPosScreen(),
        const KitchenKdsScreen(),
        const ExpensesScreen(),
      ];
      navItems = const [
        BottomNavigationBarItem(icon: Icon(Icons.point_of_sale), label: 'POS Billing'),
        BottomNavigationBarItem(icon: Icon(Icons.restaurant), label: 'KDS Orders'),
        BottomNavigationBarItem(icon: Icon(Icons.account_balance_wallet_outlined), label: 'Expenses'),
      ];
    } else if (role == 'kitchen') {
      screens = [
        const KitchenKdsScreen(),
      ];
      navItems = const [
        BottomNavigationBarItem(icon: Icon(Icons.restaurant), label: 'Live KDS'),
      ];
    } else if (role == 'waiter') {
      screens = [
        const WaiterPosScreen(),
        const KitchenKdsScreen(),
      ];
      navItems = const [
        BottomNavigationBarItem(icon: Icon(Icons.table_restaurant), label: 'Table Orders'),
        BottomNavigationBarItem(icon: Icon(Icons.restaurant), label: 'Kitchen KOT'),
      ];
    } else {
      // Customer Mode
      screens = [
        const CustomerMenuScreen(),
      ];
      navItems = const [
        BottomNavigationBarItem(icon: Icon(Icons.restaurant_menu), label: 'QR Digital Menu'),
      ];
    }

    final safeIndex = _selectedIndex < screens.length ? _selectedIndex : 0;

    return Scaffold(
      appBar: AppBar(
        backgroundColor: const Color(0xFF131A35),
        elevation: 0,
        title: Row(
          children: [
            Container(
              width: 32,
              height: 32,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(8),
                color: const Color(0xFF6366F1),
              ),
              child: const Icon(Icons.restaurant_menu, size: 20, color: Colors.white),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'DineFlow AI',
                  style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.white),
                ),
                Text(
                  'Role: ${role.toUpperCase()}',
                  style: const TextStyle(fontSize: 10, color: Color(0xFF818CF8), fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: Icon(widget.user != null ? Icons.logout : Icons.lock_outline, color: Colors.white70),
            onPressed: () async {
              if (widget.user != null) {
                await AuthService.logout();
              }
              if (mounted) {
                Navigator.of(context).pushReplacement(
                  MaterialPageRoute(builder: (_) => const LoginScreen()),
                );
              }
            },
          ),
        ],
      ),
      body: screens[safeIndex],
      bottomNavigationBar: navItems.length > 1
          ? BottomNavigationBar(
              currentIndex: safeIndex,
              onTap: (index) => setState(() => _selectedIndex = index),
              backgroundColor: const Color(0xFF131A35),
              selectedItemColor: const Color(0xFF6366F1),
              unselectedItemColor: Colors.grey,
              type: BottomNavigationBarType.fixed,
              items: navItems,
            )
          : null,
    );
  }
}