<?php
include "../koneksi.php";
include "boot.php";

// Pastikan session sudah dimulai jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------------
// Autentikasi Admin (tetap)
// -----------------------------------------------------------------------
$user_id = $_SESSION["user_id"] ?? '';
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

$current_month = date('Y-m');
$filter_month = $_GET['month'] ?? $current_month;

// -----------------------------------------------------------------------
// A. HITUNG PENGHASILAN (REVENUE) BULANAN (tetap)
// -----------------------------------------------------------------------
$penghasilan_query = $mysql->prepare("
    SELECT SUM(p.harga * k.jumlah) AS total_penghasilan
    FROM bookings k 
    JOIN pesawat p ON k.id_pesawat = p.id
    WHERE k.status = 'disetujui' AND DATE_FORMAT(k.created_at, '%Y-%m') = ?
");
$penghasilan_query->bind_param("s", $filter_month);
$penghasilan_query->execute();
$penghasilan_result = $penghasilan_query->get_result()->fetch_assoc();
$total_penghasilan = $penghasilan_result['total_penghasilan'] ?? 0;
$penghasilan_query->close();


// -----------------------------------------------------------------------
// B. HITUNG PENGELUARAN (EXPENSES) BULANAN (tetap)
// -----------------------------------------------------------------------
$pengeluaran_query = $mysql->prepare("
    SELECT SUM(jumlah) AS total_pengeluaran
    FROM expenses
    WHERE DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = ?
");
$pengeluaran_query->bind_param("s", $filter_month);
$pengeluaran_query->execute();
$pengeluaran_result = $pengeluaran_query->get_result()->fetch_assoc();
$total_pengeluaran = $pengeluaran_result['total_pengeluaran'] ?? 0;
$pengeluaran_query->close();


// -----------------------------------------------------------------------
// C. HITUNG LABA BERSIH (tetap)
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

// -----------------------------------------------------------------------
// D. QUERY DETAIL PENGELUARAN (TABEL) (tetap)
// -----------------------------------------------------------------------
$detail_pengeluaran_sql = "
    SELECT id, deskripsi, jumlah, tanggal_pengeluaran
    FROM expenses 
    WHERE DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = ?
    ORDER BY tanggal_pengeluaran DESC
";

$stmt_detail_expense = $mysql->prepare($detail_pengeluaran_sql);
$stmt_detail_expense->bind_param("s", $filter_month);
$stmt_detail_expense->execute();
$tampil_detail_pengeluaran = $stmt_detail_expense->get_result();
$stmt_detail_expense->close();

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

        /* ------------------------------------------- */
        /* CSS untuk Print */
        /* ------------------------------------------- */
        @media print {
            /* Sembunyikan elemen yang tidak perlu dicetak */
            .no-print {
                display: none !important;
            }

            /* Hapus margin/padding berlebih dan perbaiki tampilan tabel */
            body {
                margin: 0;
                padding: 0;
            }

            .container-fluid {
                padding: 0 !important;
                margin-top: 20px;
            }

            .card {
                border: 1px solid #ccc !important; /* Tambahkan border untuk kartu di print */
                box-shadow: none !important;
            }
            
            /* Ganti warna background card agar terlihat di cetakan */
            .bg-success, .bg-warning, .bg-info {
                background-color: #f0f0f0 !important;
                color: #000 !important;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid mt-3">

        <h3 class="mb-4 header-color">Laporan Laba Rugi Bulanan</h3>

        <div class="mb-3 no-print">
            <button class="btn btn-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
        </div>
        
        <form class="row g-3 mb-4 align-items-end no-print" method="GET" action="laporan_keuangan.php">
            <div class="col-md-3">
                <label for="monthFilter" class="form-label">Pilih Bulan</label>
                <input type="month" class="form-control" id="monthFilter" name="month" value="<?= htmlspecialchars($filter_month) ?>">
            </div>
            <div class="col-md-9">
                <button type="submit" class="btn btn-primary me-2">Tampilkan Laporan</button>
            </div>
        </form>

        <h4 class="mt-5 text-center">LAPORAN KEUANGAN BULAN: **<?= date('F Y', strtotime($filter_month . '-01')) ?>**</h4>
        <hr>

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

        <h5 class="mt-5 header-color">Detail Pengeluaran Bulan Ini</h5>
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
                if ($tampil_detail_pengeluaran->num_rows > 0) {
                    while ($data = $tampil_detail_pengeluaran->fetch_assoc()) {
                        $no++;
                ?>
                        <tr>
                            <th scope="row"><?= $no; ?></th>
                            <td><?= date('d M Y', strtotime($data['tanggal_pengeluaran'])) ?></td>
                            <td><?= htmlspecialchars($data['deskripsi']) ?></td>
                            <td class="text-end">Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada data pengeluaran untuk bulan ini.</td>
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