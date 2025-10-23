<?php
session_start();

include "./koneksi.php";

// Fungsi generate booking code (huruf besar) dan memastikan unik (cek DB)
function generateBookingCode(mysqli $mysql, int $length = 6): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $maxIndex = strlen($alphabet) - 1;

    // loop sampai ketemu kode unik (batasi percobaan)
    for ($attempt = 0; $attempt < 10; $attempt++) {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $idx = random_int(0, $maxIndex);
            $code .= $alphabet[$idx];
        }

        // cek unik
        // Catatan: Menggunakan nama tabel 'kode' seperti yang Anda gunakan
        $stmt = $mysql->prepare("SELECT COUNT(*) AS cnt FROM bookings WHERE kode = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ((int)$res['cnt'] === 0) {
            return $code;
        }
    }

    // fallback: gunakan uniqid jika 10 percobaan gagal (sangat jarang)
    return strtoupper(substr(uniqid('', true), -$length));
}

$kelas = $_POST['kelas'];
// Ambil id dari GET atau POST (prefer GET untuk tampilan)
$id_param = null;
if (isset($_GET['slug'])) {
    // ID seharusnya integer, tidak perlu real_escape_string jika langsung cast
    $id_param = (int)$_GET['slug'];
} elseif (isset($_POST['slug'])) {
    $id_param = (int)$_POST['slug'];
}

if (empty($id_param)) {
    // id wajib
    http_response_code(400);
    echo "Parameter id tidak ditemukan.";
    exit;
}

// Ambil user dari remember_token cookie (blok ini tidak diubah)
if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION["user_id"];

// ambil user
$stmt = $mysql->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user_row = $res->fetch_assoc();
$stmt->close();

if (!$user_row) {
    // token tidak valid
    header("Location: login.php");
    exit;
}

$user_id = (int)$user_row['id'];

// ambil data pesawat berdasarkan ID
// Query diperbaiki: Menggunakan 'id' dan kolom-kolom yang sesuai skema SQL
$stmt = $mysql->prepare("SELECT id, nama, no_penerbangan, asal, tujuan, waktu_berangkat, waktu_tiba, harga, kursi_tersedia FROM pesawat WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id_param); // 'i' untuk id (integer)
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

// cek apakah user sudah punya booking untuk pesawat ini
$stmt = $mysql->prepare("SELECT * FROM bookings WHERE id_user = ? AND id_pesawat = ? LIMIT 1");
$stmt->bind_param("ii", $user_id, $id_pesawat);
$stmt->execute();
$res = $stmt->get_result();
$existing_booking = $res->fetch_assoc();
$stmt->close();

$booking_code = "-";
$status_booking = 'Menunggu persetujuan'; // default display (user friendly)

// jika sudah ada booking -> tampilkan info booking (tidak membuat booking baru)
if ($existing_booking) {
    $booking_code = $existing_booking['kode'] ?? "-";
    // normalisasi status untuk tampilan
    $status_booking = isset($existing_booking['status']) ? ucfirst($existing_booking['status']) : 'Menunggu persetujuan';
} else {
    // belum booking -> buat booking baru dengan proteksi stok
    try {
        // mulai transaction
        $mysql->begin_transaction();

        // kunci baris pesawat (FOR UPDATE) untuk mencegah race condition
        // Mengganti 'available_seat' menjadi 'kursi_tersedia'
        $stmt = $mysql->prepare("SELECT kursi_tersedia FROM pesawat WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $id_pesawat);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if (!$row) {
            // pesawat hilang setelah query awal (unlikely)
            $mysql->rollback();
            throw new Exception("Data penerbangan tidak ditemukan saat proses booking.");
        }

        $available_seat = (int)$row['kursi_tersedia'];
        if ($available_seat <= 0) {
            // tidak tersedia
            $mysql->rollback();
            $booking_code = "-";
            $status_booking = 'Stok penuh';
        } else {
            // buat kode booking unik (tabel kode)
            $new_code = generateBookingCode($mysql, 6);

            // insert booking (status 'menunggu' default)
            $stmt = $mysql->prepare("INSERT INTO bookings (id_pesawat, id_user, kode, status, kelas) VALUES (?, ?, ?, 'menunggu', ?)");
            $stmt->bind_param("iiss", $id_pesawat, $user_id, $new_code, $kelas);
            $stmt->execute();
            $insert_id = $stmt->insert_id;
            $stmt->close();

            if ($insert_id <= 0) {
                $mysql->rollback();
                throw new Exception("Gagal menyimpan booking.");
            }

            $jumlah_penumpang = $_POST["jumlah"];
            // kurangi kursi_tersedia (Mengganti 'available_seat')
            $stmt = $mysql->prepare("UPDATE pesawat SET kursi_tersedia = kursi_tersedia - $jumlah_penumpang WHERE id = ?");
            $stmt->bind_param("i", $id_pesawat);
            $stmt->execute();
            $stmt->close();

            $mysql->commit();

            $booking_code = $new_code;
            $status_booking = 'Menunggu persetujuan';
        }
    } catch (Exception $e) {
        if ($mysql->errno) {
            // jika transaction masih aktif atau error, rollback
            @$mysql->rollback();
        }
        // Log error di server (jika ada mekanisme logging)
        error_log("Booking error: " . $e->getMessage());
        var_dump($e);
        // Tampilkan pesan sopan ke user
        $booking_code = "-";
        $status_booking = 'Terjadi kesalahan. Coba lagi nanti.';
    }
}

// untuk tampilan, escape
$booking_code_html = htmlspecialchars($booking_code, ENT_QUOTES, 'UTF-8');
$status_booking_html = htmlspecialchars($status_booking, ENT_QUOTES, 'UTF-8');
// Label penerbangan disesuaikan dengan kolom 'no_penerbangan', 'asal', dan 'tujuan'
$flight_label = htmlspecialchars(($pesawat['no_penerbangan'] ?? '') . " — " . ($pesawat['asal'] ?? '') . " → " . ($pesawat['tujuan'] ?? ''), ENT_QUOTES, 'UTF-8');

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

                    <p class="text-center text-muted mb-2">Simpan kode unik ini untuk proses check-in atau konfirmasi pembayaran.</p>

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