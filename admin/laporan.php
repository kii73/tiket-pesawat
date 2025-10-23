<?php
// laporan.php
include "boot.php"; // Memuat Bootstrap dan header/footer (jika ada di boot.php)
include "../koneksi.php"; // Memuat koneksi database

// -----------------------------------------------------------------------
// Autentikasi Admin
// -----------------------------------------------------------------------
$remember_token = $_COOKIE["remember_token"] ?? '';
$user_result = $mysql->query("SELECT role FROM users WHERE remember_token='$remember_token'");
$user_data = $user_result->fetch_assoc();

if (!$user_data || $user_data['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Catatan: Logika pembaruan status (verifikasi/persetujuan) telah dihapus karena kolom "Aksi" dihapus.

// -----------------------------------------------------------------------
// Perhitungan Total dan Performa Bulanan
// -----------------------------------------------------------------------
// Menggunakan p.created_at (tanggal pesawat dibuat) sebagai proksi untuk bulan transaksi.
$monthly_report_query = $mysql->query("
    SELECT 
        DATE_FORMAT(p.created_at, '%Y-%m') AS bulan_tahun,
        SUM(CASE WHEN k.status = 'disetujui' THEN p.harga ELSE 0 END) AS total_penjualan
    FROM kode k 
    LEFT JOIN pesawat p ON k.id_pesawat = p.id
    GROUP BY bulan_tahun
    ORDER BY bulan_tahun DESC
");

$monthly_data = [];
$previous_month_sales = 0;
$current_month_sales = 0;

while ($row = $monthly_report_query->fetch_assoc()) {
    $monthly_data[] = $row;
}

// Tentukan penjualan bulan ini dan bulan sebelumnya
if (count($monthly_data) > 0) {
    // Bulan terbaru (Bulan saat ini)
    $current_month_sales = $monthly_data[0]['total_penjualan'];

    // Bulan sebelumnya (Jika ada)
    if (count($monthly_data) > 1) {
        $previous_month_sales = $monthly_data[1]['total_penjualan'];
    }
}

// Hitung Performa Bulanan
$performance = 0;
$performance_text = "Data tidak cukup";
$performance_class = "text-secondary";

if ($previous_month_sales > 0) {
    $performance = (($current_month_sales - $previous_month_sales) / $previous_month_sales) * 100;
    $performance_text = number_format(abs($performance), 2) . "% ";

    if ($performance > 0) {
        $performance_text .= " (Naik) 📈";
        $performance_class = "text-success";
    } elseif ($performance < 0) {
        $performance_text .= " (Turun) 📉";
        $performance_class = "text-danger";
    } else {
        $performance_text .= " (Stabil) ⏸️";
        $performance_class = "text-secondary";
    }
} elseif (count($monthly_data) == 1) {
    // Jika hanya ada data 1 bulan, tidak ada pembanding
    $performance_text = "Bulan pertama";
    $performance_class = "text-info";
}
// -----------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Tiket - Admin</title>
    <style>
        .header-color {
            color: #008080 !important;
        }

        .table-custom-header {
            background-color: #581845 !important;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container-fluid mt-3">
        <h3 class="mb-4 header-color">Laporan Penjualan Tiket Pesawat</h3>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-light border-primary">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Total Penjualan Bulan Ini (<?= isset($monthly_data[0]['bulan_tahun']) ? $monthly_data[0]['bulan_tahun'] : 'N/A' ?>)</h5>
                        <p class="card-text fs-4 fw-bold">Rp <?= number_format($current_month_sales, 0, ',', '.') ?></p>
                        <small class="text-muted">Total dari semua tiket yang sudah **disetujui**.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light border-info">
                    <div class="card-body">
                        <h5 class="card-title text-info">Performa Bulanan</h5>
                        <p class="card-text fs-4 fw-bold <?= $performance_class ?>">
                            <?= $performance_text ?>
                        </p>
                        <small class="text-muted">Dibandingkan dengan bulan sebelumnya.</small>
                    </div>
                </div>
            </div>
        </div>
        <h5 class="mt-5 header-color">Detail Tiket Terlaris</h5>
        <p>Data berikut menunjukkan performa setiap rute/pesawat berdasarkan jumlah pesanan yang **telah disetujui**.</p>

        <table class="table table-striped table-bordered">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama Pesawat</th>
                    <th scope="col">No. Penerbangan</th>
                    <th scope="col">Asal - Tujuan</th>
                    <th scope="col">Harga Satuan</th>
                    <th scope="col">Jumlah Tiket yang Dipesan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query untuk menghitung jumlah tiket yang dipesan (status 'disetujui') untuk setiap pesawat
                $tampil = $mysql->query("SELECT 
                                        p.nama AS nama_pesawat, 
                                        p.no_penerbangan, 
                                        p.asal, 
                                        p.tujuan, 
                                        p.harga,
                                        COUNT(k.id) AS total_pesanan
                                    FROM pesawat p 
                                    LEFT JOIN kode k ON p.id = k.id_pesawat AND k.status = 'disetujui'
                                    GROUP BY p.id
                                    ORDER BY total_pesanan DESC, p.nama ASC");
                $no = 0;

                while ($data = $tampil->fetch_assoc()) {
                    $no++;
                ?>
                    <tr>
                        <th scope="row"><?= $no; ?></th>
                        <td><?= htmlspecialchars($data['nama_pesawat']) ?></td>
                        <td><?= htmlspecialchars($data['no_penerbangan']) ?></td>
                        <td><?= htmlspecialchars($data['asal']) ?> - <?= htmlspecialchars($data['tujuan']) ?></td>
                        <td>Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                        <td class="fw-bold text-center">
                            <span class="badge bg-primary fs-6"><?= number_format($data['total_pesanan'], 0, ',', '.') ?></span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>

</html>