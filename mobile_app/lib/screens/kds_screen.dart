import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/order.dart';
import '../services/api_service.dart';

class KitchenKdsScreen extends StatefulWidget {
  const KitchenKdsScreen({super.key});

  @override
  State<KitchenKdsScreen> createState() => _KitchenKdsScreenState();
}

class _KitchenKdsScreenState extends State<KitchenKdsScreen> {
  List<OrderModel> _orders = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadKitchenQueue();
  }

  Future<void> _loadKitchenQueue() async {
    setState(() => _isLoading = true);
    final list = await ApiService.fetchKitchenOrders();
    setState(() {
      _orders = list;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: RefreshIndicator(
        onRefresh: _loadKitchenQueue,
        color: const Color(0xFF6366F1),
        child: _isLoading
            ? const Center(child: CircularProgressIndicator(color: Color(0xFF6366F1)))
            : _orders.isEmpty
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.soup_kitchen, size: 60, color: Colors.grey),
                        const SizedBox(height: 12),
                        Text('No active orders in Kitchen Queue', style: GoogleFonts.outfit(fontSize: 16, color: Colors.grey)),
                        const SizedBox(height: 8),
                        ElevatedButton.icon(
                          onPressed: _loadKitchenQueue,
                          icon: const Icon(Icons.refresh),
                          label: const Text('Refresh Kitchen Queue'),
                        ),
                      ],
                    ),
                  )
                : ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _orders.length,
                    itemBuilder: (context, index) {
                      final order = _orders[index];
                      return _buildKotCard(order);
                    },
                  ),
      ),
    );
  }

  Widget _buildKotCard(OrderModel order) {
    Color statusColor;
    String statusText;
    if (order.status == 'completed' || order.status == 'ready') {
      statusColor = const Color(0xFF10B981);
      statusText = 'READY TO SERVE';
    } else if (order.status == 'preparing') {
      statusColor = const Color(0xFFF59E0B);
      statusText = 'COOKING...';
    } else {
      statusColor = const Color(0xFFEF4444);
      statusText = 'NEW KOT';
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF131A35),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: statusColor.withOpacity(0.6), width: 1.5),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.2), blurRadius: 8, offset: const Offset(0, 4)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(statusText, style: TextStyle(color: statusColor, fontWeight: FontWeight.bold, fontSize: 11)),
              ),
              Text(order.tableName, style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.white)),
            ],
          ),
          const SizedBox(height: 10),
          Text('Order ${order.orderNumber}', style: const TextStyle(color: Colors.grey, fontSize: 12)),
          const Divider(color: Colors.white10, height: 16),
          
          // Items List
          ...order.items.map((item) => Padding(
                padding: const EdgeInsets.symmetric(vertical: 2),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      '• ${item['name'] ?? 'Item'}',
                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600, fontSize: 13),
                    ),
                    Text(
                      'x${item['quantity'] ?? 1}',
                      style: const TextStyle(color: Color(0xFF6366F1), fontWeight: FontWeight.bold, fontSize: 13),
                    ),
                  ],
                ),
              )),

          const SizedBox(height: 14),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(order.createdAt, style: const TextStyle(color: Colors.grey, fontSize: 10)),
              ElevatedButton(
                onPressed: () async {
                  final nextStatus = order.status == 'pending' ? 'preparing' : 'ready';
                  await ApiService.updateOrderStatus(order.id, nextStatus);
                  _loadKitchenQueue();
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: statusColor,
                  foregroundColor: Colors.black,
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                ),
                child: Text(
                  order.status == 'pending' ? 'Start Cooking' : 'Mark Ready',
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
