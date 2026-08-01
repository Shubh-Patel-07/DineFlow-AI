class MenuItemModel {
  final int id;
  final String name;
  final String description;
  final double price;
  final String category;
  final String image;
  final bool isAvailable;
  final bool isVeg;
  int qty;

  MenuItemModel({
    required this.id,
    required this.name,
    required this.description,
    required this.price,
    required this.category,
    required this.image,
    this.isAvailable = true,
    this.isVeg = true,
    this.qty = 0,
  });

  factory MenuItemModel.fromJson(Map<String, dynamic> json) {
    return MenuItemModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      price: double.tryParse(json['price'].toString()) ?? 0.0,
      category: json['category'] ?? 'General',
      image: json['image'] ?? '',
      isAvailable: json['is_available'] == 1 || json['is_available'] == true,
      isVeg: json['is_veg'] == 1 || json['is_veg'] == true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'price': price,
      'quantity': qty,
    };
  }
}
