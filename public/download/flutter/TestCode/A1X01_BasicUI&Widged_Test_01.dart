import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:list_followers/home/flutter.dart';

void main() {
  group('Test Output Text', () {
    testWidgets('Output Text ', (WidgetTester tester) async {
      // memanggil pumpwidget pada MyApp//
      await tester.pumpWidget(MyApp());
      // Mencari parameter textspan //
      var textSpan = find.byType(TextSpan);
      // nothing yang berarti dinamik/
      expect(textSpan, findsNothing);
    });
    testWidgets('Text Content ', (WidgetTester tester) async {
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
      expect(text.text.style?.fontSize, 15);
    });
  });
}
