class ExpenseModel {
  final int? id;
  final String title;
  final double amount;
  final String category;
  final String paymentMethod;
  final String date;
  final String? notes;

  ExpenseModel({
    this.id,
    required this.title,
    required this.amount,
    required this.category,
    required this.paymentMethod,
    required this.date,
    this.notes,
  });

  factory ExpenseModel.fromJson(Map<String, dynamic> json) {
    return ExpenseModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id']?.toString() ?? ''),
      title: json['title'] ?? '',
      amount: (json['amount'] is num) ? (json['amount'] as num).toDouble() : double.tryParse(json['amount']?.toString() ?? '0') ?? 0.0,
      category: json['category'] ?? 'Other',
      paymentMethod: json['payment_method'] ?? json['payment'] ?? 'Cash',
      date: json['date'] ?? '',
      notes: json['notes'],
    );
  }

  Map<String, dynamic> toJson() => {
    'title': title,
    'amount': amount,
    'category': category,
    'payment_method': paymentMethod,
    'date': date,
    'notes': notes,
  };
}