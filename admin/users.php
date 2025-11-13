<?php
include "../koneksi.php";
include "boot.php";
// --- Di file users.php (atau file yang menampilkan daftar pengguna) ---

// Tampilkan Pesan Status Penghapusan
if (isset($_SESSION['delete_status'])) {
    $status = $_SESSION['delete_status'];
    $message = $_SESSION['delete_message'];
    unset($_SESSION['delete_status']); // Hapus pesan setelah ditampilkan
    unset($_SESSION['delete_message']);

    $alert_class = $status == 'sukses' ? 'alert-success' : 'alert-danger';

?>
    <div class="alert <?= $alert_class ?> alert-dismissible fade show mt-3" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
}
// --- Lanjutkan dengan kode HTML <body> Anda ---

// Pastikan session sudah dimulai jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION["user_id"] ?? '';


$is_admin = false;
if (!empty($user_id)) {

    // Menggunakan prepared statement untuk keamanan
    $stmt = $mysql->prepare("SELECT role FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    if ($user_data && $user_data['role'] === 'admin') {
        $is_admin = true;
    }
    $stmt->close();
}

// Pengamanan: Hanya admin yang bisa mengakses halaman ini
if (!$is_admin) {
    header("Location: ../index.php");
    exit;
}

// Query untuk menampilkan semua pengguna
$query = "SELECT id, nama, username, email, no_hp, jenis_kelamin, tanggal_lahir, alamat, role FROM users ORDER BY id DESC";
$tampil_users = $mysql->query($query);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin</title>
    <style>
        .header-color {
            color: #008080 !important;
        }

        .table-custom-header {
            background-color: #581845 !important;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container-fluid mt-3">

        <h3 class="mb-4 header-color">Manajemen Pengguna Terdaftar</h3>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">ID</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">No. HP</th>
                        <th scope="col">J. Kelamin</th>
                        <th scope="col">Tgl. Lahir</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Role</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    if ($tampil_users && $tampil_users->num_rows > 0) {
                        while ($data = $tampil_users->fetch_assoc()) {
                            $no++;
                            $user_id_data = htmlspecialchars($data['id']); // Ambil ID untuk aksi
                    ?>
                            <tr>
                                <th scope="row"><?= $no; ?></th>
                                <td><?= $user_id_data ?></td>
                                <td><?= htmlspecialchars($data['nama']) ?></td>
                                <td><?= htmlspecialchars($data['username']) ?></td>
                                <td><?= htmlspecialchars($data['email']) ?></td>
                                <td><?= htmlspecialchars($data['no_hp']) ?></td>
                                <td><?= htmlspecialchars($data['jenis_kelamin']) ?></td>
                                <td><?= htmlspecialchars(date('d F Y', strtotime($data['tanggal_lahir']))) ?></td>
                                <td><?= nl2br(htmlspecialchars($data['alamat'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $data['role'] == 'admin' ? 'danger' : 'success' ?>">
                                        <?= ucfirst(htmlspecialchars($data['role'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="./edit_users.php?id=<?= $user_id_data ?>" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <a href="./hapus_user.php?id=<?= $user_id_data ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna <?= htmlspecialchars($data['username']) ?>?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">Tidak ada data pengguna terdaftar.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>