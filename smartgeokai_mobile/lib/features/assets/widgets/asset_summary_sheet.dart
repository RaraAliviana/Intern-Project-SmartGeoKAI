import 'package:flutter/material.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';
import 'package:smartgeokai_mobile/features/assets/screens/asset_form_screen.dart';
import 'package:smartgeokai_mobile/features/assets/widgets/asset_photo_strip.dart';
import 'package:smartgeokai_mobile/features/assets/widgets/asset_status_chip.dart';

/// Nama yang dipakai layar Peta dan Data Aset.
Future<void> showAssetSummary(BuildContext context, AssetModel asset) {
  return showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    backgroundColor: Colors.transparent,
    builder: (_) => _AssetSummarySheet(asset: asset),
  );
}

/// Nama alternatif (kompatibilitas).
Future<void> showAssetSummarySheet(BuildContext context, AssetModel asset) =>
    showAssetSummary(context, asset);

/// Kompatibilitas dengan kode lama yang memakai StatusBadge.
class StatusBadge extends StatelessWidget {
  final AssetStatus status;
  const StatusBadge({super.key, required this.status});

  @override
  Widget build(BuildContext context) => AssetStatusChip(status: status);
}

class _AssetSummarySheet extends StatelessWidget {
  final AssetModel asset;
  const _AssetSummarySheet({required this.asset});

  String _fmt(double v) =>
      v == v.roundToDouble() ? v.toInt().toString() : v.toString();

  @override
  Widget build(BuildContext context) {
    return DraggableScrollableSheet(
      initialChildSize: 0.8,
      minChildSize: 0.45,
      maxChildSize: 0.95,
      expand: false,
      builder: (context, scrollController) {
        return Container(
          decoration: const BoxDecoration(
            color: AppColors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
          ),
          child: SafeArea(
            top: false,
            child: Column(
              children: [
                Expanded(
                  child: ListView(
                    controller: scrollController,
                    padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
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
                      const SizedBox(height: 18),

                      // Judul + status
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              asset.displayCode,
                              style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.w700,
                                color: AppColors.gray900,
                              ),
                            ),
                          ),
                          const SizedBox(width: 8),
                          AssetStatusChip(status: asset.status),
                        ],
                      ),

                      const SizedBox(height: 16),
                      const Divider(height: 1, color: AppColors.gray100),
                      const SizedBox(height: 16),

                      // Foto aset
                      AssetPhotoStrip(photos: asset.photos),

                      const SizedBox(height: 20),

                      // Informasi aset
                      _InfoRow(
                        icon: Icons.category_rounded,
                        label: 'Jenis aset',
                        value: asset.type,
                      ),
                      _InfoRow(
                        icon: Icons.account_tree_rounded,
                        label: 'Klasifikasi',
                        value: asset.classification,
                      ),
                      _InfoRow(
                        icon: Icons.folder_rounded,
                        label: 'Kelompok aset',
                        value: asset.assetGroup,
                      ),
                      _InfoRow(
                        icon: Icons.square_foot_rounded,
                        label: 'Luas',
                        value: asset.areaM2 == null
                            ? ''
                            : '${_fmt(asset.areaM2!)} m²',
                      ),
                      _InfoRow(
                        icon: Icons.map_rounded,
                        label: 'Wilayah',
                        value: asset.regionText,
                      ),
                      _InfoRow(
                        icon: Icons.my_location_rounded,
                        label: 'Koordinat',
                        value: asset.hasLocation
                            ? '${asset.latitude!.toStringAsFixed(6)}, '
                                '${asset.longitude!.toStringAsFixed(6)}'
                            : '',
                      ),
                      _InfoRow(
                        icon: Icons.notes_rounded,
                        label: 'Keterangan',
                        value: asset.description,
                      ),
                    ],
                  ),
                ),

                // Tombol aksi
                Container(
                  padding: const EdgeInsets.fromLTRB(20, 12, 20, 16),
                  decoration: const BoxDecoration(
                    color: AppColors.white,
                    border: Border(top: BorderSide(color: AppColors.gray100)),
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => Navigator.of(context).pop(),
                          style: OutlinedButton.styleFrom(
                            minimumSize: const Size.fromHeight(48),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                          ),
                          child: const Text('Tutup'),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        flex: 2,
                        child: FilledButton.icon(
                          onPressed: () {
                            final nav = Navigator.of(context);
                            nav.pop();
                            nav.push(
                              MaterialPageRoute<void>(
                                builder: (_) => AssetFormScreen(asset: asset),
                              ),
                            );
                          },
                          icon: const Icon(Icons.edit_rounded, size: 18),
                          label: const Text('Ubah Data'),
                          style: FilledButton.styleFrom(
                            backgroundColor: AppColors.accent,
                            minimumSize: const Size.fromHeight(48),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}

class _InfoRow extends StatelessWidget {
  final IconData icon;
  final String   label;
  final String   value;

  const _InfoRow({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: AppColors.primaryLight,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, size: 20, color: AppColors.primary),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.gray500,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  value.trim().isEmpty ? '-' : value,
                  style: const TextStyle(
                    fontSize: 14.5,
                    fontWeight: FontWeight.w600,
                    color: AppColors.gray900,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}