<?php
require_once '../config/auth.php';
cekLogin();
require_once '../config/koneksi.php';

$error = '';

// ==========================================
// 1. TANGKAP AKSI UPDATE STATUS WITH VALIDATION
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pesanan'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $status = $_POST['status'];
    $status_bayar = $_POST['status_bayar'];

    if ($status === 'selesai' && $status_bayar !== 'dikonfirmasi') {
        $error = "Gagal memproses! Pesanan #$id_pesanan tidak bisa diselesaikan sebelum status pembayaran diubah menjadi 'Lunas (Dikonfirmasi)'.";
    } else {
        try {
            $stmt_update = $pdo->prepare("UPDATE pesanan SET status = ?, status_bayar = ? WHERE id_pesanan = ?");
            $stmt_update->execute([$status, $status_bayar, $id_pesanan]);
            header("Location: pesanan.php?sukses=1");
            exit;
        } catch (PDOException $e) {
            $error = "Gagal memperbarui pesanan: " . $e->getMessage();
        }
    }
}

// ==========================================
// 2. OPTIMASI QUERY (MENGHINDARI N+1 PROBLEM)
// ==========================================
$stmt_pesanan = $pdo->query("SELECT * FROM pesanan WHERE status NOT IN ('selesai', 'dibatalkan') ORDER BY id_pesanan ASC");
$daftar_pesanan = $stmt_pesanan->fetchAll();

$pesanan_ids = array_column($daftar_pesanan, 'id_pesanan');
$detail_pesanan_map = [];

if (!empty($pesanan_ids)) {
    $placeholders = implode(',', array_fill(0, count($pesanan_ids), '?'));
    $sql_detail = "SELECT dp.*, m.nama_menu 
                   FROM detail_pesanan dp 
                   JOIN menu m ON dp.menu_id = m.id_menu 
                   WHERE dp.pesanan_id IN ($placeholders)";
    $stmt_detail = $pdo->prepare($sql_detail);
    $stmt_detail->execute($pesanan_ids);
    $semua_detail = $stmt_detail->fetchAll();

    foreach ($semua_detail as $detail) {
        $detail_pesanan_map[$detail['pesanan_id']][] = $detail;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan - Admin Pat-Pat Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark mb-4 shadow-sm" style="background-color: #1e3a8a;">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="menu.php">
                <img src="../assets/logo.png" alt="Logo Pat-Pat Cafe" style="height: 40px; width: auto; object-fit: contain;">
                <span class="mb-0 h3 fw-bold text-white">| Admin Panel</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <div class="d-flex flex-column flex-lg-row gap-2 ms-auto mt-3 mt-lg-0">
                    <a href="laporan_keuangan.php" class="btn btn-outline-light btn-sm fw-bold shadow-sm text-start text-lg-center">Rekap Keuangan</a>
                    <a href="pesanan.php" class="btn btn-light btn-sm fw-bold text-primary shadow-sm text-start text-lg-center">Kasir</a>
                    <a href="menu.php" class="btn btn-outline-light btn-sm fw-bold shadow-sm text-start text-lg-center">Manajemen Menu</a>
                    <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm text-start text-lg-center">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 pb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <h3 class="fw-bold m-0" style="color: #1e3a8a;"><i class="fas fa-bell me-2"></i>Pesanan Aktif</h3>
            <span id="statusRefresh" class="text-muted small align-self-start align-self-md-auto">
                <i class="fas fa-sync fa-spin me-1"></i> Auto-update
            </span>
        </div>

        <?php if (isset($_GET['sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>Status pesanan berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 fw-bold shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($daftar_pesanan)): ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0">
                <i class="fas fa-mug-hot fa-4x text-muted mb-3"></i>
                <h4 class="text-muted fw-bold">Belum ada pesanan aktif saat ini.</h4>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                <?php foreach ($daftar_pesanan as $pesanan): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 rounded-4">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">#<?= str_pad($pesanan['id_pesanan'], 4, '0', STR_PAD_LEFT) ?></h5>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm">PROSES</span>
                            </div>
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <div>
                                        <div class="fw-bold text-dark fs-5"><?= htmlspecialchars(escape($pesanan['nama_pelanggan'])) ?></div>
                                        <div class="text-muted small">
                                            <?= $pesanan['metode'] == 'table_service' ? '<i class="fas fa-chair text-success"></i> Meja ' . $pesanan['nomor_meja'] : '<i class="fas fa-shopping-bag text-warning"></i> Takeaway' ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></div>
                                        <span class="badge <?= $pesanan['status_bayar'] == 'belum' ? 'bg-secondary' : 'bg-primary' ?> rounded-pill" style="font-size: 0.7rem;">
                                            <?= strtoupper($pesanan['metode_bayar']) ?>: <?= strtoupper($pesanan['status_bayar']) ?>
                                        </span>
                                    </div>
                                </div>

                                <ul class="list-unstyled mb-0">
                                    <?php 
                                    $detail_items = $detail_pesanan_map[$pesanan['id_pesanan']] ?? [];
                                    foreach ($detail_items as $item): 
                                    ?>
                                        <li class="d-flex justify-content-between border-bottom border-light pb-1 mb-1 small">
                                            <span class="fw-bold text-secondary"><?= $item['jumlah'] ?>x <?= htmlspecialchars($item['nama_menu']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="card-footer bg-white border-top-0 pb-3 pt-0">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAksi<?= $pesanan['id_pesanan'] ?>">
                                        Kelola Pesanan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalAksi<?= $pesanan['id_pesanan'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Kelola Pesanan #<?= $pesanan['id_pesanan'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <?php if ($pesanan['metode_bayar'] == 'transfer'): ?>
                                        <div class="mb-4 text-center bg-light p-3 rounded-3 border">
                                            <h6 class="fw-bold mb-2">Bukti Transfer (QRIS)</h6>
                                            <?php if (!empty($pesanan['bukti_transfer'])): ?>
                                                <img src="../uploads/bukti_transfer/<?= htmlspecialchars($pesanan['bukti_transfer']) ?>" class="img-fluid rounded shadow-sm mb-2" style="max-height: 250px;">
                                            <?php else: ?>
                                                <p class="text-danger small">Pelanggan tidak melampirkan bukti.</p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST">
                                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status Pembayaran</label>
                                            <select name="status_bayar" class="form-select border-2 border-primary">
                                                <option value="belum" <?= $pesanan['status_bayar'] == 'belum' ? 'selected' : '' ?>>Belum Lunas / Menunggu Cek</option>
                                                <option value="dikonfirmasi" <?= $pesanan['status_bayar'] == 'dikonfirmasi' ? 'selected' : '' ?>>Lunas (Dikonfirmasi)</option>
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Status Dapur</label>
                                            <select name="status" class="form-select border-2 border-warning">
                                                <option value="proses" <?= ($pesanan['status'] !== 'selesai') ? 'selected' : '' ?>>Sedang Diproses</option>
                                                <option value="selesai" <?= ($pesanan['status'] === 'selesai') ? 'selected' : '' ?>>Selesai (Arsip)</option>
                                            </select>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" name="update_pesanan" class="btn btn-success w-100 fw-bold rounded-pill">Simpan Perubahan</button>
                                            <a href="cetak_struk.php?id=<?= $pesanan['id_pesanan'] ?>" target="_blank" class="btn btn-outline-dark fw-bold rounded-pill px-4" title="Cetak Struk">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // =================================================================
        // FITUR PEMBERSIH RIWAYAT (Mencegah Notifikasi Zombi)
        // =================================================================
        // Begitu halaman dimuat, diam-diam hapus ?sukses=1 dari URL bar
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }

        const REFRESH_INTERVAL_MS = 30000; 
        let refreshTimer;
        const statusIndicator = document.getElementById('statusRefresh');

        function startAutoRefresh() {
            refreshTimer = setInterval(function() {
                // Jangan gunakan window.location.reload() karena akan mengirim ulang POST
                // Gunakan href ke pathname murni untuk mendapatkan halaman yang benar-benar bersih
                window.location.href = window.location.pathname;
            }, REFRESH_INTERVAL_MS);
            
            if(statusIndicator) {
                statusIndicator.innerHTML = '<i class="fas fa-sync fa-spin me-1"></i> Auto-update';
                statusIndicator.classList.remove('text-danger');
                statusIndicator.classList.add('text-muted');
            }
        }

        function stopAutoRefresh() {
            clearInterval(refreshTimer);
            
            if(statusIndicator) {
                statusIndicator.innerHTML = '<i class="fas fa-pause-circle me-1"></i> Update dijeda';
                statusIndicator.classList.remove('text-muted');
                statusIndicator.classList.add('text-danger');
            }
        }

        startAutoRefresh();

        document.addEventListener('DOMContentLoaded', function () {
            const modals = document.querySelectorAll('.modal');
            
            modals.forEach(modal => {
                modal.addEventListener('show.bs.modal', function () {
                    stopAutoRefresh();
                });
                
                modal.addEventListener('hidden.bs.modal', function () {
                    startAutoRefresh();
                });
            });
        });
    </script>
</body>
</html>