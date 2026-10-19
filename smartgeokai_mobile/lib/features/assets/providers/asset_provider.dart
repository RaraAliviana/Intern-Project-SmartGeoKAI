import 'package:flutter/material.dart';
import '../models/asset_model.dart';
import '../services/asset_service.dart';

class AssetProvider extends ChangeNotifier {
  final AssetService _service = AssetService();

  // Dipakai bersama oleh tab Peta dan tab Aset supaya teksnya sinkron
  final TextEditingController searchController = TextEditingController();

  List<AssetModel> _all = const [];
  bool    _loading    = false;
  String? _error;
  int     _fitVersion = 0; // naik tiap data/filter berubah -> peta menyesuaikan zoom

  String       _query = '';
  String?      _province;
  String?      _regency;
  String?      _district;
  String?      _type;
  AssetStatus? _status;

  // ── Getter ─────────────────────────────────────────────────────────
  bool    get isLoading  => _loading;
  String? get error      => _error;
  int     get fitVersion => _fitVersion;
  int     get totalCount => _all.length;

  String?      get province => _province;
  String?      get regency  => _regency;
  String?      get district => _district;
  String?      get type     => _type;
  AssetStatus? get status   => _status;

  int get activeFilterCount => [_province, _regency, _district, _type, _status]
      .where((e) => e != null)
      .length;

  List<AssetModel> get filtered => _filter(ignoreStatus: false);

  Map<AssetStatus, int> get statusCounts {
    final base = _filter(ignoreStatus: true);
    return {
      for (final s in AssetStatus.values)
        s: base.where((a) => a.status == s).length,
    };
  }

  // ── Opsi dropdown (diambil dari data aset) ─────────────────────────
  List<String> get provinceOptions => _distinct(_all.map((a) => a.province));

  List<String> get typeOptions => _distinct(_all.map((a) => a.type));

  List<String> regencyOptions(String? province) => _distinct(_all
      .where((a) => province == null || a.province == province)
      .map((a) => a.regency));

  List<String> districtOptions(String? province, String? regency) =>
      _distinct(_all
          .where((a) =>
              (province == null || a.province == province) &&
              (regency == null || a.regency == regency))
          .map((a) => a.district));

  static List<String> _distinct(Iterable<String> v) =>
      (v.where((e) => e.isNotEmpty).toSet().toList()..sort());

  // ── Aksi ───────────────────────────────────────────────────────────
  Future<void> load({bool resetFilters = false}) async {
    if (resetFilters) {
      _query = '';
      searchController.clear();
      _province = _regency = _district = _type = null;
      _status = null;
    }

    _loading = true;
    _error   = null;
    notifyListeners();

    final result = await _service.fetchAll();
    if (result.success) {
      _all = result.assets;
      _fitVersion++;
    } else {
      _error = result.message;
    }

    _loading = false;
    notifyListeners();
  }

  void setQuery(String value) {
    _query = value;
    notifyListeners();
  }

  // Dipakai chip status: ketuk lagi untuk membatalkan
  void toggleStatus(AssetStatus s) {
    _status = (_status == s) ? null : s;
    _fitVersion++;
    notifyListeners();
  }

  void applyFilters({
    String? province,
    String? regency,
    String? district,
    String? type,
    AssetStatus? status,
  }) {
    _province = province;
    _regency  = regency;
    _district = district;
    _type     = type;
    _status   = status;
    _fitVersion++;
    notifyListeners();
  }

  void resetFilters() {
    applyFilters();
  }

  // ── Internal ───────────────────────────────────────────────────────
  List<AssetModel> _filter({required bool ignoreStatus}) {
    final q = _query.trim().toLowerCase();
    return _all.where((a) {
      if (!ignoreStatus && _status != null && a.status != _status) return false;
      if (_province != null && a.province != _province) return false;
      if (_regency != null && a.regency != _regency) return false;
      if (_district != null && a.district != _district) return false;
      if (_type != null && a.type != _type) return false;
      if (q.isNotEmpty &&
          !(a.name.toLowerCase().contains(q) ||
              a.code.toLowerCase().contains(q) ||
              a.id.toLowerCase().contains(q))) {
        return false;
      }
      return true;
    }).toList();
  }

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }
}