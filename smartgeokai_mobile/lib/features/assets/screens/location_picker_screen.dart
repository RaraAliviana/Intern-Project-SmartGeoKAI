import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/core/utils/location_helper.dart';

class LocationPickerScreen extends StatefulWidget {
  /// Titik awal (mis. koordinat yang sudah diisi). Boleh null.
  final LatLng? initial;

  const LocationPickerScreen({super.key, this.initial});

  @override
  State<LocationPickerScreen> createState() => _LocationPickerScreenState();
}

class _LocationPickerScreenState extends State<LocationPickerScreen> {
  static const _defaultCenter = LatLng(-7.7956, 110.3695); // Yogyakarta

  final _map = MapController();
  LatLng? _picked;
  bool _locating = false;

  @override
  void initState() {
    super.initState();
    _picked = widget.initial;

    // Belum ada titik → coba langsung arahkan ke lokasi petugas (diam-diam)
    if (widget.initial == null) {
      WidgetsBinding.instance
          .addPostFrameCallback((_) => _useMyLocation(silent: true));
    }
  }

  @override
  void dispose() {
    _map.dispose();
    super.dispose();
  }

  void _toast(String text) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(text)));
  }

  Future<void> _useMyLocation({bool silent = false}) async {
    if (_locating) return;
    setState(() => _locating = true);
    try {
      final pos = await getCurrentPosition();
      if (!mounted) return;
      final p = LatLng(pos.latitude, pos.longitude);
      setState(() => _picked = p);
      _map.move(p, 17);
    } on LocationException catch (e) {
      if (!silent) _toast(e.message);
    } catch (_) {
      if (!silent) _toast('Gagal mendapatkan lokasi. Coba lagi.');
    } finally {
      if (mounted) setState(() => _locating = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final center = widget.initial ?? _defaultCenter;
    final picked = _picked;

    return Scaffold(
      appBar: AppBar(title: const Text('Pilih Lokasi')),
      body: Stack(
        children: [
          FlutterMap(
            mapController: _map,
            options: MapOptions(
              initialCenter: center,
              initialZoom: widget.initial != null ? 17 : 13,
              onTap: (_, point) => setState(() => _picked = point),
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.example.smartgeokai_mobile',
              ),
              if (picked != null)
                MarkerLayer(
                  markers: [
                    Marker(
                      point: picked,
                      width: 52,
                      height: 52,
                      alignment: Alignment.topCenter, // ujung pin tepat di titik
                      child: const Icon(
                        Icons.location_on_rounded,
                        size: 52,
                        color: AppColors.accent,
                      ),
                    ),
                  ],
                ),
            ],
          ),

          // Info koordinat di atas
          Positioned(
            top: 12,
            left: 16,
            right: 16,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
              decoration: BoxDecoration(
                color: AppColors.white,
                borderRadius: BorderRadius.circular(14),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.12),
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: [
                  const Icon(Icons.touch_app_rounded,
                      size: 20, color: AppColors.primary),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      picked == null
                          ? 'Ketuk peta untuk memilih titik lokasi'
                          : '${picked.latitude.toStringAsFixed(6)}, '
                              '${picked.longitude.toStringAsFixed(6)}',
                      style: const TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: AppColors.gray800,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          // Tombol bawah
          Positioned(
            left: 16,
            right: 16,
            bottom: 0,
            child: SafeArea(
              child: Padding(
                padding: const EdgeInsets.only(bottom: 14),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Align(
                      alignment: Alignment.centerRight,
                      child: FloatingActionButton.extended(
                        heroTag: 'picker_my_location',
                        onPressed: _locating ? null : _useMyLocation,
                        backgroundColor: AppColors.white,
                        foregroundColor: AppColors.primary,
                        icon: _locating
                            ? const SizedBox(
                                width: 18,
                                height: 18,
                                child: CircularProgressIndicator(strokeWidth: 2),
                              )
                            : const Icon(Icons.my_location_rounded),
                        label: Text(_locating ? 'Mencari...' : 'Lokasi saya'),
                      ),
                    ),
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      height: 52,
                      child: FilledButton(
                        onPressed: picked == null
                            ? null
                            : () => Navigator.pop(context, picked),
                        style: FilledButton.styleFrom(
                          backgroundColor: AppColors.accent,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                        child: const Text(
                          'Gunakan titik ini',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}