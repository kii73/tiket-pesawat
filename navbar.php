<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$is_pencarian_page = (strpos($current_url, '/pencarian.php') !== false);

/**
 * Mendapatkan peran pengguna dari session.
 * Diperbaiki untuk langsung membaca $_SESSION['role'].
 */
function get_user_role()
{
    return isset($_SESSION["role"]) ? $_SESSION["role"] : "guest";
}

// Cek apakah user sudah login (role bukan 'guest') dan tentukan apakah dia admin
$user_role = get_user_role();
$is_logged_in = ($user_role !== 'guest');
$is_admin = ($user_role === 'admin');

// Kita tidak lagi mengandalkan check_remember_token(), melainkan $_SESSION["role"]
?>
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand text-white animate__animated animate__fadeIn" href="#">Kyy air line</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item animate__animated animate__fadeIn">
                    <a class="nav-link text-white" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                </li>
                <?php if (!$is_pencarian_page): // Tampilkan Promo HANYA jika bukan di halaman pencarian 
                ?>
                    <li class="nav-item animate__animated animate__fadeIn">
                        <a class="nav-link text-white" href="#promo"><i class="bi bi-tag me-1"></i>Promo</a>
                    </li>
                <?php endif; ?>

                <?php if ($is_admin): // Tampilkan Dashboard HANYA jika user adalah admin 
                ?>
                    <li class="nav-item animate__animated animate__fadeIn">
                        <a class="nav-link text-white" href="admin/index.php"><i class="bi bi-speedometer me-1"></i>Dashboard</a>
                    </li>
                <?php endif; ?>

                <?php if ($is_logged_in): // Tampilkan Riwayat HANYA jika user sudah login 
                ?>
                    <li class="nav-item animate__animated animate__fadeIn">
                        <a class="nav-link text-white active" href="riwayat.php"><i class="bi bi-clock-history me-1"></i>Riwayat</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item animate__animated animate__fadeIn">
                    <?php

                    // Cek berdasarkan status login ($is_logged_in)
                    if (!$is_logged_in) {
                        echo '<a class="btn btn-primary ms-lg-3 text-white" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>';
                    } else {
                        // Tombol logout akan muncul jika user sudah login
                        echo '<a class="btn btn-danger ms-lg-3 text-white" href="login.php?logout=1"><i class="bi bi-box-arrow-in-right me-1"></i>Logout</a>';
                    }

                    ?>
                </li>
            </ul>
        </div>
    </div>
</nav>