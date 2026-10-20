import 'package:flutter/material.dart';
import 'package:latlong2/latlong.dart';
import 'package:smartgeokai_mobile/core/config/app_config.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';

enum AssetStatus {
  // Argumen ke-3 = nilai yang DIKIRIM ke backend.
  // SESUAIKAN dengan nilai status di web (mis. 'bermasalah' kalau begitu).
  clear('Clear', AppColors.clear, 'Clear'),
  proses('Proses', AppColors.proses, 'Proses'),
  masalah('Masalah', AppColors.masalah, 'Masalah');

  final String label;
  final Color  color;
  final String apiValue;
  const AssetStatus(this.label, this.color, this.apiValue);

  static AssetStatus parse(dynamic raw) {
    final s = (raw ?? '').toString().toLowerCase();
    if (s.contains('masalah') || s.contains('problem')) return AssetStatus.masalah;
    if (s.contains('proses') || s.contains('process') || s.contains('progress')) {
      return AssetStatus.proses;
    }
    return AssetStatus.clear;
  }
}

/// Satu baris tabel asset_images.
class AssetPhoto {
  final String? id;
  final String  url;

  const AssetPhoto({this.id, required this.url});

  static AssetPhoto? from(dynamic raw) {
    String? id;
    var url = '';

    if (raw is String) {
      url = _absolute(raw);
    } else if (raw is Map) {
      id = raw['id']?.toString();
      final direct = (raw['url'] ?? raw['photo_url'] ?? raw['image_url'] ?? '')
          .toString();
      final path = (raw['file_path'] ?? raw['path'] ?? '').toString();
      if (direct.isNotEmpty) {
        url = _absolute(direct);
      } else if (path.isNotEmpty) {
        url = _absolute(path, storage: true);
      }
    }
    return url.isEmpty ? null : AssetPhoto(id: id, url: url);
  }

  // file_path biasanya relatif terhadap disk "public" → /storage/<file_path>
  static String _absolute(String value, {bool storage = false}) {
    if (value.startsWith('http://') || value.startsWith('https://')) {
      return value;
    }
    final origin = AppConfig.baseUrl.replaceFirst(RegExp(r'/api/?$'), '');
    var p = value.startsWith('/') ? value.substring(1) : value;
    if (storage && !p.startsWith('storage/')) p = 'storage/$p';
    return '$origin/$p';
  }
}

class AssetModel {
  final String id;
  final String code;            // id_asset
  final String type;            // asset_type
  final String classification;
  final String assetGroup;
  final double? areaM2;
  final String gmapsUrl;
  final String description;
  final String province;
  final String regency;
  final String district;
  final String provinceId;
  final String regencyId;
  final String districtId;
  final AssetStatus status;
  final double?   latitude;
  final double?   longitude;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final List<AssetPhoto> photos;

  const AssetModel({
    required this.id,
    required this.code,
    required this.type,
    required this.classification,
    required this.assetGroup,
    required this.areaM2,
    required this.gmapsUrl,
    required this.description,
    required this.province,
    required this.regency,
    required this.district,
    required this.provinceId,
    required this.regencyId,
    required this.districtId,
    required this.status,
    required this.latitude,
    required this.longitude,
    required this.createdAt,
    required this.updatedAt,
    required this.photos,
  });

  // Kompatibilitas dengan halaman lama (aset tidak punya "nama" sendiri)
  String get name    => code;
  String get address => description;

  bool get hasLocation => latitude != null && longitude != null;

  LatLng? get point => hasLocation ? LatLng(latitude!, longitude!) : null;

  String get displayName => code.isNotEmpty ? code : (type.isNotEmpty ? type : '-');

  String get displayCode => code.isNotEmpty ? code : id;

  String get regionText => [district, regency, province]
      .where((e) => e.isNotEmpty)
      .join(', ');

  DateTime? get activityTime => updatedAt ?? createdAt;

  bool get isNewlyAdded =>
      createdAt != null &&
      (updatedAt == null ||
          updatedAt!.difference(createdAt!).inSeconds.abs() < 5);

  factory AssetModel.fromJson(Map<String, dynamic> json) {
    final lat = _num(json['latitude'] ?? json['lat']);
    final lng = _num(json['longitude'] ?? json['lng'] ?? json['long']);
    final valid = lat != null &&
        lng != null &&
        lat >= -90 && lat <= 90 &&
        lng >= -180 && lng <= 180 &&
        !(lat == 0 && lng == 0);

    final rawPhotos = json['images'] ?? json['asset_images'] ?? json['photos'];
    final photos = <AssetPhoto>[];
    if (rawPhotos is List) {
      for (final p in rawPhotos) {
        final photo = AssetPhoto.from(p);
        if (photo != null) photos.add(photo);
      }
    }

    return AssetModel(
      id:             _first(json, ['id']),
      code:           _first(json, ['id_asset', 'code', 'asset_code', 'kode']),
      type:           _first(json, ['asset_type', 'type', 'jenis']),
      classification: _first(json, ['classification', 'klasifikasi']),
      assetGroup:     _first(json, ['asset_group', 'group']),
      areaM2:         _num(json['area_m2'] ?? json['area']),
      gmapsUrl:       _first(json, ['gmaps_url']),
      description:    _first(json, ['description', 'deskripsi']),
      province:   _first(json, ['province', 'province_name', 'provinsi']),
      regency:    _first(json, ['regency', 'regency_name', 'kabupaten', 'city']),
      district:   _first(json, ['district', 'district_name', 'kecamatan']),
      provinceId: _id(json, 'province_id', 'province'),
      regencyId:  _id(json, 'regency_id', 'regency'),
      districtId: _id(json, 'district_id', 'district'),
      status:     AssetStatus.parse(json['status']),
      latitude:   valid ? lat : null,
      longitude:  valid ? lng : null,
      createdAt:  _date(json['created_at'] ?? json['createdAt']),
      updatedAt:  _date(json['updated_at'] ?? json['updatedAt']),
      photos: photos,
    );
  }

  static String _text(dynamic v) {
    if (v == null) return '';
    if (v is Map) return _text(v['name'] ?? v['nama']);
    return v.toString();
  }

  static String _first(Map<String, dynamic> json, List<String> keys) {
    for (final k in keys) {
      final t = _text(json[k]);
      if (t.isNotEmpty) return t;
    }
    return '';
  }

  static String _id(Map<String, dynamic> json, String key, String nested) {
    final direct = json[key];
    if (direct != null) return direct.toString();
    final n = json[nested];
    if (n is Map && n['id'] != null) return n['id'].toString();
    return '';
  }

  static double? _num(dynamic v) => v == null ? null : double.tryParse(v.toString());

  static DateTime? _date(dynamic v) =>
      v == null ? null : DateTime.tryParse(v.toString());
}