import sys
from pathlib import Path
import subprocess
import importlib
import codewars_test

path_answer = sys.argv[1]
filename = sys.argv[2]

pc = importlib.import_module(path_answer, ".")

cmd = subprocess.run([sys.executable, f"%s/jawaban/{filename}.py" % (Path(__file__).parent.absolute())],capture_output=True)

# Test File : Menampilkan banyak variabel menggunakan operator +
@codewars_test.describe('BAB 2')
def percobaan4():
    @codewars_test.it('|Test Variabel nama-')
    def test_var_nama():
        try:
            codewars_test.assert_equals("Qorinda ", pc.nama,
                                        'Error : Jawaban yang benar, variabel nama adalah string "Qorinda " (Terdapat spasi sesudah Qorinda)')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Variabel lengkap-')
    def test_var_lengkap():
        try:
            codewars_test.assert_equals("Yulvarisma", pc.lengkap,
                                        'Error : Jawaban yang benar, variabel lengkap adalah string "Yulvarisma"')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Fungsi-')
    def test_fungsi():
        pengujianNama = "String A"
        pengujianLengkap = "String B"

        expected = pengujianNama + pengujianLengkap


        # handle error identation
        try:
            actual = pc.hasil(pengujianNama, pengujianLengkap)

            try:
                codewars_test.assert_equals(expected, actual, 'Error : Jawaban yang benar adalah return nama+lengkap')
            except AttributeError as e:
                print(e)

        except RecursionError as e:
            codewars_test.assert_equals(expected, "", 'Error : Ditemukan error indentasi didalam fungsi')


    @codewars_test.it('|Test Output-')
    def test_output():

        expected = "Qorinda Yulvarisma"
        try:
            actual = cmd.stdout.decode().splitlines()[0]
            try:
                codewars_test.assert_equals(expected, actual,
                                            'Error : Jawaban output yang benar adalah "Qorinda Yulvarisma"')
            except AttributeError as e:
                print(e)

        except IndexError as e:
            codewars_test.assert_equals(expected, "",
                                        'Error : Belum memanggil fungsi hasil')





if __name__ == '__main__':
    codewars_test
