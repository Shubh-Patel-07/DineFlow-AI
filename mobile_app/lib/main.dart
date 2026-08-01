import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'screens/pos_screen.dart';
import 'screens/kds_screen.dart';
import 'screens/analytics_screen.dart';

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
        ],
      ),
    );
  }
}

class WaiterPosTab extends StatelessWidget {
  const WaiterPosTab({super.key});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            '⚡ Quick Mobile Order',
            style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
          ),
          const SizedBox(height: 4),
          const Text('Select table and touch items to place KOT', style: TextStyle(color: Colors.grey, fontSize: 12)),
          const SizedBox(height: 16),
          
          // Table Selector Chips
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: List.generate(6, (index) {
                final isSelected = index == 0;
                return Container(
                  margin: const EdgeInsets.only(right: 8),
                  child: ChoiceChip(
                    label: Text('T-${index + 1} ${index == 1 ? "(Occupied)" : ""}'),
                    selected: isSelected,
                    selectedColor: const Color(0xFF6366F1),
                    backgroundColor: const Color(0xFF131A35),
                    labelStyle: TextStyle(color: isSelected ? Colors.white : Colors.grey),
                    onSelected: (val) {},
                  ),
                );
              }),
            ),
          ),
          const SizedBox(height: 20),

          // Menu Category Cards Grid
          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            childAspectRatio: 1.3,
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            children: [
              _buildDishTile('Paneer Butter Masala', '₹260', 'North Indian', Colors.orange),
              _buildDishTile('Margherita Pizza', '₹320', 'Pizza', Colors.green),
              _buildDishTile('Cold Coffee Shake', '₹140', 'Beverages', Colors.blue),
              _buildDishTile('Veg Hakka Noodles', '₹190', 'Chinese', Colors.purple),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildDishTile(String name, String price, String category, Color accentColor) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFF131A35),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white10),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: accentColor.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(category, style: TextStyle(color: accentColor, fontSize: 10, fontWeight: FontWeight.bold)),
              ),
              const Icon(Icons.add_circle, color: Color(0xFF6366F1), size: 22),
            ],
          ),
          Text(name, maxLines: 2, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.white)),
          Text(price, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: Color(0xFF10B981))),
        ],
      ),
    );
  }
}

class KitchenKdsTab extends StatelessWidget {
  const KitchenKdsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Text('🍳 Live Kitchen KOT Queue', style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
        const SizedBox(height: 12),
        _buildKotCard('KOT #1042', 'Table 3', '2x Paneer Masala, 4x Butter Naan', '12 mins ago', Colors.amber),
        _buildKotCard('KOT #1043', 'Takeaway #12', '1x Margherita Pizza', '4 mins ago', Colors.green),
      ],
    );
  }

  Widget _buildKotCard(String kotId, String table, String items, String time, Color statusColor) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF131A35),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: statusColor.withOpacity(0.5)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(kotId, style: TextStyle(fontWeight: FontWeight.bold, color: statusColor, fontSize: 16)),
              Text(table, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
            ],
          ),
          const SizedBox(height: 8),
          Text(items, style: const TextStyle(color: Colors.white70, fontSize: 13)),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(time, style: const TextStyle(color: Colors.grey, fontSize: 11)),
              ElevatedButton(
                onPressed: () {},
                style: ElevatedButton.styleFrom(backgroundColor: statusColor, foregroundColor: Colors.black),
                child: const Text('Mark Ready', style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class OwnerAnalyticsTab extends StatelessWidget {
  const OwnerAnalyticsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Text('📊 Owner Real-Time Sales', style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(child: _buildStatCard('Today Revenue', '₹ 24,850', Colors.green, Icons.monetization_on)),
            const SizedBox(width: 12),
            Expanded(child: _buildStatCard('Total Orders', '48 Bills', Colors.indigo, Icons.receipt_long)),
          ],
        ),
      ],
    );
  }

  Widget _buildStatCard(String title, String val, Color iconColor, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF131A35),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white10),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: iconColor, size: 28),
          const SizedBox(height: 12),
          Text(title, style: const TextStyle(color: Colors.grey, fontSize: 11)),
          const SizedBox(height: 4),
          Text(val, style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
        ],
      ),
    );
  }
}
