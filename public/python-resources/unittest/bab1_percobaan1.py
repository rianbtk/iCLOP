import sys
from pathlib import Path
import importlib
import subprocess
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]
pc = importlib.import_module(path_answer, ".")

# pc = importlib.import_module("jawaban.62f0a0e7b701b_1659936999")
# filename = '62f0a0e7b701b_1659936999'

# cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())], capture_output=True)
cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())], capture_output=True)

# Test File : Menampilkan kata Indonesia menggunkaan fungsi print()
@codewars_test.describe('BAB 1')
def percobaan1():
    @codewars_test.it('|Test Output Indonesia-')
    def test_indonesia():
        actual = cmd.stdout.decode().splitlines()[0]
        expected = "Indonesia"

        try:
            codewars_test.assert_equals(actual, expected, 'Error : Jawaban yang benar adalah "Indonesia"')
        except AttributeError as e:
            print(e)

if __name__ == '__main__':
    codewars_test





