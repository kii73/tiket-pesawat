<?php 
include "boot.php";
include "koneksi.php";
$id=$_GET['id'];
$tampil =$konek->query("select*from penerbagan where No='$id'");
$data = $tampil->fetch_array();
?>

<div class ="container col-5 mt-2">
  <div class ="card">
    <div class ="card-body bg-info">
<form action="" method="post">
  <div class="text-center">
    <label for=""><b>INPUT DATA</b></label>
  </div>
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label"><i class="bi bi-trash3-fill text-primary"></i>Nama</label>
    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="nama" value="<?=$data['Nama']?>" required>
  </div>
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Kelas</label>
    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="kelas" value="<?=$data['Kelas']?>">
  </div>
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Jurusan</label>
    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="jurusan" value="<?=$data['Jurusan']?>" >
  </div>

  <div class="text-center">
     <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
  </div>
</form>
  </div>
</div>
</div>

<?php
if(isset($_POST['simpan'])){
  include "koneksi.php";
$nama=$_POST['nama'];
$kelas=$_POST['kelas'];
$jurusan=$_POST['jurusan'];

$simpan=$konek->query("update siswa set Nama='$_POST[nama]',Kelas='$_POST[kelas]',Jurusan='$_POST[jurusan]'where No='$id'");
}
?>