import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';
import 'package:smartgeokai_mobile/features/assets/providers/asset_provider.dart';
import 'package:smartgeokai_mobile/features/assets/widgets/asset_filter_sheet.dart';

class AssetSearchBar extends StatelessWidget {
  const AssetSearchBar({super.key});

  @override
  Widget build(BuildContext context) {
    final p = context.watch<AssetProvider>();
    final hasText = p.searchController.text.isNotEmpty;

    return Row(
      children: [
        Expanded(
          child: Container(
            decoration: BoxDecoration(
              color: AppColors.white,
              borderRadius: BorderRadius.circular(14),
            ),
            child: TextField(
              controller: p.searchController,
              onChanged: p.setQuery,
              textInputAction: TextInputAction.search,
              style: const TextStyle(fontSize: 14),
              decoration: InputDecoration(
                hintText: 'Cari ID atau nama aset',
                hintStyle: const TextStyle(
                  color: AppColors.gray400,
                  fontSize: 14,
                ),
                prefixIcon: const Icon(Icons.search_rounded,
                    color: AppColors.gray400),
                suffixIcon: hasText
                    ? IconButton(
                        icon: const Icon(Icons.close_rounded,
                            size: 20, color: AppColors.gray400),
                        onPressed: () {
                          p.searchController.clear();
                          p.setQuery('');
                        },
                      )
                    : null,
                border: InputBorder.none,
                contentPadding: const EdgeInsets.symmetric(vertical: 14),
              ),
            ),
          ),
        ),
        const SizedBox(width: 10),
        Badge(
          isLabelVisible: p.activeFilterCount > 0,
          label: Text('${p.activeFilterCount}'),
          backgroundColor: AppColors.white,
          textColor: AppColors.primary,
          child: Material(
            color: AppColors.accent,
            borderRadius: BorderRadius.circular(14),
            child: InkWell(
              borderRadius: BorderRadius.circular(14),
              onTap: () => showAssetFilterSheet(context),
              child: const SizedBox(
                width: 50,
                height: 50,
                child: Icon(Icons.tune_rounded, color: Colors.white),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

/// Chip status. Dirancang untuk dipasang di atas header navy.
class AssetStatusFilterRow extends StatelessWidget {
  const AssetStatusFilterRow({super.key});

  @override
  Widget build(BuildContext context) {
    final p      = context.watch<AssetProvider>();
    final counts = p.statusCounts;

    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: [
          for (final s in AssetStatus.values) ...[
            _StatusChip(
              status: s,
              count: counts[s] ?? 0,
              selected: p.status == s,
              onTap: () => p.toggleStatus(s),
            ),
            const SizedBox(width: 8),
          ],
        ],
      ),
    );
  }
}

class _StatusChip extends StatelessWidget {
  final AssetStatus  status;
  final int          count;
  final bool         selected;
  final VoidCallback onTap;

  const _StatusChip({
    required this.status,
    required this.count,
    required this.selected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: selected ? AppColors.white : Colors.white.withValues(alpha: 0.14),
      borderRadius: BorderRadius.circular(20),
      child: InkWell(
        borderRadius: BorderRadius.circular(20),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 9,
                height: 9,
                decoration: BoxDecoration(
                  color: status.color,
                  shape: BoxShape.circle,
                ),
              ),
              const SizedBox(width: 6),
              Text(
                '${status.label} $count',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: selected ? AppColors.primary : Colors.white,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}