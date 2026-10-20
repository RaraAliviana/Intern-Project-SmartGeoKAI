
import 'dart:typed_data';

import 'package:dio/dio.dart';
import 'package:smartgeokai_mobile/core/constants/api_constants.dart';
import 'package:smartgeokai_mobile/core/network/api_client.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';

class AssetInput {
  final String idAsset;
  final String assetType;
  final String classification;
  final String assetGroup;
  final String provinceId;
  final String regencyId;
  final String districtId;
  final String areaM2;
  final double latitude;
  final double longitude;
  final String gmapsUrl;
  final AssetStatus status;
  final String description;

  const AssetInput({
    required this.idAsset,
    required this.assetType,
    required this.classification,
    required this.assetGroup,
    required this.provinceId,
    required this.regencyId,
    required this.districtId,
    required this.areaM2,
    required this.latitude,
    required this.longitude,
    required this.gmapsUrl,
    required this.status,
    required this.description,
  });
}

class AssetSaveResult {
  final bool success;
  final String? message;
  final Map<String, String> fieldErrors;
  final bool unauthorized;

  const AssetSaveResult({
    required this.success,
    this.message,
    this.fieldErrors = const {},
    this.unauthorized = false,
  });
}

class AssetWriteService {
  // Sesuaikan dengan nama field yang diterima AssetController Laravel.
  static const photoField = 'photos[]';
  static const removeField = 'delete_images[]';

  /// [id] null = tambah aset baru, terisi = ubah aset.
  Future<AssetSaveResult> save({
    String? id,
    required AssetInput input,
    List<Uint8List> newPhotos = const [],
    List<String> removedPhotoIds = const [],
  }) async {
    try {
      final form = FormData();

      form.fields.addAll([
        MapEntry('id_asset', input.idAsset),
        MapEntry('asset_type', input.assetType),
        MapEntry('classification', input.classification),
        MapEntry('asset_group', input.assetGroup),
        MapEntry('province_id', input.provinceId),
        MapEntry('regency_id', input.regencyId),
        MapEntry('district_id', input.districtId),
        MapEntry('area_m2', input.areaM2),
        MapEntry('latitude', input.latitude.toString()),
        MapEntry('longitude', input.longitude.toString()),
        MapEntry('gmaps_url', input.gmapsUrl),
        MapEntry('status', input.status.apiValue),
        MapEntry('description', input.description),
        if (id != null) const MapEntry('_method', 'PUT'),
      ]);

      // Foto lama yang akan dihapus.
      for (final photoId in removedPhotoIds) {
        form.fields.add(
          MapEntry(removeField, photoId),
        );
      }

      // Foto baru yang akan diunggah.
      for (var i = 0; i < newPhotos.length; i++) {
        form.files.add(
          MapEntry(
            photoField,
            MultipartFile.fromBytes(
              newPhotos[i],
              filename: 'foto_${i + 1}.jpg',
            ),
          ),
        );
      }

      final path = id == null
          ? ApiConstants.assets
          : '${ApiConstants.assets}/$id';

      final res = await ApiClient.instance.post(
        path,
        data: form,
        options: Options(
          sendTimeout: const Duration(minutes: 3),
          receiveTimeout: const Duration(minutes: 2),
        ),
      );

      final body = res.data;
      final msg = body is Map && body['message'] is String
          ? body['message'] as String
          : null;

      return AssetSaveResult(
        success: true,
        message: msg ??
            (id == null
                ? 'Aset berhasil ditambahkan.'
                : 'Aset berhasil diperbarui.'),
      );
    } on DioException catch (e) {
      final status = e.response?.statusCode;
      final data = e.response?.data;

      final serverMsg = data is Map && data['message'] is String
          ? data['message'] as String
          : null;

      if (status == 401) {
        return const AssetSaveResult(
          success: false,
          unauthorized: true,
          message: 'Sesi Anda telah berakhir. Silakan login kembali.',
        );
      }

      if (status == 403) {
        return AssetSaveResult(
          success: false,
          message: serverMsg ??
              'Anda tidak memiliki hak akses untuk aset atau wilayah ini.',
        );
      }

      if (status == 413) {
        return const AssetSaveResult(
          success: false,
          message: 'Ukuran data terlalu besar. Kurangi jumlah foto.',
        );
      }

      if (status == 422) {
        final errors = <String, String>{};

        if (data is Map && data['errors'] is Map) {
          (data['errors'] as Map).forEach((key, value) {
            errors[key.toString()] =
                value is List && value.isNotEmpty
                    ? value.first.toString()
                    : value.toString();
          });
        }

        return AssetSaveResult(
          success: false,
          message: serverMsg ?? 'Data yang dikirim tidak valid.',
          fieldErrors: errors,
        );
      }

      if (status == null) {
        return const AssetSaveResult(
          success: false,
          message:
              'Tidak dapat terhubung ke server. Periksa koneksi internet.',
        );
      }

      return AssetSaveResult(
        success: false,
        message: serverMsg ?? 'Gagal menyimpan aset ($status).',
      );
    } catch (_) {
      return const AssetSaveResult(
        success: false,
        message: 'Terjadi kesalahan tak terduga.',
      );
    }
  }
}
