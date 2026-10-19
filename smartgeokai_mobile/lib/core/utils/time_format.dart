String formatDate(DateTime d) {
  const bulan = [
    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
  ];
  return '${d.day} ${bulan[d.month - 1]} ${d.year}';
}

String formatRelative(DateTime? t) {
  if (t == null) return '-';
  final local = t.toLocal();
  final diff  = DateTime.now().difference(local);

  if (diff.isNegative || diff.inSeconds < 60) return 'Baru saja';
  if (diff.inMinutes < 60) return '${diff.inMinutes} menit lalu';
  if (diff.inHours < 24)   return '${diff.inHours} jam lalu';
  if (diff.inDays < 7)     return '${diff.inDays} hari lalu';
  return formatDate(local);
}

String formatToday() {
  const hari = [
    'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu',
  ];
  const bulan = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
  ];
  final n = DateTime.now();
  return '${hari[n.weekday - 1]}, ${n.day} ${bulan[n.month - 1]} ${n.year}';
}