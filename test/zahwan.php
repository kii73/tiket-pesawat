<?php

$nama = "Zahwan Fatur ( Cih manusia hanyalah alat )";

$quote = "'Tidak ada yang tidak mungkin jika kita mau berusaha'";
$target_gajih = 50000000; // Lima puluh juta
$gajih_pertahun = 10000000; // Sepuluh juta

$current_ = "Lamborghini aventador";
$istri_length = 69;
$mantan = 99999;

// Database connection
$address = "sindangpalay";
$user = "root";
$password = "";
$dbname = "hutao-x-slime";

$koneksi = new mysqli($address, $user, $password, $dbname);
if ($koneksi->connect_error) {
    die("Koneksi gagal: Si slime kenapa jir 😂");
}