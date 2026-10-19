import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../assets/providers/asset_provider.dart';
import '../../assets/screens/asset_list_screen.dart';
import '../../assets/screens/asset_map_screen.dart';
import '../../profile/screens/profile_screen.dart';
import '../../../core/constants/app_colors.dart';

class _NavItem {
  final String   label;
  final IconData icon;
  final IconData activeIcon;
  final Widget   page;

  const _NavItem({
    required this.label,
    required this.icon,
    required this.activeIcon,
    required this.page,
  });
}

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _index = 0;

  static const List<_NavItem> _items = [
    _NavItem(
      label: 'Peta',
      icon: Icons.map_outlined,
      activeIcon: Icons.map_rounded,
      page: AssetMapScreen(),
    ),
    _NavItem(
      label: 'Aset',
      icon: Icons.list_alt_outlined,
      activeIcon: Icons.list_alt_rounded,
      page: AssetListScreen(),
    ),
    _NavItem(
      label: 'Profil',
      icon: Icons.person_outline_rounded,
      activeIcon: Icons.person_rounded,
      page: ProfileScreen(),
    ),
  ];

  @override
  void initState() {
    super.initState();
    // Muat aset tiap kali masuk Home (juga saat ganti akun)
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) {
        context.read<AssetProvider>().load(resetFilters: true);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _index,
        children: _items.map((e) => e.page).toList(),
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        backgroundColor: AppColors.white,
        indicatorColor: AppColors.primaryLight,
        height: 68,
        destinations: _items
            .map(
              (e) => NavigationDestination(
                icon: Icon(e.icon, color: AppColors.gray500),
                selectedIcon: Icon(e.activeIcon, color: AppColors.primary),
                label: e.label,
              ),
            )
            .toList(),
      ),
    );
  }
}