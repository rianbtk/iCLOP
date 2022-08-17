import sys
import random
from pathlib import Path
import subprocess
import importlib
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]
pc = importlib.import_module(path_answer, ".")

cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())],capture_output=True)

@codewars_test.describe("BAB 5")
def percobaan1():
    @codewars_test.it("|Test Variabel angka1-")
    def test_variabel_angka1():
        try:
            codewars_test.assert_equals(150, pc.angka1, 'Error : Jawaban yang benar, variabel angka1 adalah integer 150')
        except AttributeError as e:
            print(e)

    @codewars_test.it("|Test Variabel angka2-")
    def test_variabel_angka2():
        try:
            codewars_test.assert_equals(40, pc.angka2, 'Error : Jawaban yang benar, variabel angka2 adalah integer 40')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Fungsi-')
    def test_fungsi():
        expected = 2
        try:
            # angka 1 < angka 2
            pengujianAngkaA1 = random.randint(0, 50)
            pengujianAngkaB1 = random.randint(100, 200)

            # angka 1 > angka 2
            pengujianAngkaA2 = random.randint(100, 200)
            pengujianAngkaB2 = random.randint(0, 50)

            # output
            hasilPengujianIf = "angka 2 lebih besar dari angka 1"
            hasilPengujianElse = "angka 2 tidak lebih besar dari angka 1"

            # kondisi
            actual = 0


            # pengujian kondisi if
            testIf = pc.pengecekan(pengujianAngkaA1, pengujianAngkaB1)
            if testIf == hasilPengujianIf:
                actual += 1

            # pengujian kondisi else
            testElse = pc.pengecekan(pengujianAngkaA2, pengujianAngkaB2)
            if testElse == hasilPengujianElse:
                actual += 1



            try:
                codewars_test.assert_equals(expected, actual, 'Error : Pembuatan kondisi if dan else belum tepat"')
            except AttributeError as e:
                print(e)
        except RecursionError as e:
            codewars_test.assert_equals(expected, "", 'Error : Ditemukan error indentasi didalam fungsi')

    @codewars_test.it('|Test Output-')
    def test_output():
        expected = "angka 2 tidak lebih besar dari angka 1"
        try:
            actual = cmd.stdout.decode().splitlines()[0]
            try:
                codewars_test.assert_equals(expected, actual, 'Error : Jawaban output yang benar adalah "angka 2 tidak lebih besar dari angka 1"')
            except AttributeError as e:
                print(e)
        except IndexError as e:
            codewars_test.assert_equals(expected, "",
                                        'Error : Belum memanggil fungsi pengecekan')


if __name__ == '__main__':
    codewars_test




