<?php
// TAMPILKAN SEMUA ERROR UNTUK DEBUGGING (HAPUS SETELAH BERHASIL)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include "./koneksi.php";

// Fungsi generate booking code (huruf besar) dan memastikan unik (cek DB)
function generateBookingCode(mysqli $mysql, int $length = 6): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $maxIndex = strlen($alphabet) - 1;

    for ($attempt = 0; $attempt < 10; $attempt++) {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $idx = random_int(0, $maxIndex);
            $code .= $alphabet[$idx];
        }

        $stmt = $mysql->prepare("SELECT COUNT(*) AS cnt FROM bookings WHERE kode = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ((int)$res['cnt'] === 0) {
            return $code;
        }
    }

    return strtoupper(substr(uniqid('', true), -$length));
}

// -----------------------------------------------------------------------
// 1. PENGAMBILAN INPUT DAN VALIDASI AWAL
// -----------------------------------------------------------------------
$kelas = $_POST['kelas'] ?? 'ekonomi'; // Default 'ekonomi'
$jumlah_penumpang = (int)($_POST["jumlah"] ?? 1); // Ambil jumlah tiket, default 1
if ($jumlah_penumpang <= 0) {
    $jumlah_penumpang = 1;
}

$id_param = null;
if (isset($_GET['slug'])) {
    $id_param = (int)$_GET['slug'];
} elseif (isset($_POST['slug'])) {
    $id_param = (int)$_POST['slug'];
}

if (empty($id_param)) {
    http_response_code(400);
    echo "Parameter id tidak ditemukan.";
    exit;
}

if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION["user_id"];

// ambil user
$stmt = $mysql->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user_row = $res->fetch_assoc();
$stmt->close();

if (!$user_row) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$user_row['id'];

// ambil data pesawat berdasarkan ID
$stmt = $mysql->prepare("SELECT id, nama, no_penerbangan, asal, tujuan, waktu_berangkat, waktu_tiba, harga, kursi_tersedia FROM pesawat WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id_param); 
$stmt->execute();
$res = $stmt->get_result();
$pesawat = $res->fetch_assoc();
$stmt->close();

if (!$pesawat) {
    http_response_code(404);
    echo "Penerbangan tidak ditemukan.";
    exit;
}

$id_pesawat = (int)$pesawat['id'];
$harga_satuan = (int)($pesawat['harga'] ?? 0);

// HITUNG TOTAL HARGA DI SINI
$total_harga = $harga_satuan * $jumlah_penumpang; 

// cek apakah user sudah punya booking untuk pesawat ini
$stmt = $mysql->prepare("SELECT kode, status, jumlah, total_harga FROM bookings WHERE id_user = ? AND id_pesawat = ? LIMIT 1");
$stmt->bind_param("ii", $user_id, $id_pesawat);
$stmt->execute();
$res = $stmt->get_result();
$existing_booking = $res->fetch_assoc();
$stmt->close();

$booking_code = "-";
$status_booking = 'Menunggu persetujuan'; 

if ($existing_booking) {
    // Jika sudah ada booking
    $booking_code = $existing_booking['kode'] ?? "-";
    $status_booking = isset($existing_booking['status']) ? ucfirst($existing_booking['status']) : 'Menunggu persetujuan';
    $jumlah_penumpang = (int)($existing_booking['jumlah'] ?? $jumlah_penumpang);
    $total_harga = (int)($existing_booking['total_harga'] ?? $total_harga);
} else {
    // -----------------------------------------------------------------------
    // Booking Baru
    // -----------------------------------------------------------------------
    try {
        // mulai transaction
        $mysql->begin_transaction();

        // kunci baris pesawat (FOR UPDATE)
        $stmt = $mysql->prepare("SELECT kursi_tersedia FROM pesawat WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $id_pesawat);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if (!$row) {
            $mysql->rollback();
            throw new Exception("Data penerbangan tidak ditemukan saat proses booking.");
        }

        $available_seat = (int)$row['kursi_tersedia'];
        if ($available_seat < $jumlah_penumpang) {
            // kursi tidak cukup
            $mysql->rollback();
            $booking_code = "-";
            $status_booking = 'Kursi tidak cukup';
        } else {
            // buat kode booking unik
            $new_code = generateBookingCode($mysql, 6);

            // Query INSERT harus menyertakan jumlah dan total_harga, dan TIDAK ADA TYPO.
            $stmt = $mysql->prepare("INSERT INTO bookings (id_pesawat, id_user, kode, status, kelas, jumlah, total_harga) VALUES (?, ?, ?, 'menunggu', ?, ?, ?)");
            
            // bind_param harus menyertakan 6 variabel (iissii)
            $stmt->bind_param("iissii", 
                $id_pesawat, 
                $user_id, 
                $new_code, 
                $kelas, 
                $jumlah_penumpang, 
                $total_harga // Variabel ke-6
            );
            $stmt->execute(); 
            $insert_id = $stmt->insert_id;
            $stmt->close();

            if ($insert_id <= 0) {
                $mysql->rollback();
                throw new Exception("Gagal menyimpan booking.");
            }

            // Gunakan prepared statement yang benar untuk UPDATE
            $stmt = $mysql->prepare("UPDATE pesawat SET kursi_tersedia = kursi_tersedia - ? WHERE id = ?");
            $stmt->bind_param("ii", $jumlah_penumpang, $id_pesawat);
            $stmt->execute();
            $stmt->close();

            $mysql->commit();

            $booking_code = $new_code;
            $status_booking = 'Menunggu persetujuan';
        }
    } catch (Exception $e) {
        if ($mysql->errno) {
            @$mysql->rollback();
        }
        error_log("Booking error: " . $e->getMessage());
        
        // 🚨 TAMPILKAN DETAIL ERROR SECARA EKSPLISIT 🚨
        echo "TERJADI KESALAHAN PADA PROSES BOOKING:\n";
        var_dump($e);
        exit; // Hentikan eksekusi setelah menampilkan error
        
        $booking_code = "-";
        $status_booking = 'Terjadi kesalahan. Coba lagi nanti.';
    }
}

// untuk tampilan, escape
$booking_code_html = htmlspecialchars($booking_code, ENT_QUOTES, 'UTF-8');
$status_booking_html = htmlspecialchars($status_booking, ENT_QUOTES, 'UTF-8');
$flight_label = htmlspecialchars(($pesawat['no_penerbangan'] ?? '') . " — " . ($pesawat['asal'] ?? '') . " → " . ($pesawat['tujuan'] ?? ''), ENT_QUOTES, 'UTF-8');
$jumlah_penumpang_html = number_format($jumlah_penumpang, 0, ',', '.');
$total_harga_html = number_format($total_harga, 0, ',', '.');


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Konfirmasi Pesanan - Tiket Pesawat</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #1a73e8 0%, #67c6ff 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            min-height: 100vh;
        }

        .kode-card {
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(26, 115, 232, 0.13);
            margin-top: 80px;
            margin-bottom: 80px;
            overflow: hidden;
        }

        .plane-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 18px;
        }

        .plane-icon svg {
            width: 48px;
            height: 48px;
            fill: #1a73e8;
            filter: drop-shadow(0 2px 8px #67c6ff55);
        }

        .kode-unik {
            font-size: 2rem;
            font-weight: 700;
            color: #1a73e8;
            letter-spacing: 2px;
            background: #e3f0fc;
            border-radius: 8px;
            padding: 16px 0;
            margin: 24px 0 12px 0;
            text-align: center;
            user-select: all;
        }

        .btn-home {
            background: linear-gradient(90deg, #1a73e8 60%, #67c6ff 100%);
            border: none;
            font-weight: 700;
            color: #fff;
        }

        .btn-home:hover {
            background: linear-gradient(90deg, #1669c1 60%, #4bb7f5 100%);
            color: #fff;
        }
        .summary-box {
            background-color: #f7f9fc;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid #e3f0fc;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-item strong {
            color: #1a73e8;
        }
        .summary-item.total {
            border-top: 1px dashed #ccc;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card kode-card p-4">
                    <div class="plane-icon">
                        <svg viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M44.5 6.5L3.5 22.5C2.7 22.8 2.7 23.9 3.5 24.2L13.5 28.5L20.5 44.5C20.8 45.3 21.9 45.3 22.2 44.5L26.5 34.5L36.5 44.5C37.1 45.1 38.1 44.7 38.1 43.9V34.5L44.5 31.5C45.3 31.2 45.3 30.1 44.5 29.8L34.5 25.5L44.5 6.5Z" />
                        </svg>
                    </div>

                    <h2 class="text-center mb-1" style="color:#1a73e8;font-weight:700;">Pesanan Berhasil!</h2>
                    <p class="text-center mb-2">Terima kasih, pesanan tiket pesawat Anda telah diproses.</p>

                    <div class="text-center mb-2 small text-muted"><?= $flight_label ?></div>

                    <div class="kode-unik" id="kodeUnik"><?= $booking_code_html ?></div>

                    <div class="summary-box">
                        <div class="summary-item">
                            <span>Harga Satuan:</span>
                            <span>Rp <?= number_format($harga_satuan, 0, ',', '.') ?></span>
                        </div>
                        <div class="summary-item">
                            <span>**Jumlah Tiket:**</span>
                            <span>**<?= $jumlah_penumpang_html ?>**</span>
                        </div>
                        <div class="summary-item total">
                            <strong>TOTAL HARGA:</strong>
                            <strong>Rp <?= $total_harga_html ?></strong>
                        </div>
                    </div>
                    <p class="text-center text-muted mt-3 mb-2">Simpan kode unik ini untuk proses check-in atau konfirmasi pembayaran.</p>

                    <div class="text-center mb-4">
                        <span class="badge bg-warning text-dark">
                            Status: <?= $status_booking_html ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-around">
                        <a href="index.php" class="btn btn-home px-4">Beranda</a>
                        <button onclick="handleCopy()" class="btn btn-primary px-4">Salin</button>
                        <button onclick="window.print();" class="btn btn-primary px-4">Print</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function handleCopy() {
            const code = '<?= $booking_code_html ?>';
            if (!code || code === '-') {
                alert("Tidak ada kode untuk disalin.");
                return;
            }
            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                navigator.clipboard.writeText(code).then(function() {
                    alert("Kode disalin");
                }, function() {
                    alert("Gagal menyalin kode");
                });
                return;
            }
            // fallback older browsers
            const textarea = document.createElement('textarea');
            textarea.style.position = 'fixed';
            textarea.style.top = '-9999px';
            textarea.style.left = '-9999px';
            textarea.setAttribute('readonly', '');
            textarea.value = code;
            document.body.appendChild(textarea);
            textarea.select();
            textarea.setSelectionRange(0, textarea.value.length);
            try {
                document.execCommand('copy');
                alert("Kode disalin");
            } catch (e) {
                alert("Gagal menyalin kode");
            }
            document.body.removeChild(textarea);
        }
    </script>
</body>

</html>