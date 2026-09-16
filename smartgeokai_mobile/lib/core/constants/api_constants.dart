class ApiConstants {
  // PENTING: alamat berbeda tergantung tempat testing
  //
  // - Android Emulator      -> 10.0.2.2 (bukan 127.0.0.1/localhost!)
  // - iOS Simulator         -> 127.0.0.1 atau localhost bisa langsung
  // - HP fisik (via WiFi)   -> IP address laptop kamu di jaringan yang sama,
  //                            contoh: 192.168.1.5 (cek dengan `ipconfig`)
  //
  // Ganti baris di bawah sesuai kondisi testing kamu:
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // Endpoints
  static const String login = '/login';
  static const String logout = '/logout';
  static const String me = '/me';
  static const String assets = '/assets';
  static const String provinces = '/regions/provinces';
  static const String regencies = '/regions/regencies';
  static const String districts = '/regions/districts';
}