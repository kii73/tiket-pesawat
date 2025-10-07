<?php include "boot.php"; ?>

<div class="container col-4 mt-5">
  <div class="card">
    <div class="card-body">
      <form action="" method="POST">
        <div class="text-center">
          <label for=""><b>INPUT DATA</b></label>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-tencent-qq text-primary me-2"></i>nama</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="nama" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-people-fill text-primary me-2"></i>no_penerbangan</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="no_penerbangan" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>kelas</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="kelas" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>asal</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="asal" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>tujuan</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="tujuan" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>waktu_berangkat</label>
          <input type="time" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="waktu_berangkat" required>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>harga</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="harga" required>
        </div>

        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label"><i class="bi bi-laptop text-primary me-2"></i>waktu_tiba</label>
          <input type="time" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="waktu_tiba" required>
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

  $nama = $_POST['nama'];
  $no_penerbangan = $_POST['no_penerbangan'];
  $kelas = $_POST['kelas'];
  $asal = $_POST['asal'];
  $tujuan = $_POST['tujuan'];
  $waktu_berangkat = $_POST['waktu_berangkat'];
  $harga = $_POST['harga'];
  $waktu_tiba = $_POST['waktu_tiba'];
  


  $simpan = $mysql->query("INSERT INTO pesawat (`nama`,`no_penerbangan`, `kelas`, `asal`, `tujuan`,`waktu_berangkat`,`harga`, `waktu_tiba`) VALUES ('$nama','$no_penerbangan','$kelas','$asal','$tujuan','$waktu_berangkat','$harga','$waktu_tiba')");

?>
  <script>
    document.location.href = 'data.php';
  </script>
<?php
}
?>