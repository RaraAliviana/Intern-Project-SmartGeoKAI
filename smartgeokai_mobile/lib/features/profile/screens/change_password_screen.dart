import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../auth/providers/auth_provider.dart';
import '../services/profile_service.dart';
import '../../../core/constants/app_colors.dart';
import '../../../shared/widgets/app_text_field.dart';

class ChangePasswordScreen extends StatefulWidget {
  const ChangePasswordScreen({super.key});

  @override
  State<ChangePasswordScreen> createState() => _ChangePasswordScreenState();
}

class _ChangePasswordScreenState extends State<ChangePasswordScreen> {
  final _formKey     = GlobalKey<FormState>();
  final _service     = ProfileService();
  final _currentCtrl = TextEditingController();
  final _newCtrl     = TextEditingController();
  final _confirmCtrl = TextEditingController();

  bool _saving = false;
  Map<String, String> _serverErrors = {};
  String? _generalError;

  @override
  void dispose() {
    _currentCtrl.dispose();
    _newCtrl.dispose();
    _confirmCtrl.dispose();
    super.dispose();
  }

  void _clearError(String field) {
    if (_serverErrors.containsKey(field) || _generalError != null) {
      setState(() {
        _serverErrors = Map.of(_serverErrors)..remove(field);
        _generalError = null;
      });
    }
  }

  Future<void> _save() async {
    FocusScope.of(context).unfocus();
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _saving       = true;
      _serverErrors = {};
      _generalError = null;
    });

    final result = await _service.changePassword(
      currentPassword: _currentCtrl.text,
      newPassword:     _newCtrl.text,
      confirmPassword: _confirmCtrl.text,
    );
    if (!mounted) return;

    if (result.unauthorized) {
      await context.read<AuthProvider>().logout();
      if (!mounted) return;
      Navigator.pushNamedAndRemoveUntil(context, '/login', (_) => false);
      return;
    }

    if (result.success) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result.message ?? 'Password berhasil diganti.'),
          backgroundColor: AppColors.clear,
          behavior: SnackBarBehavior.floating,
        ),
      );
      Navigator.pop(context);
      return;
    }

    setState(() {
      _saving       = false;
      _serverErrors = result.fieldErrors;
      _generalError = result.fieldErrors.isEmpty ? result.message : null;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Ganti Password')),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: AppColors.primaryLight,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Icon(Icons.info_outline_rounded,
                          size: 18, color: AppColors.primary),
                      SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          'Gunakan minimal 8 karakter dan jangan sama dengan '
                          'password lama.',
                          style: TextStyle(
                            fontSize: 12.5,
                            color: AppColors.primaryDark,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
                if (_generalError != null) ...[
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: AppColors.errorLight,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: AppColors.error.withValues(alpha: 0.3),
                      ),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.error_outline,
                            color: AppColors.error, size: 18),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            _generalError!,
                            style: const TextStyle(
                              color: AppColors.error,
                              fontSize: 13,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                ],
                AppTextField(
                  controller: _currentCtrl,
                  label: 'Password lama',
                  hint: 'Masukkan password lama',
                  icon: Icons.lock_outline_rounded,
                  isPassword: true,
                  textInputAction: TextInputAction.next,
                  errorText: _serverErrors['current_password'],
                  onChanged: (_) => _clearError('current_password'),
                  validator: (v) {
                    if (v == null || v.isEmpty) {
                      return 'Password lama tidak boleh kosong';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 20),
                AppTextField(
                  controller: _newCtrl,
                  label: 'Password baru',
                  hint: 'Masukkan password baru',
                  icon: Icons.lock_reset_rounded,
                  isPassword: true,
                  textInputAction: TextInputAction.next,
                  errorText: _serverErrors['password'],
                  onChanged: (_) => _clearError('password'),
                  validator: (v) {
                    if (v == null || v.isEmpty) {
                      return 'Password baru tidak boleh kosong';
                    }
                    if (v.length < 8) return 'Password minimal 8 karakter';
                    if (v == _currentCtrl.text) {
                      return 'Password baru tidak boleh sama dengan yang lama';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 20),
                AppTextField(
                  controller: _confirmCtrl,
                  label: 'Konfirmasi password baru',
                  hint: 'Ulangi password baru',
                  icon: Icons.check_circle_outline_rounded,
                  isPassword: true,
                  textInputAction: TextInputAction.done,
                  onSubmitted: (_) => _save(),
                  validator: (v) {
                    if (v == null || v.isEmpty) {
                      return 'Konfirmasi password tidak boleh kosong';
                    }
                    if (v != _newCtrl.text) return 'Konfirmasi tidak sama';
                    return null;
                  },
                ),
                const SizedBox(height: 32),
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: ElevatedButton(
                    onPressed: _saving ? null : _save,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor:
                          AppColors.primary.withValues(alpha: 0.6),
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                    child: _saving
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(
                              strokeWidth: 2.5,
                              valueColor:
                                  AlwaysStoppedAnimation<Color>(Colors.white),
                            ),
                          )
                        : const Text(
                            'Ganti Password',
                            style: TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}