import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  group('Button Test', () {
    testWidgets('Find Title TextStyle', (WidgetTester tester) async {
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
    testWidgets('TextButton Clicked', (tester) async {
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
  });
}