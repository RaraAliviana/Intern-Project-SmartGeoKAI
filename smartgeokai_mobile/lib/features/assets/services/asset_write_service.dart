import 'dart:typed_data';
import 'package:dio/dio.dart';
import 'package:smartgeokai_mobile/core/constants/api_constants.dart';
import 'package:smartgeokai_mobile/core/network/api_client.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';

class AssetInput {
  final String      name;
  final String      code;
  final String      type;
  final String      address;
  final AssetStatus status;
  final String      provinceId;
  final String      regencyId;
  final String      districtId;
  final double      latitude;
  final double      longitude;

  const AssetInput({
    required this.name,
    required this.code,
    required this.type,
    required this.address,
    required this.status,
    required this.provinceId,
    required this.regencyId,
    required this.districtId,
    required this.latitude,
    required this.longitude,
  });
}

class AssetSaveResult {
  final bool                success;
  final String?             message;
  final Map<String, String> fieldErrors;
  final bool                unauthorized;

  const AssetSaveResult({
    required this.success,
    this.message,
    this.fieldErrors  = const {},
    this.unauthorized = false,
  });
}

class AssetWriteService {
  /// [id] kosong = tambah aset baru, terisi = ubah aset.
  Future<AssetSaveResult> save({
    String? id,
    required AssetInput input,
    List<Uint8List> newPhotos = const [],
    List<String> removedPhotoIds = const [],
  }) async {
    try {
      final form = FormData();

      void add(String key, String value) {
        if (value.isNotEmpty) form.fields.add(MapEntry(key, value));
      }

      add('name', input.name);
      add('code', input.code);
      add('type', input.type);
      add('status', input.status.name); // clear | proses | masalah
      add('address', input.address);
      add('province_id', input.provinceId);
      add('regency_id', input.regencyId);
      add('district_id', input.districtId);
      add('latitude', input.latitude.toString());
      add('longitude', input.longitude.toString());

      if (id != null) add('_method', 'PUT');
      for (final r in removedPhotoIds) {
        add('remove_photo_ids[]', r);
      }
      for (var i = 0; i < newPhotos.length; i++) {
        form.files.add(
          MapEntry(
            'photos[]',
            MultipartFile.fromBytes(newPhotos[i], filename: 'foto_${i + 1}.jpg'),
          ),
        );
      }

      final path =
          id == null ? ApiConstants.assets : '${ApiConstants.assets}/$id';

      final res = await ApiClient.instance.post(
        path,
        data: form,
        options: Options(
          sendTimeout: const Duration(minutes: 3),
          receiveTimeout: const Duration(minutes: 2),
        ),
      );

      final body = res.data;
      final msg = (body is Map && body['message'] is String)
          ? body['message'] as String
          : null;
      return AssetSaveResult(
        success: true,
        message: msg ??
            (id == null ? 'Aset berhasil ditambahkan.' : 'Aset berhasil diperbarui.'),
      );
    } on DioException catch (e) {
      final status = e.response?.statusCode;
      final data   = e.response?.data;
      final serverMsg = (data is Map && data['message'] is String)
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
            errors[key.toString()] = value is List && value.isNotEmpty
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
          message: 'Tidak dapat terhubung ke server. Periksa koneksi internet.',
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