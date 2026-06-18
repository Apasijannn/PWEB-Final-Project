<?php
require_once '../config/auth.php';
// Kunci halaman ini!
cekLogin(); 

require_once '../config/koneksi.php';

// 1. TANGKAP ID DARI URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: menu.php");
    exit;
}

try {
    // 2. CARI NAMA FILE FOTO SEBELUM MENGHAPUS DATA
    $stmt = $pdo->prepare("SELECT foto FROM menu WHERE id_menu = ?");
    $stmt->execute([$id]);
    $menu = $stmt->fetch();

    if ($menu) {
        // 3. EKSEKUSI HAPUS DATA DARI DATABASE
        $stmt_delete = $pdo->prepare("DELETE FROM menu WHERE id_menu = ?");
        $stmt_delete->execute([$id]);

        // 4. HAPUS FILE FISIK DARI FOLDER (Garbage Collection)
        // Kita hanya menghapus foto JIKA eksekusi DELETE database di atas berhasil
        if (!empty($menu['foto']) && file_exists('../uploads/menu/' . $menu['foto'])) {
            unlink('../uploads/menu/' . $menu['foto']);
        }
    }
    
    // Kembali ke halaman menu jika mulus
    header("Location: menu.php");
    exit;

} catch (PDOException $e) {
    // 5. TANGKAP ERROR RELASI DATABASE (FOREIGN KEY CONSTRAINT)
    // Jika data tidak bisa dihapus karena nyangkut di tabel pesanan, kita hentikan eksekusi
    die("
        <div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 50px auto; border: 1px solid #dc3545; border-radius: 8px; background-color: #f8d7da; color: #842029;'>
            <h2 style='margin-top: 0;'>Aksi Ditolak oleh Database</h2>
            <p><strong>Menu ini tidak bisa dihapus</strong> karena sudah tercatat dalam riwayat pesanan pelanggan. Menghapus menu ini akan merusak data laporan keuangan dan riwayat transaksi.</p>
            <p><strong>Solusi:</strong> Jika menu ini sudah tidak dijual, silakan kembali dan ubah statusnya menjadi <b>'Habis'</b> alih-alih menghapusnya secara fisik.</p>
            <a href='menu.php' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #4e342e; color: white; text-decoration: none; border-radius: 5px;'>Kembali ke Manajemen Menu</a>
        </div>
    ");
}
?>