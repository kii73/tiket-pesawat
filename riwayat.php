<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "./koneksi.php";

$user_history = [];
$user_id = null;

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    $stmt_user = $mysql->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    if ($stmt_user) {
        $stmt_user->bind_param("s", $username);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result();
        if ($user_result->num_rows > 0) {
            $user_data = $user_result->fetch_assoc();
            $user_id = (int)$user_data["id"];
        }
        $stmt_user->close();
    }
}

if (!$user_id) {
    header("Location: login.php");
    exit;
}

$query = "
    SELECT 
        k.id, p.nama, p.no_penerbangan, p.asal, p.tujuan, 
        p.waktu_berangkat, p.waktu_tiba, p.harga,
        k.kode, k.status, k.kelas
    FROM bookings k
    JOIN pesawat p ON k.id_pesawat = p.id
    WHERE k.id_user = ?
    ORDER BY k.id DESC
";

$stmt_history = $mysql->prepare($query);
if ($stmt_history) {
    $stmt_history->bind_param("i", $user_id);
    $stmt_history->execute();
    $user_history = $stmt_history->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_history->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan Anda - Kyy Air Line</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font & Animations -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f6f8fc;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(90deg, #2563eb, #1e40af);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand i {
            transform: rotate(-20deg);
        }

        /* Section Header */
        h3 span {
            background: linear-gradient(90deg, #2563eb, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Card Styling */
        .result-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            background: white;
        }

        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .airline-logo {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #eef2ff;
        }

        .flight-line {
            position: relative;
            height: 2px;
            background: #cbd5e1;
        }

        .flight-line .flight-icon {
            position: absolute;
            left: 50%;
            top: -8px;
            transform: translateX(-50%);
            color: #2563eb;
        }

        footer {
            background: linear-gradient(90deg, #2563eb, #1e40af);
            color: white;
        }

        footer a {
            color: white;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .badge {
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-white animate__animated animate__fadeInDown" href="index.php">
                <i class="bi bi-airplane me-2"></i>Kyy air line
            </a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light me-2">Home</a>
                <a href="pencarian.php" class="btn btn-outline-light me-2">Cari Tiket</a>
                <button class="btn btn-danger" onclick="handleLogOut()">Log out</button>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <section class="container py-5 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Riwayat <span>Pemesanan Anda</span></h3>
            <div class="bg-white shadow-sm rounded-pill px-3 py-2">
                <i class="bi bi-info-circle text-primary me-1"></i>
                <small>Daftar tiket yang pernah Anda pesan</small>
            </div>
        </div>

        <?php if (empty($user_history)): ?>
            <div class="text-center py-5">
                <i class="bi bi-journal-x" style="font-size: 4rem; color: #6c757d;"></i>
                <h4 class="mt-3">Riwayat Anda Masih Kosong</h4>
                <p class="text-muted">Anda belum pernah melakukan pemesanan tiket pesawat.</p>
                <a href="pencarian.php" class="btn btn-primary mt-3"><i class="bi bi-search me-2"></i>Cari Tiket Sekarang</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($user_history as $pesanan):
                    $status = strtolower($pesanan["status"]);
                    $status_class = match($status) {
                        'menunggu', 'menunggu persetujuan' => 'bg-warning text-dark',
                        'dikonfirmasi', 'disetujui' => 'bg-success',
                        'dibatalkan', 'gagal' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="result-card card animate__animated animate__fadeInUp">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="airline-logo me-2">
                                        <i class="bi bi-airplane text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?= htmlspecialchars($pesanan["nama"]) ?></h5>
                                        <small class="text-muted"><?= htmlspecialchars($pesanan["no_penerbangan"]) ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold"><?= htmlspecialchars($pesanan["kelas"]) ?></span>
                            </div>

                            <div class="flight-route text-center mb-3">
                                <div class="fw-bold"><?= htmlspecialchars($pesanan["asal"]) ?></div>
                                <div class="flight-line my-2"><div class="flight-icon"><i class="bi bi-airplane-fill"></i></div></div>
                                <div class="fw-bold"><?= htmlspecialchars($pesanan["tujuan"]) ?></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div>
                                    <small class="text-muted d-block">Kode Booking:</small>
                                    <span class="text-primary fw-bold fs-5"><?= htmlspecialchars($pesanan["kode"]) ?></span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block mb-1">Status:</small>
                                    <span class="badge <?= $status_class ?>"><?= ucfirst($pesanan["status"]) ?></span>
                                    <?php if (in_array($status, ['disetujui','dikonfirmasi'])): ?>
                                        <div class="mt-2">
<button 
    class="btn btn-sm btn-outline-primary"
    onclick="cetakLangsung(this)"
    data-nama="<?= htmlspecialchars($pesanan['nama']) ?>"
    data-no="<?= htmlspecialchars($pesanan['no_penerbangan']) ?>"
    data-asal="<?= htmlspecialchars($pesanan['asal']) ?>"
    data-tujuan="<?= htmlspecialchars($pesanan['tujuan']) ?>"
    data-berangkat="<?= htmlspecialchars($pesanan['waktu_berangkat']) ?>"
    data-tiba="<?= htmlspecialchars($pesanan['waktu_tiba']) ?>"
    data-kelas="<?= htmlspecialchars($pesanan['kelas']) ?>"
    data-kode="<?= htmlspecialchars($pesanan['kode']) ?>"
    data-harga="<?= htmlspecialchars(number_format($pesanan['harga'], 0, ',', '.')) ?>"
>
    <i class="bi bi-printer me-1"></i> Cetak Struk
</button>




                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<!-- Overlay untuk struk (disembunyikan dulu) -->
<!-- Tempat tersembunyi untuk struk -->
<div id="strukPrintArea" class="d-none"></div>

<script>
function cetakLangsung(btn) {
    // Ambil data dari tombol
    const data = {
        nama: btn.dataset.nama,
        no: btn.dataset.no,
        asal: btn.dataset.asal,
        tujuan: btn.dataset.tujuan,
        berangkat: btn.dataset.berangkat,
        tiba: btn.dataset.tiba,
        kelas: btn.dataset.kelas,
        kode: btn.dataset.kode,
        harga: btn.dataset.harga
    };

    // Template HTML struk
    const strukHTML = `
        <div class="container py-4" style="font-family:'Montserrat',sans-serif;">
            <div class="text-center border-bottom pb-3 mb-4">
                <h3 class="text-primary fw-bold"><i class="bi bi-airplane"></i> Tiket Elektronik</h3>
                <small class="text-muted">Kode Booking: <strong>${data.kode}</strong></small>
            </div>
            <h5 class="fw-bold text-primary mb-2">Detail Penerbangan</h5>
            <div class="p-3 bg-light rounded mb-3">
                <div class="d-flex justify-content-between"><span>Pesawat</span><span>${data.nama}</span></div>
                <div class="d-flex justify-content-between"><span>No. Penerbangan</span><span>${data.no}</span></div>
                <div class="d-flex justify-content-between"><span>Kelas</span><span class="badge bg-primary">${data.kelas}</span></div>
            </div>

            <h5 class="fw-bold text-primary mb-2">Rute & Jadwal</h5>
            <div class="p-3 border rounded mb-3">
                <div class="d-flex justify-content-between"><span>Asal</span><span>${data.asal}</span></div>
                <div class="d-flex justify-content-between"><span>Berangkat</span><span>${data.berangkat}</span></div>
                <hr>
                <div class="d-flex justify-content-between"><span>Tujuan</span><span>${data.tujuan}</span></div>
                <div class="d-flex justify-content-between"><span>Tiba</span><span>${data.tiba}</span></div>
            </div>

            <h5 class="fw-bold text-primary mb-2">Pembayaran</h5>
            <div class="p-3 bg-light rounded mb-4">
                <div class="d-flex justify-content-between"><span>Harga Tiket</span><span>Rp ${data.harga}</span></div>
                <div class="d-flex justify-content-between fw-bold"><span>Total Dibayar</span><span class="text-success">Rp ${data.harga}</span></div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small">Struk ini sah sebagai bukti pemesanan yang telah disetujui.</p>
            </div>
        </div>
    `;

    // Masukkan ke area print
    const printArea = document.getElementById('strukPrintArea');
    printArea.innerHTML = strukHTML;

    // Sembunyikan semua konten lain sementara
    const allContent = document.body.innerHTML;
    document.body.innerHTML = printArea.innerHTML;

    // Cetak
    window.print();

    // Setelah print selesai, kembalikan halaman seperti semula
    document.body.innerHTML = allContent;

    // Re-bind ulang script agar tombol masih berfungsi
    const scripts = document.querySelectorAll("script");
    scripts.forEach(script => {
        if (script.src) {
            const newScript = document.createElement("script");
            newScript.src = script.src;
            document.body.appendChild(newScript);
        }
    });

    // Scroll ke atas
    window.scrollTo({ top: 0 });
}
</script>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
