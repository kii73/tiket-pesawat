<?php
// Pastikan session sudah dimulai jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Koneksi dan Autentikasi (Sama seperti users.php/laporan.php)
include "../koneksi.php";

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
    // Hanya admin yang bisa mencetak
    header("Location: ../index.php");
    exit;
}

// 2. Ambil Parameter Filter
$print_mode = $_GET['print'] ?? 'true'; // Default mode
$search_keyword = $_GET['search'] ?? '';
$filter_month = $_GET['month'] ?? date('Y-m'); // Default bulan saat ini
$start_date = $_GET['start_date'] ?? ''; // Ambil filter tanggal kustom
$end_date = $_GET['end_date'] ?? ''; 

// 3. Tentukan Judul Laporan dan Filter WHERE Clause
$where_clauses = ["k.status = 'disetujui'"];
$bind_types = "";
$bind_params = [];
$report_title = "Laporan Penjualan Tiket Pesawat";
$report_subtitle = "Semua Pesanan yang Disetujui";

switch ($print_mode) {
    case 'harian':
        $report_title = "Laporan Penjualan Harian";
        $report_subtitle = "Tanggal: " . date('d F Y');
        $where_clauses[] = "DATE(k.created_at) = CURDATE()";
        break;

    case 'bulanan':
        // Filter Bulan (bisa dari parameter 'month' atau default bulan ini)
        $report_title = "Laporan Penjualan Bulanan";
        $report_subtitle = "Bulan: " . date('F Y', strtotime($filter_month . '-01'));
        $where_clauses[] = "DATE_FORMAT(k.created_at, '%Y-%m') = ?";
        $bind_types .= "s";
        $bind_params[] = $filter_month;
        break;

    case 'true': // Laporan kustom (menggunakan filter dari URL)
    default:
        // --- LOGIKA FILTER RENTANG TANGGAL DARI HALAMAN UTAMA ---
        if (!empty($start_date) && !empty($end_date)) {
            $report_subtitle = "Rentang Tanggal: " . date('d/m/Y', strtotime($start_date)) . " s/d " . date('d/m/Y', strtotime($end_date));
            $where_clauses[] = "DATE(k.created_at) BETWEEN ? AND ?";
            $bind_types .= "ss";
            $bind_params[] = $start_date;
            $bind_params[] = $end_date;
        } elseif (!empty($start_date)) {
             $report_subtitle = "Tanggal: " . date('d/m/Y', strtotime($start_date));
             $where_clauses[] = "DATE(k.created_at) = ?";
             $bind_types .= "s";
             $bind_params[] = $start_date;
        }

        // --- LOGIKA FILTER KATA KUNCI DARI HALAMAN UTAMA ---
        if (!empty($search_keyword)) {
            $report_subtitle .= (empty($start_date) && empty($end_date) ? "" : " | ") . "Kata Kunci: " . htmlspecialchars($search_keyword);
            $search = "%" . $search_keyword . "%";
            $search_strict = $search_keyword; 

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

        if (empty($start_date) && empty($search_keyword)) {
             $report_subtitle = "Semua Pesanan yang Disetujui";
        }
        break;
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";


// 4. Query Data Laporan
$detail_report_sql = "
    SELECT 
        k.id, k.kode, k.kelas, k.jumlah, k.total_harga, k.created_at,
        p.nama AS nama_pesawat, p.no_penerbangan, p.asal, p.tujuan, p.harga AS harga_satuan, 
        u.nama AS nama_user
    FROM bookings k 
    JOIN pesawat p ON k.id_pesawat = p.id
    JOIN users u ON k.id_user = u.id
    $where_sql
    ORDER BY k.created_at ASC
";

$tampil_laporan_pesanan = null;
$grand_total_penjualan = 0;
$grand_total_jumlah = 0;

try {
    $stmt_detail = $mysql->prepare($detail_report_sql);

    if (!empty($bind_params)) {
        $refs = [&$bind_types];
        foreach ($bind_params as $key => $value) {
            $refs[] = &$bind_params[$key];
        }
        // bind_param requires arguments to be passed by reference
        if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
             $stmt_detail->bind_param(...$refs);
        } else {
             call_user_func_array([$stmt_detail, 'bind_param'], $refs);
        }
    }

    $stmt_detail->execute();
    $tampil_laporan_pesanan = $stmt_detail->get_result();
    $stmt_detail->close();
} catch (Exception $e) {
    die("Kesalahan database saat membuat laporan: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $report_title ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 16pt;
        }

        .header p {
            margin: 5px 0 10px 0;
            font-size: 11pt;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
        }

        .total-row th,
        .total-row td {
            background-color: #ddd;
            font-weight: bold;
            font-size: 11pt;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Style untuk Print */
        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <h1>LAPORAN PENJUALAN TIKET PESAWAT</h1>
        <p><?= $report_title ?> - <?= $report_subtitle ?></p>
        <p style="border:none; font-size: 9pt;">Dicetak pada: <?= date('d F Y H:i:s') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 10%;">Kode Booking</th>
                <th style="width: 15%;">Nama Pemesan</th>
                <th style="width: 15%;">Penerbangan</th>
                <th style="width: 10%;">Asal - Tujuan</th>
                <th style="width: 5%;">Kelas</th>
                <th style="width: 5%;">Jml. Tiket</th>
                <th style="width: 10%;">Harga Satuan</th>
                <th style="width: 15%;">Total Harga Jual</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 0;
            if ($tampil_laporan_pesanan && $tampil_laporan_pesanan->num_rows > 0) {
                while ($data = $tampil_laporan_pesanan->fetch_assoc()) {
                    $no++;
                    $grand_total_penjualan += $data['total_harga'];
                    $grand_total_jumlah += $data['jumlah'];
            ?>
                    <tr>
                        <td class="text-center"><?= $no; ?></td>
                        <td><?= date('d/m/Y', strtotime($data['created_at'])) ?></td>
                        <td><?= htmlspecialchars($data['kode']) ?></td>
                        <td><?= htmlspecialchars($data['nama_user']) ?></td>
                        <td><?= htmlspecialchars($data['nama_pesawat']) ?> (<?= htmlspecialchars($data['no_penerbangan']) ?>)</td>
                        <td><?= htmlspecialchars($data['asal']) ?> - <?= htmlspecialchars($data['tujuan']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($data['kelas'])) ?></td>
                        <td class="text-center"><?= number_format($data['jumlah'], 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($data['harga_satuan'], 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data pesanan yang disetujui untuk kriteria ini.</td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="7" class="text-right">TOTAL KESELURUHAN (Tiket & Penjualan):</th>
                <td class="text-center"><?= number_format($grand_total_jumlah, 0, ',', '.') ?></td>
                <td colspan="1"></td>
                <td class="text-right">Rp <?= number_format($grand_total_penjualan, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="goBackToReport()" style="padding: 10px 20px; cursor: pointer;">Kembali ke Laporan</button>
    </div>

    <script>
        // Fungsi untuk mengarahkan pengguna kembali ke halaman laporan utama
        function goBackToReport() {
            // Mengarahkan kembali ke halaman laporan di dashboard admin
            window.location.replace("index.php?page=laporan");
        }

        (function() {
            // 1. Mendeteksi ketika dialog cetak (print dialog) ditutup (Didukung oleh Firefox/IE)
            window.addEventListener('afterprint', goBackToReport);

            // 2. Alternatif/Fallback untuk mendeteksi penutupan print dialog (Chrome/Edge modern)
            if (window.matchMedia) {
                var mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (!mql.matches) {
                        // Jika media query tidak cocok dengan 'print' lagi, berarti dialog ditutup
                        // Beri penundaan singkat untuk keandalan
                        setTimeout(goBackToReport, 50); 
                    }
                });
            }
        })();
    </script>
</body>

</html>