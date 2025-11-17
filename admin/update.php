<?php
// 1. PHP Form Handling dipindahkan ke atas (Server-Side Logic)
if (isset($_POST['simpan'])) {
    include "../koneksi.php";

    // Ambil data dari POST
    $id = $_POST['id']; // Ambil ID dari hidden input
    $nama = $_POST['nama'];
    $no_penerbangan = $_POST['no_penerbangan'];
    $asal = $_POST['asal'];
    $tujuan = $_POST['tujuan'];
    $waktu_berangkat = $_POST['waktu_berangkat'];
    $harga = (int)$_POST['harga']; 
    $waktu_tiba = $_POST['waktu_tiba'];
    $kursi_tersedia = (int)$_POST['kursi_tersedia'];
    
    // Server-Side Validation: Harga dan Stok Kursi tidak boleh minus
    if ($harga < 0 || $kursi_tersedia < 0) {
        echo "<script>alert('Gagal menyimpan data: Harga dan Stok Kursi tidak boleh bernilai negatif.');</script>";
        // Penting: Tidak perlu exit di sini karena kita ingin form tetap ditampilkan jika error
    } else {
        // Lakukan UPDATE query
        $simpan = $mysql->query("UPDATE pesawat SET 
            nama='$nama',
            no_penerbangan='$no_penerbangan',
            asal='$asal',
            tujuan='$tujuan',
            waktu_berangkat='$waktu_berangkat',
            harga='$harga',
            waktu_tiba='$waktu_tiba',
            kursi_tersedia='$kursi_tersedia' 
            WHERE id='$id'");
        
        if ($simpan) {
            // REDIRECTION SUKSES dijamin karena kode ini ada di atas
            header("Location: index.php?page=data"); 
            exit; 
        } else {
            echo "<script>alert('Gagal menyimpan data: " . $mysql->error . "');</script>";
        }
    }
}

// 2. Client-Side Logic dan persiapan data form
include "boot.php";
include "../koneksi.php";

$id = $_GET['id'];
$tampil = $mysql->query("select*from pesawat where id='$id'");
$data = $tampil->fetch_array();
?>

<div class="container col-4 mt-5">
       <div class="card p-4">
            <div class="card-body ">
                   <form action="" method="post">
                        <div class="text-center">
                               <label for=""><b>UPDATE DATA</b></label>
                        </div>
                        
                        <div class="mb-3">
                               <label for="nama" class="form-label"><i class="bi bi-airplane-fill text-primary me-2"></i>nama</label>
                               <input type="text" class="form-control" id="nama" value="<?= $data["nama"] ?>" name="nama" required>
                        </div>
                        <div class="mb-3">
                               <label for="no_penerbangan" class="form-label"><i class="bi bi-ticket-detailed-fill text-primary me-2"></i>no_penerbangan</label>
                               <input type="text" class="form-control" id="no_penerbangan" value="<?= $data["no_penerbangan"] ?>" name="no_penerbangan" required>
                        </div>
                        
                        <div class="mb-3">
                               <label for="asal" class="form-label"><i class="bi bi-geo-alt-fill text-primary me-2"></i>asal</label>
                               <input type="text" class="form-control" id="asal" value="<?= $data["asal"] ?>" name="asal" required>
                               <input type="hidden" name="id" value="<?= $data['id'] ?>">                         </div>
                        <div class="mb-3">
                               <label for="tujuan" class="form-label"><i class="bi bi-geo-alt-fill text-primary me-2"></i>tujuan</label>
                               <input type="text" class="form-control" id="tujuan" value="<?= $data["tujuan"] ?>" name="tujuan" required>
                        </div>
                        <div class="mb-3">
                               <label for="waktu_berangkat" class="form-label"><i class="bi bi-clock-fill text-primary me-2"></i>waktu_berangkat</label>
                               <input type="time" class="form-control" id="waktu_berangkat" value="<?= $data["waktu_berangkat"] ?>" name="waktu_berangkat" required>
                        </div>
                        <div class="mb-3">
                               <label for="harga" class="form-label"><i class="bi bi-currency-dollar text-primary me-2"></i>harga</label>
                               <input type="number" class="form-control" id="harga" value="<?= $data["harga"] ?>" name="harga" min="0" required>
                        </div>
                        <div class="mb-3">
                               <label for="waktu_tiba" class="form-label"><i class="bi bi-clock-fill text-primary me-2"></i>waktu_tiba</label>
                               <input type="time" class="form-control" id="waktu_tiba" value="<?= $data["waktu_tiba"] ?>" name="waktu_tiba" required>
                        </div>
                        
                        <div class="mb-3">
                               <label for="kursi_tersedia" class="form-label"><i class="bi bi-armchair-fill text-primary me-2"></i>Stok Kursi Tersedia</label>
                               <input type="number" class="form-control" id="kursi_tersedia" value="<?= $data["kursi_tersedia"] ?>" name="kursi_tersedia" min="0" required>
                        </div>
                       
                        <div class="text-center">
                               <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                        </div>
                   </form>
            </div>
       </div>
</div>