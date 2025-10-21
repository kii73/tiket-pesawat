<?php
include "../koneksi.php";
$id = $_GET['id'];
$mysql->query("DELETE FROM kode WHERE id_pesawat = '$id'");
$del = $mysql->query("DELETE FROM pesawat WHERE id = '$id'");


?>

<script>
    document.location.href = 'data.php';
</script>
