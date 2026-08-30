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
  final _formKey = GlobalKey<FormState>();
  final _usernameController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  String? _errorMessage;

  @override
  void dispose() {
    _usernameController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _submitLogin() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final user = await AuthService.loginWithCredentials(
      _usernameController.text.trim(),
      _passwordController.text,
    );

    setState(() => _isLoading = false);

    if (user != null && mounted) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => RoleHomeScreen(user: user)),
      );
    } else {
      setState(() {
        _errorMessage = 'Invalid email/username or password.';
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
            child: Form(
              key: _formKey,
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
                    'Staff & Administrator Login',
                    style: GoogleFonts.inter(fontSize: 13, color: Colors.grey),
                  ),
                  const SizedBox(height: 28),

                  if (_errorMessage != null) ...[
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEF4444).withOpacity(0.15),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: const Color(0xFFEF4444).withOpacity(0.3)),
                      ),
                      child: Text(
                        _errorMessage!,
                        textAlign: TextAlign.center,
                        style: const TextStyle(color: Color(0xFFEF4444), fontSize: 13, fontWeight: FontWeight.w600),
                      ),
                    ),
                    const SizedBox(height: 20),
                  ],

                  // Email or Username Input
                  TextFormField(
                    controller: _usernameController,
                    style: const TextStyle(color: Colors.white),
                    decoration: InputDecoration(
                      prefixIcon: const Icon(Icons.email_outlined, color: Color(0xFF818CF8)),
                      hintText: 'Email or Username',
                      hintStyle: const TextStyle(color: Color(0xFF64748B)),
                      filled: true,
                      fillColor: const Color(0xFF1E293B),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(14),
                        borderSide: const BorderSide(color: Color(0xFF334155)),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(14),
                        borderSide: const BorderSide(color: Color(0xFF334155)),
                      ),
                    ),
                    validator: (val) => (val == null || val.trim().isEmpty) ? 'Email/Username is required' : null,
                  ),
                  const SizedBox(height: 16),

                  // Password Input
                  TextFormField(
                    controller: _passwordController,
                    obscureText: true,
                    style: const TextStyle(color: Colors.white),
                    decoration: InputDecoration(
                      prefixIcon: const Icon(Icons.lock_outline, color: Color(0xFF818CF8)),
                      hintText: 'Password',
                      hintStyle: const TextStyle(color: Color(0xFF64748B)),
                      filled: true,
                      fillColor: const Color(0xFF1E293B),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(14),
                        borderSide: const BorderSide(color: Color(0xFF334155)),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(14),
                        borderSide: const BorderSide(color: Color(0xFF334155)),
                      ),
                    ),
                    validator: (val) => (val == null || val.isEmpty) ? 'Password is required' : null,
                  ),
                  const SizedBox(height: 24),

                  // Submit Button
                  SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _submitLogin,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF6366F1),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                        elevation: 4,
                      ),
                      child: _isLoading
                          ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                          : Text('Sign In', style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white)),
                    ),
                  ),

                  const SizedBox(height: 24),

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
      ),
    );
  }
}