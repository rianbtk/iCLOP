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
    testWidgets('test Scrolling', (tester) async {
      await tester.pumpWidget(createHomeScreen());

      // cek item "0" tampil.
      expect(find.text('List Item 0'), findsOneWidget);

      // scrolling down.
      await tester.fling(find.byType(ListView), Offset(0, -200), 3000);
      await tester.pumpAndSettle();

      // cek item "0" hilang.
      expect(find.text('List Item 0'), findsNothing);
    });
    testWidgets('test Remove Button', (tester) async {
      await tester.pumpWidget(createFollowersScreen());

      addItems();
      await tester.pumpAndSettle();

      // get jumlah total item
      var totalItems = tester.widgetList(find.byIcon(Icons.close)).length;

      // hapus satu item.
      await tester.tap(find.byIcon(Icons.close).first);
      await tester.pumpAndSettle();

      // cek item dihapus
      expect(tester.widgetList(find.byIcon(Icons.close)).length,
          lessThan(totalItems));

      // Verify if the appropriate message is shown.
      expect(find.text('Hapus Followers.'), findsOneWidget);
    });
  });
}
