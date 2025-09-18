<?php
include "koneksi.php";
$id=$_GET['id'];

$del=$konek->query("delete from penerbagan where No='$id'");

?>

<script>
    document.location.href='data_siswa.php';
</script>