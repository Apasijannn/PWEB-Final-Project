<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_POST['add_to_cart'])) {
    $id_menu = $_POST['id_menu'];
    
    // TANGKAP JUMLAH DARI FORM MODAL, default 1 jika gagal terbaca
    $qty = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    if ($qty < 1) $qty = 1; // Mencegah user meng-inject angka minus

    $stmt = $pdo->prepare("SELECT id_menu FROM menu WHERE id_menu = ?");
    $stmt->execute([$id_menu]);
    $menu = $stmt->fetch();

    if ($menu) {
        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }

        if (isset($_SESSION['keranjang'][$id_menu])) {
            // Jika sudah ada, tambahkan dengan jumlah yang baru diinput
            $_SESSION['keranjang'][$id_menu] += $qty;
        } else {
            // Jika belum ada, masukkan sesuai jumlah
            $_SESSION['keranjang'][$id_menu] = $qty;
        }
    }
    
    header("Location: index.php?status=sukses_tambah");
    exit;
} else {
    header("Location: index.php");
    exit;
}