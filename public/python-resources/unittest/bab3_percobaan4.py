import sys
from pathlib import Path
import subprocess
import importlib
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]
pc = importlib.import_module(path_answer, ".")

cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())],capture_output=True)

# Test File : Menggantikan string dengan string lain menggunakan method replace()
@codewars_test.describe('BAB 3')
def percobaan4():
    @codewars_test.it('|Test Variabel tulis-')
    def test_var_tulis():
        try:
            codewars_test.assert_equals("Belajar PHP", pc.tulis,
                                        'Error : Jawaban yang benar, variabel tulis adalah string "Belajar PHP"')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Fungsi-')
    def test_fungsi():
        text = "String PHP"
        pengujianTulis = "Python"

        expected = text.replace("PHP", pengujianTulis)
        try:
            actual = pc.pengganti(text)
            try:
                codewars_test.assert_equals(expected, actual,
                                            'Error : Jawaban yang benar adalah return tulis.replace("PHP", "Python"')
            except AttributeError as e:
                print(e)
        except RecursionError as e:
            codewars_test.assert_equals(expected, "", 'Error : Ditemukan error indentasi didalam fungsi')

    @codewars_test.it('|Test Output-')
    def test_output():
        expected = "Belajar Python"

        try:
            actual = cmd.stdout.decode().splitlines()[0]
            try:
                codewars_test.assert_equals(expected, actual,
                                            'Error : Jawaban output replace PHP menjadi Python pada variabel tulis yang benar adalah string "Belajar Python"')
            except AttributeError as e:
                print(e)

        except IndexError as e:
            codewars_test.assert_equals(expected, "",
                                        'Error : Belum memanggil fungsi pengganti')


if __name__ == '__main__':
    codewars_test
