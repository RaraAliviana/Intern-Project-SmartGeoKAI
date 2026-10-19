import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../assets/providers/asset_provider.dart';
import '../../assets/screens/asset_list_screen.dart';
import '../../assets/screens/asset_map_screen.dart';
import '../../dashboard/screens/dashboard_screen.dart';
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

  void _goTo(int i) => setState(() => _index = i);

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
    final items = <_NavItem>[
      _NavItem(
        label: 'Beranda',
        icon: Icons.home_outlined,
        activeIcon: Icons.home_rounded,
        page: DashboardScreen(onOpenTab: _goTo),
      ),
      const _NavItem(
        label: 'Peta',
        icon: Icons.map_outlined,
        activeIcon: Icons.map_rounded,
        page: AssetMapScreen(),
      ),
      const _NavItem(
        label: 'Data Aset',
        icon: Icons.inventory_2_outlined,
        activeIcon: Icons.inventory_2_rounded,
        page: AssetListScreen(),
      ),
      const _NavItem(
        label: 'Profil',
        icon: Icons.person_outline_rounded,
        activeIcon: Icons.person_rounded,
        page: ProfileScreen(),
      ),
    ];

    return Scaffold(
      body: IndexedStack(
        index: _index,
        children: items.map((e) => e.page).toList(),
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: _goTo,
        backgroundColor: AppColors.white,
        indicatorColor: AppColors.primaryLight,
        height: 68,
        destinations: items
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