<?php

include "./proteksi.php";

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Pesawat Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'%3E%3Cpath d='M21.5,4.5L3.5,11.5C2.7,11.8,2.7,12.9,3.5,13.2L7.5,15L9.5,19.5C9.8,20.3,10.9,20.3,11.2,19.5L12.5,16.5L16.5,20.5C17.1,21.1,18.1,20.7,18.1,19.9V16.5L21.5,15.5C22.3,15.2,22.3,14.1,21.5,13.8L16.5,11.5L21.5,4.5Z'/%3E%3C/svg%3E">

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand text-white animate__animated animate__fadeIn" href="#">TiketPesawat</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item animate__animated animate__fadeIn">
                        <a class="nav-link text-white" href="#"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item animate__animated animate__fadeIn">
                        <a class="nav-link text-white" href="#promo"><i class="bi bi-tag me-1"></i>Promo</a>
                    </li>
                    <li class="nav-item animate__animated animate__fadeIn">
                        <a class="nav-link text-white" href="#"><i class="bi bi-envelope me-1"></i>Kontak</a>
                    </li>
                    <li class="nav-item animate__animated animate__fadeIn">
                        <?php

                        if (!check_remember_token()) {
                            echo '<a class="btn btn-primary ms-lg-3 text-white" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>';
                        } else {
                            echo '<button class="btn btn-danger ms-lg-3 text-white" onclick="handleLogOut();"><i class="bi bi-box-arrow-right me-1"></i>Log out</button>';
                        }

                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section pt-5 mt-5">
        <div class="container">
            <div class="row align-items-center hero-bg p-4 p-md-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="display-3 fw-bold mb-3 animate__animated animate__fadeInLeft" style="line-height:1.2;">Temukan & Pesan Tiket Pesawat <span class="text-primary-gradient">Impianmu</span></h1>
                    <ul class="list-unstyled mb-4 animate__animated animate__fadeInLeft animate__delay-1s">
                        <li class="mb-3 d-flex align-items-center">
                            <span class="badge bg-warning text-dark me-2 p-2"><i class="bi bi-lightning-charge-fill"></i></span>
                            <span>Proses pemesanan super cepat & praktis</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <span class="badge bg-info text-dark me-2 p-2"><i class="bi bi-shield-check"></i></span>
                            <span>Mitra maskapai & kereta resmi</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <span class="badge bg-success text-dark me-2 p-2"><i class="bi bi-percent"></i></span>
                            <span>Diskon & penawaran terbaik setiap hari</span>
                        </li>
                    </ul>
                    <p class="lead mb-4 animate__animated animate__fadeInLeft animate__delay-2s">Jelajahi destinasi favorit, bandingkan harga, dan nikmati pengalaman booking yang modern. Siap terbang atau berangkat ke mana saja, kapan saja!</p>
                    <div class="d-flex flex-wrap gap-3 animate__animated animate__fadeInUp animate__delay-3s">
                        <a href="./pencarian.php" class="btn btn-primary btn-lg shadow">
                            <i class="bi bi-search me-2"></i>Mulai Cari Tiket
                        </a>
                        <a href="#promo" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-tag-fill me-2"></i>Lihat Promo
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://images.unsplash.com/photo-1464037866556-6812c9d1c72e?auto=format&fit=crop&w=600&q=80" alt="Ilustrasi Pesawat Modern" class="img-fluid hero-img mb-3">
                </div>
            </div>
        </div>
    </section>

    <!-- Promo Section -->
    <section id="promo" class="container py-5 mt-4">
        <div class="text-center mb-5 animate__animated animate__fadeIn">
            <h2 class="fw-bold" style="letter-spacing:1px;">Promo <span class="text-primary-gradient">Terbaru</span></h2>
            <p class="text-muted">Penawaran spesial untuk perjalanan Anda</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 col-sm-6" data-animate="animate-fade-in" data-delay="delay-100">
                <div class="promo-card card h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Promo Bali">
                        <div class="position-absolute top-0 start-0 bg-warning text-dark p-2 m-2 rounded-pill">
                            <i class="bi bi-star-fill me-1"></i> Populer
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Diskon 20% ke Bali</h5>
                        <p class="card-text">Nikmati liburan ke Bali dengan harga spesial. Promo terbatas!</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-primary">Rp 800.000</span>
                            <a href="./pencarian.php" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6" data-animate="animate-fade-in" data-delay="delay-200">
                <div class="promo-card card h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1555899434-94d1368aa7af?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Promo Surabaya">
                        <div class="position-absolute top-0 start-0 bg-info text-dark p-2 m-2 rounded-pill">
                            <i class="bi bi-lightning-fill me-1"></i> Cepat Habis
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Tiket Murah ke Surabaya</h5>
                        <p class="card-text">Dapatkan tiket pesawat ke Surabaya mulai dari Rp 500.000!</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-primary">Rp 500.000</span>
                            <a href="./pencarian.php" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6" data-animate="animate-fade-in" data-delay="delay-300">
                <div class="promo-card card h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Promo Medan">
                        <div class="position-absolute top-0 start-0 bg-success text-dark p-2 m-2 rounded-pill">
                            <i class="bi bi-percent me-1"></i> Hemat 30%
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Trip Hemat ke Medan</h5>
                        <p class="card-text">Promo tiket pesawat ke Medan, hemat hingga 30%!</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-primary">Rp 650.000</span>
                            <a href="./pencarian.php" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6" data-animate="animate-fade-in" data-delay="delay-400">
                <div class="promo-card card h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Promo Yogyakarta">
                        <div class="position-absolute top-0 start-0 bg-primary text-white p-2 m-2 rounded-pill">
                            <i class="bi bi-calendar-event me-1"></i> Weekend
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Weekend di Yogyakarta</h5>
                        <p class="card-text">Paket lengkap weekend di Yogyakarta dengan harga terjangkau!</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-primary">Rp 750.000</span>
                            <a href="./pencarian.php" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6" data-animate="animate-fade-in" data-delay="delay-500">
                <div class="promo-card card h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Promo Lombok">
                        <div class="position-absolute top-0 start-0 bg-danger text-white p-2 m-2 rounded-pill">
                            <i class="bi bi-fire me-1"></i> Hot Deal
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Eksplor Lombok</h5>
                        <p class="card-text">Jelajahi keindahan Lombok dengan penawaran khusus minggu ini!</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-primary">Rp 900.000</span>
                            <a href="./pencarian.php" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6" data-animate="animate-fade-in" data-delay="delay-500">
                <div class="promo-card card h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Promo Jakarta">
                        <div class="position-absolute top-0 start-0 bg-secondary text-white p-2 m-2 rounded-pill">
                            <i class="bi bi-briefcase-fill me-1"></i> Bisnis
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Business Trip Jakarta</h5>
                        <p class="card-text">Paket perjalanan bisnis ke Jakarta dengan fasilitas lengkap!</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-primary">Rp 1.200.000</span>
                            <a href="./pencarian.php" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Section -->
    <section class="container py-5 mt-3">
        <div class="text-center mb-5 animate__animated animate__fadeIn">
            <h2 class="fw-bold">Kenapa Memilih <span class="text-primary-gradient">TiketPesawat</span>?</h2>
            <p class="text-muted">Nikmati berbagai keunggulan layanan kami</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-animate="animate-fade-in" data-delay="delay-100">
                <div class="card h-100 border-0 bg-glass hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-flex mb-3">
                            <i class="bi bi-shield-check text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="card-title">Aman & Terpercaya</h4>
                        <p class="card-text">Transaksi aman dengan perlindungan data dan jaminan tiket resmi dari maskapai terpercaya.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-animate="animate-fade-in" data-delay="delay-200">
                <div class="card h-100 border-0 bg-glass hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-flex mb-3">
                            <i class="bi bi-lightning-charge text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="card-title">Cepat & Praktis</h4>
                        <p class="card-text">Proses pemesanan yang cepat dan mudah, dengan konfirmasi instan dan e-tiket langsung ke email Anda.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-animate="animate-fade-in" data-delay="delay-300">
                <div class="card h-100 border-0 bg-glass hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-flex mb-3">
                            <i class="bi bi-headset text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="card-title">Layanan 24/7</h4>
                        <p class="card-text">Dukungan pelanggan siap membantu Anda 24 jam sehari, 7 hari seminggu untuk segala kebutuhan perjalanan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary bg-opacity-10 mt-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="fw-bold mb-4 animate__animated animate__fadeIn">Siap Untuk Petualangan Berikutnya?</h2>
                    <p class="lead mb-4 animate__animated animate__fadeIn animate__delay-1s">Temukan destinasi impian Anda dengan harga terbaik. Pesan sekarang dan dapatkan pengalaman perjalanan yang tak terlupakan!</p>
                    <a href="./pencarian.php" class="btn btn-primary btn-lg animate__animated animate__fadeIn animate__delay-2s">
                        <i class="bi bi-airplane-fill me-2"></i>Mulai Pesan Tiket
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer text-white text-center py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0 text-md-start">
                    <h5 class="mb-3">TiketPesawat</h5>
                    <p class="mb-0">Platform pemesanan tiket pesawat terpercaya dengan harga terbaik dan layanan prima.</p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4 mb-md-0 text-md-start">
                    <h6 class="mb-3">Perusahaan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white">Tentang Kami</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Karir</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Blog</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4 mb-md-0 text-md-start">
                    <h6 class="mb-3">Produk</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white">Tiket Pesawat</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Tiket Kereta</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Paket Wisata</a></li>
                    </ul>
                </div>
                <div class="col-md-4 text-md-start">
                    <h6 class="mb-3">Hubungi Kami</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> support@tiketpesawat.com</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> +62 123 4567 890</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Jl. Pemesanan No. 123, Jakarta</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-white">
            <div class="row">
                <div class="col-md-6 text-md-start">
                    <p class="mb-0">&copy; 2025 TiketPesawat. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Design by TiketPesawat Team</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/main.js"></script>
</body>