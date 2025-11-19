<?php
include "../koneksi.php"; 


if (isset($_POST['simpan'])) {
           

           $nama = $mysql->real_escape_string($_POST['nama']);
           $no_penerbangan = $mysql->real_escape_string($_POST['no_penerbangan']);
           $tanggal_berangkat = $mysql->real_escape_string($_POST['tanggal_berangkat']);
           $asal = $mysql->real_escape_string($_POST['asal']);
           $tujuan = $mysql->real_escape_string($_POST['tujuan']);
           $waktu_berangkat = $mysql->real_escape_string($_POST['waktu_berangkat']);
       
           $harga = (int)$_POST['harga']; 
           $waktu_tiba = $mysql->real_escape_string($_POST['waktu_tiba']);
           $kursi_tersedia = (int)$_POST['kursi_tersedia'];
              
    if ($harga < 0 || $kursi_tersedia < 0) {
            echo "<script>alert('Gagal menyimpan data: Harga dan Stok Kursi tidak boleh bernilai negatif.');</script>";
    } else {
         // Jika validasi lolos, lakukan penyimpanan
                // Perhatikan penambahan '$tanggal_berangkat'
         $simpan = $mysql->query("INSERT INTO pesawat 
                        (`nama`,`no_penerbangan`, `asal`, `tujuan`,`tanggal_berangkat`,`waktu_berangkat`,`harga`, `waktu_tiba`, `kursi_tersedia`) 
                               VALUES 
                        ('$nama','$no_penerbangan','$asal','$tujuan','$tanggal_berangkat','$waktu_berangkat','$harga','$waktu_tiba','$kursi_tersedia')");

         if ($simpan) {
                  header("Location: index.php?page=data"); 
                  exit;
         } else {
         
                  echo "<script>alert('Gagal menyimpan data: " . $mysql->error . "');</script>";
         }
    }
    // --- Akhir Server-Side Validation ---
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
                         <label for="input_nama" class="form-label"><i class="bi bi-airplane-fill text-primary me-2"></i>nama</label>
                         <input type="text" class="form-control" id="input_nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                         <label for="input_no_penerbangan" class="form-label"><i class="bi bi-ticket-detailed-fill text-primary me-2"></i>no_penerbangan</label>
                         <input type="text" class="form-control" id="input_no_penerbangan" name="no_penerbangan" required>
                    </div>
                    <div class="mb-3">
                         <label for="input_asal" class="form-label"><i class="bi bi-geo-alt-fill text-primary me-2"></i>asal</label>
                         <input type="text" class="form-control" id="input_asal" name="asal" required>
                    </div>
                    <div class="mb-3">
                         <label for="input_tujuan" class="form-label"><i class="bi bi-geo-alt-fill text-primary me-2"></i>tujuan</label>
                         <input type="text" class="form-control" id="input_tujuan" name="tujuan" required>
                    </div>
                    <div class="mb-3">
                         <label for="input_tujuan" class="form-label"><i class="bi bi-geo-alt-fill text-primary me-2"></i>tanggal berangkat</label>
                         <input type="date" class="form-control" id="input_tujuan" name="tanggal_berangkat" required>
                    </div>
                    <div class="mb-3">
                         <label for="input_berangkat" class="form-label"><i class="bi bi-clock-fill text-primary me-2"></i>waktu_berangkat</label>
                         <input type="time" class="form-control" id="input_berangkat" name="waktu_berangkat" required>
                    </div>
                                <div class="mb-3">
                         <label for="input_harga" class="form-label"><i class="bi bi-currency-dollar text-primary me-2"></i>harga</label>
                         <input type="number" class="form-control" id="input_harga" name="harga" min="0" required>
                    </div>

                    <div class="mb-3">
                         <label for="input_tiba" class="form-label"><i class="bi bi-clock-fill text-primary me-2"></i>waktu_tiba</label>
                         <input type="time" class="form-control" id="input_tiba" name="waktu_tiba" required>
                    </div>
                                <div class="mb-3">
                         <label for="input_kursi" class="form-label"><i class="bi bi-armchair-fill text-primary me-2"></i>kursi_tersedia</label>
                         <input type="number" class="form-control" id="input_kursi" name="kursi_tersedia" min="0" required>
                    </div>


                    <div class="text-center">
                         <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    </div>
           </form>
              </div>
           </div>
</div>