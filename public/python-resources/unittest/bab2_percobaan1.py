import sys
from pathlib import Path
import subprocess
import importlib
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]
pc = importlib.import_module(path_answer, ".")

cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())],capture_output=True)

# Test File : Membuat dan menampilkan variabel
@codewars_test.describe('BAB 2')
def percobaan1():
    @codewars_test.it('|Test Variabel jumlah-')
    def test_var_jumlah():
        try:
            codewars_test.assert_equals(10, pc.jumlah, 'Error : Jawaban yang benar, variabel jumlah adalah integer 10')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Variabel pesanan-')
    def test_var_pesanan():
        try:
            codewars_test.assert_equals("Komputer", pc.pesanan, 'Error : Jawaban yang benar, variabel pesanan adalah string "Komputer"')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Output Variabel jumlah-')
    def test_output_jumlah():
        actual = cmd.stdout.decode().splitlines()[0]
        expected = int(10)
        try:
            codewars_test.assert_equals(expected, int(actual), 'Error : Jawaban output yang benar, jumlah adalah int 10')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Output Variabel pesanan-')
    def test_output_pesanan():
        actual = cmd.stdout.decode().splitlines()[1]
        expected = "Komputer"
        try:
            codewars_test.assert_equals(expected, actual, 'Error : Jawaban output yang benar, pesanan adalah string "Komputer"')
        except AttributeError as e:
            print(e)

if __name__ == '__main__':
    codewars_test