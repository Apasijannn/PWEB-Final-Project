<?php
session_start();
require_once 'config/koneksi.php';

// Logika Update Keranjang (+ / - / Hapus)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id_update = $_GET['id'];

    if (isset($_SESSION['keranjang'][$id_update])) {
        if ($action === 'plus') {
            $_SESSION['keranjang'][$id_update]++;
        } elseif ($action === 'minus') {
            $_SESSION['keranjang'][$id_update]--;
            if ($_SESSION['keranjang'][$id_update] <= 0) {
                unset($_SESSION['keranjang'][$id_update]);
            }
        }
    }
    header("Location: checkout.php");
    exit;
}

require_once 'includes/header.php';

$keranjang = $_SESSION['keranjang'] ?? [];
$items = [];
$total_bayar = 0;

if (!empty($keranjang)) {
    $placeholders = implode(',', array_fill(0, count($keranjang), '?'));
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE id_menu IN ($placeholders)");
    $stmt->execute(array_keys($keranjang));
    $items = $stmt->fetchAll();
    
    foreach ($items as $item) {
        $total_bayar += $item['harga'] * $keranjang[$item['id_menu']];
    }
}
?>

<div class="container mt-5 mb-5" style="max-width: 700px;">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h2 class="fw-bold mb-4 text-center" style="color: var(--primary-color);">Keranjang Anda</h2>

        <?php if (empty($items)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada menu yang kamu pilih.</h5>
                <a href="index.php" class="btn btn-success rounded-pill mt-3 px-4">Lihat Daftar Menu</a>
            </div>
        <?php else: ?>
            
            <ul class="list-group list-group-flush mb-4">
                <?php foreach ($items as $item): 
                    if (!isset($keranjang[$item['id_menu']])) continue;
                    $qty = $keranjang[$item['id_menu']];
                    $subtotal = $item['harga'] * $qty;
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-0">
                        <div class="me-auto pe-3">
                            <h6 class="fw-bold mb-1" style="color: var(--primary-color);"><?= htmlspecialchars($item['nama_menu']) ?></h6>
                            <small class="text-muted d-block">Rp <?= number_format($item['harga'], 0, ',', '.') ?></small>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-between bg-light rounded-pill px-2 py-1 border" style="width: 85px;">
                                <a href="checkout.php?action=minus&id=<?= $item['id_menu'] ?>" class="text-dark p-0 fs-6 text-decoration-none"><i class="fas fa-minus-circle"></i></a>
                                <span class="fw-bold fs-6"><?= $qty ?></span>
                                <a href="checkout.php?action=plus&id=<?= $item['id_menu'] ?>" class="text-success p-0 fs-6 text-decoration-none"><i class="fas fa-plus-circle"></i></a>
                            </div>
                            <span class="fw-bold text-dark fs-6 text-end" style="min-width: 90px;">
                                Rp <?= number_format($subtotal, 0, ',', '.') ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
                
                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-0 border-top border-2 mt-2 bg-light rounded-3 px-3">
                    <span class="fw-bold text-dark fs-5">Subtotal</span>
                    <span class="fs-5 fw-bold text-dark">Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
                </li>
            </ul>

            <div class="d-grid mt-4">
                <button type="button" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm py-3" data-bs-toggle="modal" data-bs-target="#modalLengkapiData">
                    Lengkapi Data Pesanan <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($items)): ?>
<div class="modal fade" id="modalLengkapiData" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h4 class="fw-bold" style="color: var(--primary-color);">Data Pelanggan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <form action="pembayaran.php" method="POST">
                    
                    <h6 class="fw-bold mb-2">Pilih Layanan</h6>
                    <div class="d-flex gap-3 mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metode" id="metodeDineIn" value="table_service" required onchange="toggleFormLayanan()" checked>
                            <label class="form-check-label fw-bold" for="metodeDineIn">Dine In</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metode" id="metodeTakeaway" value="takeaway" required onchange="toggleFormLayanan()">
                            <label class="form-check-label fw-bold" for="metodeTakeaway">Takeaway</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pemesan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" required placeholder="Masukkan nama Anda">
                    </div>
                    
                    <div class="mb-4" id="boxNomorMeja">
                        <label class="form-label fw-bold">Nomor Meja</label>
                        <input type="number" name="nomor_meja" id="inputNomorMeja" class="form-control" required placeholder="Contoh: 5">
                    </div>

                    <button type="submit" name="lanjut_pembayaran" class="btn btn-primary w-100 fw-bold rounded-pill py-3" style="background-color: var(--primary-color);">
                        Lihat Rincian Biaya <i class="fas fa-file-invoice-dollar ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFormLayanan() {
        var takeaway = document.getElementById('metodeTakeaway').checked;
        var boxMeja = document.getElementById('boxNomorMeja');
        var inputMeja = document.getElementById('inputNomorMeja');
        
        if (takeaway) {
            boxMeja.classList.add('d-none');
            inputMeja.removeAttribute('required');
            inputMeja.value = ''; 
        } else {
            boxMeja.classList.remove('d-none');
            inputMeja.setAttribute('required', 'required');
        }
    }
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require_once 'includes/footer.php'; ?>