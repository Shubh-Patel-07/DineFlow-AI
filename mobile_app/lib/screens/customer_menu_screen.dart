import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/menu_item.dart';
import '../services/api_service.dart';

class CustomerMenuScreen extends StatefulWidget {
  const CustomerMenuScreen({super.key});

  @override
  State<CustomerMenuScreen> createState() => _CustomerMenuScreenState();
}

class _CustomerMenuScreenState extends State<CustomerMenuScreen> {
  List<MenuItemModel> _menuItems = [];
  bool _isLoading = true;
  String _selectedCategory = 'All';
  String _searchQuery = '';
  String _vegFilter = 'All'; // All, Veg, Non-Veg
  int _selectedTable = 1;

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
    // ensure required chips are present if not in menu
    set.addAll(['Pizza', 'Burger', 'Pasta', 'Drinks', 'Starters', 'Desserts', 'Mains']);
    return set.toList();
  }

  List<MenuItemModel> get _filteredItems {
    return _menuItems.where((item) {
      final matchesCat = _selectedCategory == 'All' || item.category == _selectedCategory;
      final matchesSearch = item.name.toLowerCase().contains(_searchQuery.toLowerCase());
      final matchesVeg = _vegFilter == 'All' || 
                         (_vegFilter == 'Veg' && item.isVeg) || 
                         (_vegFilter == 'Non-Veg' && !item.isVeg);
      return matchesCat && matchesSearch && matchesVeg;
    }).toList();
  }

  void _showItemDetailSheet(MenuItemModel item) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => StatefulBuilder(
        builder: (context, setSheetState) {
          return Container(
            height: MediaQuery.of(context).size.height * 0.85,
            decoration: const BoxDecoration(
              color: Color(0xFF131A35),
              borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Image area
                Container(
                  height: 250,
                  decoration: const BoxDecoration(
                    borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
                    gradient: LinearGradient(
                      colors: [Color(0xFF6366F1), Color(0xFF3F4194)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                  ),
                  child: const Center(
                    child: Icon(Icons.fastfood, size: 80, color: Colors.white54),
                  ),
                ),
                Expanded(
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: item.isVeg ? Colors.green.withOpacity(0.2) : Colors.red.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                item.isVeg ? '🟢 Veg' : '🔴 Non-Veg',
                                style: TextStyle(
                                  color: item.isVeg ? Colors.green : Colors.red,
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: const Color(0xFF6366F1).withOpacity(0.2),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                item.category,
                                style: const TextStyle(color: Color(0xFF6366F1), fontSize: 12, fontWeight: FontWeight.bold),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Text(
                          item.name,
                          style: GoogleFonts.outfit(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.white),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          '₹${item.price.toStringAsFixed(0)}',
                          style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Color(0xFF10B981)),
                        ),
                        const SizedBox(height: 16),
                        Text(
                          item.description.isNotEmpty ? item.description : 'Delicious and freshly prepared just for you. Order now to experience the rich flavors.',
                          style: const TextStyle(color: Colors.grey, fontSize: 14, height: 1.5),
                        ),
                        const SizedBox(height: 24),
                        Text('Cooking Notes', style: GoogleFonts.outfit(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                        const SizedBox(height: 12),
                        Wrap(
                          spacing: 8,
                          runSpacing: 8,
                          children: [
                            'Extra Spicy 🌶️', 'No Onion 🧅', 'Extra Cheese 🧀', 'Jain 🧪'
                          ].map((note) => ChoiceChip(
                            label: Text(note),
                            selected: false,
                            onSelected: (val) {},
                            backgroundColor: const Color(0xFF0B0F19),
                          )).toList(),
                        ),
                        const SizedBox(height: 16),
                        TextField(
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            hintText: 'Any custom instructions?',
                            hintStyle: const TextStyle(color: Colors.grey),
                            filled: true,
                            fillColor: const Color(0xFF0B0F19),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide.none,
                            ),
                          ),
                        ),
                        const SizedBox(height: 24),
                      ],
                    ),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.all(20),
                  child: Container(
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(colors: [Color(0xFF6366F1), Color(0xFF4F46E5)]),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: ElevatedButton(
                      onPressed: () {
                        setState(() {
                          item.qty += 1;
                        });
                        Navigator.pop(context);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.transparent,
                        shadowColor: Colors.transparent,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      ),
                      child: Text(
                        'Add to Order - ₹${item.price.toStringAsFixed(0)}',
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          );
        }
      ),
    );
  }

  void _showCartSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => StatefulBuilder(
        builder: (context, setSheetState) {
          final cartItems = _menuItems.where((i) => i.qty > 0).toList();
          final subtotal = cartItems.fold(0.0, (sum, item) => sum + (item.price * item.qty));
          final gst = subtotal * 0.18;
          final grandTotal = subtotal + gst;

          return Container(
            height: MediaQuery.of(context).size.height * 0.8,
            decoration: const BoxDecoration(
              color: Color(0xFF131A35),
              borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
            ),
            child: Column(
              children: [
                Padding(
                  padding: const EdgeInsets.all(20),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Your Order', style: GoogleFonts.outfit(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.white)),
                      IconButton(
                        icon: const Icon(Icons.close, color: Colors.white),
                        onPressed: () => Navigator.pop(context),
                      )
                    ],
                  ),
                ),
                Expanded(
                  child: cartItems.isEmpty
                    ? const Center(child: Text('Your cart is empty', style: TextStyle(color: Colors.grey)))
                    : ListView.builder(
                        padding: const EdgeInsets.symmetric(horizontal: 20),
                        itemCount: cartItems.length,
                        itemBuilder: (context, index) {
                          final item = cartItems[index];
                          return Container(
                            margin: const EdgeInsets.only(bottom: 16),
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: const Color(0xFF0B0F19),
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  width: 60,
                                  height: 60,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(12),
                                    gradient: const LinearGradient(colors: [Color(0xFF6366F1), Color(0xFF3F4194)]),
                                  ),
                                  child: const Icon(Icons.fastfood, color: Colors.white54),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(item.name, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                                      const SizedBox(height: 4),
                                      Text('₹${(item.price * item.qty).toStringAsFixed(0)}', style: const TextStyle(color: Color(0xFF10B981))),
                                    ],
                                  ),
                                ),
                                Row(
                                  children: [
                                    IconButton(
                                      icon: const Icon(Icons.remove_circle_outline, color: Colors.red),
                                      onPressed: () {
                                        setState(() { item.qty--; });
                                        setSheetState(() {});
                                      },
                                    ),
                                    Text('${item.qty}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                                    IconButton(
                                      icon: const Icon(Icons.add_circle_outline, color: Colors.green),
                                      onPressed: () {
                                        setState(() { item.qty++; });
                                        setSheetState(() {});
                                      },
                                    ),
                                  ],
                                )
                              ],
                            ),
                          );
                        },
                      ),
                ),
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: const BoxDecoration(
                    color: Color(0xFF0B0F19),
                    borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Table Number', style: TextStyle(color: Colors.grey)),
                          DropdownButton<int>(
                            value: _selectedTable,
                            dropdownColor: const Color(0xFF131A35),
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                            items: List.generate(10, (i) => DropdownMenuItem(value: i + 1, child: Text('Table T-${i + 1}'))),
                            onChanged: (val) {
                              setState(() => _selectedTable = val ?? 1);
                              setSheetState(() {});
                            },
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Subtotal', style: TextStyle(color: Colors.grey)),
                          Text('₹${subtotal.toStringAsFixed(2)}', style: const TextStyle(color: Colors.white)),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('GST (18%)', style: TextStyle(color: Colors.grey)),
                          Text('₹${gst.toStringAsFixed(2)}', style: const TextStyle(color: Colors.white)),
                        ],
                      ),
                      const Divider(color: Colors.white24, height: 24),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Grand Total', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)),
                          Text('₹${grandTotal.toStringAsFixed(2)}', style: const TextStyle(color: Color(0xFF10B981), fontWeight: FontWeight.bold, fontSize: 20)),
                        ],
                      ),
                      const SizedBox(height: 20),
                      Container(
                        width: double.infinity,
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(colors: [Color(0xFF10B981), Color(0xFF059669)]),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: ElevatedButton(
                          onPressed: cartItems.isEmpty ? null : () async {
                            final success = await ApiService.placeOrder(
                              restaurantId: 1,
                              tableId: _selectedTable,
                              cartItems: cartItems,
                              totalAmount: grandTotal,
                            );
                            if (mounted) {
                              Navigator.pop(context);
                              if (success) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(content: Text('Order placed successfully! 🚀'), backgroundColor: Colors.green),
                                );
                                setState(() {
                                  for (var item in _menuItems) {
                                    item.qty = 0;
                                  }
                                });
                              }
                            }
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.transparent,
                            shadowColor: Colors.transparent,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            disabledBackgroundColor: Colors.grey.withOpacity(0.3),
                          ),
                          child: const Text(
                            '🚀 Place Order',
                            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        }
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final totalQty = _calculateTotalQty();
    final subtotal = _calculateSubtotal();

    return Scaffold(
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator(color: Color(0xFF6366F1)))
        : CustomScrollView(
            slivers: [
              // Header
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: const Color(0xFF131A35).withOpacity(0.8),
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: Colors.white10),
                      boxShadow: [
                        BoxShadow(color: Colors.black.withOpacity(0.2), blurRadius: 10, offset: const Offset(0, 4)),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Text(
                                'DineFlow AI Gourmet Restaurant',
                                style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: Colors.amber.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: const Row(
                                children: [
                                  Icon(Icons.star, color: Colors.amber, size: 16),
                                  SizedBox(width: 4),
                                  Text('4.8', style: TextStyle(color: Colors.amber, fontWeight: FontWeight.bold)),
                                ],
                              ),
                            )
                          ],
                        ),
                        const SizedBox(height: 12),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: Colors.green.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: const Text('🟢 Open Now', style: TextStyle(color: Colors.green, fontSize: 12, fontWeight: FontWeight.bold)),
                            ),
                            DropdownButtonHideUnderline(
                              child: DropdownButton<int>(
                                value: _selectedTable,
                                dropdownColor: const Color(0xFF131A35),
                                icon: const Icon(Icons.table_restaurant, color: Color(0xFF6366F1)),
                                items: List.generate(10, (i) => DropdownMenuItem(value: i + 1, child: Text('Table ${i + 1}', style: const TextStyle(color: Colors.white)))),
                                onChanged: (val) => setState(() => _selectedTable = val ?? 1),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              // Search Bar
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
                  child: TextField(
                    onChanged: (val) => setState(() => _searchQuery = val),
                    style: const TextStyle(color: Colors.white),
                    decoration: InputDecoration(
                      hintText: 'Search for dishes...',
                      hintStyle: const TextStyle(color: Colors.grey),
                      prefixIcon: const Icon(Icons.search, color: Colors.grey),
                      filled: true,
                      fillColor: const Color(0xFF131A35),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: BorderSide.none,
                      ),
                    ),
                  ),
                ),
              ),

              // Veg/Non-Veg Filter
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
                  child: Row(
                    children: [
                      _buildVegFilterBtn('All'),
                      const SizedBox(width: 8),
                      _buildVegFilterBtn('Veg'),
                      const SizedBox(width: 8),
                      _buildVegFilterBtn('Non-Veg'),
                    ],
                  ),
                ),
              ),

              // Categories
              SliverToBoxAdapter(
                child: SizedBox(
                  height: 50,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    itemCount: _categories.length,
                    itemBuilder: (context, index) {
                      final cat = _categories[index];
                      final isSelected = cat == _selectedCategory;
                      return Padding(
                        padding: const EdgeInsets.only(right: 8.0),
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 200),
                          decoration: BoxDecoration(
                            gradient: isSelected ? const LinearGradient(colors: [Color(0xFF6366F1), Color(0xFF4F46E5)]) : null,
                            color: isSelected ? null : const Color(0xFF131A35),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: InkWell(
                            borderRadius: BorderRadius.circular(12),
                            onTap: () => setState(() => _selectedCategory = cat),
                            child: Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                              child: Text(
                                cat,
                                style: TextStyle(
                                  color: isSelected ? Colors.white : Colors.grey,
                                  fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                ),
                              ),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),
              ),

              // Menu Grid
              SliverPadding(
                padding: const EdgeInsets.all(16),
                sliver: SliverGrid(
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    childAspectRatio: 0.75,
                    crossAxisSpacing: 16,
                    mainAxisSpacing: 16,
                  ),
                  delegate: SliverChildBuilderDelegate(
                    (context, index) {
                      final item = _filteredItems[index];
                      return GestureDetector(
                        onTap: () => _showItemDetailSheet(item),
                        child: Container(
                          decoration: BoxDecoration(
                            color: const Color(0xFF131A35),
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(color: Colors.white10),
                            boxShadow: [
                              BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 8, offset: const Offset(0, 4)),
                            ],
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Image Placeholder
                              Container(
                                height: 100,
                                decoration: const BoxDecoration(
                                  borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
                                  gradient: LinearGradient(
                                    colors: [Color(0xFF6366F1), Color(0xFF3F4194)],
                                    begin: Alignment.topLeft,
                                    end: Alignment.bottomRight,
                                  ),
                                ),
                                child: Stack(
                                  children: [
                                    const Center(child: Icon(Icons.fastfood, size: 40, color: Colors.white54)),
                                    Positioned(
                                      top: 8,
                                      right: 8,
                                      child: Container(
                                        padding: const EdgeInsets.all(4),
                                        decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle),
                                        child: Icon(Icons.circle, size: 10, color: item.isVeg ? Colors.green : Colors.red),
                                      ),
                                    )
                                  ],
                                ),
                              ),
                              Padding(
                                padding: const EdgeInsets.all(12),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      item.name,
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Colors.white),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      item.description,
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                      style: const TextStyle(color: Colors.grey, fontSize: 11),
                                    ),
                                    const SizedBox(height: 8),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text('₹${item.price.toStringAsFixed(0)}', style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF10B981))),
                                        const Row(
                                          children: [
                                            Icon(Icons.star, color: Colors.amber, size: 12),
                                            Text('4.5', style: TextStyle(color: Colors.amber, fontSize: 10)),
                                          ],
                                        )
                                      ],
                                    ),
                                    const SizedBox(height: 12),
                                    SizedBox(
                                      width: double.infinity,
                                      height: 32,
                                      child: item.qty == 0
                                        ? ElevatedButton(
                                            onPressed: () => setState(() => item.qty = 1),
                                            style: ElevatedButton.styleFrom(
                                              backgroundColor: const Color(0xFF6366F1).withOpacity(0.2),
                                              foregroundColor: const Color(0xFF6366F1),
                                              elevation: 0,
                                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                            ),
                                            child: const Text('+ ADD', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                                          )
                                        : Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              IconButton(
                                                icon: const Icon(Icons.remove_circle_outline, color: Colors.red, size: 20),
                                                padding: EdgeInsets.zero,
                                                constraints: const BoxConstraints(),
                                                onPressed: () => setState(() => item.qty--),
                                              ),
                                              Text('${item.qty}', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                                              IconButton(
                                                icon: const Icon(Icons.add_circle_outline, color: Colors.green, size: 20),
                                                padding: EdgeInsets.zero,
                                                constraints: const BoxConstraints(),
                                                onPressed: () => setState(() => item.qty++),
                                              ),
                                            ],
                                          ),
                                    )
                                  ],
                                ),
                              )
                            ],
                          ),
                        ),
                      );
                    },
                    childCount: _filteredItems.length,
                  ),
                ),
              ),
              const SliverToBoxAdapter(child: SizedBox(height: 80)), // Padding for floating cart
            ],
          ),
      floatingActionButton: totalQty > 0
          ? AnimatedSwitcher(
              duration: const Duration(milliseconds: 300),
              child: GestureDetector(
                onTap: _showCartSheet,
                child: Container(
                  margin: const EdgeInsets.symmetric(horizontal: 16),
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(colors: [Color(0xFF6366F1), Color(0xFF4F46E5)]),
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: [
                      BoxShadow(color: const Color(0xFF6366F1).withOpacity(0.4), blurRadius: 12, offset: const Offset(0, 6)),
                    ],
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('$totalQty Items', style: const TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.bold)),
                          Text('₹${subtotal.toStringAsFixed(2)}', style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      const Row(
                        children: [
                          Text('View Cart', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                          SizedBox(width: 8),
                          Icon(Icons.arrow_forward_ios, color: Colors.white, size: 16),
                        ],
                      )
                    ],
                  ),
                ),
              ),
            )
          : null,
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
    );
  }

  Widget _buildVegFilterBtn(String title) {
    final isSel = _vegFilter == title;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _vegFilter = title),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 8),
          decoration: BoxDecoration(
            color: isSel ? const Color(0xFF6366F1).withOpacity(0.2) : const Color(0xFF131A35),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: isSel ? const Color(0xFF6366F1) : Colors.white10),
          ),
          child: Center(
            child: Text(
              title == 'Veg' ? '🟢 Veg' : (title == 'Non-Veg' ? '🔴 Non-Veg' : 'All'),
              style: TextStyle(
                color: isSel ? const Color(0xFF6366F1) : Colors.grey,
                fontWeight: isSel ? FontWeight.bold : FontWeight.normal,
                fontSize: 12,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
