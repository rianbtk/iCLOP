import 'package:test/test.dart';
import 'package:list_followers/models/followers.dart';

void main() {
  group('Test Provider', () {

    var followers = Followers();

    test('Menambah item baru', () {
      var number = 10;

      // menambah nomor di list
      followers.add(number);

      // verisikasi telah ditambahkan
      expect(followers.items.contains(number), true);
    });
    test('Menghapus item', () {
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

  });
}
