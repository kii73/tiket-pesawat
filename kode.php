<?php

include "./koneksi.php";

function generateBookingCode(int $length = 6): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $maxIndex = strlen($alphabet) - 1;
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $idx = random_int(0, $maxIndex);
        $code .= $alphabet[$idx];
    }

    return $code;
}

$remember_token = $_COOKIE["remember_token"];
$booking_code = "-";
$slug = $_POST["slug"];

$user = [
    "user" => [],
    "kode" => [[]]
];

$user_1 = $mysql->query("SELECT * FROM users WHERE remember_token='$remember_token'")->fetch_assoc();
$user_id = $user_1["id"];
$user["user"] = $user_1;
$bookings = $mysql->query("SELECT * FROM kode WHERE id_user=$user_id");
$i = 0;
while ($row = $bookings->fetch_assoc()) {
    foreach ($row as $key => $value) {
        $user["kode"][$i][$key] = $value;
    }
    $i++;
}

$pesawat = $mysql->query("SELECT * FROM pesawat WHERE slug='$slug'")->fetch_assoc();
$id_pesawat = $pesawat["id"];

$isBooking = array_filter($user["kode"], function ($item) use ($id_pesawat) {
    if (!isset($item["id_pesawat"])) return false;
    return $item["id_pesawat"] == $id_pesawat;
});

if (!empty($isBooking)) {
    $booking_code = $mysql->query("SELECT * FROM users u LEFT JOIN kode k ON k.id_user = u.id LEFT JOIN pesawat p ON p.id = $id_pesawat WHERE remember_token='$remember_token'")->fetch_assoc()["kode"];
} else {
    $booking_code = generateBookingCode();
    $mysql->query("INSERT INTO `kode`(`id_pesawat`, `id_user`, `kode`) VALUES ($id_pesawat, $user_id, '$booking_code')");
}

?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <svg viewBox="0 0 48 48">
                            <path d="M44.5 6.5L3.5 22.5C2.7 22.8 2.7 23.9 3.5 24.2L13.5 28.5L20.5 44.5C20.8 45.3 21.9 45.3 22.2 44.5L26.5 34.5L36.5 44.5C37.1 45.1 38.1 44.7 38.1 43.9V34.5L44.5 31.5C45.3 31.2 45.3 30.1 44.5 29.8L34.5 25.5L44.5 6.5Z" />
                        </svg>
                    </div>
                    <h2 class="text-center mb-3" style="color:#1a73e8;font-weight:700;">Pesanan Berhasil!</h2>
                    <p class="text-center mb-2">Terima kasih, pesanan tiket pesawat Anda telah dikonfirmasi.</p>
                    <div class="kode-unik" id="kodeUnik"><?= $booking_code ?></div>
                    <p class="text-center text-muted mb-4">Simpan kode unik ini untuk proses check-in atau konfirmasi pembayaran.</p>
                    <div class="d-flex justify-content-around">
                        <a href="index.php" class="btn btn-home px-4">Beranda</a>
                        <button onclick="handleCopy(this)" class="btn btn-primary px-4">Salin</button>
                        <button onclick="window.print();" class="btn btn-primary px-4">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function handleCopy(e) {
            const code = '<?= $booking_code ?>';

            alert("Kode disalin");

            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                return navigator.clipboard.writeText(code);
            }

            const textarea = document.createElement('textarea');

            textarea.style.position = 'fixed';
            textarea.style.top = '-9999px';
            textarea.style.left = '-9999px';
            textarea.setAttribute('readonly', '');
            textarea.value = text;
            document.body.appendChild(textarea);

            textarea.select();
            textarea.setSelectionRange(0, textarea.value.length);

            const successful = document.execCommand('copy');
            document.body.removeChild(textarea);
        }
    </script>
</body>

</html>