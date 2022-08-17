import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  group('Text Style', () {
    testWidgets('Find Title TextStyle', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
        title: 'Home',
        theme: ThemeData(
          primarySwatch: Colors.red,
          visualDensity: VisualDensity.adaptivePlatformDensity,
        ),
      ));
      final Finder title = find.text('Home');
    });
    testWidgets('Find Title AppBar', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
          home: Scaffold(
        appBar: AppBar(
          title: const Text('List Followers'),
        ),
      )));
      final Finder title = find.text('List Followers');
    });
    testWidgets('Test textStyle', (WidgetTester tester) async {
      await tester.pumpWidget(MaterialApp(
        home: Scaffold(
            body: Column(
          children: <Widget>[
            Container(
                padding: EdgeInsets.all(25),
                decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10), color: Colors.red),
                child: Text(
                  'Hello World!',
                  style: TextStyle(color: Colors.white, fontSize: 20.0),
                )),
          ],
        )),
      ));
      
      expect(find.byElementType(BoxDecoration), findsNothing);
    });
  });
}