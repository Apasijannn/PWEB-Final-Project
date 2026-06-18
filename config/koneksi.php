<?php
$host = 'localhost';
$dbname = 'patpat_cafe';
$username = 'root';
$password = ''; // Kosongkan jika kamu menggunakan setting default XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Paksa PDO untuk menampilkan error secara detail (sangat berguna saat development)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Kembalikan data dalam bentuk array asosiatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Jika koneksi gagal, hentikan aplikasi dan tampilkan pesan
    die("Koneksi Database Gagal. Cek XAMPP kamu: " . $e->getMessage());
}
?>