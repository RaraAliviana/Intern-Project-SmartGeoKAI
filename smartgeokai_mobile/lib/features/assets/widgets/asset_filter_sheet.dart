import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/asset_model.dart';
import '../providers/asset_provider.dart';
import '../../../core/constants/app_colors.dart';

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
  AssetStatus? _status;

  @override
  void initState() {
    super.initState();
    final p = context.read<AssetProvider>();
    _province = p.province;
    _regency  = p.regency;
    _district = p.district;
    _type     = p.type;
    _status   = p.status;
  }

  Widget _dropdown({
    required String label,
    required String? value,
    required List<String> options,
    required ValueChanged<String?> onChanged,
  }) {
    final border = OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: const BorderSide(color: AppColors.gray200),
    );

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
            value: options.contains(value) ? value : null,
            isExpanded: true,
            hint: const Text('Semua'),
            decoration: InputDecoration(
              filled: true,
              fillColor: AppColors.gray50,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
              border: border,
              enabledBorder: border,
            ),
            items: [
              const DropdownMenuItem<String?>(value: null, child: Text('Semua')),
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

  @override
  Widget build(BuildContext context) {
    final p = context.read<AssetProvider>();

    return Padding(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: Container(
        decoration: const BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
        ),
        child: SafeArea(
          top: false,
          child: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Container(
                    width: 40,
                    height: 4,
                    decoration: BoxDecoration(
                      color: AppColors.gray200,
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                const Text(
                  'Filter Aset',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppColors.gray900,
                  ),
                ),
                const SizedBox(height: 16),

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
                _dropdown(
                  label: 'Jenis aset',
                  value: _type,
                  options: p.typeOptions,
                  onChanged: (v) => setState(() => _type = v),
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
                  children: [
                    ChoiceChip(
                      label: const Text('Semua'),
                      selected: _status == null,
                      onSelected: (_) => setState(() => _status = null),
                    ),
                    for (final s in AssetStatus.values)
                      ChoiceChip(
                        label: Text(s.label),
                        selected: _status == s,
                        selectedColor: s.color.withValues(alpha: 0.25),
                        avatar: CircleAvatar(
                          radius: 5,
                          backgroundColor: s.color,
                        ),
                        onSelected: (_) => setState(() => _status = s),
                      ),
                  ],
                ),

                const SizedBox(height: 24),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () => setState(() {
                          _province = _regency = _district = _type = null;
                          _status = null;
                        }),
                        style: OutlinedButton.styleFrom(
                          minimumSize: const Size.fromHeight(48),
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
                            province: _province,
                            regency:  _regency,
                            district: _district,
                            type:     _type,
                            status:   _status,
                          );
                          Navigator.pop(context);
                        },
                        style: FilledButton.styleFrom(
                          backgroundColor: AppColors.primary,
                          minimumSize: const Size.fromHeight(48),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                        child: const Text('Terapkan'),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}