<?php
include "./koneksi.php";
session_start();

$user_id = $_SESSION["user_id"] ?? '';
if (empty($user_id)) {
    header("Location: login.php");
    exit;
}

// Ambil data pengguna
$stmt = $mysql->prepare("SELECT nama, email, no_hp, jenis_kelamin, tanggal_lahir, alamat, username FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $jk = trim($_POST['jenis_kelamin']);
    $tgl = trim($_POST['tanggal_lahir']);
    $alamat = trim($_POST['alamat']);
    $password_baru = trim($_POST['password_baru']);

    // Update data dasar
    $stmt = $mysql->prepare("UPDATE users SET nama=?, email=?, no_hp=?, jenis_kelamin=?, tanggal_lahir=?, alamat=? WHERE id=?");
    $stmt->bind_param("ssssssi", $nama, $email, $no_hp, $jk, $tgl, $alamat, $user_id);
    $stmt->execute();
    $stmt->close();

    // Jika user isi password baru
    if (!empty($password_baru)) {
        $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
        $stmt = $mysql->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "<script>alert('Profil berhasil diperbarui!');window.location='profil.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f6f8fc;
            font-family: 'Montserrat', sans-serif;
        }
        .card {
            max-width: 700px;
            margin: 60px auto;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .card-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .btn-save {
            background-color: #16a34a;
            color: white;
        }
        .btn-save:hover {
            background-color: #15803d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card shadow">
            <div class="card-header text-center">
                <h4><i class="bi bi-pencil-square"></i> Edit Profil Saya</h4>
            </div>
            <div class="card-body p-4">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($user['no_hp']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="Laki-laki" <?= $user['jenis_kelamin']=='Laki-laki'?'selected':'' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $user['jenis_kelamin']=='Perempuan'?'selected':'' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($user['tanggal_lahir']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($user['alamat']) ?></textarea>
                    </div>
                    <hr>
                    <h6 class="text-primary">Ubah Kata Sandi (Opsional)</h6>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" placeholder="Isi jika ingin mengganti sandi">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="profil.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal</a>
                        <button type="submit" class="btn btn-save"><i class="bi bi-check-circle"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
