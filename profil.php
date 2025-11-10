<?php

include "./koneksi.php"; 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION["user_id"] ?? ''; 

if (empty($user_id)) {

    header("Location: login.php"); 
    exit;
}


$profil_data = null;
$stmt = $mysql->prepare("SELECT id, nama, username, email, no_hp, jenis_kelamin, tanggal_lahir, alamat, role FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profil_data = $result->fetch_assoc();
$stmt->close();

if (!$profil_data) {
    
    die("Data profil tidak ditemukan. Silakan hubungi administrator.");
}


$role_class = $profil_data['role'] === 'admin' ? 'badge bg-danger' : 'badge bg-success';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header-color { color: #008080 !important; }
        .card-profile { max-width: 600px; margin: 30px auto; }
        .profile-label { font-weight: bold; width: 150px; }
        .profile-container { padding-top: 20px; }
    </style>
</head>

<body>
    
    <div class="container profile-container">
        
        <h3 class="mb-4 header-color">Profil Saya</h3>

        <div class="card card-profile shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Informasi Akun</h5>
            </div>
            <div class="card-body">
                
                <div class="row mb-3">
                    <div class="col-sm-4 profile-label">Nama Lengkap</div>
                    <div class="col-sm-8"><?= htmlspecialchars($profil_data['nama']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 profile-label">Username</div>
                    <div class="col-sm-8"><?= htmlspecialchars($profil_data['username']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 profile-label">Email</div>
                    <div class="col-sm-8"><?= htmlspecialchars($profil_data['email']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 profile-label">Role</div>
                    <div class="col-sm-8">
                        <span class="<?= $role_class ?>"><?= ucfirst(htmlspecialchars($profil_data['role'])) ?></span>
                    </div>
                </div>

                <hr>

                <h6 class="mt-4 mb-3 text-secondary">Data Kontak & Pribadi</h6>
                <div class="row mb-3">
                    <div class="col-sm-4 profile-label">No. HP</div>
                    <div class="col-sm-8"><?= htmlspecialchars($profil_data['no_hp']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 profile-label">Jenis Kelamin</div>
                    <div class="col-sm-8"><?= htmlspecialchars($profil_data['jenis_kelamin']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 profile-label">Tanggal Lahir</div>
                    <div class="col-sm-8"><?= htmlspecialchars(date('d F Y', strtotime($profil_data['tanggal_lahir']))) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 profile-label">Alamat</div>
                    <div class="col-sm-8"><?= nl2br(htmlspecialchars($profil_data['alamat'])) ?></div>
                </div>

            </div>
            <div class="card-footer text-end">
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
        </div>

    </div>
</body>
</html>