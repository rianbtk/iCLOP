import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import 'package:provider/provider.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:list_followers/models/followers.dart';
import 'package:list_followers/home/home.dart';
import 'package:list_followers/home/followers.dart';




Widget MyApp(BuildContext context) {
  return MaterialApp(
    home: Scaffold(
      body: Center(
      ),
    ),
  );
}

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

  group('TESTING RESULT', () {
    testWidgets('test_01_Find Title Aplication', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
        title: 'List Followers',
        theme: ThemeData(
          primarySwatch: Colors.red,
          visualDensity: VisualDensity.adaptivePlatformDensity,
        ),
      ));
      final Finder title = find.text('List Followers');
    });
    testWidgets('test_02_Find Title AppBar', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
          home: Scaffold(
            appBar: AppBar(
              title: const Text('Home'),
            ),
          )));
      final Finder title = find.text('Home');
    });
    testWidgets('test_03_find text', (tester) async{
      await tester.pumpWidget(Center());

      expect(find.text('Flutter Project'), findsNothing);

    });
    // 2 //
    testWidgets('test_04_Output Text ', (WidgetTester tester) async {
      // // memanggil pumpwidget pada MyApp//
      // await tester.pumpWidget(firstscreen());
      // // Mencari parameter textspan //
      // var textSpan = find.byType(TextSpan);
      // // nothing yang berarti dinamik/
      // expect(textSpan, findsNothing);
    });
    testWidgets('test_05_Text Content ', (WidgetTester tester) async {
      await tester.pumpWidget(
        const DefaultTextStyle(
          style: TextStyle(
            fontSize: 15,
          ),
          child: Text.rich(
            TextSpan(
              text: 'Flutter',style: TextStyle(fontSize: 25,color: Colors.blue),
              children: <TextSpan>[
                TextSpan(
                  text: 'Isi text 1',
                  style: TextStyle(fontSize: 15,color: Colors.red ),
                ),
                TextSpan(
                  text: 'Isi text 2',
                  style: TextStyle(fontSize: 15,color: Colors.blue ),
                ),
              ],
            ),
            textDirection: TextDirection.ltr,
          ),
        ),
      );

      final RichText text = tester.firstWidget(find.byType(RichText));
      expect(text, isNotNull);
      expect(text.text.style.fontSize, 15);
    });
    // 3 //
    testWidgets('test_06_Output TextRich ', (WidgetTester tester) async {
      // memanggil pumpwidget pada MyApp//
      // await tester.pumpWidget(firstscreen());
      // // Mencari parameter textRich //
      // var textRich = find.byType(TextSpan);
      // // nothing yang berarti dinamik/
      // expect(textRich, findsNothing);
    });
    testWidgets('test_07_Text Content ', (WidgetTester tester) async {
      await tester.pumpWidget(
        const DefaultTextStyle(
          style: TextStyle(
            fontSize: 15,
          ),
          child: Text.rich(
            TextSpan(
              text: 'Flutter',style: TextStyle(fontSize: 25,color: Colors.blue),
              children: <TextSpan>[
                TextSpan(
                    text: 'Daftar Materi Dasar Flutter',style: TextStyle(color: Colors.blue,fontWeight: FontWeight.bold,fontSize: 18)
                ),
                TextSpan(
                  children: const <TextSpan>[
                    TextSpan(text: 'Meliputi',
                        style: TextStyle(color: Colors.blue,fontWeight: FontWeight.bold,fontSize: 15)
                    ),
                  ],
                ),
              ],
            ),
            textDirection: TextDirection.ltr,
          ),
        ),
      );

      final RichText text = tester.firstWidget(find.byType(RichText));
      expect(text, isNotNull);
      expect(text.text.style.fontSize, 15);
    });
    // 4 //
    testWidgets('test_08_Find Title TextStyle', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
        title: 'List Followers',
        theme: ThemeData(
          primarySwatch: Colors.red,
          visualDensity: VisualDensity.adaptivePlatformDensity,
        ),
      ));
      final Finder title = find.text('List Followers');
    });
    testWidgets('test_09_Find Title AppBar', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
          home: Scaffold(
            appBar: AppBar(
              title: const Text('Home'),
            ),
          )));
      final Finder title = find.text('Home');
    });
    testWidgets('test_10_TextButton Clicked', (tester) async {
      await tester.pumpWidget(new MaterialApp(
        title: 'Home',
        theme: ThemeData(
          primarySwatch: Colors.red,
          visualDensity: VisualDensity.adaptivePlatformDensity,
        ),
        home: Scaffold(
            appBar: AppBar(
              title: const Text('Kinds of text'),
            ),
            //Text//
            body: Column(
              children: <Widget> [
                const SizedBox(height:80,width: 5000,),
                const SizedBox(height:20),
                Container(
                  padding: EdgeInsets.all(15),
                  decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(10),
                      color: Colors.blueAccent),
                  child: FlatButton(
                    onPressed: () {},
                    child: const Text('Flutter'),
                  ),
                ),
                const SizedBox(height:20),
                Container(
                  padding: EdgeInsets.all(15),
                  decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(10),
                      color: Colors.black),
                  child:FlatButton(
                    onPressed: () {},
                    child: const Text('List Followers'),
                  ),
                ),
              ],
            )
        ),
      ));

      await tester.pump();

    });
    // B1 //
    testWidgets('test_11_test daftar kosong',
            (tester) async {
          await tester.pumpWidget(createFollowersScreen());

          // verifikasi text pechloader muncul
          expect(find.text('Tambahkan Dulu'), findsOneWidget);
        });
    testWidgets('test_12_test Uji ListView', (tester) async {
      await tester.pumpWidget(createHomeScreen());
      //menguji listview tampil
      expect(find.byType(ListView), findsOneWidget);
    });
    // B2 //
    testWidgets('test_13_test tampilan button',(tester) async {
      await tester.pumpWidget(createFollowersScreen());

      addItems();
      await tester.pumpAndSettle();

      expect(find.byType(ListView), findsOneWidget);
    });
    // B3 //
    testWidgets('test_14_test IconButton', (tester) async {
      await tester.pumpWidget(createHomeScreen());

      // cek favorit.
      expect(find.byIcon(Icons.verified), findsNothing);

      // cek menambahkan icon ke favorit.
      await tester.tap(find.byIcon(Icons.verified_outlined).first);
      await tester.pumpAndSettle(Duration(seconds: 1));

      // cek tampilan pesan.
      expect(find.text('Tambah ke Followers.'), findsOneWidget);


    });
    // B4 //
    var followers = Followers();

    test('test_15_Menambah item baru', () {
      var number = 10;

      // menambah nomor di list
      followers.add(number);

      // verisikasi telah ditambahkan
      expect(followers.items.contains(number), true);
    });
    test('test_16_Menghapus item', () {
      var number = 15;

      // menambah nomor di list
      followers.add(number);

      // ferivikasi nomor ditambahkan
      expect(followers.items.contains(number), true);

      // hapus nomor dari list
      followers.remove(number);

      // ferivikasi nomor telah di hapus
      expect(followers.items.contains(number), false);
    });
    // - //
    testWidgets('test_17_Uji ListView', (tester) async {
      await tester.pumpWidget(createHomeScreen());
      //menguji listview tampil
      expect(find.byType(ListView), findsOneWidget);
    });

    testWidgets('test_18_Scrolling', (tester) async {
      await tester.pumpWidget(createHomeScreen());

      // cek item "0" tampil.
      expect(find.text('List Item 0'), findsOneWidget);

      // scrolling down.
      await tester.fling(find.byType(ListView), Offset(0, -200), 3000);
      await tester.pumpAndSettle();

      // cek item "0" hilang.
      expect(find.text('List Item 0'), findsNothing);
    });


    testWidgets('test_19_IconButton', (tester) async {
      await tester.pumpWidget(createHomeScreen());

      // cek favorit.
      expect(find.byIcon(Icons.verified), findsNothing);

      // cek menambahkan icon ke favorit.
      await tester.tap(find.byIcon(Icons.verified_outlined).first);
      await tester.pumpAndSettle(Duration(seconds: 1));

      // cek tampilan pesan.
      expect(find.text('Tambah ke Followers.'), findsOneWidget);


    });
    // - /
    testWidgets('test_20_uji pechloader muncul jika daftar kosong',
            (tester) async {
          await tester.pumpWidget(createFollowersScreen());

          // verifikasi text pechloader muncul
          expect(find.text('Tambahkan Dulu'), findsOneWidget);
        });

    testWidgets('test_21_jika ListView muncul', (tester) async {
      await tester.pumpWidget(createFollowersScreen());

      addItems();
      await tester.pumpAndSettle();

      // Verifikasi ListView tampil.
      expect(find.byType(ListView), findsOneWidget);
    });

    testWidgets('test_22_tampilan button',(tester) async {
      await tester.pumpWidget(createFollowersScreen());

      addItems();
      await tester.pumpAndSettle();

      expect(find.byType(ListView), findsOneWidget);
    });

    testWidgets('test_23_Remove Button', (tester) async {
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
