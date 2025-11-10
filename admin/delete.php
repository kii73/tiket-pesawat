<?php
include "../koneksi.php";


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    
    $delete_bookings = $mysql->query("DELETE FROM bookings WHERE id_pesawat = '$id'");

    if ($delete_bookings) {
        
        $delete_pesawat = $mysql->query("DELETE FROM pesawat WHERE id = '$id'");

        if ($delete_pesawat) {
           
            header("Location: index.php?page=data");
            exit;
        } else {
            
            echo "<script>alert('Gagal menghapus data pesawat: " . $mysql->error . "'); window.location.href='index.php?page=data';</script>";
        }
    } else {
        
        echo "<script>alert('Gagal menghapus data bookings terkait: " . $mysql->error . "'); window.location.href='index.php?page=data';</script>";
    }
} else {
    
    header("Location: index.php?page=data");
    exit;
}
?>