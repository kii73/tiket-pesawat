<?php


include "boot.php";
include "../koneksi.php"; 

 
$remember_token = $_COOKIE["remember_token"] ?? '';


$user_result = $mysql->query("SELECT role FROM users WHERE remember_token='$remember_token'");
$user_data = $user_result->fetch_assoc();

if (!$user_data || $user_data['role'] !== 'admin') {
    header("Location: ../index.php"); 
    exit;
}


if (isset($_GET["id"])) {
    $id = $_GET["id"];
    
   
    $id_safe = $mysql->real_escape_string($id);
    $mysql->query("UPDATE kode SET status = 'disetujui' WHERE id = '$id_safe'");

    
    header("Location: laporan.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pesanan - Admin</title>
    <style>
        
        .header-color {
            color: #008080 !important; 
        }
        .table-custom-header {
            background-color: #581845 !important; 
            color: white;
        }
        .btn-custom-approve {
            background-color: #008080; 
            border-color: #008080;
        }
        .btn-custom-approve:hover {
            background-color: #006666;
            border-color: #006666;
        }
    </style>
</head>
<body>

<div class="container-fluid mt-3">
    <h3 class="mb-4 header-color">Data Pesanan Tiket Pesawat</h3>
    
    <table class="table table-striped table-bordered">
        <thead class="table-custom-header">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Kode Booking</th>
                <th scope="col">Nama Pesawat</th>
                <th scope="col">No. Penerbangan</th>
                <th scope="col">Kelas</th>
                <th scope="col">Asal</th>
                <th scope="col">Tujuan</th>
                <th scope="col">Harga Satuan</th>
                <th scope="col">Status</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            
           
            $tampil = $mysql->query("SELECT 
                                        k.id, k.kode, k.status, 
                                        p.nama, p.no_penerbangan, p.kelas, p.asal, p.tujuan, p.harga 
                                    FROM kode k 
                                    LEFT JOIN pesawat p ON k.id_pesawat = p.id 
                                    ORDER BY k.id DESC");
            $no = 0;
            
            while ($data = $tampil->fetch_assoc()) {
                $no++;
                
                
                $status_class = $data["status"] == "menunggu" ? "text-danger fw-bold" : "text-success fw-bold";
            ?>
                <tr>
                    <th scope="row"><?= $no; ?></th>
                    <td class="fw-bold"><?= htmlspecialchars($data['kode']) ?></td>
                    <td><?= htmlspecialchars($data['nama']) ?></td>
                    <td><?= htmlspecialchars($data['no_penerbangan']) ?></td>
                    <td><?= htmlspecialchars($data['kelas']) ?></td>
                    <td><?= htmlspecialchars($data['asal']) ?></td>
                    <td><?= htmlspecialchars($data['tujuan']) ?></td>
                    <td>Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                    <td class="<?= $status_class ?>"><?= ucfirst(htmlspecialchars($data['status'])) ?></td>

                    <td>
                        <?php
                      
                        if ($data["status"] == "menunggu") {
                        ?>
                            <a href="laporan.php?id=<?= $data['id']; ?>" class="btn btn-sm btn-custom-approve text-white" onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI pesanan ini?');">
                                Setujui
                            </a>
                        <?php
                        } else {
                        ?>
                            <span class="badge bg-success">Selesai</span>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>