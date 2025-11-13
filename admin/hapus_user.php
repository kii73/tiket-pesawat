<?php
// Pastikan session sudah dimulai untuk mengakses $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sertakan koneksi database dan file boot/setup (jika ada)
// Asumsi: file koneksi.php berisi $mysql object/variable
include "../koneksi.php";
// include "boot.php"; // Tidak diperlukan di sini

// Cek hak akses admin
$is_admin = false;
$user_id = $_SESSION["user_id"] ?? '';

if (!empty($user_id)) {
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

// Pengamanan: Hanya admin yang bisa melanjutkan
if (!$is_admin) {
    header("Location: ../index.php");
    exit;
}

// Ambil ID pengguna yang akan dihapus dari parameter GET
$id_to_delete = $_GET['id'] ?? null;

if (empty($id_to_delete) || !is_numeric($id_to_delete)) {
    $_SESSION['delete_status'] = 'gagal';
    $_SESSION['delete_message'] = 'ID pengguna tidak valid.';
    header("Location: users.php"); // Ganti users.php dengan nama file daftar pengguna Anda
    exit;
}

// 1. Cek apakah pengguna memiliki booking terkait
try {
    $stmt_check = $mysql->prepare("SELECT COUNT(*) AS total_bookings FROM bookings WHERE id_user = ?");
    $stmt_check->bind_param("i", $id_to_delete);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $booking_count = $result_check->fetch_assoc()['total_bookings'];
    $stmt_check->close();

    if ($booking_count > 0) {
        // Jika ada booking, jangan izinkan penghapusan karena Foreign Key Constraint
        $_SESSION['delete_status'] = 'gagal';
        $_SESSION['delete_message'] = 'Gagal menghapus pengguna. Pengguna memiliki ' . $booking_count . ' pemesanan tiket pesawat terkait. Hapus pemesanan tersebut terlebih dahulu.';
    } else {
        // 2. Lakukan penghapusan (hanya jika tidak ada booking)
        $stmt_delete = $mysql->prepare("DELETE FROM users WHERE id = ?");
        $stmt_delete->bind_param("i", $id_to_delete);

        if ($stmt_delete->execute()) {
            if ($stmt_delete->affected_rows > 0) {
                $_SESSION['delete_status'] = 'sukses';
                $_SESSION['delete_message'] = 'Pengguna dengan ID ' . htmlspecialchars($id_to_delete) . ' berhasil dihapus.';
            } else {
                $_SESSION['delete_status'] = 'gagal';
                $_SESSION['delete_message'] = 'Gagal menghapus. Pengguna dengan ID ' . htmlspecialchars($id_to_delete) . ' tidak ditemukan.';
            }
        } else {
            $_SESSION['delete_status'] = 'gagal';
            $_SESSION['delete_message'] = 'Terjadi kesalahan database saat menghapus pengguna: ' . $stmt_delete->error;
        }
        $stmt_delete->close();
    }
} catch (Exception $e) {
    $_SESSION['delete_status'] = 'gagal';
    $_SESSION['delete_message'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
}

// Redirect kembali ke halaman daftar pengguna
header("Location: index.php?page=users"); // Pastikan users.php adalah file yang menampilkan daftar pengguna
exit;
?>