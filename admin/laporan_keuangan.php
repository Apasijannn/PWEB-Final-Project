<?php
require_once '../config/auth.php';
cekLogin(); 
require_once '../config/koneksi.php';

// ==================================================================
// 1. PENGATURAN FILTER BULAN & TAHUN (DENGAN BATASAN 3 TAHUN)
// ==================================================================
$bulan_pilihan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilihan = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Validasi tambahan: Pastikan tahun yang dipilih tidak lebih dari 3 tahun ke belakang
$tahun_maksimal = date('Y');
$tahun_minimal = $tahun_maksimal - 2;
if ($tahun_pilihan < $tahun_minimal) {
    $tahun_pilihan = $tahun_minimal;
}

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// ==================================================================
// 2. QUERY RINGKASAN KEUANGAN (DENGAN FILTER 3 TAHUN)
// ==================================================================
$stmt_ringkasan = $pdo->prepare("
    SELECT 
        SUM(total) as total_omzet, 
        COUNT(id_pesanan) as total_transaksi 
    FROM pesanan 
    WHERE status = 'selesai' 
      AND MONTH(created_at) = ? 
      AND YEAR(created_at) = ?
      AND created_at >= DATE_SUB(NOW(), INTERVAL 3 YEAR)
");
$stmt_ringkasan->execute([$bulan_pilihan, $tahun_pilihan]);
$ringkasan = $stmt_ringkasan->fetch();

$total_omzet = $ringkasan['total_omzet'] ?? 0;
$total_transaksi = $ringkasan['total_transaksi'] ?? 0;

// ==================================================================
// 3. QUERY BEST SELLING MENU (DENGAN FILTER 3 TAHUN)
// ==================================================================
$stmt_laris = $pdo->prepare("
    SELECT 
        m.nama_menu, 
        m.kategori, 
        SUM(dp.jumlah) as total_terjual,
        SUM(dp.jumlah * dp.harga_satuan) as kontribusi_omzet
    FROM detail_pesanan dp
    JOIN menu m ON dp.menu_id = m.id_menu
    JOIN pesanan p ON dp.pesanan_id = p.id_pesanan
    WHERE p.status = 'selesai'
      AND MONTH(p.created_at) = ?
      AND YEAR(p.created_at) = ?
      AND p.created_at >= DATE_SUB(NOW(), INTERVAL 3 YEAR)
    GROUP BY dp.menu_id
    ORDER BY total_terjual DESC
    LIMIT 5
");
$stmt_laris->execute([$bulan_pilihan, $tahun_pilihan]);
$menu_terlaris = $stmt_laris->fetchAll();

// ==================================================================
// 4. QUERY LOG ARSIP TRANSAKSI (DENGAN FILTER 3 TAHUN)
// ==================================================================
$stmt_riwayat = $pdo->prepare("
    SELECT * FROM pesanan 
    WHERE status = 'selesai'
      AND MONTH(created_at) = ?
      AND YEAR(created_at) = ?
      AND created_at >= DATE_SUB(NOW(), INTERVAL 3 YEAR)
    ORDER BY created_at DESC
");
$stmt_riwayat->execute([$bulan_pilihan, $tahun_pilihan]);
$daftar_riwayat = $stmt_riwayat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Laporan Keuangan - Pat-Pat Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark mb-4 shadow-sm" style="background-color: #1e3a8a;">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="menu.php">
                <img src="../assets/logo.png" alt="Logo" style="height: 40px; width: auto; object-fit: contain;">
                <span class="mb-0 h3 fw-bold text-white">| Admin Panel</span>
            </a>
            <div class="d-flex gap-2">
                <a href="laporan_keuangan.php" class="btn btn-light btn-sm fw-bold text-primary shadow-sm">Rekap Keuangan</a>
                <a href="pesanan.php" class="btn btn-outline-light btn-sm fw-bold">Kasir</a>
                <a href="menu.php" class="btn btn-outline-light btn-sm fw-bold">Manajemen Menu</a>
                <a href="logout.php" class="btn btn-danger btn-sm fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-lg-3 col-md-12 text-center text-lg-start mb-2 mb-lg-0">
                    <h4 class="fw-bold m-0" style="color: #1e3a8a;">
                        <i class="fas fa-chart-line me-2"></i>Rekap Periode
                    </h4>
                </div>
                <div class="col-lg-3 col-md-4">
                    <select name="bulan" class="form-select fw-bold border-2">
                        <?php foreach ($nama_bulan as $m_num => $m_name): ?>
                            <option value="<?= $m_num ?>" <?= $bulan_pilihan == $m_num ? 'selected' : '' ?>><?= $m_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="tahun" class="form-select fw-bold border-2">
                        <?php for ($i = date('Y'); $i >= date('Y') - 2; $i--): ?>
                            <option value="<?= $i ?>" <?= $tahun_pilihan == $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-lg-4 col-md-4 d-flex justify-content-center justify-content-lg-end gap-2">
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    
                    <a href="export_csv.php?bulan=<?= $bulan_pilihan ?>&tahun=<?= $tahun_pilihan ?>" class="btn btn-success fw-bold rounded-pill px-3 shadow-sm" title="Export Excel/CSV">
                        <i class="fas fa-file-excel me-1"></i> Export
                    </a>
                </div>
            </form>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-4 border-start border-primary border-5">
                    <div class="text-muted small fw-bold text-uppercase">Total Omzet</div>
                    <h2 class="fw-bold text-success mt-1">Rp <?= number_format($total_omzet, 0, ',', '.') ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-4 border-start border-warning border-5">
                    <div class="text-muted small fw-bold text-uppercase">Transaksi Sukses</div>
                    <h2 class="fw-bold text-dark mt-1"><?= $total_transaksi ?> Pesanan</h2>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-5 col-lg-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold mb-3" style="color: #1e3a8a;"><i class="fas fa-crown text-warning me-2"></i>5 Best Seller</h5>
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-secondary small">
                            <tr><th>Menu</th><th class="text-center">Terjual</th><th class="text-end">Omzet</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menu_terlaris as $laris): ?>
                            <tr>
                                <td><span class="fw-bold text-dark"><?= htmlspecialchars($laris['nama_menu']) ?></span></td>
                                <td class="text-center fw-bold text-primary"><?= $laris['total_terjual'] ?></td>
                                <td class="text-end fw-bold text-success">Rp <?= number_format($laris['kontribusi_omzet'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-xl-7 col-lg-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold mb-3" style="color: #1e3a8a;"><i class="fas fa-history me-2"></i>Log Transaksi (Arsip 3 Tahun)</h5>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-secondary small sticky-top">
                                <tr><th>ID / Waktu</th><th>Pelanggan</th><th>Metode</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daftar_riwayat as $riwayat): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark">#<?= str_pad($riwayat['id_pesanan'], 4, '0', STR_PAD_LEFT) ?></span>
                                        <span class="text-muted d-block" style="font-size: 0.7rem;"><?= date('d/m/Y H:i', strtotime($riwayat['created_at'])) ?></span>
                                    </td>
                                    <td><span class="fw-bold text-dark"><?= htmlspecialchars($riwayat['nama_pelanggan']) ?></span></td>
                                    <td><span class="badge bg-success rounded-pill"><?= $riwayat['metode_bayar'] ?></span></td>
                                    <td class="text-end fw-bold text-dark">Rp <?= number_format($riwayat['total'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>