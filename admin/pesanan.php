<?php
include "boot.php";
include "../koneksi.php";

// Set zona waktu agar perhitungan waktu akurat
date_default_timezone_set('Asia/Jakarta');

// --- PENGAMANAN HALAMAN ADMIN ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
?>
    <script>
        window.location.href = "../index.php"
    </script>
<?php
    exit;
}

if (!isset($mysql) || !$mysql instanceof mysqli) {
?>
    <script>
        window.location.href = "../index.php"
    </script>
<?php
    exit;
}

// --- FUNGSI BARU UNTUK CEK KADALUARSA BERDASARKAN SELISIH HARI ---
/**
 * Menghitung dan memberikan status kadaluarsa berdasarkan tanggal keberangkatan.
 * Pesanan dianggap kadaluarsa jika sudah lewat tanggal keberangkatan + 1 hari.
 * * @param string $tanggalBerangkat Tanggal keberangkatan dari tabel pesawat (YYYY-MM-DD).
 * @return array Berisi ['text' => Status, 'is_expired' => Boolean, 'class' => CSS Class].
 */
function getStatusKadaluarsa($tanggalBerangkat)
{
    $now = new DateTime(date('Y-m-d')); // Waktu saat ini (hanya tanggal)
    $tglBerangkat = new DateTime($tanggalBerangkat);
    $tglBerangkat->setTime(0, 0, 0); // Atur waktu ke 00:00:00 untuk perbandingan hari yang akurat

    // Tentukan Batas Kadaluarsa: 1 hari setelah tanggal keberangkatan
    $batasWaktuKadaluarsa = new DateTime($tanggalBerangkat);
    $batasWaktuKadaluarsa->modify('+1 day');
    $batasWaktuKadaluarsa->setTime(0, 0, 0);

    // 1. Cek Mutlak Kadaluarsa
    if ($now >= $batasWaktuKadaluarsa) {
        $selisih = $now->diff($batasWaktuKadaluarsa);
        $jumlahHari = $selisih->days;
        return [
            'text' => "Kadaluarsa (" . $jumlahHari . " Hari Lalu)",
            'is_expired' => true,
            'class' => "text-danger fw-bold"
        ];
    }

    // 2. Hitung Selisih Hari Menuju Keberangkatan
    $selisih = $now->diff($tglBerangkat);
    $jumlahHari = $selisih->days;

    if ($jumlahHari == 0) {
        return [
            'text' => "Berangkat HARI INI!",
            'is_expired' => false,
            'class' => "text-warning fw-bold"
        ];
    } elseif ($jumlahHari == 1) {
        return [
            'text' => "Berangkat Besok",
            'is_expired' => false,
            'class' => "text-info fw-bold"
        ];
    } else {
        return [
            'text' => $jumlahHari . " Hari Lagi",
            'is_expired' => false,
            'class' => "text-primary fw-bold"
        ];
    }
}


// --- LOGIKA PERSETUJUAN (UPDATE STATUS) ---
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    
    // Perbaikan keamanan: Gunakan prepared statement untuk UPDATE
    $stmt_update = $mysql->prepare("UPDATE bookings SET status = 'disetujui' WHERE id = ?");

    if ($stmt_update) {
        $stmt_update->bind_param("i", $id);
        $stmt_update->execute();
        $stmt_update->close();
    }

?>
    <script>
        window.location.href = "index.php?page=pesanan"
    </script>
<?php
    exit;
}


// --- QUERY UNTUK MENAMPILKAN DATA ---
$query_tampil = "SELECT
                     k.id, k.kode, k.status, k.tanggal_beli, k.kelas, k.jumlah, k.total_harga,
                     p.nama, p.no_penerbangan, p.asal, p.tujuan, p.harga, p.tanggal_berangkat 
                  FROM bookings k
                  LEFT JOIN pesawat p ON k.id_pesawat = p.id
                  ORDER BY k.id DESC";

$tampil = $mysql->query($query_tampil);
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
    <h3 class="mb-4 header-color">Data Pesanan Tiket Pesawat ✈️</h3>

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
                <th scope="col">Tgl. Berangkat</th>
                <th scope="col">Jmlh Tiket</th>
                <th scope="col">Harga Satuan</th>
                <th scope="col">Total Harga</th>
                <th scope="col">Status</th>
                <th scope="col">Jadwal Sisa</th> <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 0;

            if ($tampil) {
                while ($data = $tampil->fetch_assoc()) {
                    $no++;

                    // --- LOGIKA KADALUARSA BARU ---
                    // Dapatkan status berdasarkan tanggal keberangkatan
                    $status_info = getStatusKadaluarsa($data['tanggal_berangkat']);
                    
                    $is_expired = $status_info['is_expired'];
                    $expired_text = $status_info['text'];
                    $expired_class = $status_info['class'];
                    
                    // Format Tanggal Berangkat
                    $tanggal_berangkat_formatted = date('d/m/Y', strtotime($data['tanggal_berangkat']));

                    // Tentukan kelas CSS untuk status
                    $status_class = $data["status"] == "menunggu" ? "text-danger fw-bold" : "text-success fw-bold";
                    $display_status = $data["status"] == "menunggu" ? "Menunggu Pembayaran" : "Disetujui/Lunas";
            ?>
                    <tr>
                        <th scope="row"><?= $no; ?></th>
                        <td class="fw-bold"><?= htmlspecialchars($data['kode']) ?></td>
                        <td><?= htmlspecialchars($data['nama'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['no_penerbangan'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['kelas'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['asal'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['tujuan'] ?? '-') ?></td>
                        
                        <td><?= $tanggal_berangkat_formatted ?></td>
                        
                        <td class="text-center"><?= htmlspecialchars($data['jumlah'] ?? 0) ?></td>
                        <td>Rp <?= number_format($data['harga'] ?? 0, 0, ',', '.') ?></td>
                        <td class="fw-bold">Rp <?= number_format($data['total_harga'] ?? 0, 0, ',', '.') ?></td>
                        <td class="<?= $status_class ?>"><?= $display_status ?></td>

                        <td class="<?= $expired_class ?>"><?= $expired_text ?></td>

                        <td>
                            <?php
                            if ($data["status"] == "menunggu" && !$is_expired) {
                            ?>
                                <a href="index.php?page=pesanan&id=<?= $data['id']; ?>" class="btn btn-sm btn-custom-approve text-white mb-1" onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI (mengubah status menjadi disetujui/lunas) pesanan ini?');">
                                    Setujui
                                </a>
                            <?php
                            } elseif ($data["status"] == "disetujui") {
                            ?>
                                <a href="cetak_tiket.php?id_booking=<?= $data['id']; ?>" target="_blank" class="btn btn-sm btn-info text-white mb-1">
                                    Cetak Tiket
                                </a>
                                <span class="badge bg-success">Disetujui</span>
                            <?php
                            } else {
                                // Status menunggu dan sudah kadaluarsa
                            ?>
                                <span class="badge bg-secondary">Tidak Berlaku</span>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="15" class="text-center text-danger">Gagal mengambil data dari database: ' . $mysql->error . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>