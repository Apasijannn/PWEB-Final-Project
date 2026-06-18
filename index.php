<?php
// Tarik file koneksi
require_once 'config/koneksi.php';

// Tarik bagian atas HTML
require_once 'includes/header.php';

// 1. TANGKAP PERMINTAAN FILTER DARI URL
$kategori_aktif = isset($_GET['kategori']) ? $_GET['kategori'] : 'Semua';

// 2. AMBIL DAFTAR KATEGORI UNTUK TOMBOL CTA
$stmt_kategori = $pdo->query("SELECT DISTINCT kategori FROM menu ORDER BY kategori ASC");
$daftar_kategori = $stmt_kategori->fetchAll(PDO::FETCH_COLUMN);

// 3. AMBIL DATA MENU BERDASARKAN FILTER
if ($kategori_aktif === 'Semua') {
    $stmt_menu = $pdo->query("SELECT * FROM menu ORDER BY nama_menu ASC");
} else {
    $stmt_menu = $pdo->prepare("SELECT * FROM menu WHERE kategori = ? ORDER BY nama_menu ASC");
    $stmt_menu->execute([$kategori_aktif]);
}
$semua_menu = $stmt_menu->fetchAll();
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'payment_timeout'): ?>
    <div class="alert alert-danger text-center fw-bold rounded-3 shadow-sm my-3">
        Waktu pembayaran telah habis (maksimal 15 menit). Pesananmu otomatis dibatalkan dan stok telah dilepaskan.
    </div>
<?php endif; ?>

<div class="container mt-5 mb-5">
    <div class="text-center mb-4">
        <h1 class="display-4 fw-bold" style="color: var(--primary-color);">Pat-Pat Cafe</h1>
        <p class="lead text-muted">Pilih sajian terbaik kami hari ini</p>
    </div>

    <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
        <a href="index.php?kategori=Semua" 
           class="btn <?= ($kategori_aktif === 'Semua') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-4 fw-bold">
            Semua
        </a>
        
        <?php foreach ($daftar_kategori as $kat): ?>
            <a href="index.php?kategori=<?= urlencode($kat) ?>" 
               class="btn <?= ($kategori_aktif === $kat) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-4 fw-bold">
                <?= htmlspecialchars($kat) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5">
        <?php if (count($semua_menu) > 0): ?>
            <?php foreach ($semua_menu as $menu): ?>
                
                <div class="col mb-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 <?= ($menu['status'] == 'habis') ? 'opacity-50' : '' ?>">
                        
                        <div class="bg-secondary rounded-top-4 position-relative" style="height: 180px; overflow: hidden;">
                            
                            <?php if ($menu['status'] == 'habis'): ?>
                                <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                    <span class="badge bg-danger px-2 py-1 shadow-sm"><i class="fas fa-times-circle me-1"></i>HABIS</span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($menu['foto'])): ?>
                                <img src="uploads/menu/<?= htmlspecialchars($menu['foto']) ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?= htmlspecialchars($menu['nama_menu']) ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex justify-content-center align-items-center bg-light text-muted">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                <h5 class="card-title fw-bold mb-0 menu-nama" style="color: var(--primary-color); line-height: 1.3;">
                                    <?= htmlspecialchars($menu['nama_menu']) ?>
                                </h5>
                                
                                <span class="badge bg-success rounded-pill flex-shrink-0 menu-harga">
                                    Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                                </span>
                            </div>
                            
                            <p class="card-text menu-deskripsi mb-4">
                                <?= htmlspecialchars(substr($menu['deskripsi'] ?? 'Deskripsi belum tersedia.', 0, 50)) ?>...
                            </p>
                            
                            <div class="mt-auto">
                                <?php if ($menu['status'] == 'habis'): ?>
                                    <button type="button" class="btn btn-secondary w-100 fw-bold rounded-pill disabled" style="cursor: not-allowed;">
                                        <i class="fas fa-ban me-1"></i> Habis
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-success w-100 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalMenu<?= $menu['id_menu'] ?>">
                                        <i class="fas fa-plus me-1"></i> Tambah
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($menu['status'] !== 'habis'): ?>
                <div class="modal fade" id="modalMenu<?= $menu['id_menu'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header border-0 pb-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center pt-0 px-4 pb-4">
                                <h3 class="fw-bold mb-1" style="color: var(--primary-color);"><?= htmlspecialchars($menu['nama_menu']) ?></h3>
                                <h4 class="text-success fw-bold mb-3">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></h4>
                                <p class="text-muted mb-4"><?= htmlspecialchars($menu['deskripsi'] ?? 'Deskripsi tidak tersedia.') ?></p>

                                <form action="katalog.php" method="POST">
                                    <input type="hidden" name="id_menu" value="<?= $menu['id_menu'] ?>">
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-4 bg-light rounded-pill px-3 py-2 mx-auto" style="width: 150px;">
                                        <button type="button" class="btn btn-link text-dark p-0 fs-3" onclick="kurangiQty(<?= $menu['id_menu'] ?>)">
                                            <i class="fas fa-minus-circle"></i>
                                        </button>
                                        
                                        <input type="number" name="jumlah" id="qty_<?= $menu['id_menu'] ?>" value="1" min="1" class="form-control text-center bg-transparent border-0 fw-bold fs-4 p-0 m-0 shadow-none" style="width: 70px; pointer-events: none;" readonly>
                                        
                                        <button type="button" class="btn btn-link text-success p-0 fs-3" onclick="tambahQty(<?= $menu['id_menu'] ?>)">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    </div>
                                    
                                    <button type="submit" name="add_to_cart" class="btn btn-success w-100 rounded-pill fw-bold py-3 fs-5 shadow-sm">
                                        Masukkan Keranjang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Menu untuk kategori ini belum tersedia.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$total_qty = 0;
$total_harga = 0;

// Hitung total item dan total harga langsung dari database agar akurat
if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
    $ids = array_keys($_SESSION['keranjang']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt_cart = $pdo->prepare("SELECT id_menu, harga FROM menu WHERE id_menu IN ($placeholders)");
    $stmt_cart->execute($ids);
    $cart_items = $stmt_cart->fetchAll(PDO::FETCH_KEY_PAIR); // Menghasilkan array [id_menu => harga]

    foreach ($_SESSION['keranjang'] as $id => $qty) {
        if (isset($cart_items[$id])) {
            $total_qty += $qty;
            $total_harga += ($cart_items[$id] * $qty);
        }
    }
}
?>

<?php if ($total_qty > 0): ?>
<div class="fixed-bottom p-3 d-flex justify-content-center" style="z-index: 1050; pointer-events: none;">
    <a href="checkout.php" class="btn bg-success rounded-pill shadow-lg d-flex justify-content-between align-items-center px-4 py-2 w-100 text-decoration-none text-white" 
       style="max-width: 500px; pointer-events: auto; border: none; transition: transform 0.2s;">
        
        <div class="text-start">
            <span class="fw-bold d-block" style="font-size: 1.1rem; line-height: 1.2;"><?= $total_qty ?> item</span>
            <span class="text-white-50" style="font-size: 0.8rem;">Pat-Pat Cafe Cart</span>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <span class="fw-bold" style="font-size: 1.15rem;">Rp <?= number_format($total_harga, 0, ',', '.') ?></span>
            
            <div class="bg-white rounded-circle d-flex justify-content-center align-items-center shadow-sm" style="width: 38px; height: 38px; color: #00880f;">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
        
    </a>
</div>
<?php endif; ?>

<script>
function tambahQty(id) {
    let input = document.getElementById('qty_' + id);
    input.value = parseInt(input.value) + 1;
}

function kurangiQty(id) {
    let input = document.getElementById('qty_' + id);
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>

<?php
// Tarik penutup HTML
require_once 'includes/footer.php';
?>