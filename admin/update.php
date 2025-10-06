<?php
include "boot.php";
include "../koneksi.php";

$id = $_GET['id'];
$tampil = $mysql->query("select*from penerbagan where id='$id'");
$data = $tampil->fetch_array();
?>

<div class="container col-4 mt-5">
  <div class="card p-4">
    <div class="card-body ">
      <form action="" method="post">
        <div class="text-center">
          <label for=""><b>INPUT DATA</b></label>
        </div>
         </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-people-fill text-primary me-2"></i>kode_penerbagan</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["kode_penerbagan"] ?>" name="kode_penerbagan" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-tencent-qq text-primary me-2"></i>maskapai</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["maskapai"] ?>" name="maskapai" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>bandara_asal</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["bandara_asal"] ?>" name="bandara_asal" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>bandara_tujuan</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["bandara_tujuan"] ?>" name="bandara_tujuan" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>tanggal_berangkat</label>
          <input type="date" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["tanggal_berangkat"] ?>" name="tanggal_berangkat" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>jam_berangkat</label>
          <input type="time" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["jam_berangkat"] ?>" name="jam_berangkat" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>tanggal_tiba</label>
          <input type="date" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["tanggal_tiba"] ?>" name="tanggal_tiba" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>jam_tiba</label>
          <input type="time" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["jam_tiba"] ?>" name="jam_tiba" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>durasi</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["durasi"] ?>" name="durasi" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>harga_tiket</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?= $data["harga_tiket"] ?>" name="harga_tiket" required>
        </div>

        <div class="text-center">
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
if (isset($_POST['simpan'])) {
  include "../koneksi.php";
   $kode_penerbagan = $_POST['kode_penerbagan'];
  $maskapai = $_POST['maskapai'];
  $bandara_asal = $_POST['bandara_asal'];
  $bandara_tujuan = $_POST['bandara_tujuan'];
  $tanggal_berangkat = $_POST['tanggal_berangkat'];
  $jam_berangkat = $_POST['jam_berangkat'];
  $tanggal_tiba = $_POST['tanggal_tiba'];
  $jam_tiba = $_POST['jam_tiba'];
  $durasi = $_POST['durasi'];
  $harga_tiket = $_POST['harga_tiket'];
  $simpan = $mysql->query("UPDATE penerbagan SET kode_penerbagan='$_POST[kode_penerbagan]',maskapai='$_POST[maskapai]',bandara_asal='$_POST[bandara_asal]',bandara_tujuan='$_POST[bandara_tujuan]',tanggal_berangkat='$_POST[tanggal_berangkat]',jam_berangkat='$_POST[jam_berangkat]',tanggal_tiba='$_POST[tanggal_tiba]',jam_tiba='$_POST[jam_tiba]',durasi='$_POST[durasi]',harga_tiket='$_POST[harga_tiket]' where id='$id'");
?>
  <script>
    document.location.href = 'data.php';
  </script>
<?php
}
?>