import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/auth_service.dart';
import 'role_home_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  String _enteredPin = '';
  bool _isLoading = false;
  String? _errorMessage;

  void _onKeyTap(String digit) {
    if (_enteredPin.length < 6) {
      setState(() {
        _enteredPin += digit;
        _errorMessage = null;
      });
      if (_enteredPin.length == 4) {
        _submitPin();
      }
    }
  }

  void _onBackspace() {
    if (_enteredPin.isNotEmpty) {
      setState(() {
        _enteredPin = _enteredPin.substring(0, _enteredPin.length - 1);
        _errorMessage = null;
      });
    }
  }

  Future<void> _submitPin() async {
    setState(() => _isLoading = true);
    final user = await AuthService.loginWithPin(_enteredPin);
    setState(() => _isLoading = false);

    if (user != null && mounted) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => RoleHomeScreen(user: user)),
      );
    } else {
      setState(() {
        _errorMessage = 'Invalid PIN. Try 1234 (Admin), 9999 (Cashier), 2580 (Kitchen), 3690 (Waiter)';
        _enteredPin = '';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0B0F19),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 28.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  width: 72,
                  height: 72,
                  decoration: BoxDecoration(
                    color: const Color(0xFF6366F1),
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFF6366F1).withOpacity(0.4),
                        blurRadius: 20,
                        offset: const Offset(0, 8),
                      ),
                    ],
                  ),
                  child: const Icon(Icons.restaurant_menu, color: Colors.white, size: 40),
                ),
                const SizedBox(height: 18),
                Text(
                  'DineFlow AI',
                  style: GoogleFonts.outfit(fontSize: 26, fontWeight: FontWeight.w800, color: Colors.white),
                ),
                const SizedBox(height: 4),
                Text(
                  'Enter Quick 4-Digit Staff Login PIN',
                  style: GoogleFonts.inter(fontSize: 13, color: Colors.grey),
                ),
                const SizedBox(height: 28),

                // PIN Dots
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(4, (index) {
                    final isFilled = index < _enteredPin.length;
                    return Container(
                      margin: const EdgeInsets.symmetric(horizontal: 8),
                      width: 18,
                      height: 18,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: isFilled ? const Color(0xFF6366F1) : const Color(0xFF1E293B),
                        border: Border.all(
                          color: isFilled ? const Color(0xFF818CF8) : const Color(0xFF334155),
                          width: 2,
                        ),
                      ),
                    );
                  }),
                ),

                if (_errorMessage != null) ...[
                  const SizedBox(height: 16),
                  Text(
                    _errorMessage!,
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Color(0xFFEF4444), fontSize: 12),
                  ),
                ],

                const SizedBox(height: 32),

                // Number Keypad
                Container(
                  constraints: const BoxConstraints(maxWidth: 320),
                  child: Column(
                    children: [
                      _buildRow(['1', '2', '3']),
                      const SizedBox(height: 14),
                      _buildRow(['4', '5', '6']),
                      const SizedBox(height: 14),
                      _buildRow(['7', '8', '9']),
                      const SizedBox(height: 14),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          _buildCustomerModeButton(),
                          _buildKey('0'),
                          _buildActionButton(Icons.backspace_outlined, _onBackspace),
                        ],
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 28),

                TextButton(
                  onPressed: () async {
                    await AuthService.logout();
                    if (mounted) {
                      Navigator.of(context).pushReplacement(
                        MaterialPageRoute(builder: (_) => const RoleHomeScreen(user: null)),
                      );
                    }
                  },
                  child: Text(
                    'Continue as Guest Customer (QR Menu) →',
                    style: GoogleFonts.inter(color: const Color(0xFF818CF8), fontSize: 13, fontWeight: FontWeight.w600),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRow(List<String> digits) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: digits.map((d) => _buildKey(d)).toList(),
    );
  }

  Widget _buildKey(String digit) {
    return InkWell(
      onTap: () => _onKeyTap(digit),
      borderRadius: BorderRadius.circular(16),
      child: Container(
        width: 72,
        height: 64,
        decoration: BoxDecoration(
          color: const Color(0xFF131A35),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFF1E293B)),
        ),
        alignment: Alignment.center,
        child: Text(
          digit,
          style: GoogleFonts.outfit(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.white),
        ),
      ),
    );
  }

  Widget _buildActionButton(IconData icon, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        width: 72,
        height: 64,
        decoration: BoxDecoration(
          color: const Color(0xFF131A35),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFF1E293B)),
        ),
        alignment: Alignment.center,
        child: Icon(icon, color: Colors.white70, size: 22),
      ),
    );
  }

  Widget _buildCustomerModeButton() {
    return InkWell(
      onTap: () => setState(() => _enteredPin = ''),
      borderRadius: BorderRadius.circular(16),
      child: Container(
        width: 72,
        height: 64,
        decoration: BoxDecoration(
          color: const Color(0xFF131A35),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFF1E293B)),
        ),
        alignment: Alignment.center,
        child: const Text('C', style: TextStyle(color: Colors.white38, fontSize: 18, fontWeight: FontWeight.bold)),
      ),
    );
  }
}