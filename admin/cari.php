<?php include "boot.php"; ?>

<table class="table">
    <table class="table">
  <thead>
    <tr>
       <th scope="col">no</th>
       <th scope="col">nama</th>
       <th scope="col">no_penerbagan</th>
      <th scope="col">kelas</th>
      <th scope="col">asal</th>
      <th scope="col">tujuan</th>
      <th scope="col">waktu_berangkat</th>
      <th scope="col">harga</th>
      <th scope="col">waktu_tiba</th>
      
      <th>Aksi</th>
    </tr>
  </thead>

  <tbody>
    <?php include "../koneksi.php";
    $tampil=$mysql->query("SELECT*FROM pesawat where no_penerbangan like'$_POST[cari]%' or kelas like'$_POST[cari]%' or asal like'$_POST[cari]%'");
    foreach ($tampil as $data) {
      @$no++;
        ?>
    <tr>
     <th scope="row"><?=$no;?></th>
      <td><?=$data['nama']?></td>
      <td><?=$data['no_penerbangan']?></td>
      <td><?=$data['kelas']?></td>
      <td><?=$data['asal']?></td>
      <td><?=$data['tujuan']?></td>
      <td><?=$data['waktu_berangkat']?></td>
      <td><?=$data['harga']?></td>
      <td><?=$data['waktu_tiba']?></td>
      <td>
        <a href="delete.php?id=<?=$data['id'];?>"onClick="return confirm('apakah anda yakin?');">
           <i class="bi bi-trash3-fill text-danger"></i></button></a>

          <a href="update.php?id=<?=$data['id'];?>" onClick="return confirm;">
            <i class="bi bi-pencil-square"></i></button></a>
    </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
