class UserModel {
  final int id;
  final String name;
  final String username;
  final String email;
  final String role;
  final String? numericPin;
  final String restaurantId;

  UserModel({
    required this.id,
    required this.name,
    required this.username,
    required this.email,
    required this.role,
    this.numericPin,
    required this.restaurantId,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      name: json['name'] ?? '',
      username: json['username'] ?? '',
      email: json['email'] ?? '',
      role: (json['role'] ?? 'waiter').toString().toLowerCase(),
      numericPin: json['numeric_pin']?.toString(),
      restaurantId: (json['restaurant_id'] ?? '1').toString(),
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'name': name,
    'username': username,
    'email': email,
    'role': role,
    'numeric_pin': numericPin,
    'restaurant_id': restaurantId,
  };
}