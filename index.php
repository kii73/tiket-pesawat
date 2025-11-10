<?php
session_start();


include "./koneksi.php";


$query = "SELECT * FROM pesawat ORDER BY created_at DESC LIMIT 4";
$result_pesawat = $mysql->query($query)->fetch_all(MYSQLI_ASSOC);


$user = [
    "user" => [],
    "bookings" => [] 
];
$is_logged_in = false;


if (isset($_SESSION["username"])) {
    $session_username = $_SESSION["username"];
    $is_logged_in = true;

    $stmt_user = $mysql->prepare("SELECT id, nama, role FROM users WHERE username = ?");
    $stmt_user->bind_param("s", $session_username);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user_data = $result_user->fetch_assoc();
    $stmt_user->close();

    if ($user_data) {
        $user["user"] = $user_data;
        $user_id = $user_data["id"];

        
        $stmt_booking = $mysql->prepare("SELECT id_pesawat, status FROM bookings WHERE id_user = ?");
        $stmt_booking->bind_param("i", $user_id);
        $stmt_booking->execute();
        $bookings_result = $stmt_booking->get_result();

        $user["bookings"] = $bookings_result->fetch_all(MYSQLI_ASSOC);
        $stmt_booking->close();
    } else {
       
        session_unset();
        session_destroy();
        $is_logged_in = false;
    }
}

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
    <?php
  
    include "navbar.php";
    ?>

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
                            <span>Mitra maskapai terpercaya</span>
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

    <section class="container">
        <div class="search-results">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-5 mt-5 text-center" style="width: 100%; font-size: 40px;"><span class="text-primary-gradient" id="promo">Promo</span></h3>
            </div>
            <div class="row g-4" data-animate="animate-fade-in">
                <?php
               
                foreach ($result_pesawat as $pesawat) {
                   
                    $rupiah = "Rp " . number_format($pesawat["harga"], 0, ",", ".");
                ?>

                    <div class="col-md-6" data-animate="animate-fade-in">
                        <div class="result-card card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="airline-logo me-2 rounded-circle bg-light p-2">
                                            <i class="bi bi-airplane text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title mb-0"><?= $pesawat["nama"] ?></h5>
                                            <span class="badge bg-light text-dark"><?= $pesawat["no_penerbangan"] ?></span>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= "Tersedia " . $pesawat["kursi_tersedia"] . " kursi" ?? 'Tidak ada kursi tersedia' ?></span>
                                </div>
                                <div class="flight-route d-flex justify-content-between align-items-center mb-4 position-relative">
                                    <div class="text-center">
                                        <h6 class="fw-bold mb-0"><?= $pesawat["asal"] ?></h6>
                                        <p class="mb-0 text-primary"><?= $pesawat["waktu_berangkat"] ?></p>
                                    </div>
                                    <div class="flight-line position-relative flex-grow-1 mx-3">
                                        <div class="flight-icon">
                                            <i class="bi bi-airplane-fill text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h6 class="fw-bold mb-0"><?= $pesawat["tujuan"] ?></h6>
                                        <p class="mb-0 text-primary"><?= $pesawat["waktu_tiba"] ?></p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <div>
                                        <p class="mb-0">Harga per orang</p>
                                        <p class="card-text fw-bold text-primary mb-0 fs-5"><?= $rupiah ?></p>
                                    </div>

                                    <?php
                                   
                                    $isBooking = [];

                                    if ($is_logged_in) {
                                    
                                        $isBooking = array_values(array_filter($user["bookings"], function ($item) use ($pesawat) {
                                            return isset($item["id_pesawat"]) && $item["id_pesawat"] == $pesawat["id"];
                                        }));
                                    }

                                    if (!empty($isBooking)) {
                                       
                                    ?>
                                        <button class="btn btn-primary" disabled>
                                            <i class="bi bi-x-circle me-1"></i><?= ucfirst($isBooking[0]["status"]) ?>
                                        </button>
                                    <?php
                                    } elseif ($is_logged_in) {
                                        
                                    ?>
                                        <a href="./konfirmasi.php?pesawat=<?= $pesawat["id"] ?>" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>Pilih Tiket
                                        </a>
                                    <?php
                                    } else {
                                      
                                    ?>
                                        <a href="./login.php" class="btn btn-primary">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>Pilih Tiket (Login)
                                        </a>
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                }
                ?>
            </div>
        </div>
    </section>

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

</html>