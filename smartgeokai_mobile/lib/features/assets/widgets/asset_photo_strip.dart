import 'package:flutter/material.dart';
import 'package:smartgeokai_mobile/core/constants/app_colors.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';

/// Gambar dari server. Di Chrome memakai elemen <img> sehingga tetap
/// tampil walau server tidak mengirim header CORS. Di Android biasa saja.
Widget networkPhoto(String url, {BoxFit fit = BoxFit.cover}) {
  return Image.network(
    url,
    fit: fit,
    webHtmlElementStrategy: WebHtmlElementStrategy.prefer,
    loadingBuilder: (context, child, progress) {
      if (progress == null) return child;
      return Container(
        color: AppColors.gray100,
        child: const Center(
          child: SizedBox(
            width: 20,
            height: 20,
            child: CircularProgressIndicator(strokeWidth: 2),
          ),
        ),
      );
    },
    errorBuilder: (context, error, stack) {
      debugPrint('FOTO GAGAL DIMUAT: $url -> $error');
      return Container(
        color: AppColors.gray100,
        child: const Center(
          child: Icon(Icons.broken_image_rounded, color: AppColors.gray400),
        ),
      );
    },
  );
}

/// Deretan foto aset (geser ke samping, ketuk untuk memperbesar).
class AssetPhotoStrip extends StatelessWidget {
  final List<AssetPhoto> photos;
  const AssetPhotoStrip({super.key, required this.photos});

  void _open(BuildContext context, int index) {
    showDialog(
      context: context,
      builder: (_) => _PhotoViewer(photos: photos, initial: index),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (photos.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(vertical: 18),
        decoration: BoxDecoration(
          color: AppColors.gray50,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.gray200),
        ),
        child: const Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.image_not_supported_outlined,
                color: AppColors.gray400, size: 26),
            SizedBox(height: 6),
            Text(
              'Belum ada foto untuk aset ini',
              style: TextStyle(fontSize: 12.5, color: AppColors.gray500),
            ),
          ],
        ),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      mainAxisSize: MainAxisSize.min,
      children: [
        Text(
          '${photos.length} foto',
          style: const TextStyle(
            fontSize: 12.5,
            fontWeight: FontWeight.w600,
            color: AppColors.gray700,
          ),
        ),
        const SizedBox(height: 8),
        SizedBox(
          height: 96,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            itemCount: photos.length,
            separatorBuilder: (_, __) => const SizedBox(width: 8),
            itemBuilder: (context, i) => GestureDetector(
              onTap: () => _open(context, i),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(12),
                child: SizedBox(
                  width: 96,
                  height: 96,
                  child: networkPhoto(photos[i].url),
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

class _PhotoViewer extends StatefulWidget {
  final List<AssetPhoto> photos;
  final int initial;
  const _PhotoViewer({required this.photos, required this.initial});

  @override
  State<_PhotoViewer> createState() => _PhotoViewerState();
}

class _PhotoViewerState extends State<_PhotoViewer> {
  late final PageController _controller =
      PageController(initialPage: widget.initial);
  late int _index = widget.initial;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Dialog.fullscreen(
      backgroundColor: Colors.black,
      child: Stack(
        children: [
          PageView.builder(
            controller: _controller,
            itemCount: widget.photos.length,
            onPageChanged: (i) => setState(() => _index = i),
            itemBuilder: (_, i) => InteractiveViewer(
              minScale: 1,
              maxScale: 4,
              child: Center(
                child: networkPhoto(widget.photos[i].url, fit: BoxFit.contain),
              ),
            ),
          ),
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            child: SafeArea(
              child: Padding(
                padding: const EdgeInsets.all(8),
                child: Row(
                  children: [
                    IconButton(
                      icon: const Icon(Icons.close_rounded, color: Colors.white),
                      onPressed: () => Navigator.pop(context),
                    ),
                    const Spacer(),
                    Text(
                      '${_index + 1}/${widget.photos.length}',
                      style: const TextStyle(color: Colors.white, fontSize: 14),
                    ),
                    const SizedBox(width: 12),
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