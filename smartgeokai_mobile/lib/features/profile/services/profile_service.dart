import 'package:dio/dio.dart';
import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../../auth/models/user_model.dart';

class ProfileResult {
  final bool                success;
  final String?             message;
  final UserModel?          user;
  final Map<String, String> fieldErrors;
  final bool                unauthorized;

  const ProfileResult({
    required this.success,
    this.message,
    this.user,
    this.fieldErrors  = const {},
    this.unauthorized = false,
  });
}

class ProfileService {
  // GET /profile
  Future<ProfileResult> fetchProfile() {
    return _run(() => ApiClient.instance.get(ApiConstants.me));
  }

  // PUT /profile  { full_name, nip }
  Future<ProfileResult> updateProfile({
    required String fullName,
    required String nip,
  }) {
    return _run(
      () => ApiClient.instance.put(
        ApiConstants.me,
        data: {'full_name': fullName, 'nip': nip},
      ),
      okMessage: 'Profil berhasil diperbarui.',
    );
  }

  // PUT /profile/password
  Future<ProfileResult> changePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmPassword,
  }) {
    return _run(
      () => ApiClient.instance.put(
        ApiConstants.changePassword,
        data: {
          'current_password':      currentPassword,
          'password':              newPassword,
          'password_confirmation': confirmPassword,
        },
      ),
      okMessage: 'Password berhasil diganti.',
    );
  }

  Future<ProfileResult> _run(
    Future<Response<dynamic>> Function() call, {
    String? okMessage,
  }) async {
    try {
      final res  = await call();
      final body = res.data;
      return ProfileResult(
        success: true,
        message: _readMessage(body) ?? okMessage,
        user:    _parseUser(body),
      );
    } on DioException catch (e) {
      final status = e.response?.statusCode;
      final data   = e.response?.data;

      if (status == 401) {
        return const ProfileResult(
          success: false,
          unauthorized: true,
          message: 'Sesi Anda telah berakhir. Silakan login kembali.',
        );
      }

      if (status == 422) {
        final errors = <String, String>{};
        if (data is Map && data['errors'] is Map) {
          (data['errors'] as Map).forEach((key, value) {
            final text = value is List && value.isNotEmpty
                ? value.first.toString()
                : value.toString();
            errors[key.toString()] = text;
          });
        }
        return ProfileResult(
          success: false,
          message: _readMessage(data) ?? 'Data yang dikirim tidak valid.',
          fieldErrors: errors,
        );
      }

      if (status == 403) {
        return ProfileResult(
          success: false,
          message: _readMessage(data) ?? 'Anda tidak memiliki akses.',
        );
      }

      if (status == null) {
        return const ProfileResult(
          success: false,
          message: 'Tidak dapat terhubung ke server. Periksa koneksi internet.',
        );
      }

      return ProfileResult(
        success: false,
        message: _readMessage(data) ?? 'Terjadi kesalahan ($status).',
      );
    } catch (_) {
      return const ProfileResult(
        success: false,
        message: 'Terjadi kesalahan tak terduga.',
      );
    }
  }

  String? _readMessage(dynamic body) {
    if (body is Map && body['message'] is String) {
      return body['message'] as String;
    }
    return null;
  }

  // Terima bentuk respons: {user:{...}}, {data:{...}}, atau langsung {...}
  UserModel? _parseUser(dynamic body) {
    try {
      if (body is Map) {
        final raw = body['user'] ?? body['data'] ?? body;
        if (raw is Map && raw['id'] != null) {
          final user = UserModel.fromJson(Map<String, dynamic>.from(raw));
          if (user.fullName.isNotEmpty) return user;
        }
      }
    } catch (_) {}
    return null;
  }
}