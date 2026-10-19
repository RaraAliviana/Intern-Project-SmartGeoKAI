import 'package:flutter/material.dart';
import '../../assets/models/asset_model.dart';
import '../../assets/widgets/asset_summary_sheet.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/utils/time_format.dart';

class ActivityTile extends StatelessWidget {
  final AssetModel asset;
  const ActivityTile({super.key, required this.asset});

  @override
  Widget build(BuildContext context) {
    final isNew = asset.isNewlyAdded;
    final color = isNew ? AppColors.clear : AppColors.primary;

    return InkWell(
      onTap: () => showAssetSummary(context, asset),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        child: Row(
          children: [
            Container(
              width: 42,
              height: 42,
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.12),
                borderRadius: BorderRadius.circular(13),
              ),
              child: Icon(
                isNew
                    ? Icons.add_location_alt_rounded
                    : Icons.edit_location_alt_rounded,
                size: 21,
                color: color,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    asset.displayName,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: AppColors.gray900,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Row(
                    children: [
                      Container(
                        width: 8,
                        height: 8,
                        decoration: BoxDecoration(
                          color: asset.status.color,
                          shape: BoxShape.circle,
                        ),
                      ),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          '${isNew ? 'Ditambahkan' : 'Diperbarui'}'
                          ' • ${asset.status.label}',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontSize: 12,
                            color: AppColors.gray500,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(width: 8),
            Text(
              formatRelative(asset.activityTime),
              style: const TextStyle(fontSize: 11.5, color: AppColors.gray400),
            ),
          ],
        ),
      ),
    );
  }
}