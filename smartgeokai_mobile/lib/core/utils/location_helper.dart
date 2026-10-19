import 'package:geolocator/geolocator.dart';

class LocationException implements Exception {
  final String message;
  const LocationException(this.message);

  @override
  String toString() => message;
}

/// Ambil posisi saat ini, lengkap dengan pengecekan GPS dan izin.
Future<Position> getCurrentPosition() async {
  final enabled = await Geolocator.isLocationServiceEnabled();
  if (!enabled) {
    throw const LocationException(
      'Aktifkan layanan lokasi (GPS) terlebih dahulu.',
    );
  }

  var permission = await Geolocator.checkPermission();
  if (permission == LocationPermission.denied) {
    permission = await Geolocator.requestPermission();
  }
  if (permission == LocationPermission.denied) {
    throw const LocationException('Izin lokasi ditolak.');
  }
  if (permission == LocationPermission.deniedForever) {
    throw const LocationException(
      'Izin lokasi ditolak permanen. Aktifkan lewat pengaturan.',
    );
  }

  return Geolocator.getCurrentPosition(
    locationSettings: const LocationSettings(
      accuracy: LocationAccuracy.high,
      timeLimit: Duration(seconds: 20),
    ),
  );
}