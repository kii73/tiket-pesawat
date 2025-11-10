<?php
include "../koneksi.php"; 


if (isset($_POST['simpan'])) {
    

    $nama = $mysql->real_escape_string($_POST['nama']);
    $no_penerbangan = $mysql->real_escape_string($_POST['no_penerbangan']);
    $asal = $mysql->real_escape_string($_POST['asal']);
    $tujuan = $mysql->real_escape_string($_POST['tujuan']);
    $waktu_berangkat = $mysql->real_escape_string($_POST['waktu_berangkat']);
 
    $harga = (int)$_POST['harga']; 
    $waktu_tiba = $mysql->real_escape_string($_POST['waktu_tiba']);
    $kursi_tersedia = (int)$_POST['kursi_tersedia'];
        
  
    $simpan = $mysql->query("INSERT INTO pesawat (`nama`,`no_penerbangan`, `asal`, `tujuan`,`waktu_berangkat`,`harga`, `waktu_tiba`, `kursi_tersedia`) 
                            VALUES ('$nama','$no_penerbangan','$asal','$tujuan','$waktu_berangkat','$harga','$waktu_tiba','$kursi_tersedia')");

    if ($simpan) {
        header("Location: index.php?page=data"); 
        exit;
    } else {
    
        echo "<script>alert('Gagal menyimpan data: " . $mysql->error . "');</script>";
    }
}

include "boot.php"; 
?>

<div class="container col-4 mt-5">
    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="text-center">
                    <label for=""><b>INPUT DATA</b></label>
                </div>
                <div class="mb-3">
                    <label for="input_nama" class="form-label"><i class="bi bi-tencent-qq text-primary me-2"></i>nama</label>
                    <input type="text" class="form-control" id="input_nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="input_no_penerbangan" class="form-label"><i class="bi bi-people-fill text-primary me-2"></i>no_penerbangan</label>
                    <input type="text" class="form-control" id="input_no_penerbangan" name="no_penerbangan" required>
                </div>
                <div class="mb-3">
                    <label for="input_asal" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>asal</label>
                    <input type="text" class="form-control" id="input_asal" name="asal" required>
                </div>
                <div class="mb-3">
                    <label for="input_tujuan" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>tujuan</label>
                    <input type="text" class="form-control" id="input_tujuan" name="tujuan" required>
                </div>
                <div class="mb-3">
                    <label for="input_berangkat" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>waktu_berangkat</label>
                    <input type="time" class="form-control" id="input_berangkat" name="waktu_berangkat" required>
                </div>
                <div class="mb-3">
                    <label for="input_harga" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>harga</label>
                    <input type="text" class="form-control" id="input_harga" name="harga" required>
                </div>

                <div class="mb-3">
                    <label for="input_tiba" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>waktu_tiba</label>
                    <input type="time" class="form-control" id="input_tiba" name="waktu_tiba" required>
                </div>
                <div class="mb-3">
                    <label for="input_kursi" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>kursi_tersedia</label>
                    <input type="text" class="form-control" id="input_kursi" name="kursi_tersedia" required>
                </div>


                <div class="text-center">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>