<?php
include "boot.php";
include "../koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    ?>
    <script>
        window.location.href = "../index.php"
    </script>
    <?php
    ob_end_flush();
    exit;
}

if (!isset($mysql) || !$mysql instanceof mysqli) {
    ?>
    <script>
        window.location.href = "../index.php"
    </script>
    <?php
    ob_end_flush();
    exit;
}


if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $stmt_update = $mysql->prepare("UPDATE bookings SET status = 'disetujui' WHERE id = ?");


    $stmt_update->bind_param("i", $id);
    $stmt_update->execute();
    $stmt_update->close();

    ?>
    <script>
        window.location.href = "index.php?page=pesanan"
    </script>
    <?php
    ob_end_flush();
    exit;
}
$query_tampil = "SELECT 
                    k.id, k.kode, k.status, 
                    p.nama, p.no_penerbangan, k.kelas, p.asal, p.tujuan, p.harga 
                 FROM bookings k 
                 LEFT JOIN pesawat p ON k.id_pesawat = p.id 
                 ORDER BY k.id DESC";

$tampil = $mysql->query($query_tampil);


ob_end_clean();
?>

<style>
    .header-color {
        color: #008080 !important;
    }

    .table-custom-header {
        background-color: #581845 !important;
        color: white;
    }

    .btn-custom-approve {
        background-color: #008080;
        border-color: #008080;
    }

    .btn-custom-approve:hover {
        background-color: #006666;
        border-color: #006666;
    }
</style>

<div class="container-fluid mt-3">
    <h3 class="mb-4 header-color">Data Pesanan Tiket Pesawat</h3>

    <table class="table table-striped table-bordered">
        <thead class="table-custom-header">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Kode Booking</th>
                <th scope="col">Nama Pesawat</th>
                <th scope="col">No. Penerbangan</th>
                <th scope="col">Kelas</th>
                <th scope="col">Asal</th>
                <th scope="col">Tujuan</th>
                <th scope="col">Harga Satuan</th>
                <th scope="col">Status</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 0;

            if ($tampil) {
                while ($data = $tampil->fetch_assoc()) {
                    $no++;

                    $status_class = $data["status"] == "menunggu" ? "text-danger fw-bold" : "text-success fw-bold";
            ?>
                    <tr>
                        <th scope="row"><?= $no; ?></th>
                        <td class="fw-bold"><?= htmlspecialchars($data['kode']) ?></td>
                        <td><?= htmlspecialchars($data['nama'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['no_penerbangan'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['kelas'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['asal'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($data['tujuan'] ?? '-') ?></td>
                        <td>Rp <?= number_format($data['harga'] ?? 0, 0, ',', '.') ?></td>
                        <td class="<?= $status_class ?>"><?= ucfirst(htmlspecialchars($data['status'])) ?></td>

                        <td>
                            <?php
                            if ($data["status"] == "menunggu") {
                            ?>
                                <a href="index.php?page=pesanan&id=<?= $data['id']; ?>" class="btn btn-sm btn-custom-approve text-white" onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI pesanan ini?');">
                                    Setujui
                                </a>
                            <?php
                            } else {
                            ?>
                                <span class="badge bg-success">Selesai</span>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="10" class="text-center text-danger">Gagal mengambil data dari database: ' . $mysql->error . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>