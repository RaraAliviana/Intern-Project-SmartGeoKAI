import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/core/utils/location_helper.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';
import 'package:smartgeokai_mobile/features/assets/providers/asset_provider.dart';
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
    'name', 'code', 'type', 'status', 'address',
    'province_id', 'regency_id', 'district_id', 'latitude', 'longitude',
  };

  final _formKey     = GlobalKey<FormState>();
  final _regions     = RegionService();
  final _writer      = AssetWriteService();

  final _nameCtrl    = TextEditingController();
  final _codeCtrl    = TextEditingController();
  final _typeCtrl    = TextEditingController();
  final _addressCtrl = TextEditingController();
  final _latCtrl     = TextEditingController();
  final _lngCtrl     = TextEditingController();
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

  bool    _saving   = false;
  bool    _locating = false;
  String? _gpsInfo;

  Map<String, String> _errors = {};
  String? _generalError;

  bool get _isEdit => widget.asset != null;

  @override
  void initState() {
    super.initState();
    final a = widget.asset;
    _photos = PhotoController(remote: a?.photos ?? const []);

    if (a != null) {
      _nameCtrl.text    = a.name;
      _codeCtrl.text    = a.code;
      _typeCtrl.text    = a.type;
      _addressCtrl.text = a.address;
      _status           = a.status;
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
    _nameCtrl.dispose();
    _codeCtrl.dispose();
    _typeCtrl.dispose();
    _addressCtrl.dispose();
    _latCtrl.dispose();
    _lngCtrl.dispose();
    _photos.dispose();
    super.dispose();
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
      _errors.remove('province_id');
    });
    if (id != null) _loadRegencies(id);
  }

  void _onRegency(String? id) {
    setState(() {
      _regencyId  = id;
      _districtId = null;
      _districts  = [];
      _errors.remove('regency_id');
    });
    if (id != null) _loadDistricts(id);
  }

  // ── GPS ────────────────────────────────────────────────────────────
  void _toast(String text) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(text)));
  }

  Future<void> _useGps() async {
    if (_locating) return;
    setState(() => _locating = true);
    try {
      final pos = await getCurrentPosition();
      if (!mounted) return;
      setState(() {
        _latCtrl.text = pos.latitude.toStringAsFixed(6);
        _lngCtrl.text = pos.longitude.toStringAsFixed(6);
        _gpsInfo = 'Akurasi sekitar ${pos.accuracy.round()} meter';
        _errors.remove('latitude');
        _errors.remove('longitude');
      });
    } on LocationException catch (e) {
      _toast(e.message);
    } catch (_) {
      _toast('Gagal mendapatkan lokasi. Coba lagi.');
    } finally {
      if (mounted) setState(() => _locating = false);
    }
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

  double _parseCoord(TextEditingController c) =>
      double.parse(c.text.trim().replaceAll(',', '.'));

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

    setState(() {
      _saving       = true;
      _errors       = {};
      _generalError = null;
    });

    final input = AssetInput(
      name:       _nameCtrl.text.trim(),
      code:       _codeCtrl.text.trim(),
      type:       _typeCtrl.text.trim(),
      address:    _addressCtrl.text.trim(),
      status:     _status,
      provinceId: _provinceId ?? '',
      regencyId:  _regencyId ?? '',
      districtId: _districtId ?? '',
      latitude:   _parseCoord(_latCtrl),
      longitude:  _parseCoord(_lngCtrl),
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
      // Muat ulang daftar & peta di belakang layar
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

    // Pesan umum: error tanpa kolom khusus (mis. foto) atau pesan server
    final extra = result.fieldErrors.entries
        .where((e) => !_knownKeys.contains(e.key))
        .map((e) => e.value)
        .toList();

    setState(() {
      _saving = false;
      _errors = result.fieldErrors;
      _generalError = extra.isNotEmpty
          ? extra.join('\n')
          : (result.fieldErrors.isEmpty ? result.message : null);
    });
  }

  // ── UI ─────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final typeSuggestions =
        context.read<AssetProvider>().typeOptions.take(6).toList();

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

            // ── Informasi aset ─────────────────────────────────────
            const _SectionTitle('Informasi Aset'),
            AppTextField(
              controller: _nameCtrl,
              label: 'Nama aset',
              hint: 'Masukkan nama aset',
              icon: Icons.inventory_2_outlined,
              textInputAction: TextInputAction.next,
              errorText: _errors['name'],
              onChanged: (_) => setState(() => _errors.remove('name')),
              validator: (v) =>
                  (v == null || v.trim().isEmpty) ? 'Nama aset wajib diisi' : null,
            ),
            const SizedBox(height: 16),
            AppTextField(
              controller: _codeCtrl,
              label: 'Kode / ID aset (opsional)',
              hint: 'Kosongkan jika dibuat otomatis',
              icon: Icons.tag_rounded,
              textInputAction: TextInputAction.next,
              errorText: _errors['code'],
              onChanged: (_) => setState(() => _errors.remove('code')),
            ),
            const SizedBox(height: 16),
            AppTextField(
              controller: _typeCtrl,
              label: 'Jenis aset',
              hint: 'Contoh: Tanah, Bangunan',
              icon: Icons.category_outlined,
              textInputAction: TextInputAction.next,
              errorText: _errors['type'],
              onChanged: (_) => setState(() => _errors.remove('type')),
              validator: (v) =>
                  (v == null || v.trim().isEmpty) ? 'Jenis aset wajib diisi' : null,
            ),
            if (typeSuggestions.isNotEmpty) ...[
              const SizedBox(height: 8),
              Wrap(
                spacing: 8,
                runSpacing: 4,
                children: [
                  for (final t in typeSuggestions)
                    ActionChip(
                      label: Text(t, style: const TextStyle(fontSize: 12)),
                      onPressed: () => setState(() => _typeCtrl.text = t),
                    ),
                ],
              ),
            ],
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

            // ── Lokasi ─────────────────────────────────────────────
            const SizedBox(height: 28),
            const _SectionTitle('Lokasi'),
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
              required: false,
              errorText: _errors['district_id'],
              onChanged: (v) => setState(() {
                _districtId = v;
                _errors.remove('district_id');
              }),
            ),
            const SizedBox(height: 16),
            AppTextField(
              controller: _addressCtrl,
              label: 'Alamat',
              hint: 'Alamat lengkap atau keterangan lokasi',
              icon: Icons.place_outlined,
              maxLines: 3,
              textInputAction: TextInputAction.newline,
              keyboardType: TextInputType.multiline,
              errorText: _errors['address'],
              onChanged: (_) => setState(() => _errors.remove('address')),
              validator: (v) =>
                  (v == null || v.trim().isEmpty) ? 'Alamat wajib diisi' : null,
            ),

            // ── Koordinat ──────────────────────────────────────────
            const SizedBox(height: 28),
            const _SectionTitle('Koordinat (GPS)'),
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: AppTextField(
                    controller: _latCtrl,
                    label: 'Latitude',
                    hint: '-6.200000',
                    icon: Icons.south_rounded,
                    keyboardType: const TextInputType.numberWithOptions(
                      decimal: true,
                      signed: true,
                    ),
                    errorText: _errors['latitude'],
                    onChanged: (_) => setState(() => _errors.remove('latitude')),
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
                    hint: '106.816666',
                    icon: Icons.east_rounded,
                    keyboardType: const TextInputType.numberWithOptions(
                      decimal: true,
                      signed: true,
                    ),
                    errorText: _errors['longitude'],
                    onChanged: (_) => setState(() => _errors.remove('longitude')),
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
                onPressed: _locating ? null : _useGps,
                icon: _locating
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    : const Icon(Icons.my_location_rounded, size: 20),
                label: Text(
                  _locating ? 'Mencari lokasi...' : 'Gunakan lokasi saya (GPS)',
                ),
                style: OutlinedButton.styleFrom(
                  foregroundColor: AppColors.primary,
                  side: const BorderSide(color: AppColors.primary),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
              ),
            ),
            if (_gpsInfo != null)
              Padding(
                padding: const EdgeInsets.only(top: 6),
                child: Text(
                  _gpsInfo!,
                  style: const TextStyle(fontSize: 12, color: AppColors.gray500),
                ),
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