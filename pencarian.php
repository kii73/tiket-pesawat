<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

include "./koneksi.php";


if (!isset($mysql) || !$mysql instanceof mysqli) {
	die("Koneksi database gagal.");
}


$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$user_bookings = [];
$user_data = [];

if ($is_logged_in) {
	
	$stmt_user = $mysql->prepare("SELECT id, username, role FROM users WHERE id = ?");
	
	if ($stmt_user === false) {
		error_log("Prepare failed (users): " . $mysql->error);
	} else {
		$stmt_user->bind_param("i", $user_id);
		$stmt_user->execute();
		$user_result = $stmt_user->get_result();
		$user_data = $user_result->fetch_assoc();
		$stmt_user->close();
	}


	
	$stmt_bookings = $mysql->prepare("SELECT id_pesawat, status FROM bookings WHERE id_user = ?");
	if ($stmt_bookings === false) {
		error_log("Prepare failed (bookings): " . $mysql->error);
	} else {
		$stmt_bookings->bind_param("i", $user_id);
		$stmt_bookings->execute();
		$bookings_result = $stmt_bookings->get_result();

	
		while ($row = $bookings_result->fetch_assoc()) {
			$user_bookings[$row['id_pesawat']] = $row;
		}
		$stmt_bookings->close();
	}
}



$query = "SELECT * FROM pesawat";
$result_semua_tiket = $mysql->query($query)->fetch_all(MYSQLI_ASSOC);

$result_pencarian = null;

if (isset($_GET["dari"]) && isset($_GET["ke"])) {
	
	$dari_param = "%" . $_GET["dari"] . "%";
	$ke_param = "%" . $_GET["ke"] . "%";

	
	$stmt_search = $mysql->prepare("SELECT * FROM pesawat WHERE asal LIKE ? AND tujuan LIKE ?");

	if ($stmt_search === false) {
		error_log("Prepare failed (search): " . $mysql->error);
		$result_pencarian = [];
	} else {
		$stmt_search->bind_param("ss", $dari_param, $ke_param);
		$stmt_search->execute();
		$result_pencarian = $stmt_search->get_result()->fetch_all(MYSQLI_ASSOC);
		$stmt_search->close();
	}
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Pencarian Tiket Pesawat</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
	<link rel="stylesheet" href="./assets/css/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
	<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'%3E%3Cpath d='M21.5,4.5L3.5,11.5C2.7,11.8,2.7,12.9,3.5,13.2L7.5,15L9.5,19.5C9.8,20.3,10.9,20.3,11.2,19.5L12.5,16.5L16.5,20.5C17.1,21.1,18.1,20.7,18.1,19.9V16.5L21.5,15.5C22.3,15.2,22.3,14.1,21.5,13.8L16.5,11.5L21.5,4.5Z'/%3E%3C/svg%3E">

	<style>
		.footer-container {
			background: red !important;
			width: 100% !important;
		}
	</style>
</head>

<body>
	<?php
	include "navbar.php";
	?>

	<section class="container py-5 mt-5">
		<div class="search-form p-4 mb-5 animate__animated animate__fadeIn">
			<h2 class="mb-4 text-center fw-bold">Cari Tiket <span class="text-primary-gradient">Pesawat</span></h2>
			<form method="GET" class="needs-validation" novalidate>
				<div class="row g-3">
					<div class="col-md-3">
						<label for="dari" class="form-label"><i class="bi bi-geo-alt-fill me-1 text-primary"></i>Dari</label>
						<div class="input-group">
							<span class="input-group-text bg-light"><i class="bi bi-airplane-engines"></i></span>
							<input type="text" class="form-control" id="dari" name="dari" placeholder="Kota asal" value="<?php echo isset($_GET['dari']) ? htmlspecialchars($_GET['dari']) : ''; ?>" required>
							<div class="invalid-feedback">Masukkan kota asal</div>
						</div>
					</div>
					<div class="col-md-3">
						<label for="ke" class="form-label"><i class="bi bi-geo-alt-fill me-1 text-primary"></i>Ke</label>
						<div class="input-group">
							<span class="input-group-text bg-light"><i class="bi bi-airplane-fill"></i></span>
							<input type="text" class="form-control" id="ke" name="ke" placeholder="Kota tujuan" value="<?php echo isset($_GET['ke']) ? htmlspecialchars($_GET['ke']) : ''; ?>" required>
							<div class="invalid-feedback">Masukkan kota tujuan</div>
						</div>
					</div>
					<div class="col-md-3">
						<label for="tanggal" class="form-label"><i class="bi bi-calendar-event me-1 text-primary"></i>Tanggal</label>
						<div class="input-group">
							<span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
							<input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo isset($_GET['tanggal']) ? htmlspecialchars($_GET['tanggal']) : date('Y-m-d'); ?>" required>
							<div class="invalid-feedback">Pilih tanggal keberangkatan</div>
						</div>
					</div>
					<div class="col-md-3">
						<label for="jenis" class="form-label"><i class="bi bi-ticket-perforated me-1 text-primary"></i>Jenis Transportasi</label>
						<div class="input-group">
							<span class="input-group-text bg-light"><i class="bi bi-airplane"></i></span>
							<select class="form-select" id="jenis" name="jenis" required>
								<option value="pesawat" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'pesawat') ? 'selected' : ''; ?>>Pesawat</option>
							</select>
							<div class="invalid-feedback">Pilih jenis transportasi</div>
						</div>
					</div>
					<div class="col-12 text-center mt-4">
						<button type="submit" class="btn btn-primary px-5 py-2 animate__animated animate__pulse animate__infinite animate__slower">
							<i class="bi bi-search me-2"></i>Cari Tiket
						</button>
					</div>
				</div>
			</form>
		</div>

		<?php
		
		if (isset($result_pencarian) && isset($_GET["dari"]) && !empty($result_pencarian)) {
		?>
			<section class="container">
				<div class="search-results">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="fw-bold mb-0">Hasil <span class="text-primary-gradient">Pencarian</span></h3>
						<div class="search-summary bg-light rounded-pill px-3 py-2">
							<i class="bi bi-info-circle me-1 text-primary"></i>
							<span>Penerbangan Tersedia</span>
						</div>
					</div>
					<div class="row g-4" data-animate="animate-fade-in">
						<?php
						foreach ($result_pencarian as $pesawat) {
							$rupiah = "Rp " . number_format($pesawat["harga"], 0, ",", ".");
							
							$is_booked = $is_logged_in && isset($user_bookings[$pesawat["id"]]);
							$booking_status = $is_booked ? htmlspecialchars($user_bookings[$pesawat["id"]]['status']) : '';
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
													<h5 class="card-title mb-0"><?= htmlspecialchars($pesawat["nama"]) ?></h5>
													<span class="badge bg-light text-dark"><?= htmlspecialchars($pesawat["no_penerbangan"]) ?></span>
												</div>
											</div>
											<span class="badge bg-primary bg-opacity-10 text-primary"><?= "Tersedia " . $pesawat["kursi_tersedia"] . " kursi" ?? 'Tidak ada kursi tersedia' ?></span>
										</div>
										<div class="flight-route d-flex justify-content-between align-items-center mb-4 position-relative">
											<div class="text-center">
												<h6 class="fw-bold mb-0"><?= htmlspecialchars($pesawat["asal"]) ?></h6>
												<p class="mb-0 text-primary"><?= htmlspecialchars($pesawat["waktu_berangkat"]) ?></p>
											</div>
											<div class="flight-line position-relative flex-grow-1 mx-3">
												<div class="flight-icon">
													<i class="bi bi-airplane-fill text-primary"></i>
												</div>
											</div>
											<div class="text-center">
												<h6 class="fw-bold mb-0"><?= htmlspecialchars($pesawat["tujuan"]) ?></h6>
												<p class="mb-0 text-primary"><?= htmlspecialchars($pesawat["waktu_tiba"]) ?></p>
											</div>
										</div>
										<div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
											<div>
												<p class="mb-0">Harga per orang</p>
												<p class="card-text fw-bold text-primary mb-0 fs-5"><?= $rupiah ?></p>
											</div>
											<?php if (!$is_logged_in): ?>
												<a href="./login.php" class="btn btn-primary">
													<i class="bi bi-box-arrow-in-right me-1"></i>Pilih Tiket (Login)
												</a>
											<?php elseif ($is_booked): ?>
												<button class="btn btn-warning" disabled>
													<i class="bi bi-x-circle me-1"></i>Status: <?= ucfirst($booking_status) ?>
												</button>
											<?php else: ?>
												<a href="./konfirmasi.php?pesawat=<?= htmlspecialchars($pesawat["id"]) ?>" class="btn btn-primary">
													<i class="bi bi-check-circle me-1"></i>Pilih Tiket
												</a>
											<?php endif; ?>
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

		<?php
		} else if (empty($result_pencarian) && isset($_GET["dari"])) {
		?>
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h3 class="fw-bold mb-0">Hasil <span class="text-primary-gradient">Pencarian</span></h3>
				<div class="search-summary bg-light rounded-pill px-3 py-2">
					<i class="bi bi-info-circle me-1 text-primary"></i>
					<span>Penerbangan kosong</span>
				</div>
			</div>
			<h4 class="fw-bold mb-0">Tiket tidak ada</span></h4>
		<?php
		}
		?>


		<section class="container" style="<?php if (isset($result_pencarian) && isset($_GET["dari"])) echo "margin-top: 10rem;" ?>">
			<div class="search-results">
				<div class="d-flex justify-content-between align-items-center mb-4">
					<h3 class="fw-bold mb-0">Semua <span class="text-primary-gradient">Tiket</span></h3>
					<div class="search-summary bg-light rounded-pill px-3 py-2">
						<i class="bi bi-info-circle me-1 text-primary"></i>
						<span>Penerbangan Tersedia</span>
					</div>
				</div>
				<div class="row g-4" data-animate="animate-fade-in">
					<?php
					foreach ($result_semua_tiket as $pesawat) {
						$rupiah = "Rp " . number_format($pesawat["harga"], 0, ",", ".");
						
						$is_booked = $is_logged_in && isset($user_bookings[$pesawat["id"]]);
						$booking_status = $is_booked ? htmlspecialchars($user_bookings[$pesawat["id"]]['status']) : '';
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
												<h5 class="card-title mb-0"><?= htmlspecialchars($pesawat["nama"]) ?></h5>
												<span class="badge bg-light text-dark"><?= htmlspecialchars($pesawat["no_penerbangan"]) ?></span>
											</div>
										</div>
										<span class="badge bg-primary bg-opacity-10 text-primary"><?= "Tersedia " . $pesawat["kursi_tersedia"] . " kursi" ?? 'Tidak ada kursi tersedia' ?></span>
									</div>
									<div class="flight-route d-flex justify-content-between align-items-center mb-4 position-relative">
										<div class="text-center">
											<h6 class="fw-bold mb-0"><?= htmlspecialchars($pesawat["asal"]) ?></h6>
											<p class="mb-0 text-primary"><?= htmlspecialchars($pesawat["waktu_berangkat"]) ?></p>
										</div>
										<div class="flight-line position-relative flex-grow-1 mx-3">
											<div class="flight-icon">
												<i class="bi bi-airplane-fill text-primary"></i>
											</div>
										</div>
										<div class="text-center">
											<h6 class="fw-bold mb-0"><?= htmlspecialchars($pesawat["tujuan"]) ?></h6>
											<p class="mb-0 text-primary"><?= htmlspecialchars($pesawat["waktu_tiba"]) ?></p>
										</div>
									</div>
									<div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
										<div>
											<p class="mb-0">Harga per orang</p>
											<p class="card-text fw-bold text-primary mb-0 fs-5"><?= $rupiah ?></p>
										</div>
										<?php if (!$is_logged_in): ?>
											<a href="./login.php" class="btn btn-primary">
												<i class="bi bi-box-arrow-in-right me-1"></i>Pilih Tiket (Login)
											</a>
										<?php elseif ($is_booked): ?>
											<button class="btn btn-warning" disabled>
												<i class="bi bi-x-circle me-1"></i>Status: <?= ucfirst($booking_status) ?>
											</button>
										<?php else: ?>
											<a href="./konfirmasi.php?pesawat=<?= htmlspecialchars($pesawat["id"]) ?>" class="btn btn-primary">
												<i class="bi bi-check-circle me-1"></i>Pilih Tiket
											</a>
										<?php endif; ?>
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