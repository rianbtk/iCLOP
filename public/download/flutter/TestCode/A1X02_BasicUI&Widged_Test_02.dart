import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:list_followers/home/flutter.dart';

void main() {
  group('Test Output Text', () {
    testWidgets('Output TextRich ', (WidgetTester tester) async {
      // memanggil pumpwidget pada MyApp//
      await tester.pumpWidget(MyApp());
      // Mencari parameter textRich //
      var textRich = find.byType(TextSpan);
      // nothing yang berarti dinamik/
      expect(textRich, findsNothing);
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
      expect(text.text.style?.fontSize, 15);
    });
  });
}
