import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../auth/providers/auth_provider.dart';
import '../services/profile_service.dart';
import '../../../core/constants/app_colors.dart';
import '../../../shared/widgets/app_text_field.dart';

class EditProfileScreen extends StatefulWidget {
  const EditProfileScreen({super.key});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  final _service = ProfileService();
  late final TextEditingController _nameCtrl;
  late final TextEditingController _nipCtrl;

  bool _saving = false;
  Map<String, String> _serverErrors = {};
  String? _generalError;

  @override
  void initState() {
    super.initState();
    final user = context.read<AuthProvider>().user;
    _nameCtrl = TextEditingController(text: user?.fullName ?? '');
    _nipCtrl  = TextEditingController(text: user?.nip ?? '');
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _nipCtrl.dispose();
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

    final fullName = _nameCtrl.text.trim();
    final nip      = _nipCtrl.text.trim();

    final result = await _service.updateProfile(fullName: fullName, nip: nip);
    if (!mounted) return;

    if (result.unauthorized) {
      await context.read<AuthProvider>().logout();
      if (!mounted) return;
      Navigator.pushNamedAndRemoveUntil(context, '/login', (_) => false);
      return;
    }

    if (result.success) {
      final auth    = context.read<AuthProvider>();
      final current = auth.user;
      final updated =
          result.user ?? current?.copyWith(fullName: fullName, nip: nip);
      if (updated != null) await auth.setUser(updated);
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result.message ?? 'Profil berhasil diperbarui.'),
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
      appBar: AppBar(title: const Text('Edit Profil')),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
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
                  controller: _nameCtrl,
                  label: 'Nama lengkap',
                  hint: 'Masukkan nama lengkap',
                  icon: Icons.person_outline_rounded,
                  keyboardType: TextInputType.name,
                  textInputAction: TextInputAction.next,
                  errorText: _serverErrors['full_name'],
                  onChanged: (_) => _clearError('full_name'),
                  validator: (v) {
                    final value = v?.trim() ?? '';
                    if (value.isEmpty) return 'Nama tidak boleh kosong';
                    if (value.length < 3) return 'Nama minimal 3 karakter';
                    return null;
                  },
                ),
                const SizedBox(height: 20),
                AppTextField(
                  controller: _nipCtrl,
                  label: 'NIP',
                  hint: 'Masukkan NIP',
                  icon: Icons.badge_outlined,
                  keyboardType: TextInputType.number,
                  textInputAction: TextInputAction.done,
                  maxLength: 25,
                  inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                  errorText: _serverErrors['nip'],
                  onChanged: (_) => _clearError('nip'),
                  onSubmitted: (_) => _save(),
                  validator: (v) {
                    final value = v?.trim() ?? '';
                    if (value.isEmpty) return 'NIP tidak boleh kosong';
                    if (value.length < 8) return 'NIP minimal 8 digit';
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
                            'Simpan Perubahan',
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