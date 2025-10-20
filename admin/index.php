<?php
session_start();

include "../koneksi.php";

if (!isset(($_SESSION['username'])) || !isset($_SESSION['role']) && $_SESSION["role"] != "admin") {
  header("Location: ../login.php");
  exit;
}

?>



<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>tiket</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>

        </ul>
        <form class="d-flex" role="search" action="cari.php" method="post" target="wadah">
          <input class="form-control me-2" type="search" placeholder="cari" aria-label="Search" name="cari" />
          <button class="btn btn-outline-success" type="submit">cari</button>
        </form>
      </div>
    </div>
  </nav>


  <div class="row">
    <div class="col-3 mt-3">
      <ul class="list-group">
        <li class="list-group-item bg-primary"><i class="bi bi-person-circle me-2 text-dengger"></i>MENU</li>
        <a href="data.php" class="list-group-item" target="wadah">DATA TIKET</a>
        <a href="input.php" class="list-group-item" target="wadah">INPUT DATA TIKET</a>
        <a href="pesanan.php" class="list-group-item" target="wadah">DATA PESANAN</a>
        <a href="laporan.php" class="list-group-item" target="wadah">LAPORAN</a>

        <button onclick="handleLogOut('../login.php');" class="list-group-item">log out</button>
      </ul>
    </div>

    <div class="col">
      <iframe src="" name="wadah" frameborder="0" width="100%" height="800"></iframe>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script src="../assets/js/main.js"></script>
</body>

</html>