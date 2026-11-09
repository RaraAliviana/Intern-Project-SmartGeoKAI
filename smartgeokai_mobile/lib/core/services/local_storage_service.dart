import 'package:shared_preferences/shared_preferences.dart';
import '../storage/secure_storage.dart';

class LocalStorageService {
  static const String _userIdKey   = 'user_id';
  static const String _userNameKey = 'user_name';
  static const String _userRoleKey = 'user_role';
  static const String _userNippKey = 'user_nipp';
  // Kunci lama (sebelum NIP diganti NIPP), hanya dibaca untuk migrasi
  static const String _legacyUserNipKey = 'user_nip';

  static Future<void> saveToken(String token) => SecureStorage.saveToken(token);
  static Future<String?> getToken() => SecureStorage.getToken();
  static Future<void> removeToken() => SecureStorage.deleteToken();

  static Future<void> saveUserInfo({
    required int id,
    required String name,
    required String role,
    required String nipp,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt(_userIdKey, id);
    await prefs.setString(_userNameKey, name);
    await prefs.setString(_userRoleKey, role);
    await prefs.setString(_userNippKey, nipp);
    await prefs.remove(_legacyUserNipKey);
  }

  static Future<Map<String, dynamic>?> getUserInfo() async {
    final prefs = await SharedPreferences.getInstance();
    final id   = prefs.getInt(_userIdKey);
    final name = prefs.getString(_userNameKey);
    final role = prefs.getString(_userRoleKey);
    final nipp = prefs.getString(_userNippKey) ??
        prefs.getString(_legacyUserNipKey);

    if (id == null || name == null) return null;

    return {'id': id, 'name': name, 'role': role, 'nipp': nipp};
  }

  static Future<void> clearAll() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
    await SecureStorage.deleteToken();
  }
}