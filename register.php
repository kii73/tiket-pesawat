<?php

include "./koneksi.php";

if (isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $address = $_POST['address'];
    $confirm_password = $_POST['confirm-password'];

    if ($password === $confirm_password) {
        $query = "INSERT INTO `users` (`nama`, `username`, `email`, `no_hp`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `password`) VALUES ('$fullname','$username','$email','$phone','$gender','$birthdate','$address',md5('$password'))";
        $result = $mysql->query($query);
        if ($result) {
?>
            <script>
                window.location.href = "login.php";
            </script>
<?php
        }
    }
}

?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Tiket Pesawat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'%3E%3Cpath d='M21.5,4.5L3.5,11.5C2.7,11.8,2.7,12.9,3.5,13.2L7.5,15L9.5,19.5C9.8,20.3,10.9,20.3,11.2,19.5L12.5,16.5L16.5,20.5C17.1,21.1,18.1,20.7,18.1,19.9V16.5L21.5,15.5C22.3,15.2,22.3,14.1,21.5,13.8L16.5,11.5L21.5,4.5Z'/%3E%3C/svg%3E">
</head>

<body class="auth-page">
    <!-- Navbar Sederhana -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-airplane-fill me-2 text-white" style="font-size: 1.5rem;"></i>
                <span class="fw-bold">SkyTicket</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pencarian.php">Cari Tiket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card register-card p-4 animate__animated animate__fadeIn">
                    <div class="plane-icon animate__animated animate__zoomIn">
                        <i class="bi bi-airplane-fill text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="text-center mb-2 fw-bold">Daftar <span class="text-primary-gradient">Akun Baru</span></h2>
                    <p class="text-center text-muted mb-4">Buat akun untuk memesan tiket pesawat dengan mudah</p>
                    
                    <?php
                    if (isset($_POST['register'])) {
                        if ($password !== $confirm_password) {
                            echo '<div class="alert alert-danger animate__animated animate__shakeX" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Password dan konfirmasi password tidak sama. Silakan coba lagi.
                            </div>';
                        }
                    }
                    ?>
                    
                    <form action="" method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullname" class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Masukkan nama lengkap" required>
                                    <div class="invalid-feedback">Masukkan nama lengkap</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email aktif" required>
                                    <div class="invalid-feedback">Masukkan email yang valid</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Buat username" required>
                                    <div class="invalid-feedback">Masukkan username</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Nomor HP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-phone"></i></span>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Masukkan nomor HP" required>
                                    <div class="invalid-feedback">Masukkan nomor HP</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-gender-ambiguous"></i></span>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="" disabled selected>Pilih jenis kelamin</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <div class="invalid-feedback">Pilih jenis kelamin</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birthdate" class="form-label">Tanggal Lahir</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-calendar-date"></i></span>
                                    <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                                    <div class="invalid-feedback">Masukkan tanggal lahir</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Masukkan alamat lengkap" required></textarea>
                                <div class="invalid-feedback">Masukkan alamat lengkap</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Kata Sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Buat kata sandi" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">Masukkan kata sandi</div>
                                </div>
                                <div class="password-strength mt-1 small" id="passwordStrength"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm-password" class="form-label">Konfirmasi Kata Sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Ulangi kata sandi" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">Konfirmasi kata sandi harus sama</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="register" class="btn btn-primary py-2 animate__animated animate__pulse animate__infinite animate__slower">
                                <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p>Sudah punya akun? <a href="login.php" class="login-link">Login sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="particles-js" class="particles-container"></div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="./assets/js/main.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirm-password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            let strength = 0;
            let message = '';
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]+/)) strength += 1;
            
            switch (strength) {
                case 0:
                case 1:
                    message = '<span class="text-danger">Sangat lemah</span>';
                    break;
                case 2:
                    message = '<span class="text-warning">Lemah</span>';
                    break;
                case 3:
                    message = '<span class="text-info">Sedang</span>';
                    break;
                case 4:
                    message = '<span class="text-primary">Kuat</span>';
                    break;
                case 5:
                    message = '<span class="text-success">Sangat kuat</span>';
                    break;
            }
            
            strengthDiv.innerHTML = password.length > 0 ? 'Kekuatan password: ' + message : '';
        });
        
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm-password');
            
            function validatePassword() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords tidak sama');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
            
            password.addEventListener('change', validatePassword);
            confirmPassword.addEventListener('keyup', validatePassword);
            
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
        
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#ffffff' },
                shape: { type: 'circle' },
                opacity: { value: 0.5, random: false },
                size: { value: 3, random: true },
                line_linked: { enable: true, distance: 150, color: '#ffffff', opacity: 0.4, width: 1 },
                move: { enable: true, speed: 2, direction: 'none', random: false, straight: false, out_mode: 'out', bounce: false }
            },
            interactivity: {
                detect_on: 'canvas',
                events: { onhover: { enable: true, mode: 'grab' }, onclick: { enable: true, mode: 'push' } },
                modes: { grab: { distance: 140, line_linked: { opacity: 1 } } }
            },
            retina_detect: true
        });
    </script>
</body>

</html>