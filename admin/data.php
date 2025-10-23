<?php include "boot.php"; ?>
<div class="container-fluid mt-3">
    <h3 class="mb-4 text-primary">Data Pesawat</h3>
    
<table class="table table-striped table-bordered mt-4">
  
    <thead>
      <tr>
        <th scope="col">no</th>
        <th scope="col">nama</th>
        <th scope="col">no_penerbangan</th>
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
      $tampil = $mysql->query("SELECT * FROM pesawat");
      foreach ($tampil as $data) {
        @$no++;
      ?>
        <tr>
          <th scope="row"><?= $no; ?></th>
          <td><?= $data['nama'] ?></td>
          <td><?= $data['no_penerbangan'] ?></td>
          <td><?= $data['asal'] ?></td>
          <td><?= $data['tujuan'] ?></td>
          <td><?= $data['waktu_berangkat'] ?></td>
          <td><?= $data['harga'] ?></td>
          <td><?= $data['waktu_tiba'] ?></td>

          <td>
            <a href="delete.php?id=<?= $data['id']; ?>" onclick="return confirm('apakah anda yakin?');">
              <i class="bi bi-trash3-fill text-danger"></i></button></a>

            <a href="update.php?id=<?= $data['id']; ?>" onclick="return confirm;">
              <i class="bi bi-pencil-square"></i></button></a>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  </div>