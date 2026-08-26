import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/restaurant_settings_model.dart';
import '../services/api_service.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  RestaurantSettingsModel? _settings;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    setState(() => _isLoading = true);
    final s = await ApiService.fetchSettings();
    setState(() {
      _settings = s;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0B0F19),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF6366F1)))
          : RefreshIndicator(
              onRefresh: _loadSettings,
              color: const Color(0xFF6366F1),
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  Text('System Configuration', style: GoogleFonts.outfit(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                  const SizedBox(height: 14),

                  _buildSettingCard(
                    title: 'Restaurant Legal Profile',
                    icon: Icons.storefront,
                    items: [
                      'Name: ${_settings?.name ?? 'DineFlow AI Gourmet'}',
                      'GSTIN: ${_settings?.gstin ?? '24AAACD1234E1Z5'}',
                      'GST Tax Rate: ${_settings?.gstRate ?? 5.0}%',
                      'Service Charge: ${_settings?.serviceCharge ?? 5.0}%',
                    ],
                  ),

                  const SizedBox(height: 14),

                  _buildSettingCard(
                    title: 'Hardware & Audio Setup',
                    icon: Icons.print_outlined,
                    items: [
                      'Printer Mode: ${_settings?.printerType ?? 'SYSTEM_DEFAULT'}',
                      'Printer Name: ${_settings?.printerName ?? 'Default Thermal'}',
                      'Auto-Print KOT: ${_settings?.autoPrintKot == true ? 'Enabled (ON)' : 'Disabled'}',
                      'Soundbox Language: ${(_settings?.soundboxLang ?? 'gu').toUpperCase()}',
                    ],
                  ),

                  const SizedBox(height: 14),

                  _buildSettingCard(
                    title: 'Loyalty & Customer Offers',
                    icon: Icons.card_giftcard,
                    items: [
                      'Store Currency: ${_settings?.currency ?? 'INR'}',
                      'Festival Banner: ${_settings?.promoBannerText ?? 'Active'}',
                    ],
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildSettingCard({required String title, required IconData icon, required List<String> items}) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: const Color(0xFF131A35),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFF1E293B)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, color: const Color(0xFF6366F1), size: 20),
              const SizedBox(width: 8),
              Text(title, style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)),
            ],
          ),
          const Divider(color: Color(0xFF1E293B), height: 24),
          ...items.map((it) => Padding(
                padding: const EdgeInsets.only(bottom: 6),
                child: Text(it, style: const TextStyle(color: Colors.white70, fontSize: 13)),
              )),
        ],
      ),
    );
  }
}