<?php
// Catatan: File ini di-include oleh index.php admin, 
// jadi koneksi database ($mysql) dan session sudah tersedia.

// --- 1. Ambil Data Statistik Ringkasan ---

// Total Users
$query_users = "SELECT COUNT(id) AS total_users FROM users WHERE role='user'";
$result_users = $mysql->query($query_users);
$total_users = $result_users->fetch_assoc()['total_users'] ?? 0;

// Total Pesawat (Penerbangan Terdaftar)
$query_flights = "SELECT COUNT(id) AS total_flights FROM pesawat";
$result_flights = $mysql->query($query_flights);
$total_flights = $result_flights->fetch_assoc()['total_flights'] ?? 0;

// Total Pesanan (Kode/Tiket)
$query_orders = "SELECT COUNT(id) AS total_orders FROM bookings";
$result_orders = $mysql->query($query_orders);
$total_orders = $result_orders->fetch_assoc()['total_orders'] ?? 0;

// Total Pesanan Disetujui
$query_approved = "SELECT COUNT(id) AS approved_orders FROM bookings WHERE status='disetujui'";
$result_approved = $mysql->query($query_approved);
$approved_orders = $result_approved->fetch_assoc()['approved_orders'] ?? 0;

// Persentase Disetujui
$percent_approved = ($total_orders > 0) ? round(($approved_orders / $total_orders) * 100) : 0;


// --- 2. Ambil Data untuk Chart: Pesanan per Bulan (Contoh Sederhana) ---
// Catatan: Karena tabel 'kode' tidak memiliki created_at, kita akan menggunakan data status pesanan untuk chart.
// Kita akan membuat chart sederhana berdasarkan status pesanan

$query_status = "SELECT 
    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui_count,
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) AS menunggu_count
    FROM bookings";
$result_status = $mysql->query($query_status);
$status_data = $result_status->fetch_assoc();

$disetujui_count = $status_data['disetujui_count'];
$menunggu_count = $status_data['menunggu_count'];

// --- 3. Ambil Data untuk Chart: Pesawat Terpopuler (Berdasarkan jumlah kode/pesanan) ---
$query_popular_flights = "SELECT 
    p.nama, COUNT(k.id) AS total_pesanan
    FROM bookings k
    JOIN pesawat p ON k.id_pesawat = p.id
    GROUP BY p.nama
    ORDER BY total_pesanan DESC
    LIMIT 5";

$result_popular_flights = $mysql->query($query_popular_flights);
$flight_labels = [];
$flight_counts = [];
while ($row = $result_popular_flights->fetch_assoc()) {
    $flight_labels[] = $row['nama'];
    $flight_counts[] = $row['total_pesanan'];
}

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<div class="container-fluid">
    <h2 class="mt-3 mb-4">Dashboard Administrator</h2>
    <p class="text-muted">Ringkasan statistik sistem pemesanan tiket pesawat.</p>

    <div class="row mb-5">

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people-fill me-2"></i>Total Pelanggan</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $total_users; ?></p>
                    <p class="card-text">Pengguna Terdaftar (Role User)</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-airplane-fill me-2"></i>Total Penerbangan</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $total_flights; ?></p>
                    <p class="card-text">Penerbangan Aktif Terdaftar</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-ticket-fill me-2"></i>Total Pesanan</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $total_orders; ?></p>
                    <p class="card-text">Total Tiket yang Dipesan</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-check-circle-fill me-2"></i>Pesanan Disetujui</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $approved_orders; ?> <span class="fs-6">(<?php echo $percent_approved; ?>%)</span></p>
                    <p class="card-text">Persentase pesanan berhasil diproses</p>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-bold">Status Pesanan</div>
                <div class="card-body">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-bold">5 Pesawat Terpopuler (Berdasarkan Pesanan)</div>
                <div class="card-body">
                    <canvas id="popularFlightsChart"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    // Data untuk Chart 1: Status Pesanan
    const statusData = {
        labels: ['Disetujui', 'Menunggu'],
        datasets: [{
            label: 'Jumlah Pesanan',
            data: [<?php echo $disetujui_count; ?>, <?php echo $menunggu_count; ?>],
            backgroundColor: [
                'rgba(40, 167, 69, 0.7)', // Hijau (Success)
                'rgba(255, 193, 7, 0.7)' // Kuning (Warning)
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(255, 193, 7, 1)'
            ],
            borderWidth: 1
        }]
    };

    // Konfigurasi Chart 1
    const configStatus = {
        type: 'pie',
        data: statusData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            }
        },
    };

    // Render Chart 1
    const orderStatusChart = new Chart(
        document.getElementById('orderStatusChart'),
        configStatus
    );

    // Data untuk Chart 2: Pesawat Terpopuler
    const flightData = {
        labels: <?php echo json_encode($flight_labels); ?>,
        datasets: [{
            label: 'Total Pesanan',
            data: <?php echo json_encode($flight_counts); ?>,
            backgroundColor: 'rgba(0, 123, 255, 0.5)', // Biru
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 1
        }]
    };

    // Konfigurasi Chart 2
    const configFlights = {
        type: 'bar',
        data: flightData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false,
                }
            }
        },
    };

    // Render Chart 2
    const popularFlightsChart = new Chart(
        document.getElementById('popularFlightsChart'),
        configFlights
    );
</script>