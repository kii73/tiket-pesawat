<?php
include "../koneksi.php";
$id=$_GET['id'];

$del=$mysql->query("DELETE FROM penerbagan WHERE id='$id'");

?>

<script>
    document.location.href='data.php';
</script>