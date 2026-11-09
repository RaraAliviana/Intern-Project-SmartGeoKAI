class UserModel {
  final int    id;
  final String fullName;
  final String username;
  final String nipp;
  final String role;
  final bool   isActive;

  const UserModel({
    required this.id,
    required this.fullName,
    required this.username,
    required this.nipp,
    required this.role,
    required this.isActive,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    final active = json['is_active'];
    return UserModel(
      id:       (json['id'] as num?)?.toInt() ?? 0,
      fullName: (json['full_name'] ?? json['name'] ?? '').toString(),
      username: (json['username'] ?? '').toString(),
      // Server mengirim 'nipp'; 'nip' tetap dibaca agar kompatibel dengan server lama
      nipp:     (json['nipp'] ?? json['nip'] ?? '').toString(),
      role:     (json['role'] ?? 'petugas').toString(),
      isActive: active == null
          ? true
          : (active == 1 || active == true || active == '1'),
    );
  }

  UserModel copyWith({
    String? fullName,
    String? username,
    String? nipp,
    String? role,
    bool?   isActive,
  }) {
    return UserModel(
      id:       id,
      fullName: fullName ?? this.fullName,
      username: username ?? this.username,
      nipp:     nipp     ?? this.nipp,
      role:     role     ?? this.role,
      isActive: isActive ?? this.isActive,
    );
  }

  Map<String, dynamic> toJson() => {
    'id':        id,
    'full_name': fullName,
    'username':  username,
    'nipp':      nipp,
    'role':      role,
    'is_active': isActive,
  };
}