class OrderModel {
  final int id;
  final String orderNumber;
  final String tableName;
  final double totalAmount;
  final String status;
  final String paymentStatus;
  final String orderType;
  final List<dynamic> items;
  final String createdAt;

  OrderModel({
    required this.id,
    required this.orderNumber,
    required this.tableName,
    required this.totalAmount,
    required this.status,
    required this.paymentStatus,
    required this.orderType,
    required this.items,
    required this.createdAt,
  });

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    return OrderModel(
      id: json['id'] ?? 0,
      orderNumber: json['order_number'] ?? '#${json['id']}',
      tableName: json['table'] != null ? (json['table']['name'] ?? 'Table') : 'Takeaway',
      totalAmount: double.tryParse(json['total_amount'].toString()) ?? 0.0,
      status: json['status'] ?? 'pending',
      paymentStatus: json['payment_status'] ?? 'unpaid',
      orderType: json['order_type'] ?? 'dine_in',
      items: json['items'] ?? [],
      createdAt: json['created_at'] ?? '',
    );
  }
}
