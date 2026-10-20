
import 'dart:typed_data';

import 'package:flutter/foundation.dart';
import 'package:image/image.dart' as img;

/// Batas ukuran foto sedikit di bawah 1 MB
/// agar aman dengan validasi Laravel max:1024.
const int kMaxPhotoBytes = 1000 * 1000;

/// Kompres foto hingga maksimal sekitar 1 MB.
Future<Uint8List> compressToMax1MB(Uint8List input) {
  return compute(_compress, input);
}

Uint8List _compress(Uint8List input) {
  final decodedImage = img.decodeImage(input);

  if (decodedImage == null) {
    throw const FormatException('Gambar tidak dapat dibaca');
  }

  // Perbaiki orientasi foto berdasarkan metadata EXIF.
  img.Image image = img.bakeOrientation(decodedImage);

  // Batasi dimensi awal agar proses kompresi lebih ringan.
  const maxSide = 1920;

  if (image.width > maxSide || image.height > maxSide) {
    if (image.width >= image.height) {
      image = img.copyResize(
        image,
        width: maxSide,
      );
    } else {
      image = img.copyResize(
        image,
        height: maxSide,
      );
    }
  }

  var quality = 85;
  var output = Uint8List.fromList(
    img.encodeJpg(image, quality: quality),
  );

  // Turunkan kualitas terlebih dahulu.
  while (output.lengthInBytes > kMaxPhotoBytes && quality > 45) {
    quality -= 10;

    output = Uint8List.fromList(
      img.encodeJpg(image, quality: quality),
    );
  }

  // Jika kualitas minimum belum cukup, perkecil dimensi gambar.
  while (output.lengthInBytes > kMaxPhotoBytes) {
    final newWidth = (image.width * 0.8).round();
    final newHeight = (image.height * 0.8).round();

    // Hindari dimensi terlalu kecil atau tidak valid.
    if (newWidth < 1 || newHeight < 1) {
      break;
    }

    image = img.copyResize(
      image,
      width: newWidth,
      height: newHeight,
    );

    output = Uint8List.fromList(
      img.encodeJpg(image, quality: 45),
    );
  }

  return output;
}
