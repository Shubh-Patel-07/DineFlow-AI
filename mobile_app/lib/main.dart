import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'screens/pos_screen.dart';
import 'screens/kds_screen.dart';
import 'screens/analytics_screen.dart';
import 'screens/customer_menu_screen.dart';

void main() {
  runApp(const DineFlowMobileApp());
}

class DineFlowMobileApp extends StatelessWidget {
  const DineFlowMobileApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'DineFlow AI Mobile',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        brightness: Brightness.dark,
        scaffoldBackgroundColor: const Color(0xFF0B0F19),
        primaryColor: const Color(0xFF6366F1),
        colorScheme: const ColorScheme.dark(
          primary: Color(0xFF6366F1),
          surface: Color(0xFF131A35),
        ),
        textTheme: GoogleFonts.interTextTheme(ThemeData.dark().textTheme),
        useMaterial3: true,
      ),
      home: const MobileHomeScreen(),
    );
  }
}

class MobileHomeScreen extends StatefulWidget {
  const MobileHomeScreen({super.key});

  @override
  State<MobileHomeScreen> createState() => _MobileHomeScreenState();
}

class _MobileHomeScreenState extends State<MobileHomeScreen> {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const WaiterPosScreen(),
    const KitchenKdsScreen(),
    const OwnerAnalyticsScreen(),
    const CustomerMenuScreen(),
  ];

  @override
  Widget build(BuildContext context) {
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
            Text(
              'DineFlow AI Mobile',
              style: GoogleFonts.outfit(
                fontWeight: FontWeight.bold,
                fontSize: 18,
                color: Colors.white,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_none, color: Colors.white),
            onPressed: () {},
          ),
        ],
      ),
      body: _screens[_selectedIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: (index) => setState(() => _selectedIndex = index),
        backgroundColor: const Color(0xFF131A35),
        selectedItemColor: const Color(0xFF6366F1),
        unselectedItemColor: Colors.grey,
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.point_of_sale),
            label: 'POS & Waiter',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.soup_kitchen),
            label: 'Kitchen KDS',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.analytics),
            label: 'Analytics',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.restaurant_menu),
            label: 'Menu',
          ),
        ],
      ),
    );
  }
}

