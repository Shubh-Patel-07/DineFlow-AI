class TableModel {
  final int id;
  final String tableNumber;
  final int capacity;
  final String status;
  final String? activeOrderTotal;

  TableModel({
    required this.id,
    required this.tableNumber,
    required this.capacity,
    required this.status,
    this.activeOrderTotal,
  });

  factory TableModel.fromJson(Map<String, dynamic> json) {
    return TableModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      tableNumber: json['table_number'] ?? json['name'] ?? 'T-01',
      capacity: json['capacity'] is int ? json['capacity'] : int.tryParse(json['capacity']?.toString() ?? '4') ?? 4,
      status: json['status'] ?? 'available',
      activeOrderTotal: json['active_order_total']?.toString(),
    );
  }
}