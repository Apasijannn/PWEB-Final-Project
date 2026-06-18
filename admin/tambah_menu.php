<?php
require_once '../config/auth.php';
cekLogin();
require_once '../config/koneksi.php';

$pesan_error = '';
$pesan_sukses = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Tangkap Data Teks dari Form
    $nama_menu = $_POST['nama_menu'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];
    $nama_file_foto = null;

    // 2. Logika Upload Gambar (Jika Admin Memilih File)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        
        // Ekstrak ekstensi file dan pastikan huruf kecil
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];

        // Validasi 1: Cek Ekstensi
        if (!in_array($file_ext, $ekstensi_diizinkan)) {
            $pesan_error = "Gagal: Format foto harus JPG, JPEG, atau PNG.";
        } 
        // Validasi 2: Cek Ukuran (Maksimal 2MB)
        elseif ($file_size > 2097152) {
            $pesan_error = "Gagal: Ukuran foto maksimal 2 MB.";
        } 
        else {
            // Ubah nama file agar unik (menghindari nama file sama menimpa satu sama lain)
            $nama_file_foto = uniqid() . '-' . time() . '.' . $file_ext;
            $lokasi_upload = '../uploads/menu/' . $nama_file_foto;
            
            // Pindahkan file dari penyimpanan sementara server ke folder uploads/menu/
            if (!move_uploaded_file($file_tmp, $lokasi_upload)) {
                $pesan_error = "Gagal: Terjadi kesalahan saat mengunggah foto.";
                $nama_file_foto = null; // Batalkan nama file jika gagal pindah
            }
        }
    }

    // 3. Eksekusi Simpan ke Database (Jika tidak ada error upload)
    if (empty($pesan_error)) {
        try {
            // Gunakan Prepared Statement untuk keamanan (Standar Industri)
            $sql = "INSERT INTO menu (nama_menu, kategori, harga, deskripsi, foto, status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama_menu, $kategori, $harga, $deskripsi, $nama_file_foto, $status]);
            
            $pesan_sukses = "Menu baru berhasil ditambahkan!";
        } catch (PDOException $e) {
            $pesan_error = "Gagal menyimpan ke database: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Menu - Admin Pat-Pat Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light pb-5">

    <nav class="navbar navbar-dark mb-4" style="background-color: #1e3a8a;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="menu.php">
                <img src="../assets/logo.png" alt="Logo Pat-Pat Cafe" style="height: 40px; width: auto; object-fit: contain;">
                <span class="mb-0 h3 fw-bold text-white" style="font-family: 'Segoe UI', Tahoma, sans-serif;">| Admin Panel</span>
            </a>
            <a href="menu.php" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        </div>
    </nav>

    <div class="container">     
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h3 class="fw-bold">Tambah Menu Baru</h3>
                        <hr>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($pesan_error): ?>
                            <div class="alert alert-danger"><?= $pesan_error ?></div>
                        <?php endif; ?>
                        
                        <?php if ($pesan_sukses): ?>
                            <div class="alert alert-success"><?= $pesan_sukses ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Menu</label>
                                    <input type="text" name="nama_menu" class="form-control" required placeholder="Contoh: Kopi Americano">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Kategori</label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Minuman">Minuman</option>
                                        <option value="Snack">Snack</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" required min="0" placeholder="Contoh: 15000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Awal</label>
                                    <select name="status" class="form-select" required>
                                        <option value="tersedia">Tersedia</option>
                                        <option value="habis">Habis</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Foto Menu (Opsional)</label>
                                <input type="file" name="foto" class="form-control" accept=".jpg, .jpeg, .png">
                                <small class="text-muted">Format: JPG/PNG. Maksimal ukuran: 2 MB.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsikan komposisi atau rasa menu ini..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 fw-bold py-2">
                                <i class="fas fa-save me-1"></i> Simpan Menu
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>