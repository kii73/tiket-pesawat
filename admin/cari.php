<?php include "boot.php"; ?>

<table class="table">
    <table class="table">
  <thead>
    <tr>
       <th scope="col">kode_penerbagan</th>
      <th scope="col">maskapai</th>
      <th scope="col">bandara_asal</th>
      <th scope="col">bandara_tujuan</th>
      <th scope="col">tangga_berangkat</th>
      <th scope="col">jam_berangkat</th>
      <th scope="col">tanggal_tiba</th>
      <th scope="col">jam_tiba</th>
      <th scope="col">durasi</th>
      <th scope="col">harga_tiket</th>
      <th>Aksi</th>
    </tr>
  </thead>

  <tbody>
    <?php include "../koneksi.php";
    $tampil=$mysql->query("SELECT*FROM penerbagan where kode_penerbagan like'$_POST[cari]%' or maskapai like'$_POST[cari]%'");
    foreach ($tampil as $data) {
      @$no++;
        ?>
    <tr>
     <th scope="row"><?=$no;?></th>
      <td><?=$data['kode_penerbagan']?></td>
      <td><?=$data['maskapai']?></td>
      <td><?=$data['bandara_asal']?></td>
      <td><?=$data['bandara_tujuan']?></td>
      <td><?=$data['tanggal_berangkat']?></td>
      <td><?=$data['jam_berangkat']?></td>
      <td><?=$data['tanggal_tiba']?></td>
      <td><?=$data['jam_tiba']?></td>
      <td><?=$data['durasi']?></td>
      <td><?=$data['harga_tiket']?></td>
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
