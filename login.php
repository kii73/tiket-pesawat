<?php

include "./koneksi.php";
include "./token.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE (username='$username' OR email='$username') AND password=md5('$password')";
    $result = $mysql->query($query);
    if ($result->num_rows > 0) {
        $first_data = $result->fetch_assoc();
        $id = $first_data["id"];
        $remember_token = generateUUIDv4();

        $mysql->query("UPDATE users SET remember_token='$remember_token' WHERE id=$id");
        setcookie("remember_token", $remember_token, 0, "/");

        if ($first_data["role"] == "admin") {
?>
            <script>
                window.location.href = "admin";
            </script>
        <?php
        } else {
        ?>
            <script>
                window.location.href = "index.php";
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
    <title>Login - Tiket Pesawat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'%3E%3Cpath d='M21.5,4.5L3.5,11.5C2.7,11.8,2.7,12.9,3.5,13.2L7.5,15L9.5,19.5C9.8,20.3,10.9,20.3,11.2,19.5L12.5,16.5L16.5,20.5C17.1,21.1,18.1,20.7,18.1,19.9V16.5L21.5,15.5C22.3,15.2,22.3,14.1,21.5,13.8L16.5,11.5L21.5,4.5Z'/%3E%3C/svg%3E">

    <style>
        * {
            padding: 0;
            margin: 0;
        }

        #particles-js {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 90%;
            z-index: -1;
        }

        .container-login {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
        }

        .center-login {
            display: flex;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container-login">
        <div class="row justify-content-center center-login">
            <div class="col-md-6 col-lg-5">
                <div class="login-card p-4 animate__animated animate__fadeIn">
                    <div class="text-center mb-4">
                        <div class="plane-icon animate__animated animate__zoomIn">
                            <i class="bi bi-airplane-fill text-black" style="font-size: 1.8rem;"></i>
                        </div>
                        <h2 class="mb-3 fw-bold">Login <span class="text-primary-gradient">kyy air line</span></h2>
                        <p class="text-muted">Masuk untuk memesan tiket pesawat Anda</p>
                    </div>

                    <?php

                    if (isset($_POST['login'])) {
                        if ($result->num_rows == 0) {
                            echo '<div class="alert alert-danger animate__animated animate__shakeX" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>Pengguna tidak ditemukan. Silakan periksa username/email Anda.</div>';
                        } else {
                            echo '<div class="alert alert-danger animate__animated animate__shakeX" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>Password salah.</div>';
                        }
                    }

                    ?>
                    <form action="" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username / Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username atau email" required>
                                <div class="invalid-feedback">Masukkan username atau email</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <div class="invalid-feedback">Masukkan kata sandi</div>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100 animate__animated animate__pulse animate__infinite animate__slower">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="register.php" class="register-link">Belum punya akun? Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Particles Background -->
    <div id="particles-js" class="particles-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="./assets/js/main.js"></script>
    <script>
        // Toggle password visibility
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

        // Form validation
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

        // Initialize particles.js
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: '#2563eb'
                },
                shape: {
                    type: 'circle'
                },
                opacity: {
                    value: 0.5,
                    random: false
                },
                size: {
                    value: 3,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#2563eb',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: false,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: {
                        enable: false,
                        mode: 'grab'
                    },
                    onclick: {
                        enable: false,
                        mode: 'push'
                    }
                },
                modes: {
                    grab: {
                        distance: 140,
                        line_linked: {
                            opacity: 1
                        }
                    }
                }
            },
            retina_detect: true
        });
    </script>
</body>

</html>