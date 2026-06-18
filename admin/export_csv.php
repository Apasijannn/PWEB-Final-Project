<?php
require_once '../config/auth.php';
cekLogin();
require_once '../config/koneksi.php';

// Tangkap parameter filter dari URL
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// 1. SET HEADER HTTP UNTUK MEMAKSA DOWNLOAD FILE
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Rekap_Penjualan_PatPat_' . $tahun . '_' . $bulan . '.csv');

// 2. BUKA JALUR OUTPUT LANGSUNG KE BROWSER
$output = fopen('php://output', 'w');

// Tambahkan BOM (Byte Order Mark) agar Excel membaca karakter UTF-8 dengan benar
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// 3. TULIS BARIS PERTAMA (JUDUL KOLOM)
// Sesuai spesifikasi: Tanggal, No Transaksi, Nama Pelanggan, Item, Jumlah, Total
fputcsv($output, ['Tanggal & Waktu', 'No. Transaksi', 'Nama Pelanggan', 'Item Menu', 'Kategori', 'Jumlah Beli', 'Subtotal Item (Rp)', 'Total Nota Final (Rp)']);

// 4. TARIK DATA DARI DATABASE (JOIN 3 TABEL)
$stmt = $pdo->prepare("
    SELECT 
        p.created_at,
        p.id_pesanan,
        p.nama_pelanggan,
        m.nama_menu,
        m.kategori,
        dp.jumlah,
        (dp.jumlah * dp.harga_satuan) AS subtotal,
        p.total
    FROM pesanan p
    JOIN detail_pesanan dp ON p.id_pesanan = dp.pesanan_id
    JOIN menu m ON dp.menu_id = m.id_menu
    WHERE p.status = 'selesai' 
      AND MONTH(p.created_at) = ? 
      AND YEAR(p.created_at) = ?
    ORDER BY p.created_at ASC
");
$stmt->execute([$bulan, $tahun]);

// 5. MASUKKAN DATA BARIS DEMI BARIS KE DALAM FILE
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        date('d/m/Y H:i', strtotime($row['created_at'])),
        '#' . str_pad($row['id_pesanan'], 4, '0', STR_PAD_LEFT),
        $row['nama_pelanggan'],
        $row['nama_menu'],
        $row['kategori'],
        $row['jumlah'],
        $row['subtotal'],
        $row['total']
    ]);
}

// Tutup file dan matikan eksekusi script
fclose($output);
exit;