<?php
require_once '../config/auth.php';
// Kunci halaman ini! Hanya yang sudah login yang bisa lewat
cekLogin(); 

require_once '../config/koneksi.php';

// Ambil semua data menu dari database
$stmt = $pdo->query("SELECT * FROM menu ORDER BY id_menu DESC");
$menus = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Menu - Admin Pat-Pat Cafe</title>
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
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <div class="d-flex flex-column flex-lg-row gap-2 ms-auto mt-3 mt-lg-0">
                    <a href="laporan_keuangan.php" class="btn btn-outline-light btn-sm fw-bold shadow-sm text-start text-lg-center">Rekap Keuangan</a>
                    <a href="pesanan.php" class="btn btn-outline-light btn-sm fw-bold shadow-sm text-start text-lg-center">Kasir</a>
                    <a href="menu.php" class="btn btn-light btn-sm fw-bold text-primary shadow-sm text-start text-lg-center">Manajemen Menu</a>
                    <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm text-start text-lg-center">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 pb-5">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h3 class="fw-bold m-0" style="color: #1e3a8a;"><i class="fas fa-utensils me-2"></i>Manajemen Menu</h3>
            
            <div>
                <a href="tambah_menu.php" class="btn btn-success fw-bold shadow-sm rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>Tambah Menu Baru
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th class="py-3 px-4">Foto</th>
                                <th class="py-3">Nama Menu</th>
                                <th class="py-3">Kategori</th>
                                <th class="py-3">Harga</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menus as $item): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <?php if($item['foto']): ?>
                                        <img src="../uploads/menu/<?= htmlspecialchars($item['foto']) ?>" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded d-flex justify-content-center align-items-center shadow-sm" style="width: 60px; height: 60px; color: white;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($item['nama_menu']) ?></td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1"><?= htmlspecialchars($item['kategori']) ?></span>
                                </td>
                                <td class="fw-bold text-success">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $item['status'] == 'tersedia' ? 'bg-success' : 'bg-danger' ?> rounded-pill px-3 py-2 shadow-sm">
                                        <?= strtoupper($item['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="edit_menu.php?id=<?= $item['id_menu'] ?>" class="btn btn-sm btn-warning rounded-circle shadow-sm me-1" title="Edit Menu" style="width: 32px; height: 32px; line-height: 20px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus_menu.php?id=<?= $item['id_menu'] ?>" class="btn btn-sm btn-danger rounded-circle shadow-sm" onclick="return confirm('Yakin ingin menghapus menu ini?');" title="Hapus Menu" style="width: 32px; height: 32px; line-height: 20px;">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>