import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/asset_model.dart';
import '../providers/asset_provider.dart';
import 'asset_filter_sheet.dart';
import '../../../core/constants/app_colors.dart';

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
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.08),
                  blurRadius: 14,
                  offset: const Offset(0, 4),
                ),
              ],
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
          backgroundColor: AppColors.error,
          child: Material(
            color: AppColors.primary,
            borderRadius: BorderRadius.circular(16),
            elevation: 3,
            shadowColor: Colors.black26,
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
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

class AssetStatusFilterRow extends StatelessWidget {
  const AssetStatusFilterRow({super.key});

  @override
  Widget build(BuildContext context) {
    final p      = context.watch<AssetProvider>();
    final counts = p.statusCounts;

    return Row(
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
      color: selected ? status.color.withValues(alpha: 0.2) : AppColors.white,
      borderRadius: BorderRadius.circular(20),
      elevation: selected ? 0 : 2,
      shadowColor: Colors.black26,
      child: InkWell(
        borderRadius: BorderRadius.circular(20),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: selected ? status.color : Colors.transparent,
              width: 1.4,
            ),
          ),
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
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: AppColors.gray800,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}