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
    testWidgets('test tampilan button',(tester) async {
      await tester.pumpWidget(createFollowersScreen());

      addItems();
      await tester.pumpAndSettle();

      expect(find.byType(ListView), findsOneWidget);
    });

  });
}
