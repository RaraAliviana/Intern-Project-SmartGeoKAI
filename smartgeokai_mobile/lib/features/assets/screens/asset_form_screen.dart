import 'package:flutter/material.dart';
import 'package:latlong2/latlong.dart';
import 'package:provider/provider.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';
import 'package:smartgeokai_mobile/features/assets/providers/asset_provider.dart';
import 'package:smartgeokai_mobile/features/assets/screens/location_picker_screen.dart';
import 'package:smartgeokai_mobile/features/assets/services/asset_write_service.dart';
import 'package:smartgeokai_mobile/features/assets/services/region_service.dart';
import 'package:smartgeokai_mobile/features/assets/widgets/asset_photo_picker.dart';
import 'package:smartgeokai_mobile/features/auth/providers/auth_provider.dart';
import 'package:smartgeokai_mobile/shared/widgets/app_text_field.dart';

class AssetFormScreen extends StatefulWidget {
  /// null = tambah aset baru, terisi = ubah aset.
  final AssetModel? asset;

  const AssetFormScreen({super.key, this.asset});

  @override
  State<AssetFormScreen> createState() => _AssetFormScreenState();
}

class _AssetFormScreenState extends State<AssetFormScreen> {
  static const _knownKeys = {
    'id_asset', 'asset_type', 'classification', 'asset_group',
    'province_id', 'regency_id', 'district_id', 'area_m2',
    'latitude', 'longitude', 'gmaps_url', 'status', 'description',
  };

  static const _typeOptions = ['Tanah', 'Bangunan'];
  static const _classOptions = ['ROW', 'Fasilitas', 'Operasional'];
  static const _groupOptions = ['Produksi', 'Non Produksi'];

  final _formKey = GlobalKey<FormState>();
  final _regions = RegionService();
  final _writer  = AssetWriteService();

  final _idCtrl    = TextEditingController();
  final _typeCtrl  = TextEditingController();
  final _classCtrl = TextEditingController();
  final _groupCtrl = TextEditingController();
  final _areaCtrl  = TextEditingController();
  final _latCtrl   = TextEditingController();
  final _lngCtrl   = TextEditingController();
  final _gmapsCtrl = TextEditingController();
  final _descCtrl  = TextEditingController();
  late final PhotoController _photos;

  AssetStatus _status = AssetStatus.clear;

  String? _provinceId;
  String? _regencyId;
  String? _districtId;
  List<RegionItem> _provinces = [];
  List<RegionItem> _regencies = [];
  List<RegionItem> _districts = [];
  bool    _loadingProv = false;
  bool    _loadingReg  = false;
  bool    _loadingDist = false;
  String? _regionError;

  bool    _saving = false;
  Map<String, String> _errors = {};
  String? _generalError;

  bool get _isEdit => widget.asset != null;

  @override
  void initState() {
    super.initState();
    final a = widget.asset;
    _photos = PhotoController(remote: a?.photos ?? const []);

    if (a != null) {
      _idCtrl.text    = a.code;
      _typeCtrl.text  = a.type;
      _classCtrl.text = a.classification;
      _groupCtrl.text = a.assetGroup;
      _areaCtrl.text  = a.areaM2 == null ? '' : _fmtNum(a.areaM2!);
      _gmapsCtrl.text = a.gmapsUrl;
      _descCtrl.text  = a.description;
      _status         = a.status;
      if (a.hasLocation) {
        _latCtrl.text = a.latitude!.toStringAsFixed(6);
        _lngCtrl.text = a.longitude!.toStringAsFixed(6);
      }
      _provinceId = a.provinceId.isEmpty ? null : a.provinceId;
      _regencyId  = a.regencyId.isEmpty ? null : a.regencyId;
      _districtId = a.districtId.isEmpty ? null : a.districtId;
    }
    _initRegions();
  }

  @override
  void dispose() {
    for (final c in [
      _idCtrl, _typeCtrl, _classCtrl, _groupCtrl, _areaCtrl,
      _latCtrl, _lngCtrl, _gmapsCtrl, _descCtrl,
    ]) {
      c.dispose();
    }
    _photos.dispose();
    super.dispose();
  }

  String _fmtNum(double v) =>
      v == v.roundToDouble() ? v.toInt().toString() : v.toString();

  void _clearError(String key) {
    if (_errors.containsKey(key)) {
      setState(() => _errors = Map.of(_errors)..remove(key));
    }
  }

  void _toast(String text) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(text)));
  }

  // ── Wilayah ────────────────────────────────────────────────────────
  String? _matchByName(List<RegionItem> items, String name) {
    if (name.isEmpty) return null;
    for (final i in items) {
      if (i.name.toLowerCase() == name.toLowerCase()) return i.id;
    }
    return null;
  }

  Future<void> _initRegions() async {
    final a = widget.asset;
    setState(() {
      _loadingProv = true;
      _regionError = null;
    });
    try {
      final provs = await _regions.provinces();
      if (!mounted) return;
      setState(() {
        _provinces = provs;
        _loadingProv = false;
        if (_provinceId == null && a != null) {
          _provinceId = _matchByName(provs, a.province);
        }
      });

      if (_provinceId != null) {
        await _loadRegencies(_provinceId!);
        if (!mounted) return;
        if (_regencyId == null && a != null) {
          setState(() => _regencyId = _matchByName(_regencies, a.regency));
        }
        if (_regencyId != null) {
          await _loadDistricts(_regencyId!);
          if (!mounted) return;
          if (_districtId == null && a != null) {
            setState(() => _districtId = _matchByName(_districts, a.district));
          }
        }
      }
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _loadingProv = false;
        _regionError = 'Daftar wilayah gagal dimuat. Periksa koneksi lalu coba lagi.';
      });
    }
  }

  Future<void> _loadRegencies(String provinceId) async {
    setState(() => _loadingReg = true);
    try {
      final list = await _regions.regencies(provinceId);
      if (!mounted) return;
      setState(() {
        _regencies = list;
        _loadingReg = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _loadingReg = false;
        _regionError = 'Daftar kabupaten/kota gagal dimuat.';
      });
    }
  }

  Future<void> _loadDistricts(String regencyId) async {
    setState(() => _loadingDist = true);
    try {
      final list = await _regions.districts(regencyId);
      if (!mounted) return;
      setState(() {
        _districts = list;
        _loadingDist = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _loadingDist = false;
        _regionError = 'Daftar kecamatan gagal dimuat.';
      });
    }
  }

  void _onProvince(String? id) {
    setState(() {
      _provinceId = id;
      _regencyId  = null;
      _districtId = null;
      _regencies  = [];
      _districts  = [];
    });
    _clearError('province_id');
    if (id != null) _loadRegencies(id);
  }

  void _onRegency(String? id) {
    setState(() {
      _regencyId  = id;
      _districtId = null;
      _districts  = [];
    });
    _clearError('regency_id');
    if (id != null) _loadDistricts(id);
  }

  // ── Koordinat ──────────────────────────────────────────────────────
  double? _tryCoord(TextEditingController c) =>
      double.tryParse(c.text.trim().replaceAll(',', '.'));

  Future<void> _pickOnMap() async {
    final lat = _tryCoord(_latCtrl);
    final lng = _tryCoord(_lngCtrl);
    final initial = (lat != null && lng != null) ? LatLng(lat, lng) : null;

    final picked = await Navigator.push<LatLng>(
      context,
      MaterialPageRoute(builder: (_) => LocationPickerScreen(initial: initial)),
    );
    if (picked == null || !mounted) return;

    setState(() {
      _latCtrl.text = picked.latitude.toStringAsFixed(6);
      _lngCtrl.text = picked.longitude.toStringAsFixed(6);
      // Isi otomatis link Google Maps kalau masih kosong
      if (_gmapsCtrl.text.trim().isEmpty) {
        _gmapsCtrl.text =
            'https://www.google.com/maps?q=${_latCtrl.text},${_lngCtrl.text}';
      }
      _errors = Map.of(_errors)
        ..remove('latitude')
        ..remove('longitude');
    });
  }

  String? _validateCoord(
    String? v, {
    required double min,
    required double max,
    required String label,
  }) {
    final t = (v ?? '').trim().replaceAll(',', '.');
    if (t.isEmpty) return '$label wajib diisi';
    final n = double.tryParse(t);
    if (n == null) return '$label tidak valid';
    if (n < min || n > max) return '$label harus antara $min dan $max';
    return null;
  }

  String? _validateArea(String? v) {
    final t = (v ?? '').trim().replaceAll(',', '.');
    if (t.isEmpty) return 'Luas wajib diisi';
    final n = double.tryParse(t);
    if (n == null || n < 0) return 'Luas tidak valid';
    return null;
  }

  // ── Simpan ─────────────────────────────────────────────────────────
  Future<void> _save() async {
    FocusScope.of(context).unfocus();

    if (!_formKey.currentState!.validate()) {
      _toast('Periksa kembali data yang belum lengkap.');
      return;
    }
    if (_photos.busy) {
      _toast('Tunggu sebentar, foto sedang diproses.');
      return;
    }

    if (!_isEdit && _photos.total == 0) {
      _photos.setMessage('Minimal 1 foto wajib diunggah.');
      _toast('Tambahkan minimal 1 foto.');
      return;
    }

    setState(() {
      _saving       = true;
      _errors       = {};
      _generalError = null;
    });

    final input = AssetInput(
      idAsset:        _idCtrl.text.trim(),
      assetType:      _typeCtrl.text.trim(),
      classification: _classCtrl.text.trim(),
      assetGroup:     _groupCtrl.text.trim(),
      provinceId:     _provinceId ?? '',
      regencyId:      _regencyId ?? '',
      districtId:     _districtId ?? '',
      areaM2:         _areaCtrl.text.trim().replaceAll(',', '.'),
      latitude:       _tryCoord(_latCtrl)!,
      longitude:      _tryCoord(_lngCtrl)!,
      gmapsUrl:       _gmapsCtrl.text.trim(),
      status:         _status,
      description:    _descCtrl.text.trim(),
    );

    final result = await _writer.save(
      id: widget.asset?.id,
      input: input,
      newPhotos: _photos.local,
      removedPhotoIds: _photos.removedRemoteIds,
    );
    if (!mounted) return;

    if (result.unauthorized) {
      await context.read<AuthProvider>().logout();
      if (!mounted) return;
      Navigator.pushNamedAndRemoveUntil(context, '/login', (_) => false);
      return;
    }

    if (result.success) {
      context.read<AssetProvider>().load();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result.message ?? 'Aset berhasil disimpan.'),
          backgroundColor: AppColors.clear,
        ),
      );
      Navigator.pop(context, true);
      return;
    }

    // Error tanpa kolom khusus (mis. foto) ditampilkan di kotak merah
    final extra = result.fieldErrors.entries
        .where((e) => !_knownKeys.contains(e.key))
        .map((e) => e.value)
        .toList();

    setState(() {
      _saving = false;
      _errors = Map.of(result.fieldErrors);
      _generalError = extra.isNotEmpty
          ? extra.join('\n')
          : (result.fieldErrors.isEmpty ? result.message : null);
    });
  }

  // ── UI ─────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(_isEdit ? 'Ubah Aset' : 'Tambah Aset')),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 10, 20, 14),
          child: SizedBox(
            height: 52,
            child: FilledButton(
              onPressed: _saving ? null : _save,
              style: FilledButton.styleFrom(
                backgroundColor: AppColors.accent,
                disabledBackgroundColor:
                    AppColors.accent.withValues(alpha: 0.6),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(14),
                ),
              ),
              child: _saving
                  ? const SizedBox(
                      width: 22,
                      height: 22,
                      child: CircularProgressIndicator(
                        strokeWidth: 2.5,
                        valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                      ),
                    )
                  : Text(
                      _isEdit ? 'Simpan Perubahan' : 'Simpan Aset',
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
            ),
          ),
        ),
      ),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.fromLTRB(20, 20, 20, 24),
          children: [
            if (_generalError != null) ...[
              _ErrorBanner(message: _generalError!),
              const SizedBox(height: 16),
            ],

            // ── Identitas aset ─────────────────────────────────────
            const _SectionTitle('Identitas Aset'),
            AppTextField(
              controller: _idCtrl,
              label: 'ID Aset',
              hint: 'Masukkan ID aset',
              icon: Icons.tag_rounded,
              maxLength: 20,
              textInputAction: TextInputAction.next,
              errorText: _errors['id_asset'],
              onChanged: (_) => _clearError('id_asset'),
              validator: (v) =>
                  (v == null || v.trim().isEmpty) ? 'ID aset wajib diisi' : null,
            ),
            const SizedBox(height: 16),
            _choiceField(
              controller: _typeCtrl,
              label: 'Jenis Aset',
              options: _typeOptions,
              errorKey: 'asset_type',
            ),
            const SizedBox(height: 16),
            _choiceField(
              controller: _classCtrl,
              label: 'Klasifikasi',
              options: _classOptions,
              errorKey: 'classification',
            ),
            const SizedBox(height: 16),
            _choiceField(
              controller: _groupCtrl,
              label: 'Kelompok Aset',
              options: _groupOptions,
              errorKey: 'asset_group',
            ),
            const SizedBox(height: 16),
            const _FieldLabel('Status'),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              children: [
                for (final s in AssetStatus.values)
                  ChoiceChip(
                    label: Text(s.label),
                    selected: _status == s,
                    selectedColor: s.color.withValues(alpha: 0.25),
                    avatar: CircleAvatar(radius: 5, backgroundColor: s.color),
                    onSelected: (_) => setState(() => _status = s),
                  ),
              ],
            ),
            if (_errors['status'] != null)
              Padding(
                padding: const EdgeInsets.only(top: 6),
                child: Text(
                  _errors['status']!,
                  style: const TextStyle(fontSize: 12, color: AppColors.error),
                ),
              ),

            // ── Lokasi administratif ───────────────────────────────
            const SizedBox(height: 28),
            const _SectionTitle('Wilayah'),
            if (_regionError != null) ...[
              Row(
                children: [
                  Expanded(
                    child: Text(
                      _regionError!,
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.error,
                      ),
                    ),
                  ),
                  TextButton(
                    onPressed: _initRegions,
                    child: const Text('Muat ulang'),
                  ),
                ],
              ),
              const SizedBox(height: 8),
            ],
            _regionDropdown(
              label: 'Provinsi',
              value: _provinceId,
              items: _provinces,
              loading: _loadingProv,
              required: true,
              errorText: _errors['province_id'],
              onChanged: _onProvince,
            ),
            const SizedBox(height: 16),
            _regionDropdown(
              label: 'Kabupaten / Kota',
              value: _regencyId,
              items: _regencies,
              loading: _loadingReg,
              required: _provinceId != null,
              errorText: _errors['regency_id'],
              onChanged: _onRegency,
            ),
            const SizedBox(height: 16),
            _regionDropdown(
              label: 'Kecamatan',
              value: _districtId,
              items: _districts,
              loading: _loadingDist,
              required: true,
              errorText: _errors['district_id'],
              onChanged: (v) {
                setState(() => _districtId = v);
                _clearError('district_id');
              },
            ),
            const SizedBox(height: 16),
            AppTextField(
              controller: _areaCtrl,
              label: 'Luas (m²)',
              hint: 'Contoh: 1250',
              icon: Icons.square_foot_rounded,
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              textInputAction: TextInputAction.next,
              errorText: _errors['area_m2'],
              onChanged: (_) => _clearError('area_m2'),
              validator: _validateArea,
            ),

            // ── Koordinat ──────────────────────────────────────────
            const SizedBox(height: 28),
            const _SectionTitle('Koordinat'),
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: AppTextField(
                    controller: _latCtrl,
                    label: 'Latitude',
                    hint: '-7.795600',
                    icon: Icons.south_rounded,
                    keyboardType: const TextInputType.numberWithOptions(
                      decimal: true,
                      signed: true,
                    ),
                    errorText: _errors['latitude'],
                    onChanged: (_) => _clearError('latitude'),
                    validator: (v) => _validateCoord(
                      v,
                      min: -90,
                      max: 90,
                      label: 'Latitude',
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: AppTextField(
                    controller: _lngCtrl,
                    label: 'Longitude',
                    hint: '110.369500',
                    icon: Icons.east_rounded,
                    keyboardType: const TextInputType.numberWithOptions(
                      decimal: true,
                      signed: true,
                    ),
                    errorText: _errors['longitude'],
                    onChanged: (_) => _clearError('longitude'),
                    validator: (v) => _validateCoord(
                      v,
                      min: -180,
                      max: 180,
                      label: 'Longitude',
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              height: 48,
              child: OutlinedButton.icon(
                onPressed: _pickOnMap,
                icon: const Icon(Icons.map_rounded, size: 20),
                label: const Text('Pilih dari peta'),
                style: OutlinedButton.styleFrom(
                  foregroundColor: AppColors.primary,
                  side: const BorderSide(color: AppColors.primary),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 6),
            const Text(
              'Isi manual, atau pilih titik di peta (bisa pakai lokasi saya).',
              style: TextStyle(fontSize: 12, color: AppColors.gray500),
            ),
            const SizedBox(height: 16),
            AppTextField(
              controller: _gmapsCtrl,
              label: 'Link Google Maps (opsional)',
              hint: 'https://maps.google.com/...',
              icon: Icons.link_rounded,
              keyboardType: TextInputType.url,
              textInputAction: TextInputAction.next,
              errorText: _errors['gmaps_url'],
              onChanged: (_) => _clearError('gmaps_url'),
            ),

            // ── Keterangan ─────────────────────────────────────────
            const SizedBox(height: 28),
            const _SectionTitle('Keterangan'),
            AppTextField(
              controller: _descCtrl,
              label: 'Deskripsi',
              hint: 'Catatan atau keterangan aset',
              icon: Icons.notes_rounded,
              maxLines: 4,
              keyboardType: TextInputType.multiline,
              textInputAction: TextInputAction.newline,
              errorText: _errors['description'],
              onChanged: (_) => _clearError('description'),
            ),

            // ── Foto ───────────────────────────────────────────────
            const SizedBox(height: 28),
            const _SectionTitle('Foto'),
            AssetPhotoPicker(controller: _photos),
            const SizedBox(height: 12),
          ],
        ),
      ),
    );
  }

  Widget _choiceField({
    required TextEditingController controller,
    required String label,
    required List<String> options,
    required String errorKey,
  }) {
    final value = options.contains(controller.text) ? controller.text : null;
    final border = OutlineInputBorder(
      borderRadius: BorderRadius.circular(14),
      borderSide: const BorderSide(color: AppColors.gray200),
    );

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _FieldLabel(label),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: value,
          isExpanded: true,
          hint: Text(
            'Pilih $label',
            style: const TextStyle(
              fontSize: 14,
              color: AppColors.gray400,
            ),
          ),
          decoration: InputDecoration(
            filled: true,
            fillColor: AppColors.white,
            errorText: _errors[errorKey],
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: border,
            enabledBorder: border,
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(
                color: AppColors.primary,
                width: 1.5,
              ),
            ),
          ),
          items: [
            for (final o in options)
              DropdownMenuItem<String>(
                value: o,
                child: Text(o),
              ),
          ],
          onChanged: (v) {
            setState(() => controller.text = v ?? '');
            _clearError(errorKey);
          },
          validator: (v) => v == null ? 'Pilih $label' : null,
        ),
      ],
    );
  }

  Widget _regionDropdown({
    required String label,
    required String? value,
    required List<RegionItem> items,
    required bool loading,
    required bool required,
    required String? errorText,
    required ValueChanged<String?> onChanged,
  }) {
    final safeValue = items.any((e) => e.id == value) ? value : null;
    final border = OutlineInputBorder(
      borderRadius: BorderRadius.circular(14),
      borderSide: const BorderSide(color: AppColors.gray200),
    );

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _FieldLabel(label),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: safeValue,
          isExpanded: true,
          hint: Text(
            loading ? 'Memuat...' : 'Pilih $label',
            style: const TextStyle(fontSize: 14, color: AppColors.gray400),
          ),
          decoration: InputDecoration(
            filled: true,
            fillColor: AppColors.white,
            errorText: errorText,
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: border,
            enabledBorder: border,
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
            ),
          ),
          items: [
            for (final i in items)
              DropdownMenuItem<String>(
                value: i.id,
                child: Text(i.name, overflow: TextOverflow.ellipsis),
              ),
          ],
          onChanged: (loading || items.isEmpty) ? null : onChanged,
          validator: (v) =>
              (required && items.isNotEmpty && v == null) ? 'Pilih $label' : null,
        ),
      ],
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String text;
  const _SectionTitle(this.text);

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Row(
        children: [
          Container(
            width: 4,
            height: 18,
            decoration: BoxDecoration(
              color: AppColors.accent,
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(width: 10),
          Text(
            text,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: AppColors.primary,
            ),
          ),
        ],
      ),
    );
  }
}

class _FieldLabel extends StatelessWidget {
  final String text;
  const _FieldLabel(this.text);

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: const TextStyle(
        fontSize: 13,
        fontWeight: FontWeight.w600,
        color: AppColors.gray700,
      ),
    );
  }
}

class _ErrorBanner extends StatelessWidget {
  final String message;
  const _ErrorBanner({required this.message});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.errorLight,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.error.withValues(alpha: 0.3)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(Icons.error_outline, color: AppColors.error, size: 18),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              message,
              style: const TextStyle(color: AppColors.error, fontSize: 13),
            ),
          ),
        ],
      ),
    );
  }
}