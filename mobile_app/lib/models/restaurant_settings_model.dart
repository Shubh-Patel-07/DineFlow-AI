class RestaurantSettingsModel {
  final String name;
  final String? gstin;
  final double gstRate;
  final double serviceCharge;
  final String currency;
  final String soundboxLang;
  final String printerType;
  final String? printerName;
  final bool autoPrintKot;
  final String? promoBannerText;

  RestaurantSettingsModel({
    required this.name,
    this.gstin,
    required this.gstRate,
    required this.serviceCharge,
    required this.currency,
    required this.soundboxLang,
    required this.printerType,
    this.printerName,
    required this.autoPrintKot,
    this.promoBannerText,
  });

  factory RestaurantSettingsModel.fromJson(Map<String, dynamic> json) {
    final cfg = (json['settings'] is Map) ? json['settings'] : {};
    return RestaurantSettingsModel(
      name: json['name'] ?? 'DineFlow AI Restaurant',
      gstin: json['gstin'],
      gstRate: double.tryParse(json['gst_rate']?.toString() ?? '5.0') ?? 5.0,
      serviceCharge: double.tryParse(json['service_charge']?.toString() ?? '5.0') ?? 5.0,
      currency: cfg['currency'] ?? 'INR',
      soundboxLang: cfg['soundbox_lang'] ?? 'gu',
      printerType: cfg['printer_type'] ?? 'SYSTEM_DEFAULT',
      printerName: cfg['printer_name'],
      autoPrintKot: cfg['auto_print_kot'] == true || cfg['auto_print_kot'] == 'true',
      promoBannerText: cfg['promo_banner_text'],
    );
  }
}