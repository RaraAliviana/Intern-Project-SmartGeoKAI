class UserModel {
  final int    id;
  final String fullName;
  final String username;
  final String nip;
  final String role;
  final bool   isActive;

  const UserModel({
    required this.id,
    required this.fullName,
    required this.username,
    required this.nip,
    required this.role,
    required this.isActive,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    final active = json['is_active'];
    return UserModel(
      id:       (json['id'] as num?)?.toInt() ?? 0,
      fullName: (json['full_name'] ?? json['name'] ?? '').toString(),
      username: (json['username'] ?? '').toString(),
      nip:      (json['nip'] ?? '').toString(),
      role:     (json['role'] ?? 'petugas').toString(),
      isActive: active == null
          ? true
          : (active == 1 || active == true || active == '1'),
    );
  }

  UserModel copyWith({
    String? fullName,
    String? username,
    String? nip,
    String? role,
    bool?   isActive,
  }) {
    return UserModel(
      id:       id,
      fullName: fullName ?? this.fullName,
      username: username ?? this.username,
      nip:      nip      ?? this.nip,
      role:     role     ?? this.role,
      isActive: isActive ?? this.isActive,
    );
  }

  Map<String, dynamic> toJson() => {
    'id':        id,
    'full_name': fullName,
    'username':  username,
    'nip':       nip,
    'role':      role,
    'is_active': isActive,
  };
}