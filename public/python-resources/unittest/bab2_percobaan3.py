import sys
from pathlib import Path
import subprocess
import importlib
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]
pc = importlib.import_module(path_answer, ".")

cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())],capture_output=True)

# Test File : Mengindentifikasi tipe dari sebuah variabel menggunakan type()
@codewars_test.describe('BAB 2')
def percobaan3():
    @codewars_test.it('|Test Variabel buah-')
    def test_var_buah():
        try:
            codewars_test.assert_equals("Mangga", pc.buah, 'Error : Jawaban yang benar, variabel buah adalah string "Mangga"')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Variabel jumlah-')
    def test_var_jumlah():
        try:
            codewars_test.assert_equals(int(6), pc.jumlah, 'Error : Jawaban yang benar, variabel jumlah adalah integer 6')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Variabel berat-')
    def test_var_berat():
        try:
            codewars_test.assert_equals(float(1.6), pc.berat, 'Error : Jawaban yang benar, variabel berat adalah float 1.6')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Output Variabel buah-')
    def test_output_buah():
        actual = cmd.stdout.decode().splitlines()[0]
        expected = "<class 'str'>"
        try:
            codewars_test.assert_equals(expected, actual, 'Error : Jawaban output yang benar type variabel buah adalah class str')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Output Variabel jumlah-')
    def test_output_jumlah():
        actual = cmd.stdout.decode().splitlines()[1]
        expected = "<class 'int'>"
        try:
            codewars_test.assert_equals(expected, actual, 'Error : Jawaban output yang benar type variabel jumlah adalah class int')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Output Variabel berat-')
    def test_output_berat():
        actual = cmd.stdout.decode().splitlines()[2]
        expected = "<class 'float'>"
        try:
            codewars_test.assert_equals(expected, actual, 'Error : Jawaban output yang benar type variabel berat adalah class float')
        except AttributeError as e:
            print(e)

if __name__ == '__main__':
    codewars_test




