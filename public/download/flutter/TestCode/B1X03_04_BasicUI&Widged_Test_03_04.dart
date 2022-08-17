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

void addItems() {
  for (var i = 0; i < 5; i++) {
    followersList.add(i);
  }
}
void main() {
  group('Test widget halaman Followers & Home ', () {
    testWidgets('test IconButton', (tester) async {
      await tester.pumpWidget(createHomeScreen());

      // cek favorit.
      expect(find.byIcon(Icons.verified), findsNothing);

      // cek menambahkan icon ke favorit.
      await tester.tap(find.byIcon(Icons.verified_outlined).first);
      await tester.pumpAndSettle(Duration(seconds: 1));

      // cek tampilan pesan.
      expect(find.text('Tambah ke Followers.'), findsOneWidget);


    });
  });
}
