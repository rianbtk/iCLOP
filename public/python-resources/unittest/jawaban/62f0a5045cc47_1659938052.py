# Tuliskan variabel dibawah ini
angka1 = 70
angka2 = 70

def pengecekanelif(angka1, angka2):
    #Tuliskan kode program dibawah ini
    if angka2>angka1:
        return "angka 2 lebih besar dari angka 1"
    elif angka2 == angka1:
        return "angka 1 dan angka 2 sama"
    else:
        return "angka 2 tidak lebih besar dari angka 1"
        
print(pengecekanelif(angka1, angka2))