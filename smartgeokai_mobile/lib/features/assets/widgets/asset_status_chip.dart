import 'package:flutter/material.dart';
import 'package:smartgeokai_mobile/features/assets/models/asset_model.dart';

class AssetStatusChip extends StatelessWidget {
  final AssetStatus status;
  const AssetStatusChip({super.key, required this.status});

  Color get _textColor => switch (status) {
        AssetStatus.clear   => const Color(0xFF15803D),
        AssetStatus.proses  => const Color(0xFFA16207),
        AssetStatus.masalah => const Color(0xFFB91C1C),
      };

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: status.color.withValues(alpha: 0.16),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 8,
            height: 8,
            decoration: BoxDecoration(
              color: status.color,
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 6),
          Text(
            status.label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: _textColor,
            ),
          ),
        ],
      ),
    );
  }
}