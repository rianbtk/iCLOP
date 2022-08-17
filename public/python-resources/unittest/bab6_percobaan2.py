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
def percobaan2():
    @codewars_test.it("|Test Array-")
    def test_variabel_a():
        try:

            actual_list = pc.sayur

            ekspektasi_list = ["sawi", "wortel", "buncis"]
            array_user = []
            array_user_tidakada = []

            for nilai in actual_list:

                if nilai in ekspektasi_list:
                    array_user.append(nilai)

                else:
                    array_user_tidakada.append(nilai)



            splitArray = ", ". join(array_user_tidakada)
            codewars_test.assert_equals(len(ekspektasi_list), len(array_user), f"Error : Array tidak sama, nilai array {splitArray} tidak diketahui")

        except AttributeError as e:
            print(e)

    @codewars_test.it("|Test jumlah perulangan-")
    def test_variabel_b():
        try:

            actual_list = pc.sayur
            ekspektasi_list = ["sawi", "wortel", "buncis"]

            totalPerulangan = 0

            for nilai in actual_list:

                totalPerulangan += 1

            codewars_test.assert_equals(len(ekspektasi_list), totalPerulangan, f"Error : Perulangan hanya dilakukan {totalPerulangan}")

        except AttributeError as e:
            print(e)

if __name__ == '__main__':
    codewars_test




