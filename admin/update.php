<?php
include "boot.php";
include "../koneksi.php";

$id = $_GET['id'];
$tampil = $konek->query("select*from penerbagan where id='$id'");
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
  include "koneksi.php";
  $nama = $_POST['nama'];
  $kelas = $_POST['kelas'];
  $jurusan = $_POST['jurusan'];

  $simpan = $konek->query("update siswa set Nama='$_POST[nama]',Kelas='$_POST[kelas]',Jurusan='$_POST[jurusan]'where No='$id'");
}
?>