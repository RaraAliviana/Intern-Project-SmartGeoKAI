import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';
import 'package:smartgeokai_mobile/features/assets/providers/asset_provider.dart';

Future<void> showAssetFilterSheet(BuildContext context) {
  return showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    backgroundColor: Colors.transparent,
    builder: (_) => const _FilterSheet(),
  );
}

class _FilterSheet extends StatefulWidget {
  const _FilterSheet();

  @override
  State<_FilterSheet> createState() => _FilterSheetState();
}

class _FilterSheetState extends State<_FilterSheet> {
  String?      _province;
  String?      _regency;
  String?      _district;
  String?      _type;
  String?      _classification;
  String?      _group;
  AssetStatus? _status;

  @override
  void initState() {
    super.initState();
    final p = context.read<AssetProvider>();
    _province       = p.province;
    _regency        = p.regency;
    _district       = p.district;
    _type           = p.type;
    _classification = p.classification;
    _group          = p.assetGroup;
    _status         = p.status;
  }

  void _reset() {
    setState(() {
      _province = _regency = _district = null;
      _type = _classification = _group = null;
      _status = null;
    });
  }

  Widget _dropdown({
    required String label,
    required String? value,
    required List<String> options,
    required ValueChanged<String?> onChanged,
  }) {
    final border = OutlineInputBorder(
      borderRadius: BorderRadius.circular(14),
      borderSide: const BorderSide(color: AppColors.gray200),
    );
    final current = options.contains(value) ? value : null;

    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 12.5,
              fontWeight: FontWeight.w600,
              color: AppColors.gray700,
            ),
          ),
          const SizedBox(height: 6),
          DropdownButtonFormField<String?>(
            // key ikut berubah supaya tampilan selalu sinkron saat
            // di-reset atau saat induknya (provinsi/kabupaten) diganti
            key: ValueKey('$label|$current'),
            value: current,
            isExpanded: true,
            hint: const Text(
              'Semua',
              style: TextStyle(fontSize: 14, color: AppColors.gray500),
            ),
            decoration: InputDecoration(
              filled: true,
              fillColor: AppColors.white,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
              border: border,
              enabledBorder: border,
              focusedBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(14),
                borderSide:
                    const BorderSide(color: AppColors.primary, width: 1.5),
              ),
            ),
            items: [
              const DropdownMenuItem<String?>(
                value: null,
                child: Text('Semua'),
              ),
              ...options.map(
                (o) => DropdownMenuItem<String?>(
                  value: o,
                  child: Text(o, overflow: TextOverflow.ellipsis),
                ),
              ),
            ],
            onChanged: onChanged,
          ),
        ],
      ),
    );
  }

  Widget _section(String text) {
    return Padding(
      padding: const EdgeInsets.only(top: 6, bottom: 12),
      child: Row(
        children: [
          Container(
            width: 4,
            height: 16,
            decoration: BoxDecoration(
              color: AppColors.accent,
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(width: 8),
          Text(
            text,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w700,
              color: AppColors.primary,
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final p = context.read<AssetProvider>();
    final media = MediaQuery.of(context);

    return Padding(
      padding: EdgeInsets.only(bottom: media.viewInsets.bottom),
      child: Container(
        constraints: BoxConstraints(maxHeight: media.size.height * 0.9),
        decoration: const BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
        ),
        child: SafeArea(
          top: false,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Handle + judul
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 12, 8, 4),
                child: Column(
                  children: [
                    Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: AppColors.gray200,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        const Expanded(
                          child: Text(
                            'Filter Aset',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w700,
                              color: AppColors.gray900,
                            ),
                          ),
                        ),
                        IconButton(
                          onPressed: () => Navigator.pop(context),
                          icon: const Icon(Icons.close_rounded,
                              color: AppColors.gray500),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // Isi (bisa digulir)
              Flexible(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.fromLTRB(20, 8, 20, 12),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _section('Wilayah'),
                      _dropdown(
                        label: 'Provinsi',
                        value: _province,
                        options: p.provinceOptions,
                        onChanged: (v) => setState(() {
                          _province = v;
                          _regency  = null;
                          _district = null;
                        }),
                      ),
                      _dropdown(
                        label: 'Kabupaten / Kota',
                        value: _regency,
                        options: p.regencyOptions(_province),
                        onChanged: (v) => setState(() {
                          _regency  = v;
                          _district = null;
                        }),
                      ),
                      _dropdown(
                        label: 'Kecamatan',
                        value: _district,
                        options: p.districtOptions(_province, _regency),
                        onChanged: (v) => setState(() => _district = v),
                      ),

                      const SizedBox(height: 6),
                      _section('Aset'),
                      _dropdown(
                        label: 'Jenis aset',
                        value: _type,
                        options: p.typeOptions,
                        onChanged: (v) => setState(() => _type = v),
                      ),
                      _dropdown(
                        label: 'Klasifikasi',
                        value: _classification,
                        options: p.classificationOptions,
                        onChanged: (v) => setState(() => _classification = v),
                      ),
                      _dropdown(
                        label: 'Kelompok aset',
                        value: _group,
                        options: p.groupOptions,
                        onChanged: (v) => setState(() => _group = v),
                      ),

                      const Text(
                        'Status',
                        style: TextStyle(
                          fontSize: 12.5,
                          fontWeight: FontWeight.w600,
                          color: AppColors.gray700,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Wrap(
                        spacing: 8,
                        runSpacing: 4,
                        children: [
                          ChoiceChip(
                            label: const Text('Semua'),
                            selected: _status == null,
                            showCheckmark: false,
                            selectedColor: AppColors.primaryLight,
                            onSelected: (_) => setState(() => _status = null),
                          ),
                          for (final s in AssetStatus.values)
                            ChoiceChip(
                              label: Text(s.label),
                              selected: _status == s,
                              showCheckmark: false,
                              selectedColor: s.color.withValues(alpha: 0.25),
                              avatar: CircleAvatar(
                                radius: 5,
                                backgroundColor: s.color,
                              ),
                              onSelected: (_) => setState(() => _status = s),
                            ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),

              // Tombol aksi (tetap di bawah)
              Container(
                padding: const EdgeInsets.fromLTRB(20, 12, 20, 16),
                decoration: const BoxDecoration(
                  border: Border(top: BorderSide(color: AppColors.gray100)),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: _reset,
                        style: OutlinedButton.styleFrom(
                          minimumSize: const Size.fromHeight(48),
                          foregroundColor: AppColors.primary,
                          side: const BorderSide(color: AppColors.primary),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                        child: const Text('Reset'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      flex: 2,
                      child: FilledButton(
                        onPressed: () {
                          p.applyFilters(
                            province:       _province,
                            regency:        _regency,
                            district:       _district,
                            type:           _type,
                            classification: _classification,
                            assetGroup:     _group,
                            status:         _status,
                          );
                          Navigator.pop(context);
                        },
                        style: FilledButton.styleFrom(
                          backgroundColor: AppColors.accent,
                          minimumSize: const Size.fromHeight(48),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                        child: const Text(
                          'Terapkan',
                          style: TextStyle(fontWeight: FontWeight.w600),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}