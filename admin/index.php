<?php
session_start();

include "../koneksi.php";

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION["role"] != "admin") {
  header("Location: ../login.php");
  exit;
}

// 1. Tentukan halaman yang akan dimuat
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; // Default ke 'dashboard' jika tidak ada parameter

// 2. Fungsi untuk menentukan file yang akan disertakan
function get_include_file($page)
{
  switch ($page) {
    case 'data':
      return 'data.php';
    case 'input':
      return 'input.php';
    case 'pesanan':
      return 'pesanan.php';
    case 'laporan':
      return 'laporan.php';
    case 'cari':
      return 'cari.php'; // Digunakan untuk hasil pencarian
    case 'dashboard':
      return 'dashboard.php'; // Digunakan untuk hasil pencarian
    default:
      return 'dashboard.php';
  }
}

$include_file = get_include_file($page);

// Fungsi untuk menandai menu aktif
function is_active($currentPage, $targetPage)
{
  return $currentPage === $targetPage ? 'active bg-light' : '';
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - Kyy Airline</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    /* Tambahkan sedikit CSS untuk menyesuaikan tampilan yang tidak lagi menggunakan iframe */
    body {
      background-color: #f8f9fa;
    }

    .sidebar {
      min-height: 100vh;
      background-color: #fff;
      padding-top: 20px;
    }

    .list-group-item.active {
      color: #0d6efd !important;
      font-weight: bold;
    }

    .content-area {
      padding: 20px;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand text-white" href="?page=dashboard">Admin Panel</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link text-white" aria-current="page" href="?page=dashboard">Dashboard</a>
          </li>
        </ul>
        <form class="d-flex" role="search" action="index.php" method="GET">
          <input type="hidden" name="page" value="cari">
          <input class="form-control me-2" type="search" placeholder="Cari" aria-label="Search" name="cari" required />
          <button class="btn btn-outline-light" type="submit">Cari</button>
        </form>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 col-lg-2 sidebar">
        <ul class="list-group">
          <li class="list-group-item bg-primary text-white"><i class="bi bi-person-circle me-2"></i>MENU ADMIN</li>

          <a href="?page=dashboard" class="list-group-item list-group-item-action <?php echo is_active($page, 'dashboard'); ?>">DASHBOARD</a>
          <a href="?page=data" class="list-group-item list-group-item-action <?php echo is_active($page, 'data'); ?>">DATA TIKET</a>
          <a href="?page=input" class="list-group-item list-group-item-action <?php echo is_active($page, 'input'); ?>">INPUT DATA TIKET</a>
          <a href="?page=pesanan" class="list-group-item list-group-item-action <?php echo is_active($page, 'pesanan'); ?>">DATA PESANAN</a>
          <a href="?page=laporan" class="list-group-item list-group-item-action <?php echo is_active($page, 'laporan'); ?>">LAPORAN</a>

          <a href="../login.php?logout=1" class="list-group-item list-group-item-action text-danger fw-bold">
            <i class="bi bi-box-arrow-right me-2"></i>LOG OUT
          </a>
        </ul>
      </div>

      <div class="col-md-9 col-lg-10 content-area">
        <?php
        if (file_exists($include_file)) {
          include $include_file;
        } else {
          include "dashboard.php";
        }
        ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script src="../assets/js/main.js"></script>
</body>

</html>