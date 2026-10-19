import 'package:smartgeokai_mobile/core/constants/api_constants.dart';
import 'package:smartgeokai_mobile/core/network/api_client.dart';

class RegionItem {
  final String id;
  final String name;
  const RegionItem(this.id, this.name);
}

class RegionService {
  Future<List<RegionItem>> provinces() => _fetch(ApiConstants.provinces);

  Future<List<RegionItem>> regencies(String provinceId) =>
      _fetch(ApiConstants.regencies, {'province_id': provinceId});

  Future<List<RegionItem>> districts(String regencyId) =>
      _fetch(ApiConstants.districts, {'regency_id': regencyId});

  // Melempar DioException kalau gagal; ditangani pemanggil.
  Future<List<RegionItem>> _fetch(
    String path, [
    Map<String, dynamic>? query,
  ]) async {
    final res  = await ApiClient.instance.get(path, queryParameters: query);
    final body = res.data;

    List<dynamic> raw = const [];
    if (body is List) {
      raw = body;
    } else if (body is Map) {
      final d = body['data'] ?? body['regions'] ?? body['items'];
      if (d is List) raw = d;
    }

    final items = <RegionItem>[];
    for (final r in raw) {
      if (r is Map) {
        final id   = (r['id'] ?? r['code'])?.toString();
        final name = (r['name'] ?? r['nama'])?.toString();
        if (id != null && name != null) items.add(RegionItem(id, name));
      }
    }
    return items;
  }
}