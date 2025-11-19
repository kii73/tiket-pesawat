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

$stmt = $mysql->prepare("SELECT id, nama, username, email, no_hp, jenis_kelamin, tanggal_lahir, alamat, role FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profil_data = $result->fetch_assoc();
$stmt->close();

if (!$profil_data) {
    die("Data profil tidak ditemukan.");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f6f8fc;
            font-family: 'Montserrat', sans-serif;
        }

        .profile-card {
            max-width: 700px;
            margin: 60px auto;
            border: none;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .profile-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 30px 20px;
            text-align: center;
        }

        .profile-header i {
            font-size: 3rem;
        }

        .profile-body {
            padding: 25px 30px;
        }

        .profile-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #f0f0f0;
            padding: 10px 0;
        }

        .profile-item span:first-child {
            color: #6c757d;
            font-weight: 500;
        }

        .btn-back, .btn-edit {
            border-radius: 30px;
            padding: 8px 20px;
            transition: 0.3s;
        }

        .btn-back {
            background-color: #2563eb;
            color: white;
        }

        .btn-back:hover {
            background-color: #1e3a8a;
        }

        .btn-edit {
            background-color: #16a34a;
            color: white;
        }

        .btn-edit:hover {
            background-color: #15803d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="profile-card shadow">
            <div class="profile-header">
                <i class="bi bi-person-circle"></i>
                <h4 class="mt-2 mb-0"><?= htmlspecialchars($profil_data['nama']) ?></h4>
                <small class="text-light"><?= htmlspecialchars($profil_data['username']) ?></small>
                <div class="mt-2">
                    <span class="<?= $role_class ?>"><?= ucfirst(htmlspecialchars($profil_data['role'])) ?></span>
                </div>
            </div>

            <div class="profile-body">
                <div class="profile-item"><span>Email</span><span><?= htmlspecialchars($profil_data['email']) ?></span></div>
                <div class="profile-item"><span>No. HP</span><span><?= htmlspecialchars($profil_data['no_hp']) ?></span></div>
                <div class="profile-item"><span>Jenis Kelamin</span><span><?= htmlspecialchars($profil_data['jenis_kelamin']) ?></span></div>
                <div class="profile-item"><span>Tanggal Lahir</span><span><?= htmlspecialchars(date('d M Y', strtotime($profil_data['tanggal_lahir']))) ?></span></div>
                <div class="profile-item"><span>Alamat</span><span style="text-align:right"><?= nl2br(htmlspecialchars($profil_data['alamat'])) ?></span></div>
            </div>

            <div class="text-center p-3 d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
                <a href="edit_profil.php" class="btn btn-edit"><i class="bi bi-pencil"></i> Edit Profil</a>
            </div>
        </div>
    </div>
</body>
</html>
