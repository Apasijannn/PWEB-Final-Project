<?php
require_once '../config/auth.php';
// Kunci halaman ini!
cekLogin(); 

require_once '../config/koneksi.php';

// 1. TANGKAP ID MENU DARI URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: menu.php");
    exit;
}

// 2. AMBIL DATA MENU SAAT INI UNTUK MENGISI FORM
$stmt = $pdo->prepare("SELECT * FROM menu WHERE id_menu = ?");
$stmt->execute([$id]);
$menu = $stmt->fetch();

if (!$menu) {
    die("Data menu tidak ditemukan!");
}

$error = '';

// 3. TANGKAP DATA KETIKA FORM DISUBMIT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_menu'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];
    
    // Secara default, gunakan nama foto yang lama
    $foto_baru = $menu['foto']; 

    // 4. LOGIKA UPLOAD FOTO BARU (JIKA ADA)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        
        // Validasi Ekstensi dan Ukuran (Maks 2MB)
        if (!in_array($file_ext, $allowed_ext)) {
            $error = "Ekstensi file tidak diizinkan. Hanya JPG, JPEG, PNG.";
        } elseif ($file_size > 2097152) { 
            $error = "Ukuran file maksimal 2 MB.";
        } else {
            // Generate nama file unik agar tidak bentrok
            $new_file_name = time() . '_' . rand(100, 999) . '.' . $file_ext;
            $upload_path = '../uploads/menu/' . $new_file_name;
            
            // Pindahkan file ke folder uploads/menu/
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $foto_baru = $new_file_name; // Update nama file untuk database
                
                // Hapus foto lama dari server jika ada
                if (!empty($menu['foto']) && file_exists('../uploads/menu/' . $menu['foto'])) {
                    unlink('../uploads/menu/' . $menu['foto']);
                }
            } else {
                $error = "Sistem gagal mengupload gambar ke server.";
            }
        }
    }

    // 5. EKSEKUSI UPDATE KE DATABASE
    if (empty($error)) {
        $stmt_update = $pdo->prepare("UPDATE menu SET nama_menu=?, kategori=?, harga=?, deskripsi=?, status=?, foto=? WHERE id_menu=?");
        if ($stmt_update->execute([$nama, $kategori, $harga, $deskripsi, $status, $foto_baru, $id])) {
            // Jika sukses, kembali ke tabel manajemen menu
            header("Location: menu.php");
            exit;
        } else {
            $error = "Gagal menyimpan data ke database.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Menu - Admin Pat-Pat Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark mb-4" style="background-color: #1e3a8a;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="menu.php">
                <img src="../assets/logo.png" alt="Logo Pat-Pat Cafe" style="height: 40px; width: auto; object-fit: contain;">
                <span class="mb-0 h3 fw-bold text-white">| Admin Panel</span>
            </a>
            <a href="menu.php" class="btn btn-outline-light btn-sm">Kembali</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white pt-4 pb-2 border-0">
                        <h3 class="fw-bold text-center" style="color: #4e342e;">Edit Data Menu</h3>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Nama Menu</label>
                                    <input type="text" name="nama_menu" class="form-control" value="<?= htmlspecialchars($menu['nama_menu']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="tersedia" <?= $menu['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                        <option value="habis" <?= $menu['status'] == 'habis' ? 'selected' : '' ?>>Habis</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Kategori</label>
                                    <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($menu['kategori']) ?>" required placeholder="Contoh: Minuman, Snack">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($menu['harga']) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($menu['deskripsi']) ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Ganti Foto Menu (Opsional)</label>
                                
                                <?php if (!empty($menu['foto'])): ?>
                                    <div class="mb-2">
                                        <img src="../uploads/menu/<?= htmlspecialchars($menu['foto']) ?>" class="img-thumbnail" style="height: 100px;" alt="Foto Saat Ini">
                                        <div class="small text-muted mt-1">Foto saat ini</div>
                                    </div>
                                <?php endif; ?>
                                
                                <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                                <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG/PNG, Maks: 2MB. Resolusi disarankan: 600x450px.</div>
                            </div>

                            <hr class="mb-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg text-white fw-bold">Simpan Perubahan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>