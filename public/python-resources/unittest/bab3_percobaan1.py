import random
import sys
from pathlib import Path
import subprocess
import importlib
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]

# path_answer = "jawaban.62ecaf4851728_1659678536"
# filename = "62ecaf4851728_1659678536"

pc = importlib.import_module(path_answer, ".")

cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())],capture_output=True)

# Test File : Menghitung panjang string menggunakan fungsi len()
@codewars_test.describe('BAB 3')
def percobaan1():
    @codewars_test.it('|Test Variabel tulis-')
    def test_var_tulis():
        try:
            codewars_test.assert_equals("Belajar Python", pc.tulis,
                                        'Error : Jawaban yang benar, variabel tulis adalah string "Belajar Python"')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Fungsi-')
    def test_fungsi():
        pengujianTulis = "String"
        expected = len(pengujianTulis)
        try:
            actual = pc.panjang(pengujianTulis)
            try:
                codewars_test.assert_equals(expected, actual,
                                            'Error : Jawaban yang benar adalah return len(tulis)')
            except AttributeError as e:
                print(e)
        except RecursionError as e:
            codewars_test.assert_equals(expected, "", 'Error : Ditemukan error indentasi didalam fungsi')

    @codewars_test.it('|Test Output-')
    def test_output():
        expected = "14"
        try:
            actual = cmd.stdout.decode().splitlines()[0]
            try:
                codewars_test.assert_equals(expected, actual,
                                            'Error : Jawaban output panjang string variabel tulis atau len(tulis) yang benar adalah 14')
            except AttributeError as e:
                print(e)
        except IndexError as e:
            codewars_test.assert_equals(expected, "",
                                        'Error : Belum memanggil fungsi panjang')


if __name__ == '__main__':
    codewars_test
