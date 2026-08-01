import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/menu_item.dart';
import '../services/api_service.dart';

class WaiterPosScreen extends StatefulWidget {
  const WaiterPosScreen({super.key});

  @override
  State<WaiterPosScreen> createState() => _WaiterPosScreenState();
}

class _WaiterPosScreenState extends State<WaiterPosScreen> {
  List<MenuItemModel> _menuItems = [];
  bool _isLoading = true;
  String _selectedCategory = 'All';
  String _searchQuery = '';
  int _selectedTable = 1;
  bool _isNewCustomer = true;
  Map<String, dynamic>? _activeAutoCoupon;

  @override
  void initState() {
    super.initState();
    _loadMenu();
  }

  Future<void> _loadMenu() async {
    setState(() => _isLoading = true);
    final items = await ApiService.fetchMenu();
    setState(() {
      _menuItems = items;
      _isLoading = false;
    });
  }

  void _recalculateAutoCoupon() async {
    final subtotal = _calculateSubtotal();
    if (subtotal <= 0) {
      setState(() => _activeAutoCoupon = null);
      return;
    }
    final result = await ApiService.checkAutoApplyCoupon(
      orderAmount: subtotal,
      isNewCustomer: _isNewCustomer,
    );
    if (mounted) {
      setState(() => _activeAutoCoupon = result);
    }
  }

  double _calculateSubtotal() {
    return _menuItems.fold(0.0, (sum, item) => sum + (item.price * item.qty));
  }

  int _calculateTotalQty() {
    return _menuItems.fold(0, (sum, item) => sum + item.qty);
  }

  List<String> get _categories {
    final set = {'All'};
    for (var i in _menuItems) {
      set.add(i.category);
    }
    return set.toList();
  }

  List<MenuItemModel> get _filteredItems {
    return _menuItems.where((item) {
      final matchesCat = _selectedCategory == 'All' || item.category == _selectedCategory;
      final matchesSearch = item.name.toLowerCase().contains(_searchQuery.toLowerCase());
      return matchesCat && matchesSearch;
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    final subtotal = _calculateSubtotal();
    final discount = _activeAutoCoupon != null ? double.parse(_activeAutoCoupon!['discount'].toString()) : 0.0;
    final totalQty = _calculateTotalQty();
    final grandTotal = (subtotal - discount).clamp(0.0, double.infinity);

    return Scaffold(
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF6366F1)))
          : Column(
              children: [
                // Search Bar & Customer Selector Header
                Container(
                  padding: const EdgeInsets.all(12),
                  color: const Color(0xFF131A35),
                  child: Column(
                    children: [
                      TextField(
                        onChanged: (val) => setState(() => _searchQuery = val),
                        style: const TextStyle(color: Colors.white, fontSize: 13),
                        decoration: InputDecoration(
                          hintText: 'Search dishes...',
                          hintStyle: const TextStyle(color: Colors.grey),
                          prefixIcon: const Icon(Icons.search, color: Colors.grey, size: 20),
                          filled: true,
                          fillColor: const Color(0xFF0B0F19),
                          contentPadding: const EdgeInsets.symmetric(vertical: 0, horizontal: 12),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Expanded(
                            child: DropdownButtonFormField<int>(
                              value: _selectedTable,
                              decoration: const InputDecoration(
                                labelText: 'Select Table',
                                labelStyle: TextStyle(color: Colors.grey, fontSize: 11),
                                contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                              ),
                              dropdownColor: const Color(0xFF131A35),
                              items: List.generate(10, (i) => DropdownMenuItem(value: i + 1, child: Text('Table T-${i + 1}', style: const TextStyle(fontSize: 13, color: Colors.white)))),
                              onChanged: (val) => setState(() => _selectedTable = val ?? 1),
                            ),
                          ),
                          const SizedBox(width: 10),
                          FilterChip(
                            label: const Text('🆕 New Customer'),
                            selected: _isNewCustomer,
                            selectedColor: const Color(0xFF6366F1).withOpacity(0.3),
                            onSelected: (val) {
                              setState(() => _isNewCustomer = val);
                              _recalculateAutoCoupon();
                            },
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                // Category Chips
                Container(
                  height: 48,
                  padding: const EdgeInsets.symmetric(vertical: 6),
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    itemCount: _categories.length,
                    itemBuilder: (context, index) {
                      final cat = _categories[index];
                      final isSel = cat == _selectedCategory;
                      return Padding(
                        padding: const EdgeInsets.only(right: 6),
                        child: ChoiceChip(
                          label: Text(cat),
                          selected: isSel,
                          selectedColor: const Color(0xFF6366F1),
                          backgroundColor: const Color(0xFF131A35),
                          labelStyle: TextStyle(color: isSel ? Colors.white : Colors.grey, fontSize: 12, fontWeight: isSel ? FontWeight.bold : FontWeight.normal),
                          onSelected: (val) => setState(() => _selectedCategory = cat),
                        ),
                      );
                    },
                  ),
                ),

                // Menu Grid
                Expanded(
                  child: GridView.builder(
                    padding: const EdgeInsets.all(12),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 0.95,
                      crossAxisSpacing: 10,
                      mainAxisSpacing: 10,
                    ),
                    itemCount: _filteredItems.length,
                    itemBuilder: (context, index) {
                      final item = _filteredItems[index];
                      return _buildItemCard(item);
                    },
                  ),
                ),

                // Auto-Coupon Alert Banner
                if (_activeAutoCoupon != null)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    color: const Color(0xFFF59E0B).withOpacity(0.15),
                    child: Row(
                      children: [
                        const Icon(Icons.bolt, color: Color(0xFFF59E0B), size: 18),
                        const SizedBox(width: 6),
                        Expanded(
                          child: Text(
                            '⚡ Auto-Applied: ${_activeAutoCoupon!['coupon']['code']} (Saved ₹${discount.toStringAsFixed(1)})',
                            style: const TextStyle(color: Color(0xFFF59E0B), fontWeight: FontWeight.bold, fontSize: 11),
                          ),
                        ),
                      ],
                    ),
                  ),

                // Cart Summary Bottom Bar
                if (totalQty > 0)
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: const BoxDecoration(
                      color: Color(0xFF131A35),
                      borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
                    ),
                    child: SafeArea(
                      child: Row(
                        children: [
                          Expanded(
                            child: Column(
                              mainAxisSize: MainAxisSize.min,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('$totalQty Items • Table T-$_selectedTable', style: const TextStyle(color: Colors.grey, fontSize: 11)),
                                const SizedBox(height: 2),
                                Text(
                                  '₹ ${grandTotal.toStringAsFixed(2)}',
                                  style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: const Color(0xFF10B981)),
                                ),
                              ],
                            ),
                          ),
                          ElevatedButton.icon(
                            onPressed: () => _handlePlaceKot(grandTotal),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF6366F1),
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            icon: const Icon(Icons.send_rounded, size: 18),
                            label: const Text('Send to Kitchen KOT', style: TextStyle(fontWeight: FontWeight.bold)),
                          ),
                        ],
                      ),
                    ),
                  ),
              ],
            ),
    );
  }

  Widget _buildItemCard(MenuItemModel item) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: const Color(0xFF131A35),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: item.qty > 0 ? const Color(0xFF6366F1) : Colors.white10, width: item.qty > 0 ? 1.5 : 1),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                width: 10,
                height: 10,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: item.isVeg ? Colors.green : Colors.red,
                ),
              ),
              Text('₹${item.price.toStringAsFixed(0)}', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF10B981))),
            ],
          ),
          const SizedBox(height: 6),
          Text(item.name, maxLines: 2, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.white)),
          const Spacer(),
          item.qty == 0
              ? SizedBox(
                  width: double.infinity,
                  child: OutlinedButton(
                    onPressed: () {
                      setState(() => item.qty = 1);
                      _recalculateAutoCoupon();
                    },
                    style: OutlinedButton.styleFrom(
                      foregroundColor: const Color(0xFF6366F1),
                      side: const BorderSide(color: Color(0xFF6366F1)),
                      padding: EdgeInsets.zero,
                    ),
                    child: const Text('+ ADD', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                  ),
                )
              : Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    IconButton(
                      icon: const Icon(Icons.remove_circle_outline, color: Colors.red, size: 20),
                      onPressed: () {
                        setState(() => item.qty -= 1);
                        _recalculateAutoCoupon();
                      },
                      constraints: const BoxConstraints(),
                      padding: EdgeInsets.zero,
                    ),
                    Text('${item.qty}', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 14)),
                    IconButton(
                      icon: const Icon(Icons.add_circle_outline, color: Color(0xFF10B981), size: 20),
                      onPressed: () {
                        setState(() => item.qty += 1);
                        _recalculateAutoCoupon();
                      },
                      constraints: const BoxConstraints(),
                      padding: EdgeInsets.zero,
                    ),
                  ],
                ),
        ],
      ),
    );
  }

  void _handlePlaceKot(double finalAmount) async {
    final success = await ApiService.placeOrder(
      restaurantId: 1,
      tableId: _selectedTable,
      cartItems: _menuItems,
      totalAmount: finalAmount,
      couponCode: _activeAutoCoupon?['coupon']?['code'],
    );

    if (mounted) {
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('🍳 Order sent to Kitchen for Table T-$_selectedTable!'),
            backgroundColor: const Color(0xFF10B981),
          ),
        );
        setState(() {
          for (var i in _menuItems) {
            i.qty = 0;
          }
          _activeAutoCoupon = null;
        });
      }
    }
  }
}
