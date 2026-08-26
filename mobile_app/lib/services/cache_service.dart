import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/menu_item.dart';
import '../models/restaurant_settings_model.dart';

class CacheService {
  static const String _cachedMenuKey = 'dineflow_cached_menu_items';
  static const String _cachedSettingsKey = 'dineflow_cached_settings';

  // Cache Menu Items
  static Future<void> cacheMenu(List<MenuItemModel> items) async {
    final prefs = await SharedPreferences.getInstance();
    final jsonList = items.map((i) => i.toJson()).toList();
    await prefs.setString(_cachedMenuKey, json.encode(jsonList));
  }

  static Future<List<MenuItemModel>?> getCachedMenu() async {
    final prefs = await SharedPreferences.getInstance();
    final data = prefs.getString(_cachedMenuKey);
    if (data != null) {
      final List list = json.decode(data);
      return list.map((j) => MenuItemModel.fromJson(j)).toList();
    }
    return null;
  }

  // Cache Restaurant Settings
  static Future<void> cacheSettings(RestaurantSettingsModel settings) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_cachedSettingsKey, json.encode({
      'name': settings.name,
      'gstin': settings.gstin,
      'gst_rate': settings.gstRate,
      'service_charge': settings.serviceCharge,
      'settings': {
        'currency': settings.currency,
        'soundbox_lang': settings.soundboxLang,
        'printer_type': settings.printerType,
        'printer_name': settings.printerName,
        'auto_print_kot': settings.autoPrintKot,
        'promo_banner_text': settings.promoBannerText,
      }
    }));
  }

  static Future<RestaurantSettingsModel?> getCachedSettings() async {
    final prefs = await SharedPreferences.getInstance();
    final data = prefs.getString(_cachedSettingsKey);
    if (data != null) {
      return RestaurantSettingsModel.fromJson(json.decode(data));
    }
    return null;
  }
}