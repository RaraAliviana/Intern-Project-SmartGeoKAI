
import 'package:flutter/material.dart';

import '../models/asset_model.dart';
import '../services/asset_service.dart';

class AssetProvider extends ChangeNotifier {
  final AssetService _service = AssetService();

  // Dipakai bersama oleh tab Peta dan tab Aset
  // supaya teks pencarian tetap sinkron.
  final TextEditingController searchController = TextEditingController();

  List<AssetModel> _all = const [];
  bool _loading = false;
  String? _error;
  int _fitVersion = 0;

  String _query = '';
  String? _province;
  String? _regency;
  String? _district;
  String? _type;
  AssetStatus? _status;

  // ============================================================
  // GETTER
  // ============================================================

  bool get isLoading => _loading;
  String? get error => _error;
  int get fitVersion => _fitVersion;
  int get totalCount => _all.length;

  String? get province => _province;
  String? get regency => _regency;
  String? get district => _district;
  String? get type => _type;
  AssetStatus? get status => _status;

  int get activeFilterCount => [
        _province,
        _regency,
        _district,
        _type,
        _status,
      ].where((e) => e != null).length;

  // Semua aset setelah pencarian dan filter diterapkan.
  List<AssetModel> get filtered => _filter(ignoreStatus: false);

  // Jumlah aset berdasarkan status.
  // Mengikuti filter lokasi, jenis, dan pencarian.
  Map<AssetStatus, int> get statusCounts {
    final base = _filter(ignoreStatus: true);

    return {
      for (final s in AssetStatus.values)
        s: base.where((a) => a.status == s).length,
    };
  }

  // ============================================================
  // OPSI KLASIFIKASI DAN GRUP ASET
  // ============================================================

  // Mengambil pilihan klasifikasi unik dari data aset.
  List<String> get classificationOptions =>
      _distinctOf((a) => a.classification);

  // Mengambil pilihan grup aset unik dari data aset.
  List<String> get groupOptions =>
      _distinctOf((a) => a.assetGroup);

  // Mengambil nilai unik, menghapus nilai kosong,
  // kemudian mengurutkannya berdasarkan abjad.
  List<String> _distinctOf(String Function(AssetModel a) pick) {
    final set = <String>{};

    for (final a in _all) {
      final value = pick(a).trim();

      if (value.isNotEmpty) {
        set.add(value);
      }
    }

    return set.toList()..sort();
  }

  // ============================================================
  // FITUR DASHBOARD
  // Perhitungan menggunakan SEMUA aset, tanpa filter.
  // ============================================================

  // Menghitung jumlah aset berdasarkan status.
  int countByStatus(AssetStatus s) {
    return _all.where((a) => a.status == s).length;
  }

  // Menghitung aset yang belum memiliki lokasi.
  int get withoutLocationCount {
    return _all.where((a) => !a.hasLocation).length;
  }

  // Mengambil aktivitas aset terbaru.
  List<AssetModel> recentActivities({int? limit}) {
    final list = _all
        .where((a) => a.activityTime != null)
        .toList()
      ..sort(
        (a, b) => b.activityTime!.compareTo(a.activityTime!),
      );

    return limit == null ? list : list.take(limit).toList();
  }

  // Dipakai kartu dashboard untuk menampilkan aset berdasarkan status.
  void showOnlyStatus(AssetStatus? s) {
    _query = '';
    searchController.clear();

    _province = null;
    _regency = null;
    _district = null;
    _type = null;

    _status = s;

    _fitVersion++;
    notifyListeners();
  }

  // ============================================================
  // OPSI DROPDOWN
  // ============================================================

  List<String> get provinceOptions =>
      _distinct(_all.map((a) => a.province));

  List<String> get typeOptions =>
      _distinct(_all.map((a) => a.type));

  List<String> regencyOptions(String? province) {
    return _distinct(
      _all
          .where(
            (a) => province == null || a.province == province,
          )
          .map((a) => a.regency),
    );
  }

  List<String> districtOptions(
    String? province,
    String? regency,
  ) {
    return _distinct(
      _all
          .where(
            (a) =>
                (province == null || a.province == province) &&
                (regency == null || a.regency == regency),
          )
          .map((a) => a.district),
    );
  }

  static List<String> _distinct(Iterable<String> values) {
    return values
        .where((e) => e.isNotEmpty)
        .toSet()
        .toList()
      ..sort();
  }

  // ============================================================
  // AKSI
  // ============================================================

  Future<void> load({bool resetFilters = false}) async {
    if (resetFilters) {
      _query = '';
      searchController.clear();

      _province = null;
      _regency = null;
      _district = null;
      _type = null;
      _status = null;
    }

    _loading = true;
    _error = null;
    notifyListeners();

    try {
      final result = await _service.fetchAll();

      if (result.success) {
        _all = result.assets;
        _fitVersion++;
      } else {
        _error = result.message;
      }
    } catch (e) {
      _error = e.toString();
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  // Mengatur pencarian.
  void setQuery(String value) {
    _query = value;
    notifyListeners();
  }

  // Dipakai chip status: ketuk lagi untuk membatalkan filter.
  void toggleStatus(AssetStatus s) {
    _status = (_status == s) ? null : s;

    _fitVersion++;
    notifyListeners();
  }

  // Menerapkan filter aset.
  void applyFilters({
    String? province,
    String? regency,
    String? district,
    String? type,
    AssetStatus? status,
  }) {
    _province = province;
    _regency = regency;
    _district = district;
    _type = type;
    _status = status;

    _fitVersion++;
    notifyListeners();
  }

  // Menghapus semua filter.
  void resetFilters() {
    _query = '';
    searchController.clear();

    _province = null;
    _regency = null;
    _district = null;
    _type = null;
    _status = null;

    _fitVersion++;
    notifyListeners();
  }

  // ============================================================
  // FILTER INTERNAL
  // ============================================================

  List<AssetModel> _filter({
    required bool ignoreStatus,
  }) {
    final q = _query.trim().toLowerCase();

    return _all.where((a) {
      if (!ignoreStatus && _status != null && a.status != _status) {
        return false;
      }

      if (_province != null && a.province != _province) {
        return false;
      }

      if (_regency != null && a.regency != _regency) {
        return false;
      }

      if (_district != null && a.district != _district) {
        return false;
      }

      if (_type != null && a.type != _type) {
        return false;
      }

      if (q.isNotEmpty &&
          !(a.name.toLowerCase().contains(q) ||
              a.code.toLowerCase().contains(q) ||
              a.id.toLowerCase().contains(q))) {
        return false;
      }

      return true;
    }).toList();
  }

  // ============================================================
  // DISPOSE
  // ============================================================

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }
}
