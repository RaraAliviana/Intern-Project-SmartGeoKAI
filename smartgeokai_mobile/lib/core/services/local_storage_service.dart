import 'package:shared_preferences/shared_preferences.dart';
import '../storage/secure_storage.dart';

class LocalStorageService {
  static const String _userIdKey   = 'user_id';
  static const String _userNameKey = 'user_name';
  static const String _userRoleKey = 'user_role';
  static const String _userNipKey  = 'user_nip';

  static Future<void> saveToken(String token) => SecureStorage.saveToken(token);
  static Future<String?> getToken() => SecureStorage.getToken();
  static Future<void> removeToken() => SecureStorage.deleteToken();

  static Future<void> saveUserInfo({
    required int id,
    required String name,
    required String role,
    required String nip,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt(_userIdKey, id);
    await prefs.setString(_userNameKey, name);
    await prefs.setString(_userRoleKey, role);
    await prefs.setString(_userNipKey, nip);
  }

  static Future<Map<String, dynamic>?> getUserInfo() async {
    final prefs = await SharedPreferences.getInstance();
    final id   = prefs.getInt(_userIdKey);
    final name = prefs.getString(_userNameKey);
    final role = prefs.getString(_userRoleKey);
    final nip  = prefs.getString(_userNipKey);

    if (id == null || name == null) return null;

    return {'id': id, 'name': name, 'role': role, 'nip': nip};
  }

  static Future<void> clearAll() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
    await SecureStorage.deleteToken();
  }
}