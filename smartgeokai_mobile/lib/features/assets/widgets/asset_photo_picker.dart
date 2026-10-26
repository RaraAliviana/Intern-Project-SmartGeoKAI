import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/core/utils/image_compressor.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';
import 'package:smartgeokai_mobile/features/assets/widgets/asset_photo_strip.dart';

class PhotoController extends ChangeNotifier {
  static const int maxPhotos = 10;

  PhotoController({List<AssetPhoto> remote = const []})
      : remote = List.of(remote);

  final List<AssetPhoto> remote;
  final List<Uint8List> local = [];
  final List<String> removedRemoteIds = [];

  bool busy = false;
  String? message;

  int get total => remote.length + local.length;
  int get remaining => maxPhotos - total;
  bool get canAdd => remaining > 0 && !busy;

  void setMessage(String? text) {
    message = text;
    notifyListeners();
  }

  Future<void> addFiles(List<XFile> files) async {
    if (files.isEmpty) return;

    busy = true;
    message = null;
    notifyListeners();

    var skipped = 0;
    var failed = 0;

    try {
      for (final f in files) {
        if (remaining <= 0) {
          skipped++;
          continue;
        }

        try {
          final bytes = await f.readAsBytes();
          final compressed = await compressToMax1MB(bytes);

          local.add(compressed);
        } catch (_) {
          failed++;
        }
      }

      final notes = <String>[];

      if (skipped > 0) {
        notes.add(
          '$skipped foto dilewati (maksimal $maxPhotos foto).',
        );
      }

      if (failed > 0) {
        notes.add('$failed foto gagal diproses.');
      }

      message = notes.isEmpty ? null : notes.join(' ');
    } finally {
      busy = false;
      notifyListeners();
    }
  }

  void removeLocal(int index) {
    local.removeAt(index);
    notifyListeners();
  }

  void removeRemote(int index) {
    final photo = remote.removeAt(index);

    if (photo.id != null) {
      removedRemoteIds.add(photo.id!);
    }

    notifyListeners();
  }
}

class AssetPhotoPicker extends StatelessWidget {
  final PhotoController controller;

  const AssetPhotoPicker({
    super.key,
    required this.controller,
  });

  Future<void> _pick(BuildContext context) async {
    final source = await showModalBottomSheet<ImageSource>(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.vertical(
            top: Radius.circular(24),
          ),
        ),
        child: SafeArea(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const SizedBox(height: 8),
              ListTile(
                leading: const Icon(
                  Icons.photo_camera_rounded,
                  color: AppColors.primary,
                ),
                title: const Text('Ambil dari kamera'),
                onTap: () {
                  Navigator.pop(ctx, ImageSource.camera);
                },
              ),
              ListTile(
                leading: const Icon(
                  Icons.photo_library_rounded,
                  color: AppColors.primary,
                ),
                title: const Text('Pilih dari galeri'),
                onTap: () {
                  Navigator.pop(ctx, ImageSource.gallery);
                },
              ),
              const SizedBox(height: 8),
            ],
          ),
        ),
      ),
    );

    if (source == null) return;

    try {
      final picker = ImagePicker();

      if (source == ImageSource.camera) {
        final file = await picker.pickImage(
          source: ImageSource.camera,
        );

        if (file != null) {
          await controller.addFiles([file]);
        }
      } else {
        final files = await picker.pickMultiImage();
        await controller.addFiles(files);
      }
    } catch (_) {
      controller.setMessage(
        'Tidak dapat membuka kamera atau galeri.',
      );
    }
  }

  String _size(int bytes) {
    return bytes < 1024 * 1024
        ? '${(bytes / 1024).round()} KB'
        : '${(bytes / (1024 * 1024)).toStringAsFixed(2)} MB';
  }

  @override
  Widget build(BuildContext context) {
    return ListenableBuilder(
      listenable: controller,
      builder: (context, _) {
        final tiles = <Widget>[
          // Foto lama dari server.
          for (var i = 0; i < controller.remote.length; i++)
            _Thumb(
              onRemove: () => controller.removeRemote(i),
              child: networkPhoto(controller.remote[i].url),
            ),

          // Foto baru yang dipilih dari kamera atau galeri.
          for (var i = 0; i < controller.local.length; i++)
            _Thumb(
              onRemove: () => controller.removeLocal(i),
              label: _size(
                controller.local[i].lengthInBytes,
              ),
              child: Image.memory(
                controller.local[i],
                fit: BoxFit.cover,
              ),
            ),

          // Tombol tambah foto.
          if (controller.canAdd || controller.busy)
            InkWell(
              onTap: controller.canAdd
                  ? () => _pick(context)
                  : null,
              borderRadius: BorderRadius.circular(14),
              child: Container(
                decoration: BoxDecoration(
                  color: AppColors.accentLight,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: AppColors.accent.withValues(alpha: 0.5),
                  ),
                ),
                child: Center(
                  child: controller.busy
                      ? const SizedBox(
                          width: 22,
                          height: 22,
                          child: CircularProgressIndicator(
                            strokeWidth: 2.5,
                          ),
                        )
                      : const Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              Icons.add_a_photo_rounded,
                              color: AppColors.accentDark,
                            ),
                            SizedBox(height: 4),
                            Text(
                              'Tambah',
                              style: TextStyle(
                                fontSize: 11.5,
                                fontWeight: FontWeight.w600,
                                color: AppColors.accentDark,
                              ),
                            ),
                          ],
                        ),
                ),
              ),
            ),
        ];

        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                const Expanded(
                  child: Text(
                    'Foto Aset',
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: AppColors.gray700,
                    ),
                  ),
                ),
                Text(
                  '${controller.total}/${PhotoController.maxPhotos}',
                  style: const TextStyle(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w600,
                    color: AppColors.primary,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 4),
            const Text(
              'Maksimal 10 foto. Setiap foto dikompres otomatis hingga '
              'di bawah 1 MB.',
              style: TextStyle(
                fontSize: 12,
                color: AppColors.gray500,
              ),
            ),
            const SizedBox(height: 12),
            if (tiles.isNotEmpty)
              GridView.count(
                crossAxisCount: 3,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                mainAxisSpacing: 8,
                crossAxisSpacing: 8,
                children: tiles,
              ),
            if (controller.message != null) ...[
              const SizedBox(height: 8),
              Text(
                controller.message!,
                style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.error,
                ),
              ),
            ],
          ],
        );
      },
    );
  }
}

class _Thumb extends StatelessWidget {
  final Widget child;
  final VoidCallback onRemove;
  final String? label;

  const _Thumb({
    required this.child,
    required this.onRemove,
    this.label,
  });

  @override
  Widget build(BuildContext context) {
    return Stack(
      fit: StackFit.expand,
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(14),
          child: child,
        ),
        if (label != null)
          Positioned(
            left: 6,
            bottom: 6,
            child: Container(
              padding: const EdgeInsets.symmetric(
                horizontal: 6,
                vertical: 2,
              ),
              decoration: BoxDecoration(
                color: Colors.black.withValues(alpha: 0.6),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                label!,
                style: const TextStyle(
                  fontSize: 10,
                  color: Colors.white,
                ),
              ),
            ),
          ),
        Positioned(
          right: 4,
          top: 4,
          child: InkWell(
            onTap: onRemove,
            child: Container(
              width: 24,
              height: 24,
              decoration: const BoxDecoration(
                color: AppColors.error,
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.close_rounded,
                size: 15,
                color: Colors.white,
              ),
            ),
          ),
        ),
      ],
    );
  }
}
