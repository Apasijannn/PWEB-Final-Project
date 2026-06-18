<?php
// 1. NYALAKAN MESIN SESSION (Hanya jika belum menyala)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. HITUNG TOTAL ITEM DI KERANJANG
$total_item_keranjang = 0;
if (isset($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id_menu => $qty) {
        $total_item_keranjang += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pat-Pat Cafe</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
        --primary-color: #1a2a6c;
        --accent-color: #f2a341;
        }
        
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .navbar { background-color: var(--primary-color) !important; }
        .btn-primary { background-color: var(--primary-color); border: none; }
        .btn-accent { background-color: var(--accent-color); color: white; border: none; }
        
        /* Efek hover dan transisi pada card telah dihapus */
        .card { border: none; border-radius: 12px; }

        .menu-nama { font-size: 0.95rem; font-weight: 600; color: #333; }
        .menu-deskripsi { font-size: 0.75rem; color: #6c757d; margin-top: 2px; }
        .menu-harga { font-size: 0.8rem; padding: 0.4em 0.6em; }

        @media (min-width: 768px) {
            .menu-nama { font-size: 1.15rem; }
            .menu-deskripsi { font-size: 0.9rem; }
            .menu-harga { font-size: 1rem; padding: 0.5em 0.8em; }
        }

        /* ======================================================== */
        /* MEMATIKAN SEMUA ANIMASI DAN TRANSISI SECARA PAKSA        */
        /* ======================================================== */
        *, *::before, *::after {
            transition: none !important;
            animation: none !important;
            transform: none !important;
        }

        /* Mematikan efek sorotan warna pada tabel Bootstrap di halaman admin */
        .table-hover > tbody > tr:hover > * {
            --bs-table-accent-bg: transparent !important;
            color: inherit !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm"> 
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/Logo.png" alt="Logo Pat-Pat Cafe" style="height: 40px; width: auto; object-fit: contain;">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fw-bold">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Menu</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="nav-link position-relative d-inline-block" href="checkout.php" style="color: var(--accent-color);">
                            <i class="fas fa-shopping-cart"></i> Keranjang
                            
                            <?php if ($total_item_keranjang > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;">
                                    <?= $total_item_keranjang ?>
                                </span>
                            <?php endif; ?>
                            
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>