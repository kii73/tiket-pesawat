<?php include "boot.php"; ?>
<div class="container-fluid mt-3">
    <h3 class="mb-4 text-primary">Data Pesawat</h3>
    
    <table class="table table-striped table-bordered mt-4">
    
        <thead>
            <tr>
                <th scope="col">no</th>
                <th scope="col">nama</th>
                <th scope="col">no_penerbangan</th>
                <th scope="col">asal</th>
                <th scope="col">tujuan</th>
                <th scope="col">tanggal berangkat</th>
                <th scope="col">waktu_berangkat</th>
                <th scope="col">harga</th>
                <th scope="col">waktu_tiba</th>
                <th scope="col">kursi_tersedia</th>            
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            // Asumsi file koneksi.php berisi koneksi ke database dengan variabel $mysql
            include "../koneksi.php";
            
            // Mengambil semua data dari tabel pesawat
            // PENTING: Gunakan prepared statement untuk query dinamis di aplikasi nyata!
            $tampil = $mysql->query("SELECT * FROM pesawat");
            
            $no = 0; // Inisialisasi nomor
            
            // Loop untuk menampilkan data
            foreach ($tampil as $data) {
                $no++; // Increment nomor

                // Mengubah format tanggal dari YYYY-MM-DD menjadi DD/MM/YYYY
                $tanggal_formatted = date("d/m/Y", strtotime($data['tanggal_berangkat']));
            ?>
                <tr>
                    <th scope="row"><?= $no; ?></th>
                    <td><?= htmlspecialchars($data['nama']) ?></td>
                    <td><?= htmlspecialchars($data['no_penerbangan']) ?></td>
                    <td><?= htmlspecialchars($data['asal']) ?></td>
                    <td><?= htmlspecialchars($data['tujuan']) ?></td>
                    
                    <td><?= $tanggal_formatted ?></td>
                    
                    <td><?= htmlspecialchars($data['waktu_berangkat']) ?></td>
                    
                    <td>Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                    
                    <td><?= htmlspecialchars($data['waktu_tiba']) ?></td>
                    <td><?= htmlspecialchars($data['kursi_tersedia']) ?></td>            
                    <td>
                        <a href="delete.php?id=<?= htmlspecialchars($data['id']); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                            <i class="bi bi-trash3-fill text-danger"></i>
                        </a>

                        <a href="update.php?id=<?= htmlspecialchars($data['id']); ?>">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>