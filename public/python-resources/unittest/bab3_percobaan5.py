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

# Test File : Memasukkan angka ke dalam string menggunakan method format()
@codewars_test.describe('BAB 3')
def percobaan1():
    @codewars_test.it('|Test Variabel tahun-')
    def test_var_tahun():
        try:
            codewars_test.assert_equals(2018, pc.tahun, 'Error : Jawaban yang benar, variabel tahun adalah int 2018')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Variabel teks-')
    def test_var_teks():
        try:
            codewars_test.assert_equals(pc.teks,"Saya adalah mahasiswa Polinema angkatan {}",
                            'Error : Jawaban yang benar, variabel teks adalah string "Saya adalah mahasiswa Polinema angkatan {}"')
        except AttributeError as e:
            print(e)

    @codewars_test.it('|Test Fungsi-')
    def test_fungsi():
        pengujianTahun = random.randint(0,50)
        pengujianTeks = "String {}"

        expected = pengujianTeks.format(pengujianTahun)
        try:
            actual = pc.penggabungan(pengujianTahun, pengujianTeks)
            try:
                codewars_test.assert_equals(expected, actual,
                                'Error : Jawaban yang benar adalah return teks.format(tahun)')
            except AttributeError as e:
                print(e)
        except RecursionError as e:
            codewars_test.assert_equals(expected, "", 'Error : Ditemukan error indentasi didalam fungsi')

    @codewars_test.it('|Test Output Format-')
    def test_output_format():
        expected = "Saya adalah mahasiswa Polinema angkatan 2018"

        try:
            actual = cmd.stdout.decode().splitlines()[0]
            try:
                codewars_test.assert_equals(expected, actual,
                                'Error : Jawaban output format tahun pada variabel teks yang benar adalah string "Saya adalah mahasiswa Polinema angkatan 2018"')
            except AttributeError as e:
                print(e)
        except IndexError as e:
            codewars_test.assert_equals(expected, "",
                                        'Error : Belum memanggil fungsi penggabungan')


if __name__ == '__main__':
    codewars_test
