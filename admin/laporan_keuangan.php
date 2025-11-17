<?php
// Pastikan file koneksi.php dan boot.php sudah tersedia di direktori yang sesuai
include "../koneksi.php"; 
include "boot.php";

// Pastikan session sudah dimulai jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

## 🔒 Autentikasi Admin
// -----------------------------------------------------------------------
$user_id = $_SESSION["user_id"] ?? '';
$is_admin = false;

if (!empty($user_id) && isset($mysql)) {
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

## ⚙️ Logika Filter Tanggal Fleksibel
// Set default filter ke awal dan akhir bulan berjalan
$default_start = date('Y-m-01');
$default_end = date('Y-m-t');

// Ambil filter dari GET. Jika tidak ada, gunakan default.
$start_date = $_GET['start_date'] ?? $default_start;
$end_date = $_GET['end_date'] ?? $default_end;

// Format Tanggal Akhir untuk Query SQL: 
// Tambahkan waktu 23:59:59 agar hasil filter benar-benar inklusif sampai akhir hari tersebut.
$end_date_query = $end_date . ' 23:59:59'; 


## 📊 A. HITUNG PENGHASILAN (REVENUE)
// -----------------------------------------------------------------------
$penghasilan_query = $mysql->prepare("
    SELECT IFNULL(SUM(p.harga * k.jumlah), 0) AS total_penghasilan
    FROM bookings k 
    JOIN pesawat p ON k.id_pesawat = p.id
    WHERE k.status = 'disetujui' 
    AND k.created_at BETWEEN ? AND ?
");
$penghasilan_query->bind_param("ss", $start_date, $end_date_query);
$penghasilan_query->execute();
$penghasilan_result = $penghasilan_query->get_result()->fetch_assoc();
$total_penghasilan = $penghasilan_result['total_penghasilan'] ?? 0;
$penghasilan_query->close();


## 📉 B. HITUNG PENGELUARAN (EXPENSES)
// -----------------------------------------------------------------------
$pengeluaran_query = $mysql->prepare("
    SELECT IFNULL(SUM(jumlah), 0) AS total_pengeluaran
    FROM expenses
    WHERE tanggal_pengeluaran BETWEEN ? AND ?
");
$pengeluaran_query->bind_param("ss", $start_date, $end_date_query);
$pengeluaran_query->execute();
$pengeluaran_result = $pengeluaran_query->get_result()->fetch_assoc();
$total_pengeluaran = $pengeluaran_result['total_pengeluaran'] ?? 0;
$pengeluaran_query->close();


## 💰 C. HITUNG LABA BERSIH
// -----------------------------------------------------------------------
$laba_bersih = $total_penghasilan - $total_pengeluaran;

// Menentukan status laba
if ($laba_bersih > 0) {
    $laba_status = "Laba 💰";
    $laba_class = "text-success";
} elseif ($laba_bersih < 0) {
    $laba_status = "Rugi 🔻";
    $laba_class = "text-danger";
} else {
    $laba_status = "Impás (Break-even) ⏸️";
    $laba_class = "text-secondary";
}

## 📜 D. QUERY DETAIL PENGELUARAN (TABEL)
// -----------------------------------------------------------------------
$detail_pengeluaran_sql = "
    SELECT id, deskripsi, jumlah, tanggal_pengeluaran
    FROM expenses 
    WHERE tanggal_pengeluaran BETWEEN ? AND ?
    ORDER BY tanggal_pengeluaran DESC
";

// Simpan query string filter untuk tombol cetak
$print_query_string = http_build_query(['start_date' => $start_date, 'end_date' => $end_date]);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Admin</title>
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

        <h3 class="mb-4 header-color">Laporan Laba Rugi Berdasarkan Rentang Tanggal</h3>

        <div class="mb-3">
            <a href="cetak_laporan.php?<?= $print_query_string ?>" target="_blank" class="btn btn-secondary">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
        </div>

        ## 🔎 Filter Periode Laporan
        ---
        <form class="row g-3 mb-4 align-items-end" method="GET" action="index.php">
            <input type="hidden" name="page" value="laporan_keuangan">
            
            <div class="col-md-3">
                <label for="startDate" class="form-label">Tanggal Awal</label>
                <input type="date" class="form-control" id="startDate" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
            </div>
            
            <div class="col-md-3">
                <label for="endDate" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="endDate" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
            </div>
            
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary me-2">Tampilkan Laporan</button>
            </div>
        </form>

        <h4 class="mt-5 text-center">LAPORAN KEUANGAN PERIODE: **<?= date('d F Y', strtotime($start_date)) ?>** s/d **<?= date('d F Y', strtotime($end_date)) ?>**</h4>
        <hr>

        ## 📈 Ringkasan Keuangan
        ---
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Penghasilan (Revenue)</h5>
                        <p class="card-text fs-3 fw-bold">Rp <?= number_format($total_penghasilan, 0, ',', '.') ?></p>
                        <small>Total Penjualan Tiket Disetujui</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengeluaran (Expenses)</h5>
                        <p class="card-text fs-3 fw-bold">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></p>
                        <small>Total Biaya Operasional & Lain-lain</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Laba Bersih (Net Profit)</h5>
                        <p class="card-text fs-3 fw-bold <?= $laba_class ?>">Rp <?= number_format(abs($laba_bersih), 0, ',', '.') ?></p>
                        <small class="fw-bold"><?= $laba_status ?></small>
                    </div>
                </div>
            </div>
        </div>

        ## 🧾 Detail Pengeluaran
        ---
        <h5 class="mt-5 header-color">Detail Pengeluaran Periode Ini</h5>
        <table class="table table-striped table-bordered">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Deskripsi</th>
                    <th scope="col">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 0;
                // Menjalankan query untuk detail pengeluaran
                $stmt_detail_expense_re = $mysql->prepare($detail_pengeluaran_sql);
                $stmt_detail_expense_re->bind_param("ss", $start_date, $end_date_query);
                $stmt_detail_expense_re->execute();
                $tampil_detail_pengeluaran_re = $stmt_detail_expense_re->get_result();

                if ($tampil_detail_pengeluaran_re->num_rows > 0) {
                    while ($data = $tampil_detail_pengeluaran_re->fetch_assoc()) {
                        $no++;
                ?>
                        <tr>
                            <th scope="row"><?= $no; ?></th>
                            <td><?= date('d M Y', strtotime($data['tanggal_pengeluaran'])) ?></td>
                            <td><?= htmlspecialchars($data['deskripsi']) ?></td>
                            <td class="text-end">Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                    <?php }
                    $stmt_detail_expense_re->close();
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada data pengeluaran untuk periode ini.</td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">TOTAL PENGELUARAN</th>
                    <th class="text-end bg-warning fw-bold">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>

    </div>
</body>

</html>
<?php
// Tutup koneksi
if (isset($mysql)) {
    $mysql->close();
}
?>