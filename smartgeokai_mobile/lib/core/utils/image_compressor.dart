import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:image/image.dart' as img;

/// Batas ukuran foto. Sengaja sedikit di bawah 1 MB supaya aman
/// terhadap aturan `max:1024` (KB) di Laravel.
const int kMaxPhotoBytes = 1000 * 1000;

/// Kompres foto hingga <= 1 MB (dijalankan di isolate agar UI tidak macet).
Future<Uint8List> compressToMax1MB(Uint8List input) =>
    compute(_compress, input);

Uint8List _compress(Uint8List input) {
  var image = img.decodeImage(input);
  if (image == null) {
    throw const FormatException('Gambar tidak dapat dibaca');
  }

  // Perbaiki rotasi dari data EXIF (foto HP sering miring)
  image = img.bakeOrientation(image);

  // Perkecil dimensi kalau terlalu besar
  const maxSide = 1920;
  if (image.width > maxSide || image.height > maxSide) {
    image = image.width >= image.height
        ? img.copyResize(image, width: maxSide)
        : img.copyResize(image, height: maxSide);
  }

  var quality = 85;
  var out = Uint8List.fromList(img.encodeJpg(image, quality: quality));

  // Turunkan kualitas, lalu dimensi, sampai <= batas
  while (out.lengthInBytes > kMaxPhotoBytes) {
    if (quality > 45) {
      quality -= 10;
    } else {
      image = img.copyResize(image, width: (image.width * 0.8).round());
    }
    out = Uint8List.fromList(img.encodeJpg(image, quality: quality));
    if (image.width < 400) break;
  }
  return out;
}