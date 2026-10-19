
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter/foundation.dart';

import '../../../core/config/app_config.dart';
import '../../../core/services/local_storage_service.dart';
import '../models/user_model.dart';

class AuthResult {
  final bool success;
  final String? message;
  final UserModel? user;
  final String? token;

  AuthResult({
    required this.success,
    this.message,
    this.user,
    this.token,
  });
}

class AuthService {
  // Nama perangkat untuk token Sanctum
  // Contoh: SmartGeoKAI-android, SmartGeoKAI-windows
  String _deviceName() {
    final platform = kIsWeb ? 'web' : defaultTargetPlatform.name;
    return 'SmartGeoKAI-$platform';
  }

  // Login
  Future<AuthResult> login({
    required String username,
    required String password,
  }) async {
    try {
      final response = await http
          .post(
            Uri.parse(
              '${AppConfig.baseUrl}${AppConfig.loginEndpoint}',
            ),
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
            },
            body: jsonEncode({
              'username': username,
              'password': password,
              'device_name': _deviceName(),
            }),
          )
          .timeout(const Duration(seconds: 15));

      final decoded = jsonDecode(response.body);

      if (decoded is! Map<String, dynamic>) {
        debugPrint('LOGIN ERROR: Format respons API tidak valid.');

        return AuthResult(
          success: false,
          message: 'Respons server tidak valid.',
        );
      }

      final data = decoded;

      if (response.statusCode == 200) {
        // Respons Laravel:
        // {
        //   "success": true,
        //   "message": "Login berhasil.",
        //   "data": {
        //     "token": "...",
        //     "user": {...}
        //   }
        // }

        final resultData = data['data'];

        if (resultData is! Map<String, dynamic>) {
          debugPrint('LOGIN ERROR: Data login tidak ditemukan.');

          return AuthResult(
            success: false,
            message: 'Format data login dari server tidak valid.',
          );
        }

        final token = resultData['token'];

        final userData = resultData['user'];

        if (token is! String ||
            userData is! Map<String, dynamic>) {
          debugPrint('LOGIN ERROR: Token atau data user tidak valid.');

          return AuthResult(
            success: false,
            message: 'Data login dari server tidak lengkap.',
          );
        }

        final user = UserModel.fromJson(userData);

        // Simpan token dan informasi user
        await LocalStorageService.saveToken(token);

        await LocalStorageService.saveUserInfo(
          id: user.id,
          name: user.fullName,
          role: user.role,
          nip: user.nip,
        );

        return AuthResult(
          success: true,
          message: data['message'] as String? ?? 'Login berhasil.',
          user: user,
          token: token,
        );
      }

      // Penanganan error Laravel: 401, 403, 422, dan lainnya
      final message = data['message'] as String? ?? 'Login gagal.';

      debugPrint(
        'LOGIN ERROR: HTTP ${response.statusCode} - $message',
      );

      return AuthResult(
        success: false,
        message: message,
      );
    } catch (e, stackTrace) {
      debugPrint('LOGIN ERROR: $e');
      debugPrintStack(stackTrace: stackTrace);

      return AuthResult(
        success: false,
        message:
            'Tidak dapat terhubung ke server. Periksa koneksi internet.',
      );
    }
  }

  // Logout
  Future<void> logout() async {
    try {
      final token = await LocalStorageService.getToken();

      if (token != null) {
        await http
            .post(
              Uri.parse(
                '${AppConfig.baseUrl}${AppConfig.logoutEndpoint}',
              ),
              headers: {
                'Authorization': 'Bearer $token',
                'Accept': 'application/json',
              },
            )
            .timeout(const Duration(seconds: 10));
      }
    } catch (e) {
      debugPrint('LOGOUT ERROR: $e');

      // Tetap logout lokal meski server error
    } finally {
      await LocalStorageService.clearAll();
    }
  }

  // Cek apakah token tersimpan
  Future<bool> isLoggedIn() async {
    final token = await LocalStorageService.getToken();
    return token != null;
  }
}
