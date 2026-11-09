import 'package:flutter/foundation.dart';

class AppConfig {
  // Opsional: flutter run --dart-define=API_BASE_URL=http://192.168.x.x:8000/api
  static const String _override = String.fromEnvironment('API_BASE_URL');

  static String get baseUrl {
    if (_override.isNotEmpty) return _override;
    // Web, Android (lewat adb reverse), dan platform lain sama-sama 127.0.0.1
    return 'http://127.0.0.1:8000/api';
  }

  static const String loginEndpoint = '/auth/login';
  static const String logoutEndpoint = '/auth/logout';
  static const String profileEndpoint = '/profile';
  static const String changePasswordEndpoint = '/profile/password';
}