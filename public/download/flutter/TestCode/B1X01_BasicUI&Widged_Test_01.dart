import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:list_followers/models/followers.dart';
import 'package:list_followers/home/followers.dart';
import 'package:list_followers/home/home.dart';

Followers followersList;

Widget createFollowersScreen() => ChangeNotifierProvider<Followers>(
      create: (context) {
        followersList = Followers();
        return followersList;
      },
      child: MaterialApp(
        home: FollowersPage(),
      ),
    );

Widget createHomeScreen() => ChangeNotifierProvider<Followers>(
      create: (context) => Followers(),
      child: MaterialApp(
        home: HomePages(),
      ),
    );

void main() {
  group('test widget halaman Followers', () {
    testWidgets('test daftar kosong',
        (tester) async {
      await tester.pumpWidget(createFollowersScreen());

      // verifikasi text pechloader muncul
      expect(find.text('Tambahkan Dulu'), findsOneWidget);
    });
    testWidgets('test Uji ListView', (tester) async {
      await tester.pumpWidget(createHomeScreen());
      //menguji listview tampil
      expect(find.byType(ListView), findsOneWidget);
    });
  });
}
