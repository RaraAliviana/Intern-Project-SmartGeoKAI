
import 'package:dio/dio.dart';

import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../../auth/models/user_model.dart';

class ProfileResult {
  final bool success;
  final String? message;
  final UserModel? user;
  final Map<String, String> fieldErrors;
  final bool unauthorized;

  const ProfileResult({
    required this.success,
    this.message,
    this.user,
    this.fieldErrors = const {},
    this.unauthorized = false,
  });
}

class ProfileService {
  /// GET /profile
  Future<ProfileResult> fetchProfile() {
    return _run(
      () => ApiClient.instance.get(ApiConstants.me),
    );
  }

  /// PUT /profile
  /// Body: { full_name, nipp }
  Future<ProfileResult> updateProfile({
    required String fullName,
    required String nipp,
  }) {
    return _run(
      () => ApiClient.instance.put(
        ApiConstants.me,
        data: {
          'full_name': fullName,
          'nipp': nipp,
        },
      ),
      okMessage: 'Profil berhasil diperbarui.',
    );
  }

  /// PUT /profile/password
  Future<ProfileResult> changePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmPassword,
  }) {
    return _run(
      () => ApiClient.instance.put(
        ApiConstants.changePassword,
        data: {
          'current_password': currentPassword,
          'password': newPassword,
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
      final response = await call();
      final body = response.data;

      return ProfileResult(
        success: true,
        message: _readMessage(body) ?? okMessage,
        user: _parseUser(body),
      );
    } on DioException catch (e) {
      final status = e.response?.statusCode;
      final data = e.response?.data;

      if (status == 401) {
        return const ProfileResult(
          success: false,
          unauthorized: true,
          message: 'Sesi Anda telah berakhir. Silakan login kembali.',
        );
      }

      if (status == 422) {
        return ProfileResult(
          success: false,
          message: _readMessage(data) ??
              'Data yang dikirim tidak valid.',
          fieldErrors: _readFieldErrors(data),
        );
      }

      if (status == 403) {
        return ProfileResult(
          success: false,
          message: _readMessage(data) ??
              'Anda tidak memiliki akses.',
        );
      }

      if (status == 404) {
        return const ProfileResult(
          success: false,
          message: 'Endpoint profil tidak ditemukan (404).',
        );
      }

      if (status != null) {
        return ProfileResult(
          success: false,
          message: _readMessage(data) ??
              'Terjadi kesalahan pada server (HTTP $status).',
        );
      }

      return ProfileResult(
        success: false,
        message: 'Tidak dapat terhubung ke server. Periksa koneksi internet.',
      );
    } catch (e, stackTrace) {
      // Informasi ini membantu debugging saat pengembangan.
      print('PROFILE SERVICE ERROR: $e');
      print(stackTrace);

      return const ProfileResult(
        success: false,
        message: 'Terjadi kesalahan tak terduga.',
      );
    }
  }

  String? _readMessage(dynamic body) {
    if (body is Map && body['message'] is String) {
      return body['message'].toString();
    }

    return null;
  }

  Map<String, String> _readFieldErrors(dynamic body) {
    final errors = <String, String>{};

    if (body is Map && body['errors'] is Map) {
      (body['errors'] as Map).forEach((key, value) {
        if (value is List && value.isNotEmpty) {
          errors[key.toString()] = value.first.toString();
        } else {
          errors[key.toString()] = value.toString();
        }
      });
    }

    return errors;
  }

  UserModel? _parseUser(dynamic body) {
    try {
      if (body is! Map) return null;

      dynamic raw;

      if (body['user'] is Map) {
        raw = body['user'];
      } else if (body['data'] is Map) {
        raw = body['data'];
      } else {
        raw = body;
      }

      if (raw is! Map || raw['id'] == null) {
        return null;
      }

      final user = UserModel.fromJson(
        Map<String, dynamic>.from(raw),
      );

      if (user.id <= 0) return null;

      return user;
    } catch (e) {
      print('PROFILE PARSE ERROR: $e');
      return null;
    }
  }
}
