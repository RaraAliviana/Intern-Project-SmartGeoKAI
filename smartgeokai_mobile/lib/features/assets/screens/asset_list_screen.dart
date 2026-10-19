import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/asset_model.dart';
import '../providers/asset_provider.dart';
import '../widgets/asset_summary_sheet.dart';
import '../widgets/asset_toolbar.dart';
import '../../../core/constants/app_colors.dart';

class AssetListScreen extends StatelessWidget {
  const AssetListScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final p     = context.watch<AssetProvider>();
    final items = p.filtered;
    final top   = MediaQuery.of(context).padding.top;

    Widget body;
    if (p.isLoading && p.totalCount == 0) {
      body = const Center(child: CircularProgressIndicator());
    } else if (p.error != null && p.totalCount == 0) {
      body = Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(Icons.cloud_off_rounded,
                  size: 48, color: AppColors.gray400),
              const SizedBox(height: 12),
              Text(
                p.error!,
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.gray700),
              ),
              const SizedBox(height: 12),
              FilledButton(
                onPressed: () => p.load(),
                child: const Text('Coba lagi'),
              ),
            ],
          ),
        ),
      );
    } else {
      body = RefreshIndicator(
        onRefresh: () => p.load(),
        child: items.isEmpty
            ? ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: const [
                  SizedBox(height: 120),
                  Icon(Icons.search_off_rounded,
                      size: 48, color: AppColors.gray400),
                  SizedBox(height: 12),
                  Center(
                    child: Text(
                      'Tidak ada aset yang cocok.',
                      style: TextStyle(color: AppColors.gray500),
                    ),
                  ),
                ],
              )
            : ListView.separated(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
                itemCount: items.length,
                separatorBuilder: (_, __) => const SizedBox(height: 10),
                itemBuilder: (_, i) => _AssetTile(asset: items[i]),
              ),
      );
    }

    return Column(
      children: [
        Container(
          width: double.infinity,
          padding: EdgeInsets.fromLTRB(16, top + 14, 16, 14),
          decoration: const BoxDecoration(
            color: AppColors.white,
            borderRadius: BorderRadius.vertical(bottom: Radius.circular(28)),
            boxShadow: [
              BoxShadow(
                color: Color(0x14000000),
                blurRadius: 14,
                offset: Offset(0, 4),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Expanded(
                    child: Text(
                      'Daftar Aset',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.w700,
                        color: AppColors.gray900,
                      ),
                    ),
                  ),
                  Text(
                    '${items.length} dari ${p.totalCount}',
                    style: const TextStyle(
                      fontSize: 12.5,
                      color: AppColors.gray500,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              const AssetSearchBar(),
              const SizedBox(height: 10),
              const SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: AssetStatusFilterRow(),
              ),
            ],
          ),
        ),
        Expanded(child: body),
      ],
    );
  }
}

class _AssetTile extends StatelessWidget {
  final AssetModel asset;
  const _AssetTile({required this.asset});

  @override
  Widget build(BuildContext context) {
    final subtitle = [
      asset.displayCode,
      if (asset.type.isNotEmpty) asset.type,
    ].join(' • ');

    return Material(
      color: AppColors.white,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        borderRadius: BorderRadius.circular(18),
        onTap: () => showAssetSummary(context, asset),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: AppColors.gray100),
          ),
          child: Row(
            children: [
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  color: asset.status.color.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(
                  Icons.location_on_rounded,
                  color: asset.status.color,
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
                        fontSize: 14.5,
                        fontWeight: FontWeight.w600,
                        color: AppColors.gray900,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.gray500,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      asset.hasLocation
                          ? (asset.regionText.isEmpty ? '-' : asset.regionText)
                          : 'Belum ada koordinat',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                        fontSize: 11.5,
                        color: asset.hasLocation
                            ? AppColors.gray400
                            : AppColors.masalah,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              StatusBadge(status: asset.status),
            ],
          ),
        ),
      ),
    );
  }
}