import 'package:flutter/foundation.dart';

class AppConfig {
  static String get baseUrl {
    if (kIsWeb) return 'http://127.0.0.1:8000/api';

    if (defaultTargetPlatform == TargetPlatform.android) {
      return 'http://10.0.2.2:8000/api';
    }

    return 'http://127.0.0.1:8000/api';
  }

  static const String loginEndpoint          = '/auth/login';
  static const String logoutEndpoint         = '/auth/logout';
  static const String profileEndpoint        = '/profile';
  static const String changePasswordEndpoint = '/profile/password';
}