<?php
session_start();

/**
 * ASUMSI: File koneksi.php menyediakan objek koneksi mysqli yang disimpan
 * dalam variabel $mysql (sesuai dengan penggunaan $mysql->prepare).
 */
include "./koneksi.php";

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$pilih = null;
$user = null;

if (isset($_GET["pesawat"])) {
    // Ambil input dan session
    $slug = $_GET["pesawat"];
    $username_session = $_SESSION["username"];

    // =========================================================================
    // FIX KEAMANAN KRITIS: Menggunakan Prepared Statements untuk mencegah SQL Injection
    // =========================================================================

    // 2. Ambil data penerbangan (pesawat)
    if ($stmt_pesawat = $mysql->prepare("SELECT * FROM pesawat WHERE id = ?")) {
        // 's' menandakan parameter adalah string (walaupun id biasanya INT, 
        // tapi 's' aman, atau gunakan 'i' jika Anda yakin 'id' adalah INT)
        $stmt_pesawat->bind_param("s", $slug);
        $stmt_pesawat->execute();
        $result_pesawat = $stmt_pesawat->get_result();
        $pilih = $result_pesawat->fetch_assoc();
        $stmt_pesawat->close();
    } else {
        die("Error mempersiapkan query penerbangan: " . $mysql->error);
    }

    // 3. Ambil data user
    if ($stmt_user = $mysql->prepare("SELECT * FROM users WHERE username = ?")) {
        $stmt_user->bind_param("s", $username_session);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $user = $result_user->fetch_assoc();
        $stmt_user->close();
    } else {
        die("Error mempersiapkan query user: " . $mysql->error);
    }

    // 4. Cek hasil query
    if (!$pilih) {
        die("Data penerbangan tidak ditemukan atau link tidak valid.");
    }
    if (!$user) {
        session_destroy();
        header("Location: login.php?error=user_data_missing");
        exit();
    }
} else {
    // Arahkan jika parameter 'pesawat' tidak ada
    header("Location: pencarian.php");
    exit();
}

// 5. Kalkulasi Harga
$base_price = (int)$pilih["harga"];
$tax = 150000;
$initial_total = $base_price + $tax;

// Fungsi untuk memformat waktu (jika hanya waktu yang tersimpan)
function format_time($time) {
    return date("H:i", strtotime($time));
}

// Fungsi untuk memformat tanggal (jika tanggal terpisah)
function format_date($date) {
    return date("Y-m-d", strtotime($date));
}
?>


<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Data & Penerbangan - Tiket Pesawat</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="./assets/css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'%3E%3Cpath d='M21.5,4.5L3.5,11.5C2.7,11.8,2.7,12.9,3.5,13.2L7.5,15L9.5,19.5C9.8,20.3,10.9,20.3,11.2,19.5L12.5,16.5L16.5,20.5C17.1,21.1,18.1,20.7,18.1,19.9V16.5L21.5,15.5C22.3,15.2,22.3,14.1,21.5,13.8L16.5,11.5L21.5,4.5Z'/%3E%3C/svg%3E">
    <style>
        body {
            background: linear-gradient(120deg, #1a73e8 0%, #67c6ff 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            min-height: 100vh;
        }

        .konfirmasi-card {
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

        .btn-konfirmasi {
            background: linear-gradient(90deg, #1a73e8 60%, #67c6ff 100%);
            border: none;
            font-weight: 700;
            color: #fff;
        }

        .btn-konfirmasi:hover {
            background: linear-gradient(90deg, #1669c1 60%, #4bb7f5 100%);
            color: #fff;
        }

        .form-label {
            color: #1a73e8;
            font-weight: 500;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a73e8;
            margin-bottom: 12px;
            margin-top: 18px;
        }

        input:disabled,
        select:disabled {
            cursor: not-allowed;
        }

        /* Modern styles */
        .navbar {
            background: rgba(26, 115, 232, 0.9);
            backdrop-filter: blur(10px);
        }

        .booking-progress {
            margin-bottom: 2rem;
        }

        .booking-step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #1a73e8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
        }

        .booking-step.active .step-number {
            background: #1a73e8;
            color: white;
        }

        .booking-step.completed .step-number {
            background: #10b981;
            color: white;
        }

        .text-primary-gradient {
            background: linear-gradient(90deg, #1a73e8 0%, #67c6ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand text-white animate__animated animate__fadeIn" href="index.php">
                </i>Kyy air line
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item animate__animated animate__fadeIn animate__delay-1s">
                        <a class="nav-link text-white" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item animate__animated animate__fadeIn animate__delay-2s">
                        <a class="nav-link text-white" href="pencarian.php"><i class="bi bi-search me-1"></i>Cari Tiket</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card konfirmasi-card p-4 animate__animated animate__fadeIn">
                    <div class="plane-icon animate__animated animate__zoomIn">
                        <svg viewBox="0 0 48 48">

                            <path d="M44.5 6.5L3.5 22.5C2.7 22.8 2.7 23.9 3.5 24.2L13.5 28.5L20.5 44.5C20.8 45.3 21.9 45.3 22.2 44.5L26.5 34.5L36.5 44.5C37.1 45.1 38.1 44.7 38.1 43.9V34.5L44.5 31.5C45.3 31.2 45.3 30.1 44.5 29.8L34.5 25.5L44.5 6.5Z" />

                        </svg>
                    </div>
                    <h2 class="text-center mb-3 fw-bold">Isi <span class="text-primary-gradient">Data & Detail Penerbangan</span></h2>
                    <p class="text-muted text-center mb-4">Silakan lengkapi data pemesanan tiket Anda</p>

                    <div class="booking-progress mb-5">
                        <div class="d-flex justify-content-between">
                            <div class="booking-step completed text-center">
                                <div class="step-number">1</div>
                                <div class="step-label">Pencarian</div>
                            </div>
                            <div class="booking-step active text-center">
                                <div class="step-number">2</div>
                                <div class="step-label">Konfirmasi</div>
                            </div>
                            <div class="booking-step text-center">
                                <div class="step-number">3</div>
                                <div class="step-label">Pembayaran</div>
                            </div>
                        </div>
                    </div>


                    <form class="needs-validation" action="kode.php" method="POST">
                        <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
                        <div class="card mb-4 border-0 shadow-sm" data-animate="animate-fade-in">
                            <div class="card-header bg-primary bg-opacity-10 border-0">
                                <h5 class="mb-0"><i class="bi bi-airplane me-2 text-primary"></i>Data Penerbangan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="asal" class="form-label">Kota Asal</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" disabled class="form-control" id="asal" name="asal" value="<?= htmlspecialchars($pilih["asal"]) ?>" placeholder="Contoh: Jakarta" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="tujuan" class="form-label">Kota Tujuan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt-fill"></i></span>
                                            <input type="text" disabled class="form-control" id="tujuan" name="tujuan" value="<?= htmlspecialchars($pilih["tujuan"]) ?>" placeholder="Contoh: Surabaya" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="tanggal" class="form-label">Tanggal Keberangkatan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                                            <input type="date" class="form-control" disabled id="tanggal" value="<?= htmlspecialchars($pilih["tanggal_berangkat"]) ?>" name="tanggal" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="waktu_berangkat" class="form-label">Waktu Berangkat</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-clock"></i></span>
                                            <input type="time" class="form-control" disabled id="waktu_berangkat" value="<?= format_time($pilih["waktu_berangkat"]) ?>" name="waktu_berangkat" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="waktu_tiba" class="form-label">Waktu Tiba</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-clock-history"></i></span>
                                            <input type="time" class="form-control" disabled id="waktu_tiba" value="<?= format_time($pilih["waktu_tiba"]) ?>" name="waktu_tiba" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="maskapai" class="form-label">Maskapai</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-airplane"></i></span>
                                            <input type="text" class="form-control" disabled id="maskapai" value="<?= htmlspecialchars($pilih["nama"]) ?>" name="maskapai" placeholder="Contoh: Garuda Indonesia" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="kelas" class="form-label">Kelas Penerbangan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-star"></i></span>
                                            <select class="form-select" id="kelas" name="kelas" required>
                                                <option value="">Pilih kelas</option>
                                                <option value="Ekonomi">Ekonomi</option>
                                                <option value="Bisnis">Bisnis</option>
                                                <option value="First Class">First Class</option>
                                            </select>
                                            <div class="invalid-feedback">Pilih kelas penerbangan</div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="jumlah" class="form-label">Jumlah Penumpang</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-people"></i></span>
                                            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" max="10" value="1" required>
                                            <div class="invalid-feedback">Masukkan jumlah penumpang</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-0 shadow-sm" data-animate="animate-fade-in" data-delay="delay-100">
                            <div class="card-header bg-primary bg-opacity-10 border-0">
                                <h5 class="mb-0"><i class="bi bi-person-vcard me-2 text-primary"></i>Data Penumpang</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                        <input type="text" value="<?= htmlspecialchars($user["nama"]) ?>" disabled class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                                        <div class="invalid-feedback">Masukkan nama lengkap</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="gender" class="form-label">Jenis Kelamin</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-gender-ambiguous"></i></span>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="">Pilih jenis kelamin</option>
                                            <option value="Laki-laki" <?= (isset($user["gender"]) && $user["gender"] == "Laki-laki") ? "selected" : "" ?>>Laki-laki</option>
                                            <option value="Perempuan" <?= (isset($user["gender"]) && $user["gender"] == "Perempuan") ? "selected" : "" ?>>Perempuan</option>
                                        </select>
                                        <div class="invalid-feedback">Pilih jenis kelamin</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Nomor HP</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-phone"></i></span>
                                        <input type="number" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user["phone"] ?? '') ?>" placeholder="Masukkan nomor HP aktif" required>
                                        <div class="invalid-feedback">Masukkan nomor HP</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card mb-4 border-0 shadow-sm" data-animate="animate-fade-in" data-delay="delay-200">
                            <div class="card-header bg-primary bg-opacity-10 border-0">
                                <h5 class="mb-0"><i class="bi bi-cash-coin me-2 text-primary"></i>Ringkasan Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Harga Tiket per Orang</span>
                                    <span>Rp <?= number_format($base_price, 0, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Jumlah Penumpang</span>
                                    <span id="summary-passengers">1</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Pajak & Biaya Layanan</span>
                                    <span>Rp <?= number_format($tax, 0, ',', '.') ?></span>
                                </div>

                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total Pembayaran</span>
                                    <span class="text-primary" id="total-price">Rp <?= number_format($initial_total, 0, ',', '.') ?></span>
                                </div>

                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-konfirmasi btn-lg mt-2 animate__animated animate__pulse animate__infinite animate__slower">
                                <i class="bi bi-check-circle me-2"></i>Konfirmasi & Lanjut Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/main.js"></script>
    <script>
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        
        document.getElementById('jumlah').addEventListener('change', function() {
            
            const basePrice = <?= $base_price ?>;
            const tax = <?= $tax ?>;
            // Pastikan nilai adalah integer positif
            const passengers = Math.max(1, parseInt(this.value) || 1); 

            // Kalkulasi ulang total harga
            const total = (basePrice * passengers) + tax;

            document.getElementById('summary-passengers').textContent = passengers;
            // Menggunakan toLocaleString untuk format angka Indonesia
            document.getElementById('total-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
        });

        // Efek Animasi
        const animateElements = document.querySelectorAll('[data-animate]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const animation = el.getAttribute('data-animate');
                    const delay = el.getAttribute('data-delay') || '';
                    el.classList.add('animate__animated', animation, delay);
                    observer.unobserve(el);
                }
            });
        }, {
            threshold: 0.1
        });

        animateElements.forEach(el => observer.observe(el));
    </script>
</body>

</html>