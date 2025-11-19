<?php
// Pastikan session sudah dimulai untuk mengakses $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sertakan koneksi database dan file boot/setup (jika ada)
// Asumsi: file koneksi.php berisi $mysql object/variable
include "../koneksi.php";
include "boot.php"; // Asumsi: boot.php berisi link CSS/JS bootstrap

// Cek hak akses admin (dari users.php)
$is_admin = false;
$user_id = $_SESSION["user_id"] ?? '';

if (!empty($user_id)) {
    $stmt_role = $mysql->prepare("SELECT role FROM users WHERE id=?");
    $stmt_role->bind_param("i", $user_id);
    $stmt_role->execute();
    $user_result_role = $stmt_role->get_result();
    $user_data_role = $user_result_role->fetch_assoc();
    if ($user_data_role && $user_data_role['role'] === 'admin') {
        $is_admin = true;
    }
    $stmt_role->close();
}

// Pengamanan: Hanya admin yang bisa mengakses
if (!$is_admin) {
    header("Location: ../index.php");
    exit;
}

$id_to_edit = $_GET['id'] ?? null;

// ----------------------------------------------------
// A. Ambil Data Pengguna untuk Ditampilkan di Form
// ----------------------------------------------------
if (empty($id_to_edit) || !is_numeric($id_to_edit)) {
    $_SESSION['delete_status'] = 'gagal';
    $_SESSION['delete_message'] = 'ID pengguna tidak valid.';
    header("Location: users.php");
    exit;
}

$data_user = null;
try {
    $stmt_fetch = $mysql->prepare("SELECT id, nama, username, email, no_hp, jenis_kelamin, tanggal_lahir, alamat, role FROM users WHERE id = ?");
    $stmt_fetch->bind_param("i", $id_to_edit);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    $data_user = $result_fetch->fetch_assoc();
    $stmt_fetch->close();
} catch (Exception $e) {
    // Handle error fetching data
}

if (!$data_user) {
    $_SESSION['delete_status'] = 'gagal';
    $_SESSION['delete_message'] = 'Data pengguna tidak ditemukan.';
    header("Location: users.php");
    exit;
}

// ----------------------------------------------------
// B. Proses Form Update
// ----------------------------------------------------
$update_message = '';
$update_status = ''; // sukses atau gagal

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $role = $_POST['role'] ?? '';
    $password_new = $_POST['password_new'] ?? ''; // Opsional, hanya diisi jika ingin ganti password

    // Validasi sederhana
    if (empty($nama) || empty($username) || empty($email) || empty($no_hp) || empty($jenis_kelamin) || empty($tanggal_lahir) || empty($alamat) || empty($role)) {
        $update_status = 'gagal';
        $update_message = 'Semua kolom harus diisi kecuali Password baru.';
    } else if (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan']) || !in_array($role, ['admin', 'user'])) {
        $update_status = 'gagal';
        $update_message = 'Pilihan Jenis Kelamin atau Role tidak valid.';
    } else {
        // Siapkan query update
        if (!empty($password_new)) {
            // Jika password diisi, enkripsi dan sertakan dalam update
            // Asumsi: Anda menggunakan MD5 ('21232f297a57a5a743894a0e4a801fc3' adalah MD5 dari 'admin' di DB)
            $password_hashed = md5($password_new); 
            $query_update = "UPDATE users SET nama=?, username=?, email=?, no_hp=?, jenis_kelamin=?, tanggal_lahir=?, alamat=?, role=?, password=? WHERE id=?";
            $stmt_update = $mysql->prepare($query_update);
            $stmt_update->bind_param("sssssssssi", $nama, $username, $email, $no_hp, $jenis_kelamin, $tanggal_lahir, $alamat, $role, $password_hashed, $id_to_edit);
        } else {
            // Jika password kosong, jangan ubah password
            $query_update = "UPDATE users SET nama=?, username=?, email=?, no_hp=?, jenis_kelamin=?, tanggal_lahir=?, alamat=?, role=? WHERE id=?";
            $stmt_update = $mysql->prepare($query_update);
            $stmt_update->bind_param("ssssssssi", $nama, $username, $email, $no_hp, $jenis_kelamin, $tanggal_lahir, $alamat, $role, $id_to_edit);
        }

        try {
            if ($stmt_update->execute()) {
                $update_status = 'sukses';
                $update_message = 'Data pengguna ' . htmlspecialchars($username) . ' berhasil diperbarui!';
                
                // Ambil ulang data terbaru setelah update berhasil untuk ditampilkan di form
                $stmt_fetch_new = $mysql->prepare("SELECT id, nama, username, email, no_hp, jenis_kelamin, tanggal_lahir, alamat, role FROM users WHERE id = ?");
                $stmt_fetch_new->bind_param("i", $id_to_edit);
                $stmt_fetch_new->execute();
                $result_fetch_new = $stmt_fetch_new->get_result();
                $data_user = $result_fetch_new->fetch_assoc(); // Update $data_user
                $stmt_fetch_new->close();

                // Redirect ke halaman daftar users setelah update
                $_SESSION['delete_status'] = $update_status;
                $_SESSION['delete_message'] = $update_message;
                header("Location: index.php?page=users");
                exit;

            } else {
                $update_status = 'gagal';
                $update_message = 'Gagal memperbarui data: ' . $stmt_update->error;
            }
            $stmt_update->close();
        } catch (Exception $e) {
            $update_status = 'gagal';
            $update_message = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Admin</title>
</head>

<body>

    <div class="container-fluid mt-4">

        <h3 class="mb-4 text-primary">Edit Data Pengguna: <?= htmlspecialchars($data_user['username']) ?></h3>
        
        <a href="index.php?page=users" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Pengguna</a>

        <?php if (!empty($update_message)) { ?>
            <div class="alert alert-<?= $update_status == 'sukses' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($update_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="card p-4 shadow-sm">
            <form method="POST" action="edit_users.php?id=<?= $id_to_edit ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($data_user['nama']) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($data_user['username']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($data_user['email']) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($data_user['no_hp']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="Laki-laki" <?= $data_user['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $data_user['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($data_user['tanggal_lahir']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($data_user['alamat']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user" <?= $data_user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $data_user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password_new" class="form-label">Password Baru (Kosongkan jika tidak ingin ganti)</label>
                        <input type="password" class="form-control" id="password_new" name="password_new">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save"></i> Simpan Perubahan</button>
            </form>
        </div>

    </div>
</body>

</html>