import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class OwnerAnalyticsScreen extends StatelessWidget {
  const OwnerAnalyticsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('📊 Executive Dashboard', style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
                  const Text('Real-time restaurant sales & metrics', style: TextStyle(color: Colors.grey, fontSize: 12)),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(color: const Color(0xFF10B981).withOpacity(0.2), borderRadius: BorderRadius.circular(12)),
                child: const Row(
                  children: [
                    CircleAvatar(radius: 3, backgroundColor: Color(0xFF10B981)),
                    SizedBox(width: 4),
                    Text('LIVE', style: TextStyle(color: Color(0xFF10B981), fontWeight: FontWeight.bold, fontSize: 10)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // Stat Cards Grid
          Row(
            children: [
              Expanded(child: _buildMetricCard('Total Revenue', '₹ 28,450.00', Colors.green, Icons.account_balance_wallet)),
              const SizedBox(width: 10),
              Expanded(child: _buildMetricCard('Total Bills', '54 Orders', Colors.indigo, Icons.receipt)),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(child: _buildMetricCard('Avg Order Value', '₹ 526.85', Colors.amber, Icons.trending_up)),
              const SizedBox(width: 10),
              Expanded(child: _buildMetricCard('Active Tables', '6 / 10 Tables', Colors.cyan, Icons.table_bar)),
            ],
          ),

          const SizedBox(height: 20),
          Text('🔥 Top Selling Menu Items', style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
          const SizedBox(height: 10),

          _buildTopItemRow('1. Paneer Butter Masala', '34 orders', '₹ 8,840'),
          _buildTopItemRow('2. Margherita Pizza 10"', '28 orders', '₹ 8,960'),
          _buildTopItemRow('3. Cold Coffee Frappe', '22 orders', '₹ 3,080'),
          _buildTopItemRow('4. Crispy Spring Rolls', '18 orders', '₹ 3,240'),
        ],
      ),
    );
  }

  Widget _buildMetricCard(String label, String val, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFF131A35),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white10),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(height: 10),
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 11)),
          const SizedBox(height: 2),
          Text(val, style: GoogleFonts.outfit(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
        ],
      ),
    );
  }

  Widget _buildTopItemRow(String name, String orders, String total) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFF131A35),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(name, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 13)),
              Text(orders, style: const TextStyle(color: Colors.grey, fontSize: 11)),
            ],
          ),
          Text(total, style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF10B981), fontSize: 14)),
        ],
      ),
    );
  }
}
