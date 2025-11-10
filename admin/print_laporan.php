<?php
include "../koneksi.php"; 
include "boot.php";

session_start();
$user_id = $_SESSION["user_id"] ?? ''; 

// -----------------------------------------------------------------------
// Autentikasi Admin
// -----------------------------------------------------------------------
$is_admin = false;
if (!empty($user_id)) {
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
// LOGIKA FILTER UNTUK CETAK (PRINT MODE) - MENGGUNAKAN k.created_at
// -----------------------------------------------------------------------
$print_mode_type = $_GET['print'] ?? 'false'; 
$search_keyword = $_GET['search'] ?? '';
$filter_month = $_GET['month'] ?? ''; 
$current_month = date('Y-m'); 
$current_date = date('Y-m-d'); 

$where_clauses = ["k.status = 'disetujui'"];
$bind_types = "";
$bind_params = [];
$print_title = "LAPORAN PESANAN TIKET PESAWAT (DISETUJUI)"; 
$print_filter_text = "";

if ($print_mode_type === 'harian') {
    // FIX: Menggunakan k.created_at
    $where_clauses[] = "DATE(k.created_at) = ?";
    $bind_types .= "s";
    $bind_params[] = $current_date;
    $print_title = "STRUK PENJUALAN TIKET HARI INI";
    $print_filter_text = "Tanggal: **" . date('d F Y') . "**";

} elseif ($print_mode_type === 'bulanan') {
    // FIX: Menggunakan k.created_at
    $month_to_print = !empty($filter_month) ? $filter_month : $current_month; 
    $where_clauses[] = "DATE_FORMAT(k.created_at, '%Y-%m') = ?";
    $bind_types .= "s";
    $bind_params[] = $month_to_print;
    $print_title = "STRUK PENJUALAN TIKET BULAN INI";
    $print_filter_text = "Bulan: **" . date('F Y', strtotime($month_to_print . '-01')) . "**";

} elseif ($print_mode_type === 'true') {
    // Mode cetak Laporan Penuh (menggunakan filter form)
    if (!empty($filter_month)) {
         // FIX: Menggunakan k.created_at
        $where_clauses[] = "DATE_FORMAT(k.created_at, '%Y-%m') = ?"; 
        $bind_types .= "s";
        $bind_params[] = $filter_month;
    }
    
    $filter_text = empty($filter_month) ? 'Semua Waktu' : date('F Y', strtotime($filter_month . '-01'));
    $search_text = empty($search_keyword) ? 'Tidak Ada' : htmlspecialchars($search_keyword);
    $print_filter_text = "Filter: **{$filter_text}** | Pencarian: **{$search_text}**";
}

// Pencarian Kata Kunci (berlaku untuk semua mode cetak)
if (!empty($search_keyword)) {
    $search = "%" . $search_keyword . "%";
    $where_clauses[] = "(p.nama LIKE ? OR p.no_penerbangan LIKE ? OR p.asal LIKE ? OR p.tujuan LIKE ? OR k.kode LIKE ?)";
    $bind_types .= "sssss";
    for ($i = 0; $i < 5; $i++) {
        $bind_params[] = $search;
    }
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";


// -----------------------------------------------------------------------
// QUERY UNTUK DETAIL LAPORAN PEMESANAN (TABEL UTAMA)
// -----------------------------------------------------------------------
$tampil_laporan_pesanan = null;
$detail_report_sql = "
    SELECT 
        k.id, k.kode, k.kelas, p.nama AS nama_pesawat, p.no_penerbangan, p.asal, p.tujuan, p.harga, u.nama AS nama_user
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
?>

<script>
window.onload = function() {
    // 1. Panggil dialog cetak
    window.print();
    
    // 2. Deteksi penutupan dialog cetak
    const mediaQueryList = window.matchMedia('print');

    // Listener (untuk peramban modern)
    if (window.matchMedia) {
        mediaQueryList.addListener(function(mql) {
            if (!mql.matches) {
                // Ketika dialog print ditutup
                window.close(); // Tutup jendela cetak
            }
        });
    }

    // Fallback (untuk peramban yang tidak mendukung listener/jika gagal)
    setTimeout(function() {
        if (!mediaQueryList.matches) {
             window.close();
        }
    }, 500); 
};
</script>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $print_title ?></title>
    <style>
        .header-color { color: #008080 !important; }
        .table-custom-header { background-color: #581845 !important; color: white; }

        @media print {
            .container-fluid { width: 100%; margin: 0; padding: 0; }
            body { font-size: 10pt; }
            .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
            .table-custom-header th { color: black !important; background-color: #f0f0f0 !important; } 
            .mb-2 { margin-bottom: 0.5rem !important; }
            .text-danger { color: #dc3545 !important; }
            .table-info { background-color: #cfe2ff !important; }
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        
        <div class="print-content">
            <h3 class="text-center header-color mb-2"><?= $print_title ?></h3>
            <p class="text-center">
                <?= $print_filter_text ?>
            </p>
            <hr>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr class="table-custom-header">
                    <th scope="col">No</th>
                    <th scope="col">Kode Booking</th>
                    <th scope="col">Nama Pemesan</th>
                    <th scope="col">Pesawat (No. Penerbangan)</th>
                    <th scope="col">Asal - Tujuan</th>
                    <th scope="col">Kelas</th>
                    <th scope="col">Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 0;
                $total_harga = 0; 
                if ($tampil_laporan_pesanan && $tampil_laporan_pesanan->num_rows > 0) {
                    while ($data = $tampil_laporan_pesanan->fetch_assoc()) {
                        $no++;
                        $total_harga += $data['harga'];
                ?>
                        <tr>
                            <th scope="row"><?= $no; ?></th>
                            <td class="fw-bold text-danger"><?= htmlspecialchars($data['kode']) ?></td>
                            <td><?= htmlspecialchars($data['nama_user']) ?></td>
                            <td><?= htmlspecialchars($data['nama_pesawat']) ?> (<?= htmlspecialchars($data['no_penerbangan']) ?>)</td>
                            <td><?= htmlspecialchars($data['asal']) ?> - <?= htmlspecialchars($data['tujuan']) ?></td>
                            <td><?= ucfirst(htmlspecialchars($data['kelas'])) ?></td>
                            <td>Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Tidak ada data pesanan yang disetujui untuk kriteria ini.</td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="table-info fw-bold">
                    <td colspan="6" class="text-end">TOTAL PENJUALAN</td>
                    <td>Rp <?= number_format($total_harga, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>