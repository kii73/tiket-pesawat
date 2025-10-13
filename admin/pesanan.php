<?php include "boot.php";
include "../koneksi.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $mysql->query("UPDATE kode SET status = 'disetujui' WHERE id = $id");
}

?>

<table class="table">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">no</th>
                <th scope="col">nama</th>
                <th scope="col">no_penerbangan</th>
                <th scope="col">kelas</th>
                <th scope="col">asal</th>
                <th scope="col">tujuan</th>
                <th scope="col">harga</th>
                <th scope="col">kode booking</th>
                <th scope="col">status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php include "../koneksi.php";
            $tampil = $mysql->query("SELECT p.*, p.id as id_pesawat, k.* FROM kode k LEFT JOIN pesawat p ON k.id_pesawat = p.id");
            foreach ($tampil as $data) {
                @$no++;
            ?>
                <tr>
                    <th scope="row"><?= $no; ?></th>
                    <td><?= $data['nama'] ?></td>
                    <td><?= $data['no_penerbangan'] ?></td>
                    <td><?= $data['kelas'] ?></td>
                    <td><?= $data['asal'] ?></td>
                    <td><?= $data['tujuan'] ?></td>
                    <td><?= $data['harga'] ?></td>
                    <td><?= $data['kode'] ?></td>
                    <td class="<?php echo $data["status"] == "menunggu" ? "text-danger" : "text-success" ?>"><?= $data['status'] ?></td>

                    <td>
                        <?php
                        if ($data["status"] == "menunggu") {
                        ?>
                            <a href="pesanan.php?id=<?= $data['id']; ?>" style="text-decoration: none;" class="btn btn-primary" onclick="return confirm('apakah anda yakin?');">Setujui</a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>