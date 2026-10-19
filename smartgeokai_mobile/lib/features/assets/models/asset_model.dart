import 'package:flutter/material.dart';
import 'package:latlong2/latlong.dart';
import '../../../core/constants/app_colors.dart';

enum AssetStatus {
  clear('Clear', AppColors.clear),
  proses('Proses', AppColors.proses),
  masalah('Masalah', AppColors.masalah);

  final String label;
  final Color  color;
  const AssetStatus(this.label, this.color);

  static AssetStatus parse(dynamic raw) {
    final s = (raw ?? '').toString().toLowerCase();
    if (s.contains('masalah') || s.contains('problem')) return AssetStatus.masalah;
    if (s.contains('proses') || s.contains('process') || s.contains('progress')) {
      return AssetStatus.proses;
    }
    return AssetStatus.clear;
  }
}

class AssetModel {
  final String id;
  final String code;
  final String name;
  final String type;
  final String address;
  final String province;
  final String regency;
  final String district;
  final AssetStatus status;
  final double?   latitude;
  final double?   longitude;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const AssetModel({
    required this.id,
    required this.code,
    required this.name,
    required this.type,
    required this.address,
    required this.province,
    required this.regency,
    required this.district,
    required this.status,
    required this.latitude,
    required this.longitude,
    required this.createdAt,
    required this.updatedAt,
  });

  bool get hasLocation => latitude != null && longitude != null;

  LatLng? get point => hasLocation ? LatLng(latitude!, longitude!) : null;

  String get displayName => name.isNotEmpty ? name : (code.isNotEmpty ? code : '-');

  String get displayCode => code.isNotEmpty ? code : id;

  String get regionText => [district, regency, province]
      .where((e) => e.isNotEmpty)
      .join(', ');

  // Waktu aktivitas terakhir pada aset ini
  DateTime? get activityTime => updatedAt ?? createdAt;

  // Aset baru ditambahkan (belum pernah diubah setelah dibuat)
  bool get isNewlyAdded =>
      createdAt != null &&
      (updatedAt == null ||
          updatedAt!.difference(createdAt!).inSeconds.abs() < 5);

  // ── Parsing: SESUAIKAN DI SINI kalau nama field backend berbeda ──────
  factory AssetModel.fromJson(Map<String, dynamic> json) {
    final lat = _num(json['latitude'] ?? json['lat']);
    final lng = _num(json['longitude'] ?? json['lng'] ?? json['long'] ?? json['lon']);
    final valid = lat != null &&
        lng != null &&
        lat >= -90 && lat <= 90 &&
        lng >= -180 && lng <= 180 &&
        !(lat == 0 && lng == 0);

    return AssetModel(
      id:       _first(json, ['id']),
      code:     _first(json, ['code', 'asset_code', 'kode', 'kode_aset']),
      name:     _first(json, ['name', 'nama', 'asset_name', 'nama_aset']),
      type:     _first(json, ['type', 'asset_type', 'jenis', 'jenis_aset', 'category']),
      address:  _first(json, ['address', 'alamat']),
      province: _first(json, ['province', 'province_name', 'provinsi']),
      regency:  _first(json, ['regency', 'regency_name', 'kabupaten', 'city']),
      district: _first(json, ['district', 'district_name', 'kecamatan']),
      status:   AssetStatus.parse(json['status']),
      latitude:  valid ? lat : null,
      longitude: valid ? lng : null,
      createdAt: _date(json['created_at'] ?? json['createdAt']),
      updatedAt: _date(json['updated_at'] ?? json['updatedAt']),
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

  static double? _num(dynamic v) => v == null ? null : double.tryParse(v.toString());

  static DateTime? _date(dynamic v) =>
      v == null ? null : DateTime.tryParse(v.toString());
}