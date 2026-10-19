import 'package:dio/dio.dart';
import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/asset_model.dart';

class AssetListResult {
  final bool             success;
  final String?          message;
  final List<AssetModel> assets;
  final bool             unauthorized;

  const AssetListResult({
    required this.success,
    this.message,
    this.assets       = const [],
    this.unauthorized = false,
  });
}

class AssetService {
  // GET /assets  (otomatis ikut semua halaman kalau backend memakai paginate)
  Future<AssetListResult> fetchAll() async {
    try {
      final all  = <AssetModel>[];
      var page     = 1;
      var lastPage = 1;

      do {
        final res = await ApiClient.instance.get(
          ApiConstants.assets,
          queryParameters: {'page': page, 'per_page': 500},
        );
        final body = res.data;

        List<dynamic> raw = const [];
        if (body is List) {
          raw = body;
        } else if (body is Map) {
          final d = body['data'] ?? body['assets'];
          if (d is List) {
            raw = d;
          } else if (d is Map && d['data'] is List) {
            raw = d['data'] as List;
          }
          lastPage = _lastPage(body);
        }

        for (final item in raw) {
          if (item is Map) {
            all.add(AssetModel.fromJson(Map<String, dynamic>.from(item)));
          }
        }
        page++;
      } while (page <= lastPage && page <= 100);

      return AssetListResult(success: true, assets: all);
    } on DioException catch (e) {
      final status = e.response?.statusCode;
      final data   = e.response?.data;

      if (status == 401) {
        return const AssetListResult(
          success: false,
          unauthorized: true,
          message: 'Sesi Anda telah berakhir. Silakan login kembali.',
        );
      }
      if (status == null) {
        return const AssetListResult(
          success: false,
          message: 'Tidak dapat terhubung ke server. Periksa koneksi internet.',
        );
      }
      final msg = (data is Map && data['message'] is String)
          ? data['message'] as String
          : 'Gagal memuat aset ($status).';
      return AssetListResult(success: false, message: msg);
    } catch (e) {
      return AssetListResult(
        success: false,
        message: 'Gagal membaca data aset. Format respons tidak sesuai.',
      );
    }
  }

  int _lastPage(Map body) {
    dynamic v = body['meta'] is Map ? (body['meta'] as Map)['last_page'] : null;
    v ??= body['last_page'];
    if (v == null && body['data'] is Map) v = (body['data'] as Map)['last_page'];
    if (v is num) return v.toInt();
    return int.tryParse('$v') ?? 1;
  }
}