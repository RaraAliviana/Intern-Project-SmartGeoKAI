import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:geolocator/geolocator.dart';
import 'package:latlong2/latlong.dart';
import 'package:provider/provider.dart';
import '../models/asset_model.dart';
import '../providers/asset_provider.dart';
import '../widgets/asset_summary_sheet.dart';
import '../widgets/asset_toolbar.dart';
import '../../../core/constants/app_colors.dart';
import '../../../shared/widgets/app_header.dart';

class AssetMapScreen extends StatefulWidget {
  const AssetMapScreen({super.key});

  @override
  State<AssetMapScreen> createState() => _AssetMapScreenState();
}

class _AssetMapScreenState extends State<AssetMapScreen> {
  final MapController _mapController = MapController();

  bool    _mapReady      = false;
  int     _fittedVersion = 0;
  bool    _locating      = false;
  LatLng? _myPosition;

  // ── Zoom otomatis ke semua aset yang tampil ────────────────────────
  void _maybeFit(AssetProvider p, List<AssetModel> assets) {
    if (!_mapReady || p.isLoading || p.fitVersion == _fittedVersion) return;
    _fittedVersion = p.fitVersion;
    if (assets.isEmpty) return;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) _fitAll(assets);
    });
  }

  void _fitAll(List<AssetModel> assets) {
    final points = assets.map((a) => a.point!).toList();
    if (points.isEmpty) return;

    if (points.length == 1) {
      _mapController.move(points.first, 15);
      return;
    }
    _mapController.fitCamera(
      CameraFit.bounds(
        bounds: LatLngBounds.fromPoints(points),
        padding: const EdgeInsets.fromLTRB(48, 260, 48, 120),
        maxZoom: 17,
      ),
    );
  }

  // ── Lokasi saya ────────────────────────────────────────────────────
  void _toast(String text) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(text)));
  }

  Future<void> _goToMyLocation() async {
    if (_locating) return;
    setState(() => _locating = true);

    try {
      final enabled = await Geolocator.isLocationServiceEnabled();
      if (!enabled) {
        _toast('Aktifkan layanan lokasi (GPS) terlebih dahulu.');
        return;
      }

      var permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
      if (permission == LocationPermission.denied) {
        _toast('Izin lokasi ditolak.');
        return;
      }
      if (permission == LocationPermission.deniedForever) {
        _toast('Izin lokasi ditolak permanen. Aktifkan lewat pengaturan.');
        return;
      }

      final pos = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.high,
          timeLimit: Duration(seconds: 15),
        ),
      );
      if (!mounted) return;

      final here = LatLng(pos.latitude, pos.longitude);
      setState(() => _myPosition = here);
      _mapController.move(here, 16);
    } catch (_) {
      _toast('Gagal mendapatkan lokasi. Coba lagi.');
    } finally {
      if (mounted) setState(() => _locating = false);
    }
  }

  // ── Pin ────────────────────────────────────────────────────────────
  Marker _buildMarker(AssetModel a) {
    return Marker(
      point: a.point!,
      width: 46,
      height: 46,
      alignment: Alignment.topCenter,
      child: GestureDetector(
        onTap: () => showAssetSummary(context, a),
        child: Stack(
          alignment: Alignment.topCenter,
          children: [
            const Icon(Icons.location_on, size: 46, color: Colors.white),
            Icon(Icons.location_on, size: 40, color: a.status.color),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final p      = context.watch<AssetProvider>();
    final assets = p.filtered.where((a) => a.hasLocation).toList();

    _maybeFit(p, assets);

    return Stack(
      children: [
        // Peta penuh layar
        Positioned.fill(
          child: FlutterMap(
            mapController: _mapController,
            options: MapOptions(
              initialCenter: const LatLng(-2.5, 118),
              initialZoom: 4.5,
              minZoom: 3,
              maxZoom: 19,
              onMapReady: () {
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  if (mounted && !_mapReady) setState(() => _mapReady = true);
                });
              },
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.smartgeokai.mobile',
              ),
              MarkerLayer(markers: assets.map(_buildMarker).toList()),
              if (_myPosition != null)
                MarkerLayer(
                  markers: [
                    Marker(
                      point: _myPosition!,
                      width: 30,
                      height: 30,
                      child: const _MyLocationDot(),
                    ),
                  ],
                ),
              const RichAttributionWidget(
                alignment: AttributionAlignment.bottomLeft,
                attributions: [
                  TextSourceAttribution('© OpenStreetMap contributors'),
                ],
              ),
            ],
          ),
        ),

        // Header standar (di atas peta)
        Align(
          alignment: Alignment.topCenter,
          child: AppHeader(
            title: 'Peta Sebaran Aset',
            subtitle: '${assets.length} aset ditampilkan',
            child: const Column(
              children: [
                AssetSearchBar(),
                SizedBox(height: 10),
                AssetStatusFilterRow(),
              ],
            ),
          ),
        ),

        // Tombol kanan bawah
        Positioned(
          right: 16,
          bottom: 24,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              FloatingActionButton.small(
                heroTag: null,
                backgroundColor: AppColors.white,
                foregroundColor: AppColors.primary,
                onPressed: p.isLoading ? null : () => p.load(),
                child: const Icon(Icons.refresh_rounded),
              ),
              const SizedBox(height: 10),
              FloatingActionButton.small(
                heroTag: null,
                backgroundColor: AppColors.white,
                foregroundColor: AppColors.primary,
                onPressed: assets.isEmpty ? null : () => _fitAll(assets),
                child: const Icon(Icons.zoom_out_map_rounded),
              ),
              const SizedBox(height: 10),
              FloatingActionButton(
                heroTag: null,
                backgroundColor: AppColors.accent,
                foregroundColor: Colors.white,
                onPressed: _goToMyLocation,
                child: _locating
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(
                          strokeWidth: 2.5,
                          color: Colors.white,
                        ),
                      )
                    : const Icon(Icons.my_location_rounded),
              ),
            ],
          ),
        ),

        // Status memuat / error / kosong
        if (p.isLoading)
          const _StatusCard(
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                SizedBox(
                  width: 18,
                  height: 18,
                  child: CircularProgressIndicator(strokeWidth: 2.5),
                ),
                SizedBox(width: 12),
                Text('Memuat aset...'),
              ],
            ),
          )
        else if (p.error != null)
          _StatusCard(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  p.error!,
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: AppColors.error),
                ),
                const SizedBox(height: 8),
                TextButton(
                  onPressed: () => p.load(),
                  child: const Text('Coba lagi'),
                ),
              ],
            ),
          )
        else if (assets.isEmpty)
          const _StatusCard(
            child: Text(
              'Tidak ada aset dengan koordinat yang cocok.',
              textAlign: TextAlign.center,
            ),
          ),
      ],
    );
  }
}

class _StatusCard extends StatelessWidget {
  final Widget child;
  const _StatusCard({required this.child});

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.center,
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 32),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
        decoration: BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.12),
              blurRadius: 16,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: child,
      ),
    );
  }
}

class _MyLocationDot extends StatelessWidget {
  const _MyLocationDot();

  @override
  Widget build(BuildContext context) {
    const blue = Color(0xFF2563EB);
    return Container(
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: blue.withValues(alpha: 0.2),
      ),
      child: Center(
        child: Container(
          width: 16,
          height: 16,
          decoration: BoxDecoration(
            color: blue,
            shape: BoxShape.circle,
            border: Border.all(color: Colors.white, width: 3),
            boxShadow: const [BoxShadow(color: Colors.black26, blurRadius: 4)],
          ),
        ),
      ),
    );
  }
}