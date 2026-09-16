import 'package:flutter/material.dart';
import 'core/network/api_client.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'SmartGeoKAI Mobile',
      home: const TestConnectionScreen(),
    );
  }
}

class TestConnectionScreen extends StatefulWidget {
  const TestConnectionScreen({super.key});

  @override
  State<TestConnectionScreen> createState() => _TestConnectionScreenState();
}

class _TestConnectionScreenState extends State<TestConnectionScreen> {
  String _result = 'Belum dites';

  Future<void> _testLogin() async {
    setState(() => _result = 'Menghubungi server...');

    try {
      final response = await ApiClient.instance.post('/login', data: {
        'username': 'GANTI_DENGAN_USERNAME_PETUGAS',
        'password': 'GANTI_DENGAN_PASSWORD',
        'device_name': 'Flutter Test',
      });

      setState(() => _result = 'BERHASIL:\n${response.data}');
    } catch (e) {
      setState(() => _result = 'GAGAL:\n$e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Test Koneksi API')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ElevatedButton(
              onPressed: _testLogin,
              child: const Text('Test Login ke Laravel'),
            ),
            const SizedBox(height: 16),
            Text(_result),
          ],
        ),
      ),
    );
  }
}