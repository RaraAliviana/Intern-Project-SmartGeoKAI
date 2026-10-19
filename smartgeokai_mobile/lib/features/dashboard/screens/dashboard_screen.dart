import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../assets/models/asset_model.dart';
import '../../assets/providers/asset_provider.dart';
import '../../auth/providers/auth_provider.dart';
import '../../assets/widgets/activity_tile.dart';
import 'activity_screen.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/utils/time_format.dart';

class DashboardScreen extends StatelessWidget {
  // Dipanggil untuk pindah tab di navigasi bawah (0 Beranda, 1 Peta,
  // 2 Data Aset, 3 Profil)
  final ValueChanged<int> onOpenTab;

  const DashboardScreen({super.key, required this.onOpenTab});

  static const int _tabAssets = 2;

  void _openStatus(BuildContext context, AssetStatus status) {
    context.read<AssetProvider>().showOnlyStatus(status);
    onOpenTab(_tabAssets);
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final p    = context.watch<AssetProvider>();
    final top  = MediaQuery.of(context).padding.top;

    final total     = p.totalCount;
    final nClear    = p.countByStatus(AssetStatus.clear);
    final nProses   = p.countByStatus(AssetStatus.proses);
    final nMasalah  = p.countByStatus(AssetStatus.masalah);
    final noCoord   = p.withoutLocationCount;
    final activities = p.recentActivities(limit: 5);
    final area      = p.provinceOptions.join(', ');

    final name = user?.fullName.trim() ?? '';
    final firstName =
        name.isEmpty ? 'Petugas' : name.split(RegExp(r'\s+')).first;

    final hasAttention = nMasalah > 0 || noCoord > 0;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.light,
      child: RefreshIndicator(
        onRefresh: () => p.load(),
        edgeOffset: top,
        child: ListView(
          padding: EdgeInsets.zero,
          physics: const AlwaysScrollableScrollPhysics(),
          children: [
            // ── Header ───────────────────────────────────────────────
            Container(
              width: double.infinity,
              padding: EdgeInsets.fromLTRB(24, top + 20, 12, 84),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [AppColors.primary, AppColors.primaryDark],
                ),
                borderRadius:
                    BorderRadius.vertical(bottom: Radius.circular(36)),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          formatToday(),
                          style: TextStyle(
                            fontSize: 12.5,
                            color: Colors.white.withValues(alpha: 0.8),
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'Halo, $firstName',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.w700,
                            color: Colors.white,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          area.isEmpty
                              ? 'Ringkasan pekerjaan Anda'
                              : 'Wilayah kerja: $area',
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(
                            fontSize: 12.5,
                            color: Colors.white.withValues(alpha: 0.85),
                          ),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    onPressed: p.isLoading ? null : () => p.load(),
                    icon: const Icon(Icons.refresh_rounded, color: Colors.white),
                  ),
                ],
              ),
            ),

            // ── Isi (menimpa bagian bawah header) ────────────────────
            Transform.translate(
              offset: const Offset(0, -56),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Total aset + bar proporsi
                    _Card(
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                const Text(
                                  'Total Aset',
                                  style: TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.w600,
                                    color: AppColors.gray500,
                                  ),
                                ),
                                const Spacer(),
                                if (p.isLoading)
                                  const SizedBox(
                                    width: 16,
                                    height: 16,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                    ),
                                  ),
                              ],
                            ),
                            const SizedBox(height: 4),
                            Text(
                              '$total',
                              style: const TextStyle(
                                fontSize: 40,
                                height: 1.1,
                                fontWeight: FontWeight.w800,
                                color: AppColors.gray900,
                              ),
                            ),
                            const SizedBox(height: 14),
                            _StackedBar(
                              clear: nClear,
                              proses: nProses,
                              masalah: nMasalah,
                            ),
                            const SizedBox(height: 10),
                            Text(
                              p.error ?? 'Seluruh aset di wilayah kerja Anda',
                              style: TextStyle(
                                fontSize: 12,
                                color: p.error != null
                                    ? AppColors.error
                                    : AppColors.gray500,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 14),

                    // Kartu per status (ketuk untuk melihat daftarnya)
                    Row(
                      children: [
                        Expanded(
                          child: _StatCard(
                            label: 'Clear',
                            count: nClear,
                            color: AppColors.clear,
                            icon: Icons.check_circle_rounded,
                            onTap: () => _openStatus(context, AssetStatus.clear),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: _StatCard(
                            label: 'Proses',
                            count: nProses,
                            color: AppColors.proses,
                            icon: Icons.timelapse_rounded,
                            onTap: () => _openStatus(context, AssetStatus.proses),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: _StatCard(
                            label: 'Bermasalah',
                            count: nMasalah,
                            color: AppColors.masalah,
                            icon: Icons.error_rounded,
                            onTap: () => _openStatus(context, AssetStatus.masalah),
                          ),
                        ),
                      ],
                    ),

                    // Perlu perhatian
                    if (hasAttention) ...[
                      const SizedBox(height: 24),
                      const _SectionTitle('Perlu Perhatian'),
                      const SizedBox(height: 10),
                      if (nMasalah > 0)
                        _AttentionTile(
                          color: AppColors.masalah,
                          icon: Icons.warning_amber_rounded,
                          title: '$nMasalah aset bermasalah',
                          subtitle: 'Perlu segera ditindaklanjuti',
                          onTap: () => _openStatus(context, AssetStatus.masalah),
                        ),
                      if (nMasalah > 0 && noCoord > 0) const SizedBox(height: 10),
                      if (noCoord > 0)
                        _AttentionTile(
                          color: const Color(0xFF6366F1),
                          icon: Icons.location_off_rounded,
                          title: '$noCoord aset belum punya koordinat',
                          subtitle: 'Lengkapi lokasi agar tampil di peta',
                          onTap: () {
                            context.read<AssetProvider>().showOnlyStatus(null);
                            onOpenTab(_tabAssets);
                          },
                        ),
                    ],

                    // Aktivitas terbaru
                    const SizedBox(height: 24),
                    Row(
                      children: [
                        const Expanded(child: _SectionTitle('Aktivitas Terbaru')),
                        TextButton(
                          onPressed: () => Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => const ActivityScreen(),
                            ),
                          ),
                          child: const Text('Lihat semua'),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    _Card(
                      child: activities.isEmpty
                          ? Padding(
                              padding: const EdgeInsets.all(24),
                              child: Center(
                                child: Text(
                                  p.isLoading
                                      ? 'Memuat aktivitas...'
                                      : 'Belum ada aktivitas.',
                                  style: const TextStyle(
                                    fontSize: 13,
                                    color: AppColors.gray500,
                                  ),
                                ),
                              ),
                            )
                          : Column(
                              children: [
                                for (var i = 0; i < activities.length; i++) ...[
                                  if (i > 0)
                                    const Divider(
                                      height: 1,
                                      indent: 70,
                                      endIndent: 16,
                                      color: AppColors.gray100,
                                    ),
                                  ActivityTile(asset: activities[i]),
                                ],
                              ],
                            ),
                    ),
                    const SizedBox(height: 8),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ── Komponen kecil ─────────────────────────────────────────────────────
class _Card extends StatelessWidget {
  final Widget child;
  const _Card({required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 18,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(20),
        clipBehavior: Clip.antiAlias,
        child: child,
      ),
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String text;
  const _SectionTitle(this.text);

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: const TextStyle(
        fontSize: 16,
        fontWeight: FontWeight.w700,
        color: AppColors.gray900,
      ),
    );
  }
}

class _StackedBar extends StatelessWidget {
  final int clear;
  final int proses;
  final int masalah;

  const _StackedBar({
    required this.clear,
    required this.proses,
    required this.masalah,
  });

  @override
  Widget build(BuildContext context) {
    final total = clear + proses + masalah;

    return ClipRRect(
      borderRadius: BorderRadius.circular(8),
      child: SizedBox(
        height: 10,
        child: total == 0
            ? Container(color: AppColors.gray100)
            : Row(
                children: [
                  if (clear > 0)
                    Expanded(
                      flex: clear,
                      child: Container(color: AppColors.clear),
                    ),
                  if (proses > 0)
                    Expanded(
                      flex: proses,
                      child: Container(color: AppColors.proses),
                    ),
                  if (masalah > 0)
                    Expanded(
                      flex: masalah,
                      child: Container(color: AppColors.masalah),
                    ),
                ],
              ),
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  final String       label;
  final int          count;
  final Color        color;
  final IconData     icon;
  final VoidCallback onTap;

  const _StatCard({
    required this.label,
    required this.count,
    required this.color,
    required this.icon,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return _Card(
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 34,
                height: 34,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(11),
                ),
                child: Icon(icon, size: 19, color: color),
              ),
              const SizedBox(height: 12),
              Text(
                '$count',
                style: const TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.w800,
                  color: AppColors.gray900,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontSize: 12, color: AppColors.gray500),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _AttentionTile extends StatelessWidget {
  final Color        color;
  final IconData     icon;
  final String       title;
  final String       subtitle;
  final VoidCallback onTap;

  const _AttentionTile({
    required this.color,
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: color.withValues(alpha: 0.1),
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Row(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.18),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, size: 21, color: color),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: AppColors.gray900,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.gray700,
                      ),
                    ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right_rounded, color: AppColors.gray500),
            ],
          ),
        ),
      ),
    );
  }
}