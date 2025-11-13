<?php
include "boot.php";
include "../koneksi.php";

// Set zona waktu agar perhitungan waktu akurat
date_default_timezone_set('Asia/Jakarta'); // Ganti sesuai zona waktu server/lokasi Anda

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    ?>
    <script>
        window.location.href = "../index.php"
    </script>
    <?php
    ob_end_flush();
    exit;
}

if (!isset($mysql) || !$mysql instanceof mysqli) {
    ?>
    <script>
        window.location.href = "../index.php"
    </script>
    <?php
    ob_end_flush();
    exit;
}

// --- FUNGSI UNTUK MENGHITUNG WAKTU KADALUARSA ---
function hitungWaktuSisa($tanggalKadaluarsa) {
    $now = new DateTime();
    $expiry = new DateTime($tanggalKadaluarsa);
    $interval = $now->diff($expiry);
    
    if ($now > $expiry) {
        return "Kadaluarsa";
    }

    if ($interval->days > 0) {
        return $interval->days . " hari lagi";
    } elseif ($interval->h > 0) {
        return $interval->h . " jam lagi";
    } elseif ($interval->i > 0) {
        return $interval->i . " menit lagi";
    } else {
        return $interval->s . " detik lagi";
    }
}

// --- LOGIKA HAPUS PESANAN KADALUARSA ---
// Batas waktu kadaluarsa (misalnya 48 jam atau 2 hari) dalam detik
$BATAS_KADALUARSA_DETIK = 48 * 3600; 

// Hitung waktu 48 jam yang lalu
$waktuKadaluarsa = date('Y-m-d H:i:s', time() - $BATAS_KADALUARSA_DETIK);

// Hapus pesanan yang statusnya 'menunggu' dan sudah melewati batas kadaluarsa
$stmt_delete = $mysql->prepare("DELETE FROM bookings WHERE status = 'menunggu' AND tanggal_beli < ?");
$stmt_delete->bind_param("s", $waktuKadaluarsa);
$stmt_delete->execute();
$stmt_delete->close();
// Logika penghapusan selesai. Pesanan yang kadaluarsa sudah dihapus sebelum ditampilkan.


// --- LOGIKA PERSETUJUAN (UPDATE STATUS) ---
if (isset($_GET["id"])) {
    $id = $_GET["id"];

   $stmt_update = $mysql->prepare("UPDATE bookings SET status = 'sudah bayar' WHERE id = ?");

    $stmt_update->bind_param("i", $id);
    $stmt_update->execute();
    $stmt_update->close();

    ?>
    <script>
        window.location.href = "index.php?page=pesanan"
    </script>
    <?php
    ob_end_flush();
    exit;
}


// --- QUERY UNTUK MENAMPILKAN DATA ---
$query_tampil = "SELECT 
                    k.id, k.kode, k.status, k.tanggal_beli, 
                    p.nama, p.no_penerbangan, k.kelas, p.asal, p.tujuan, p.harga 
                  FROM bookings k 
                  LEFT JOIN pesawat p ON k.id_pesawat = p.id 
                  ORDER BY k.id DESC";

$tampil = $mysql->query($query_tampil);


ob_end_clean();
?>

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
                <th scope="col">Kadaluarsa</th> <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 0;
            // Batas kadaluarsa 48 jam (dikonversi ke detik untuk penambahan waktu)
            $BATAS_KADALUARSA_DETIK = 48 * 3600; 

            if ($tampil) {
                while ($data = $tampil->fetch_assoc()) {
                    $no++;

                    // Hitung tanggal kadaluarsa: tanggal_beli + 48 jam
                    $tanggal_beli_timestamp = strtotime($data['tanggal_beli']);
                    $tanggal_kadaluarsa = date('Y-m-d H:i:s', $tanggal_beli_timestamp + $BATAS_KADALUARSA_DETIK);
                    
                    $sisa_waktu = hitungWaktuSisa($tanggal_kadaluarsa);

                    // Tentukan kelas CSS untuk status dan kadaluarsa
                 $status_class = $data["status"] == "belum bayar" ? "text-danger fw-bold" : "text-success fw-bold";

                    $expired_class = ($sisa_waktu == "Kadaluarsa") ? "text-secondary fw-bold" : "text-info fw-bold";
            ?>
                    <tr>
                        <th scope="row"><?= $no; ?></th>
                        <td class="fw-bold"><?= htmlspecialchars($data['kode']) ?></td>
                        <td><?= htmlspecialchars($data['nama'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['no_penerbangan'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['kelas'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['asal'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['tujuan'] ?? '-') ?></td>
                        <td>Rp <?= number_format($data['harga'] ?? 0, 0, ',', '.') ?></td>
                        <td class="<?= $status_class ?>"><?= ucfirst(htmlspecialchars($data['status'])) ?></td>

                        <td class="<?= $expired_class ?>"><?= $sisa_waktu ?></td>

                        <td>
                            <?php
                            // Tampilkan tombol 'setujui' hanya jika status 'menunggu' DAN belum kadaluarsa
                            if ($data["status"] == "menunggu" && $sisa_waktu != "Kadaluarsa") {
                            ?>
                                <a href="index.php?page=pesanan&id=<?= $data['id']; ?>" class="btn btn-sm btn-custom-approve text-white" onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI pesanan ini?');">
                                belum bayar
                                </a>
                            <?php
                            } elseif ($sisa_waktu == "Kadaluarsa" && $data["status"] == "menunggu") {
                            ?>
                                <span class="badge bg-secondary">Dihapus</span>
                            <?php
                            } else {
                            ?>
                                <span class="badge bg-success">sudah bayar</span>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="11" class="text-center text-danger">Gagal mengambil data dari database: ' . $mysql->error . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>