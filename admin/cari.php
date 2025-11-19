<?php 
// Pastikan file boot.php (untuk Bootstrap/styling) dan koneksi.php sudah tersedia
include "boot.php"; 
include "../koneksi.php";

// 1. Ambil dan sanitasi data pencarian (Logika ini dipertahankan, tapi tanpa input, $cari akan kosong)
$cari = isset($_REQUEST['cari']) ? $mysql->real_escape_string($_REQUEST['cari']) : '';
$searchTerm = "%" . $cari . "%";

// 2. Query SQL: Karena $searchTerm adalah "%%", maka akan mencari dan menampilkan SEMUA data
$sql = "SELECT * FROM pesawat 
        WHERE nama LIKE '$searchTerm' 
        OR no_penerbangan LIKE '$searchTerm' 
        OR asal LIKE '$searchTerm' 
        OR tujuan LIKE '$searchTerm' 
        OR waktu_berangkat LIKE '$searchTerm' 
        OR harga LIKE '$searchTerm' 
        OR waktu_tiba LIKE '$searchTerm' 
        OR kursi_tersedia LIKE '$searchTerm'";

$tampil = $mysql->query($sql);
?>

<div class="container-fluid mt-3">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Nama</th>
                <th scope="col">No. Penerbangan</th>
                <th scope="col">Asal</th>
                <th scope="col">Tujuan</th>
                <th scope="col">Waktu Berangkat</th>
                <th scope="col">Harga</th>
                <th scope="col">Waktu Tiba</th>
                <th scope="col">Kursi Tersedia</th> 
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            $no = 0;
            // Memulai perulangan data
            if ($tampil && $tampil->num_rows > 0) {
                foreach ($tampil as $data) {
                    $no++; 
            ?>
            <tr>
                <th scope="row"><?=$no;?></th>
                <td><?=$data['nama']?></td>
                <td><?=$data['no_penerbangan']?></td>
                <td><?=$data['asal']?></td>
                <td><?=$data['tujuan']?></td>
                <td><?=$data['waktu_berangkat']?></td>
                <td><?=$data['harga']?></td>
                <td><?=$data['waktu_tiba']?></td>
                <td><?=$data['kursi_tersedia']?></td>
                <td>
                    <a href="delete.php?id=<?=$data['id'];?>" onClick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                        <i class="bi bi-trash3-fill text-danger"></i>
                    </a>
                    
                    <a href="update.php?id=<?=$data['id'];?>">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                </td>
            </tr>
            <?php 
                }
            } else {
                // Tampilkan pesan jika data tidak ditemukan atau tabel kosong
                $colspan_count = 10; 
                echo '<tr><td colspan="' . $colspan_count . '" class="text-center">Data tidak ditemukan.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>