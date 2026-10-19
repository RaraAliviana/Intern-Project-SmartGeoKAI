import '../config/app_config.dart';

class ApiConstants {
  static String get baseUrl => AppConfig.baseUrl;

  static const String login          = AppConfig.loginEndpoint;
  static const String logout         = AppConfig.logoutEndpoint;
  static const String me             = AppConfig.profileEndpoint;
  static const String changePassword = AppConfig.changePasswordEndpoint;
  static const String assets         = '/assets';
  static const String provinces      = '/regions/provinces';
  static const String regencies      = '/regions/regencies';
  static const String districts      = '/regions/districts';
}