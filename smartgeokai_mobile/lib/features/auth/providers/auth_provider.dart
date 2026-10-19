import 'package:flutter/foundation.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';
import '../../../core/services/local_storage_service.dart';

enum AuthStatus { initial, loading, authenticated, unauthenticated, error }

class AuthProvider extends ChangeNotifier {
  final AuthService _authService = AuthService();

  AuthStatus _status  = AuthStatus.initial;
  UserModel? _user;
  String?    _errorMessage;

  AuthStatus get status       => _status;
  UserModel? get user         => _user;
  String?    get errorMessage => _errorMessage;
  bool       get isLoading    => _status == AuthStatus.loading;

  Future<void> checkAuthStatus() async {
    _status = AuthStatus.loading;
    notifyListeners();

    final isLoggedIn = await _authService.isLoggedIn();

    if (isLoggedIn) {
      final userInfo = await LocalStorageService.getUserInfo();
      if (userInfo != null) {
        _user = UserModel(
          id:       userInfo['id']   as int,
          fullName: userInfo['name'] as String,
          username: '',
          nip:      userInfo['nip']  as String? ?? '',
          role:     userInfo['role'] as String? ?? 'petugas',
          isActive: true,
        );
        _status = AuthStatus.authenticated;
      } else {
        _status = AuthStatus.unauthenticated;
      }
    } else {
      _status = AuthStatus.unauthenticated;
    }

    notifyListeners();
  }

  Future<bool> login({
    required String username,
    required String password,
  }) async {
    _status       = AuthStatus.loading;
    _errorMessage = null;
    notifyListeners();

    final result = await _authService.login(
      username: username,
      password: password,
    );

    if (result.success) {
      _user   = result.user;
      _status = AuthStatus.authenticated;
      notifyListeners();
      return true;
    }

    _status       = AuthStatus.error;
    _errorMessage = result.message;
    notifyListeners();
    return false;
  }

  // BARU: perbarui data user setelah edit profil / refresh dari server
  Future<void> setUser(UserModel user) async {
    _user = user;
    await LocalStorageService.saveUserInfo(
      id:   user.id,
      name: user.fullName,
      role: user.role,
      nip:  user.nip,
    );
    notifyListeners();
  }

  Future<void> logout() async {
    _status = AuthStatus.loading;
    notifyListeners();

    await _authService.logout();

    _user   = null;
    _status = AuthStatus.unauthenticated;
    notifyListeners();
  }
}