import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../assets/providers/asset_provider.dart';
import '../../assets/widgets/activity_tile.dart';
import '../../../core/constants/app_colors.dart';

class ActivityScreen extends StatelessWidget {
  const ActivityScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final p     = context.watch<AssetProvider>();
    final items = p.recentActivities();

    return Scaffold(
      appBar: AppBar(title: const Text('Aktivitas Terbaru')),
      body: RefreshIndicator(
        onRefresh: () => p.load(),
        child: items.isEmpty
            ? ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: const [
                  SizedBox(height: 140),
                  Icon(Icons.history_rounded,
                      size: 48, color: AppColors.gray400),
                  SizedBox(height: 12),
                  Center(
                    child: Text(
                      'Belum ada aktivitas.',
                      style: TextStyle(color: AppColors.gray500),
                    ),
                  ),
                ],
              )
            : ListView.separated(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                itemCount: items.length,
                separatorBuilder: (_, __) => const SizedBox(height: 10),
                itemBuilder: (_, i) => Material(
                  color: AppColors.white,
                  borderRadius: BorderRadius.circular(16),
                  clipBehavior: Clip.antiAlias,
                  child: ActivityTile(asset: items[i]),
                ),
              ),
      ),
    );
  }
}