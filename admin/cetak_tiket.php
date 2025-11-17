<?php
// Ganti path ini jika file koneksi.php berada di lokasi yang berbeda
include "../koneksi.php";

// Set zona waktu (penting untuk konsistensi waktu)
date_default_timezone_set('Asia/Jakarta');

// --- Pengecekan ID Booking ---
if (!isset($_GET['id_booking']) || empty($_GET['id_booking'])) {
        die("ID Booking tidak ditemukan.");
}

$id_booking = $_GET['id_booking'];

// --- Query untuk mengambil detail pesanan ---
$query_detail = "SELECT
        k.id, k.kode, k.status, k.tanggal_beli, k.kelas, k.jumlah, k.total_harga,
        p.nama AS nama_pesawat, p.no_penerbangan, p.asal, p.tujuan, p.waktu_berangkat, p.waktu_tiba, p.harga AS harga_satuan,
        u.nama AS nama_user
        FROM bookings k
        LEFT JOIN pesawat p ON k.id_pesawat = p.id
        LEFT JOIN users u ON k.id_user = u.id
        WHERE k.id = ?";

$stmt = $mysql->prepare($query_detail);

if (!$stmt) {
        die("Gagal menyiapkan statement: " . $mysql->error);
}

$stmt->bind_param("i", $id_booking);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
// Tutup koneksi setelah query selesai
if (isset($mysql)) {
    $mysql->close();
}

if (!$data) {
        die("Data booking tidak ditemukan.");
}

// --- Pengecekan Status Tiket ---
if ($data['status'] !== 'disetujui') {
        die("Tiket ini belum disetujui atau masih menunggu pembayaran.");
}

// --- Fungsi Helper ---
function formatTanggalWaktu($timestamp) {
        return date('d M Y, H:i', strtotime($timestamp));
}
function formatRupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
}

?>
<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tiket Pesawat - <?= htmlspecialchars($data['kode']) ?></title>
        <style>
                /* Gaya untuk tampilan Boarding Pass */
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
                .ticket-container { max-width: 700px; margin: 20px auto; background-color: white; border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                .ticket-header { background-color: #581845; color: white; padding: 15px; text-align: center; border-bottom: 5px solid #008080; }
                .ticket-header h2 { margin: 0; font-size: 1.5em; }
                .ticket-body { padding: 25px; display: flex; flex-wrap: wrap; }
                .section { width: 50%; padding-right: 15px; box-sizing: border-box; margin-bottom: 20px; }
                .section h4 { color: #008080; border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 0; }
                .section p { margin: 8px 0; font-size: 0.95em; }
                .highlight { font-weight: bold; color: #581845; font-size: 1.1em; }
                .rute { font-size: 1.1em; font-weight: bold; color: #333; }
                .separator { width: 100%; border-top: 1px dashed #ccc; margin: 10px 0; }

                /* Detail Pembelian (Tabel) */
                .info-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                .info-table th, .info-table td { padding: 10px; text-align: left; border: 1px solid #f0f0f0; }
                .info-table th { background-color: #f0f0f0; color: #333; }
                .info-table tr:nth-child(even) { background-color: #f9f9f9; }
                .text-right { text-align: right; }

                /* Print Area (Dihilangkan saat cetak) */
                .print-area { text-align: center; padding: 20px; }
                .print-btn { display: none; /* Tombol ini tidak lagi diperlukan karena otomatis */ }

                /* Media Print (Menghilangkan elemen non-tiket saat dicetak) */
                @media print {
                        body { background-color: white !important; padding: 0; }
                        .ticket-container { box-shadow: none; border: none; margin: 0; }
                        .print-area { display: none; } /* Pastikan area ini tersembunyi */
                }
        </style>
</head>
<body onload="window.print()"> 

<div class="ticket-container">
        <div class="ticket-header">
                <h2>✈️ Boarding Pass / Tiket Pesawat</h2>
        </div>

        <div class="ticket-body">
                
                <div class="section" style="width: 100%;">
                        <h4>Informasi Penerbangan</h4>
                        <p class="rute"><?= htmlspecialchars($data['asal']) ?> &rarr; <?= htmlspecialchars($data['tujuan']) ?></p>
                        <p><strong>Maskapai:</strong> <span class="highlight"><?= htmlspecialchars($data['nama_pesawat']) ?></span></p>
                        <p><strong>No. Penerbangan:</strong> <span class="highlight"><?= htmlspecialchars($data['no_penerbangan']) ?></span></p>
                        <p><strong>Kelas:</strong> <?= htmlspecialchars($data['kelas']) ?></p>
                </div>
                
                <div class="separator"></div>

                <div class="section">
                        <h4>Detail Penumpang & Tiket</h4>
                        <p><strong>Nama Penumpang:</strong> <span class="highlight"><?= htmlspecialchars($data['nama_user']) ?></span></p>
                        <p><strong>Kode Booking:</strong> <span class="highlight"><?= htmlspecialchars($data['kode']) ?></span></p>
                        <p><strong>Jumlah Tiket:</strong> <?= htmlspecialchars($data['jumlah']) ?> Pax</p>
                </div>

                <div class="section">
                        <h4>Waktu</h4>
                        <p><strong>Keberangkatan:</strong> <?= formatTanggalWaktu($data['waktu_berangkat']) ?></p>
                        <p><strong>Kedatangan:</strong> <?= formatTanggalWaktu($data['waktu_tiba']) ?></p>
                        <p><strong>Tanggal Beli:</strong> <?= formatTanggalWaktu($data['tanggal_beli']) ?></p>
                </div>

                <div class="separator"></div>

                <div class="section" style="width: 100%;">
                        <h4>Detail Pembayaran</h4>
                        <table class="info-table">
                                <thead>
                                        <tr>
                                                <th>Deskripsi</th>
                                                <th class="text-right">Harga</th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                                <td>Harga Satuan</td>
                                                <td class="text-right"><?= formatRupiah($data['harga_satuan']) ?></td>
                                        </tr>
                                        <tr>
                                                <td>Jumlah Beli</td>
                                                <td class="text-right">x <?= htmlspecialchars($data['jumlah']) ?></td>
                                        </tr>
                                        <tr>
                                                <td class="highlight">TOTAL HARGA (LUNAS)</td>
                                                <td class="text-right highlight"><?= formatRupiah($data['total_harga']) ?></td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
        </div>
        
        <div class="print-area">
                <p style="color: #888;">Harap tunjukkan kode booking ini saat check-in.</p>
                        </div>
</div>

<script>
    // PENTING: Gunakan onafterprint untuk menutup tab/window setelah dialog cetak selesai.
    window.onafterprint = function() {
        window.close(); 
    }
</script>
</body>
</html>