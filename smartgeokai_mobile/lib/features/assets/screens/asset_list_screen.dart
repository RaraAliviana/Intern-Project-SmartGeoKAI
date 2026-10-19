
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';
import 'package:smartgeokai_mobile/features/assets/providers/asset_provider.dart';
import 'package:smartgeokai_mobile/features/assets/screens/asset_form_screen.dart';
import 'package:smartgeokai_mobile/features/assets/widgets/asset_summary_sheet.dart';
import 'package:smartgeokai_mobile/features/assets/widgets/asset_toolbar.dart';
import 'package:smartgeokai_mobile/shared/widgets/app_header.dart';

class AssetListScreen extends StatelessWidget {
  const AssetListScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<AssetProvider>(
      builder: (context, p, child) {
        final items = p.filtered;

        return Scaffold(
          body: SafeArea(
            child: Column(
              children: [
                AppHeader(
                  title: 'Data Aset',
                  subtitle: '${items.length} dari ${p.totalCount} aset',
                  actions: [
                    Padding(
                      padding: const EdgeInsets.only(right: 16),
                      child: Material(
                        color: AppColors.accent,
                        borderRadius: BorderRadius.circular(12),
                        child: InkWell(
                          borderRadius: BorderRadius.circular(12),
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const AssetFormScreen(),
                              ),
                            );
                          },
                          child: const SizedBox(
                            width: 44,
                            height: 44,
                            child: Icon(
                              Icons.add,
                              color: Colors.white,
                              size: 26,
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                  child: Column(
                    children: const [
                      AssetSearchBar(),
                      SizedBox(height: 10),
                      AssetStatusFilterRow(),
                    ],
                  ),
                ),

                Expanded(
                  child: _buildContent(context, p, items),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildContent(
    BuildContext context,
    AssetProvider p,
    List<AssetModel> items,
  ) {
    if (p.isLoading) {
      return const Center(
        child: CircularProgressIndicator(),
      );
    }

    if (p.error != null && p.error!.isNotEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.error_outline,
                size: 48,
                color: Colors.red,
              ),
              const SizedBox(height: 12),
              Text(
                p.error!,
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 16),
              FilledButton.icon(
                onPressed: () => p.load(),
                icon: const Icon(Icons.refresh),
                label: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }

    if (items.isEmpty) {
      return const Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              Icons.inventory_2_outlined,
              size: 56,
              color: Colors.grey,
            ),
            SizedBox(height: 12),
            Text(
              'Tidak ada data aset',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
              ),
            ),
            SizedBox(height: 6),
            Text(
              'Coba ubah kata kunci atau filter.',
              style: TextStyle(color: Colors.grey),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: () => p.load(),
      child: ListView.separated(
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, index) {
          final asset = items[index];

          return _AssetTile(
            asset: asset,
            onTap: () => showAssetSummary(context, asset),
          );
        },
      ),
    );
  }
}

class _AssetTile extends StatelessWidget {
  final AssetModel asset;
  final VoidCallback onTap;

  const _AssetTile({
    required this.asset,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 0,
      margin: EdgeInsets.zero,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(14),
        side: BorderSide(
          color: Colors.grey.withValues(alpha: 0.2),
        ),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  color: AppColors.accent.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(
                  Icons.location_on_outlined,
                  color: AppColors.accent,
                  size: 25,
                ),
              ),
              const SizedBox(width: 12),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      asset.displayCode,
                      style: const TextStyle(
                        fontSize: 12,
                        color: Colors.grey,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      asset.name,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      asset.type,
                      style: const TextStyle(
                        fontSize: 13,
                        color: Colors.black54,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      asset.regionText,
                      style: const TextStyle(
                        fontSize: 12,
                        color: Colors.grey,
                      ),
                    ),
                    const SizedBox(height: 10),
                    _StatusBadge(status: asset.status),
                  ],
                ),
              ),

              const SizedBox(width: 8),
              const Icon(
                Icons.chevron_right,
                color: Colors.grey,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _StatusBadge extends StatelessWidget {
  final String status;

  const _StatusBadge({
    required this.status,
  });

  @override
  Widget build(BuildContext context) {
    final normalized = status.toLowerCase();

    final isActive = normalized == 'aktif';
    final background = isActive
        ? Colors.green.withValues(alpha: 0.12)
        : Colors.orange.withValues(alpha: 0.12);
    final foreground = isActive ? Colors.green.shade700 : Colors.orange.shade800;

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: 10,
        vertical: 5,
      ),
      decoration: BoxDecoration(
        color: background,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        status,
        style: TextStyle(
          color: foreground,
          fontSize: 11,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }
}
