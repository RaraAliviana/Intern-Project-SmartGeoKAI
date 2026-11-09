/// Batas panjang input, harus sama dengan struktur database dan aturan
/// validasi di Laravel (lihat User::FULL_NAME_MAX, User::NIPP_MAX, dst).
class FieldLimits {
  FieldLimits._();

  static const int fullNameMax = 60;
  static const int nippMax = 10;

  // Aset
  static const int idAssetLength = 11; // format NN.NN.NNNNN
  static const int gmapsUrlMax = 255;
  static const int descriptionMax = 5000;
  static const double areaMax = 999999999.99;
}