<?php
include "../koneksi.php";
include "boot.php";

// Pastikan session sudah dimulai jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION["user_id"] ?? '';

// -----------------------------------------------------------------------
// Autentikasi Admin
// -----------------------------------------------------------------------
$is_admin = false;
if (!empty($user_id)) {
    // Diasumsikan $mysql sudah tersedia dari koneksi.php
    $stmt = $mysql->prepare("SELECT role FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    if ($user_data && $user_data['role'] === 'admin') {
        $is_admin = true;
    }
    $stmt->close();
}

if (!$is_admin) {
    header("Location: ../index.php");
    exit;
}
// -----------------------------------------------------------------------

// -----------------------------------------------------------------------
// LOGIKA FILTER UNTUK TAMPILAN (Menggunakan Rentang Tanggal)
// -----------------------------------------------------------------------
$search_keyword = $_GET['search'] ?? '';
$start_date = $_GET['start_date'] ?? ''; // Tanggal Mulai (YYYY-MM-DD)
$end_date = $_GET['end_date'] ?? '';     // Tanggal Akhir (YYYY-MM-DD)
$current_month = date('Y-m'); // Tetap digunakan untuk Performa Card dan Cetak Bulanan

$where_clauses = ["k.status = 'disetujui'"];
$bind_types = "";
$bind_params = [];

// Filter Rentang Tanggal
if (!empty($start_date) && !empty($end_date)) {
    // Mencari di antara tanggal mulai dan akhir (inklusif)
    $where_clauses[] = "DATE(k.created_at) BETWEEN ? AND ?";
    $bind_types .= "ss";
    $bind_params[] = $start_date;
    $bind_params[] = $end_date;
} elseif (!empty($start_date)) {
    // Jika hanya tanggal mulai yang diisi, filter pesanan pada hari itu saja
    $where_clauses[] = "DATE(k.created_at) = ?";
    $bind_types .= "s";
    $bind_params[] = $start_date;
}
// Catatan: Jika hanya end_date yang diisi, filter diabaikan untuk menjaga query tetap logis.

// Pencarian Kata Kunci (Termasuk Pencarian Tanggal/Hari)
if (!empty($search_keyword)) {
    $search = "%" . $search_keyword . "%";
    $search_strict = $search_keyword;

    // Mencari berdasarkan nama pesawat, no_penerbangan, asal, tujuan, kode booking, ATAU TANGGAL
    $where_clauses[] = "(
        p.nama LIKE ? OR 
        p.no_penerbangan LIKE ? OR 
        p.asal LIKE ? OR 
        p.tujuan LIKE ? OR 
        k.kode LIKE ? OR
        DATE(k.created_at) = ? OR 
        DATE_FORMAT(k.created_at, '%d-%m-%Y') LIKE ?
    )";

    $bind_types .= "sssssss";

    for ($i = 0; $i < 5; $i++) {
        $bind_params[] = $search;
    }

    $bind_params[] = $search_strict;
    $bind_params[] = $search;
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";


// -----------------------------------------------------------------------
// QUERY BULANAN UNTUK TOTAL PENJUALAN & PERFORMA (CARDS) - LOGIKA TIDAK BERUBAH
// -----------------------------------------------------------------------
$current_month_sales = 0;
$previous_month_sales = 0;
$current_month_tickets = 0;
$monthly_data = [];

$monthly_report_query = $mysql->query("
    SELECT 
        DATE_FORMAT(k.created_at, '%Y-%m') AS bulan_tahun,
        SUM(CASE WHEN k.status = 'disetujui' THEN k.total_harga ELSE 0 END) AS total_penjualan,
        SUM(CASE WHEN k.status = 'disetujui' THEN k.jumlah ELSE 0 END) AS total_tiket
    FROM bookings k 
    WHERE k.created_at IS NOT NULL
    GROUP BY bulan_tahun
    ORDER BY bulan_tahun DESC
");

while ($row = $monthly_report_query->fetch_assoc()) {
    $monthly_data[] = $row;
}

// Hitung penjualan dan tiket bulan ini dan bulan sebelumnya (Logika Performa)
if (count($monthly_data) > 0) {
    $current_month_sales = 0;
    $previous_month_sales = 0;

    foreach ($monthly_data as $data) {
        if ($data['bulan_tahun'] == $current_month) {
            $current_month_sales = $data['total_penjualan'];
            $current_month_tickets = $data['total_tiket'];
            break;
        }
    }

    $current_month_timestamp = strtotime($current_month . '-01');
    $previous_month = date('Y-m', strtotime('-1 month', $current_month_timestamp));

    foreach ($monthly_data as $data) {
        if ($data['bulan_tahun'] == $previous_month) {
            $previous_month_sales = $data['total_penjualan'];
            break;
        }
    }
}
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
} elseif ($current_month_sales > 0) {
    $performance_text = "Bulan pertama dengan data";
    $performance_class = "text-info";
}


// -----------------------------------------------------------------------
// QUERY UNTUK DETAIL LAPORAN PEMESANAN (TABEL UTAMA) - Prepared Statement
// -----------------------------------------------------------------------
$tampil_laporan_pesanan = null;
$detail_report_sql = "
    SELECT 
        k.id, k.kode, k.kelas, k.jumlah, k.total_harga, 
        p.nama AS nama_pesawat, p.no_penerbangan, p.asal, p.tujuan, p.harga AS harga_satuan, 
        u.nama AS nama_user
    FROM bookings k 
    JOIN pesawat p ON k.id_pesawat = p.id
    JOIN users u ON k.id_user = u.id
    $where_sql
    ORDER BY k.id DESC
";

$stmt_detail = $mysql->prepare($detail_report_sql);

if (!empty($bind_params)) {
    $refs = [&$bind_types];
    foreach ($bind_params as $key => $value) {
        $refs[] = &$bind_params[$key];
    }
    call_user_func_array([$stmt_detail, 'bind_param'], $refs);
}

$stmt_detail->execute();
$tampil_laporan_pesanan = $stmt_detail->get_result();
$stmt_detail->close();


// -----------------------------------------------------------------------
// QUERY UTAMA UNTUK DETAIL TIKET TERLARIS (TABEL AGREGAT)
// -----------------------------------------------------------------------
// Query ini tidak terpengaruh oleh filter tanggal, karena bersifat agregat total seluruh waktu.
$detail_query_sql = "
    SELECT 
        p.nama AS nama_pesawat, 
        p.no_penerbangan, 
        p.asal, 
        p.tujuan, 
        p.harga,
        SUM(k.jumlah) AS total_pesanan,
        SUM(k.total_harga) AS total_penjualan_rute
    FROM pesawat p 
    LEFT JOIN bookings k ON p.id = k.id_pesawat AND k.status = 'disetujui'
    GROUP BY p.id
    ORDER BY total_pesanan DESC, p.nama ASC";

$tampil_terlaris = $mysql->query($detail_query_sql);
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
            <div class="col-md-4">
                <div class="card bg-light border-primary">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Total Penjualan Bulan Ini (<?= date('F Y', strtotime($current_month . '-01')) ?>)</h5>
                        <p class="card-text fs-4 fw-bold">Rp <?= number_format($current_month_sales, 0, ',', '.') ?></p>
                        <small class="text-muted">Total dari semua tiket yang sudah **disetujui**.</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-light border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">Jumlah Tiket Terjual Bulan Ini</h5>
                        <p class="card-text fs-4 fw-bold"><?= number_format($current_month_tickets, 0, ',', '.') ?> Tiket</p>
                        <small class="text-muted">Total kursi yang terjual.</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
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

        <h5 class="mt-5 header-color">Detail Laporan Pesanan (Disetujui)</h5>
        <p>Gunakan filter dan kolom pencarian di bawah ini untuk melihat data pesanan yang telah **disetujui**.</p>

        <form class="row g-3 mb-4 align-items-end" method="GET" action="index.php">
            <input type="hidden" name="page" value="laporan">

            <div class="col-md-2">
                <label for="startDateFilter" class="form-label">Tgl. Mulai</label>
                <input
                    type="date"
                    class="form-control"
                    id="startDateFilter"
                    name="start_date"
                    value="<?= htmlspecialchars($start_date) ?>"
                    max="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-md-2">
                <label for="endDateFilter" class="form-label">Tgl. Akhir</label>
                <input
                    type="date"
                    class="form-control"
                    id="endDateFilter"
                    name="end_date"
                    value="<?= htmlspecialchars($end_date) ?>"
                    max="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-md-3">
                <label for="searchKeyword" class="form-label">Cari Pesanan</label>
                <input type="text" class="form-control" id="searchKeyword" name="search" placeholder="Nama Pesawat, Kode, No. Penerbangan, Asal/Tujuan, TANGGAL (DD-MM-YYYY)..." value="<?= htmlspecialchars($search_keyword) ?>">
            </div>

            <div class="col-md-5">
                <button type="submit" class="btn btn-primary me-2">Tampilkan</button>

                <?php
                // Siapkan parameter cetak yang membawa rentang tanggal
                $print_params = http_build_query([
                    'search' => $search_keyword,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]);
                // Tentukan bulan yang akan dicetak (gunakan bulan saat ini jika tidak ada rentang)
                $month_for_print = empty($start_date) ? $current_month : date('Y-m', strtotime($start_date));
                ?>
                <a href="print_laporan.php?print=true&<?= $print_params ?>" class="btn btn-success me-2" >Cetak Laporan</a>

                <a href="print_laporan.php?print=harian" class="btn btn-info me-2" >Cetak Harian</a>

                <a href="print_laporan.php?print=bulanan&month=<?= $month_for_print ?>" class="btn btn-warning" >Cetak Bulanan</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Kode Booking</th>
                        <th scope="col">Nama Pemesan</th>
                        <th scope="col">Pesawat (No. Penerbangan)</th>
                        <th scope="col">Asal - Tujuan</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Jml. Tiket</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Total Harga Jual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    $grand_total_penjualan = 0;
                    if ($tampil_laporan_pesanan && $tampil_laporan_pesanan->num_rows > 0) {
                        while ($data = $tampil_laporan_pesanan->fetch_assoc()) {
                            $no++;
                            $grand_total_penjualan += $data['total_harga'];
                    ?>
                            <tr>
                                <th scope="row"><?= $no; ?></th>
                                <td class="fw-bold text-danger"><?= htmlspecialchars($data['kode']) ?></td>
                                <td><?= htmlspecialchars($data['nama_user']) ?></td>
                                <td><?= htmlspecialchars($data['nama_pesawat']) ?> (<?= htmlspecialchars($data['no_penerbangan']) ?>)</td>
                                <td><?= htmlspecialchars($data['asal']) ?> - <?= htmlspecialchars($data['tujuan']) ?></td>
                                <td><?= ucfirst(htmlspecialchars($data['kelas'])) ?></td>
                                <td class="text-center fw-bold"><?= number_format($data['jumlah'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($data['harga_satuan'], 0, ',', '.') ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada data pesanan yang disetujui untuk kriteria ini.</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" class="text-end bg-light">GRAND TOTAL PENJUALAN (Filter Saat Ini):</th>
                        <th class="bg-success text-white fw-bold">Rp <?= number_format($grand_total_penjualan, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <h5 class="mt-5 header-color">Detail Tiket Terlaris</h5>
        <p>Data berikut menunjukkan performa setiap rute/pesawat berdasarkan jumlah pesanan yang **telah disetujui** (Agregat Total).</p>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Pesawat</th>
                        <th scope="col">No. Penerbangan</th>
                        <th scope="col">Asal - Tujuan</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Jumlah Tiket Terjual</th>
                        <th scope="col">Total Penjualan Rute</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    while ($data = $tampil_terlaris->fetch_assoc()) {
                        $no++;
                    ?>
                        <tr>
                            <th scope="row"><?= $no; ?></th>
                            <td><?= htmlspecialchars($data['nama_pesawat']) ?></td>
                            <td><?= htmlspecialchars($data['no_penerbangan']) ?></td>
                            <td><?= htmlspecialchars($data['asal']) ?> - <?= htmlspecialchars($data['tujuan']) ?></td>
                            <td>Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                            <td class="fw-bold text-center">
                                <span class="badge bg-primary fs-6"><?= number_format($data['total_pesanan'] ?? 0, 0, ',', '.') ?></span>
                            </td>
                            <td class="fw-bold text-info">
                                Rp <?= number_format($data['total_penjualan_rute'] ?? 0, 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>