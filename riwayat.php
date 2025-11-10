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
    if ($stmt_user === false) {
       
        error_log("Error preparing user query: " . $mysql->error);
        header("Location: login.php");
        exit;
    }

    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();

    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $user_id = (int)$user_data["id"];
    }
    $stmt_user->close();
}


if ($user_id) {
   
    $query = "
        SELECT 
            p.nama, p.no_penerbangan, p.asal, p.tujuan, 
            p.waktu_berangkat, p.waktu_tiba, p.harga,
            k.kode, k.status, k.kelas
        FROM bookings k
        JOIN pesawat p ON k.id_pesawat = p.id
        WHERE k.id_user = ?
        ORDER BY k.id DESC
    ";

  
    $stmt_history = $mysql->prepare($query);
    if ($stmt_history === false) {
       
        error_log("Error preparing history query: " . $mysql->error);
    } else {
        
        $stmt_history->bind_param("i", $user_id);
        $stmt_history->execute();
        $history_result = $stmt_history->get_result();

       
        if ($history_result) {
            $user_history = $history_result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt_history->close();
    }
} else {
   
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - Tiket Pesawat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'%3E%3Cpath d='M21.5,4.5L3.5,11.5C2.7,11.8,2.7,12.9,3.5,13.2L7.5,15L9.5,19.5C9.8,20.3,10.9,20.3,11.2,19.5L12.5,16.5L16.5,20.5C17.1,21.1,18.1,20.7,18.1,19.9V16.5L21.5,15.5C22.3,15.2,22.3,14.1,21.5,13.8L16.5,11.5L21.5,4.5Z'/%3E%3C/svg%3E">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand text-white animate__animated animate__fadeIn" href="index.php">
                <i class="bi bi-airplane me-2"></i>Kyy air line
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item animate__animated animate__fadeIn">
                        <a class="nav-link text-white" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item animate__animated animate__fadeIn">
                        <a class="nav-link text-white" href="pencarian.php"><i class="bi bi-search me-1"></i>Cari Tiket</a>
                    </li>

                    <li class="nav-item animate__animated animate__fadeIn">
                        <button class="btn btn-danger ms-lg-3 text-white" onclick="handleLogOut();"><i class="bi bi-box-arrow-right me-1"></i>Log out</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="container py-5 mt-5">
        <div class="search-results">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">Riwayat <span class="text-primary-gradient">Pemesanan Anda</span></h3>
                <div class="search-summary bg-light rounded-pill px-3 py-2">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    <span>Daftar tiket yang pernah Anda pesan</span>
                </div>
            </div>

            <?php if (empty($user_history)) : ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x" style="font-size: 4rem; color: #6c757d;"></i>
                    <h4 class="mt-3">Riwayat Anda Masih Kosong</h4>
                    <p class="text-muted">Anda belum pernah melakukan pemesanan tiket pesawat.</p>
                    <a href="pencarian.php" class="btn btn-primary mt-3">
                        <i class="bi bi-search me-2"></i>Cari Tiket Sekarang
                    </a>
                </div>
            <?php else : ?>
                <div class="row g-4" data-animate="animate-fade-in">
                    <?php foreach ($user_history as $pesanan) :
                       
                        $status_class = 'bg-secondary'; 
                        $status_text = strtolower($pesanan["status"]);

                        if ($status_text == 'menunggu' || $status_text == 'menunggu persetujuan') {
                            $status_class = 'bg-warning text-dark';
                        } elseif ($status_text == 'dikonfirmasi' || $status_text == 'disetujui') {
                            $status_class = 'bg-success';
                        } elseif ($status_text == 'dibatalkan' || $status_text == 'gagal') {
                            $status_class = 'bg-danger';
                        }
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
                                                <h5 class="card-title mb-0"><?= htmlspecialchars($pesanan["nama"]) ?></h5>
                                                <span class="badge bg-light text-dark"><?= htmlspecialchars($pesanan["no_penerbangan"]) ?></span>
                                            </div>
                                        </div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= htmlspecialchars($pesanan["kelas"] ?? '-') ?></span>
                                    </div>
                                    <div class="flight-route d-flex justify-content-between align-items-center mb-4 position-relative">
                                        <div class="text-center">
                                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($pesanan["asal"]) ?></h6>
                                            <p class="mb-0 text-primary"><?= htmlspecialchars($pesanan["waktu_berangkat"]) ?></p>
                                        </div>
                                        <div class="flight-line position-relative flex-grow-1 mx-3">
                                            <div class="flight-icon">
                                                <i class="bi bi-airplane-fill text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($pesanan["tujuan"]) ?></h6>
                                            <p class="mb-0 text-primary"><?= htmlspecialchars($pesanan["waktu_tiba"]) ?></p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                        <div>
                                            <p class="mb-1 small text-muted">Kode Booking:</p>
                                            <p class="card-text fw-bold text-primary mb-0 fs-5"><?= htmlspecialchars($pesanan["kode"]) ?></p>
                                        </div>
                                        <div class="text-end">
                                            <p class="mb-1 small text-muted">Status:</p>
                                            <span class="badge <?= $status_class ?>"><?= ucfirst(htmlspecialchars($pesanan["status"])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

</html>