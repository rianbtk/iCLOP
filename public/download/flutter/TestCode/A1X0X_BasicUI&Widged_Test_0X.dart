import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

Widget MyApp(BuildContext context) {
  return MaterialApp(
    home: Scaffold(
      body: Center(
      ),
    ),
  );
}

void main() {

  group('Kinds of text', () {
    testWidgets('Find Title Aplication', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
        title: 'List Followers',
        theme: ThemeData(
          primarySwatch: Colors.red,
          visualDensity: VisualDensity.adaptivePlatformDensity,
        ),
      ));
      final Finder title = find.text('List Followers');
    });
    testWidgets('Find Title AppBar', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
          home: Scaffold(
            appBar: AppBar(
              title: const Text('Home'),
            ),
          )));
      final Finder title = find.text('Home');
    });
    testWidgets('find text', (tester) async{
      await tester.pumpWidget(Center());

      expect(find.text('Flutter Project'), findsNothing);

    });
  });
}
