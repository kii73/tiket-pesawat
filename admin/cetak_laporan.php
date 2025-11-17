<?php
// Pastikan file koneksi.php sudah tersedia
include "../koneksi.php"; 

// Pastikan session sudah dimulai jika belum
if (session_status() == PHP_SESSION_NONE) {
        session_start();
}

## 🔒 Autentikasi Admin (Sama seperti file utama)
// -----------------------------------------------------------------------
$user_id = $_SESSION["user_id"] ?? '';
$is_admin = false;

if (!empty($user_id) && isset($mysql)) {
        $stmt = $mysql->prepare("SELECT role FROM users WHERE id=?");
        if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user_result = $stmt->get_result();
                $user_data = $user_result->fetch_assoc();
                if ($user_data && $user_data['role'] === 'admin') {
                        $is_admin = true;
                }
                $stmt->close();
        }
}

if (!$is_admin) {
        // Jika otentikasi gagal, keluar
        exit("Akses Ditolak.");
}
// -----------------------------------------------------------------------

## ⚙️ Logika Filter Rentang Tanggal (DIKOREKSI)
// Ambil filter dari GET. Harusnya selalu ada dari link cetak.
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Format Tanggal Akhir untuk Query SQL: 
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
        $laba_status = "Laba";
        $laba_class = "text-success";
} elseif ($laba_bersih < 0) {
        $laba_status = "Rugi";
        $laba_class = "text-danger";
} else {
        $laba_status = "Impás (Break-even)";
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

$stmt_detail_expense = $mysql->prepare($detail_pengeluaran_sql);
$stmt_detail_expense->bind_param("ss", $start_date, $end_date_query);
$stmt_detail_expense->execute();
$tampil_detail_pengeluaran = $stmt_detail_expense->get_result();

?>
<!DOCTYPE html>
<html lang="id">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetak Laporan Keuangan</title>
                <style>
                body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                }
                .container-cetak { max-width: 800px; margin: auto; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                table, th, td { border: 1px solid black; }
                th, td { padding: 8px; text-align: left; }
                .summary-box { 
                        border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; 
                        display: inline-block; width: 32%; background-color: #f9f9f9; 
                        box-sizing: border-box; 
                }
                .text-end { text-align: right; }
                .text-center { text-align: center; }
                .fw-bold { font-weight: bold; }
                .text-success { color: green; }
                .text-danger { color: red; }
                @media print {
                        /* Penting untuk mencetak background warna */
                        .summary-box { 
                                -webkit-print-color-adjust: exact; color-adjust: exact; 
                                background-color: #f0f0f0 !important; 
                        }
                        tfoot th { background-color: #ddd !important; }
                }
        </style>
</head>

<body onload="window.print()">

        <div class="container-cetak">
                <h2 class="text-center">LAPORAN LABA RUGI</h2>
                <h4 class="text-center">PERIODE: **<?= date('d F Y', strtotime($start_date)) ?>** s/d **<?= date('d F Y', strtotime($end_date)) ?>**</h4>
                <hr>

                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <div class="summary-box">
                                <strong>Total Penghasilan</strong>
                                <p class="fw-bold text-success">Rp <?= number_format($total_penghasilan, 0, ',', '.') ?></p>
                        </div>

                        <div class="summary-box">
                                <strong>Total Pengeluaran</strong>
                                <p class="fw-bold text-danger">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></p>
                        </div>

                        <div class="summary-box">
                                <strong>Laba Bersih</strong>
                                <p class="fw-bold <?= $laba_class ?>">Rp <?= number_format(abs($laba_bersih), 0, ',', '.') ?> (<?= $laba_status ?>)</p>
                        </div>
                </div>

                <h5>Detail Pengeluaran</h5>
                <table>
                        <thead>
                                <tr style="background-color: #eee;">
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Deskripsi</th>
                                        <th class="text-end">Jumlah (Rp)</th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php
                                $no = 0;
                                $tampil_detail_pengeluaran->data_seek(0); // Reset pointer
                                if ($tampil_detail_pengeluaran->num_rows > 0) {
                                        while ($data = $tampil_detail_pengeluaran->fetch_assoc()) {
                                                $no++;
                                ?>
                                                <tr>
                                                        <td><?= $no; ?></td>
                                                        <td><?= date('d M Y', strtotime($data['tanggal_pengeluaran'])) ?></td>
                                                        <td><?= htmlspecialchars($data['deskripsi']) ?></td>
                                                        <td class="text-end">Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></td>
                                                </tr>
                                        <?php }
                                } else { ?>
                                        <tr>
                                                <td colspan="4" class="text-center text-muted">Tidak ada data pengeluaran untuk periode ini.</td>
                                        </tr>
                                <?php } ?>
                        </tbody>
                        <tfoot>
                                <tr>
                                        <th colspan="3" class="text-end" style="background-color: #eee;">TOTAL PENGELUARAN</th>
                                        <th class="text-end fw-bold" style="background-color: #ddd;">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></th>
                                </tr>
                        </tfoot>
                </table>
        </div>

        <script>
                // Setelah cetak selesai, tab akan mencoba ditutup (best practice untuk window.open)
                window.onafterprint = function() {
                        window.close();
                }
        </script>
</body>

</html>
<?php
// Tutup koneksi
if (isset($mysql)) {
        $mysql->close();
}
// Tutup statement untuk detail pengeluaran
if (isset($stmt_detail_expense)) {
    $stmt_detail_expense->close();
}
?>