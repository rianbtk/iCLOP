import random
import sys
from pathlib import Path
import subprocess
import importlib
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]
pc = importlib.import_module(path_answer, ".")

cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())],capture_output=True)

# Test File : Menentukan tipe data variabel menggunakan casting
@codewars_test.describe('BAB 2')
def percobaan2():
    @codewars_test.it('|Test Variabel umur-')
    def test_var_umur():
        try:
            codewars_test.assert_equals(23, pc.umur, 'Error : Jawaban yang benar, variabel umur adalah integer 23')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Fungsi Variabel umur-')
    def test_fungsi_umur():
        pengujianUmur = random.randint(0,50)
        expected = str(pengujianUmur)

        try:
            actual = pc.konversi(pengujianUmur)

            try:
                codewars_test.assert_equals(expected, actual, 'Error : Jawaban yang benar adalah return str(umur)')
            except AttributeError as e:
                print(e)

        except RecursionError as e:
            codewars_test.assert_equals(expected, "", 'Error : Ditemukan error indentasi didalam fungsi')


    @codewars_test.it('|Test Output Variabel umur-')
    def test_output_umur():
        expected = "<class 'str'>"

        try:
            actual = cmd.stdout.decode().splitlines()[0]

            try:
                codewars_test.assert_equals(expected, actual,
                                            "Error : Jawaban output yang benar tipe data umur adalah class str")
            except AttributeError as e:
                print(e)

        except IndexError as e:
            codewars_test.assert_equals(expected, "",
                                        'Error : Belum memanggil fungsi konversi')


if __name__ == '__main__':
    codewars_test
