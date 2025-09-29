<?php include "boot.php"; ?>

<div class="container col-4 mt-5">
  <div class="card">
    <div class="card-body">
      <form action="" method="POST">
        <div class="text-center">
          <label for=""><b>INPUT DATA</b></label>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-people-fill text-primary me-2"></i>kode_penerbagan</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="kode_penerbagan" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-tencent-qq text-primary me-2"></i>maskapai</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="maskapai" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>bandara_asal</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="bandara_asal" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>bandara_tujuan</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="bandara_tujuan" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>tanggal_berangkat</label>
          <input type="date" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="tanggal_berangkat" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>jam_berangkat</label>
          <input type="time" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="jam_berangkat" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>tanggal_tiba</label>
          <input type="date" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="tanggal_tiba" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>jam_tiba</label>
          <input type="time" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="jam_tiba" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>durasi</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="durasi" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>harga_tiket</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="harga_tiket" required>
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


  $simpan = $konek->query("INSERT INTO `penerbagan` (`kode_penerbagan`, `maskapai`, `bandara_asal`, `bandara_tujuan`, `tanggal_berangkat`, `jam_berangkat`, `tanggal_tiba`, `jam_tiba`, `durasi`, `harga_tiket`) VALUES ($kode_penerbagan,'$maskapai','$bandara_asal','$bandara_tujuan','$tanggal_berangkat','$jam_berangkat','$tanggal_tiba','$jam_tiba','$durasi','$harga_tiket')");

?>
  <script>
    document.location.href = 'data.php';
  </script>
<?php
}
?>