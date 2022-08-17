import sys
from pathlib import Path
import subprocess
import importlib
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]

pc = importlib.import_module(path_answer, ".")

cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())],capture_output=True)

# Test File : While Loop
@codewars_test.describe("BAB 6")
def percobaan1():
    @codewars_test.it("|Test Variabel i-")
    def test_variabel_i():
        try:

            actual = cmd.stdout.decode().splitlines()[0]
            codewars_test.assert_equals(6, int(actual), 'Error : Jawaban yang benar, variabel i adalah integer 6')


        except IndexError as e:
            codewars_test.assert_equals(6, '', 'Error : Jawaban yang benar, variabel i adalah integer 6')

    @codewars_test.it('|Test Output-')
    def test_variabel_i():
        try:

            actual_perulangan = len(cmd.stdout.decode().splitlines())
            ekspektasi_perulangan = 6

            codewars_test.assert_equals(actual_perulangan, ekspektasi_perulangan, "Error : Looping tidak sama")

        except IndexError as e:

            codewars_test.assert_equals(6, '', 'Error : Looping tidak sama')


if __name__ == '__main__':
    codewars_test




