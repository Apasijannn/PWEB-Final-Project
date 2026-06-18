<?php
ob_start(); // Tahan output agar tidak error FPDF
require_once '../config/auth.php';
cekLogin();
require_once '../config/koneksi.php';
require_once '../libs/fpdf/fpdf.php'; 

$id_pesanan = $_GET['id'] ?? null;
if (!$id_pesanan) die("ID Pesanan tidak valid.");

// 1. QUERY DATABASE
$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id_pesanan = ?");
$stmt->execute([$id_pesanan]);
$pesanan = $stmt->fetch();
if (!$pesanan) die("Data pesanan tidak ditemukan.");

$stmt_detail = $pdo->prepare("
    SELECT dp.*, m.nama_menu 
    FROM detail_pesanan dp 
    JOIN menu m ON dp.menu_id = m.id_menu 
    WHERE dp.pesanan_id = ?
");
$stmt_detail->execute([$id_pesanan]);
$details = $stmt_detail->fetchAll();

// 2. KALKULASI
$subtotal = 0;
foreach ($details as $item) {
    $subtotal += ($item['harga_satuan'] * $item['jumlah']);
}
$ppn = $subtotal * 0.10;
$service_tax = 3000;
$grand_total = $pesanan['total']; 
$waktu_db = date('d/m/Y H:i', strtotime($pesanan['created_at']));

// ==============================================================================
// 3. RENDER PDF (Desain Struk Modern 80mm - 2 Baris Per Item)
// ==============================================================================

// Set ukuran kertas (Panjang 250mm agar aman untuk banyak item)
$pdf = new FPDF('P', 'mm', array(80, 250)); 
$pdf->AddPage();
$pdf->SetMargins(4, 5, 4); 
$pdf->SetAutoPageBreak(true, 5);

// FUNGSI BANTUAN UNTUK GARIS PUTUS-PUTUS
function garisPutus($pdf, $pw) {
    $pdf->SetFont('Courier', '', 10);
    $pdf->Cell($pw, 3, '--------------------------------------', 0, 1, 'C');
}

// HEADER KAFE
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell($pw, 6, 'PAT-PAT CAFE', 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($pw, 4, 'Jl. Raya Kenangan No. 123', 0, 1, 'C');
$pdf->Cell($pw, 4, 'Surabaya, Indonesia', 0, 1, 'C');
$pdf->Cell($pw, 4, 'Telp: 0812-3456-7890', 0, 1, 'C');
$pdf->Ln(1);

garisPutus($pdf, $pw);

// INFO TRANSAKSI
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(30, 4, 'No. Pesanan', 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(42, 4, '#' . str_pad($pesanan['id_pesanan'], 4, '0', STR_PAD_LEFT), 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);

$tanggal_fmt = date('d F Y', strtotime($pesanan['created_at']));
$jam_fmt     = date('H:i', strtotime($pesanan['created_at'])) . ' WIB';

$pdf->Cell(30, 4, 'Tanggal', 0, 0);
$pdf->Cell(42, 4, $tanggal_fmt, 0, 1, 'R');

$pdf->Cell(30, 4, 'Jam', 0, 0);
$pdf->Cell(42, 4, $jam_fmt, 0, 1, 'R');

$pdf->Cell(30, 4, 'Pelanggan', 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(42, 4, substr(htmlspecialchars($pesanan['nama_pelanggan']), 0, 25), 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);

$pdf->Cell(30, 4, 'Layanan', 0, 0);
$layanan = ($pesanan['metode'] == 'table_service') ? 'Dine In (Meja ' . $pesanan['nomor_meja'] . ')' : 'Takeaway';
$pdf->Cell(42, 4, $layanan, 0, 1, 'R');

garisPutus($pdf, $pw);

// RINCIAN ITEM (Gaya Modern)
$pdf->SetFont('Arial', '', 9);

foreach ($details as $item) {
    // Baris 1: Nama Menu (Bold, rata kiri)
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($pw, 5, htmlspecialchars($item['nama_menu']), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 9);
    
    // Baris 2: Qty x Harga (Kiri) & Total (Kanan)
    $qty_harga = $item['jumlah'] . ' x ' . number_format($item['harga_satuan'], 0, ',', '.');
    $total_item = number_format($item['jumlah'] * $item['harga_satuan'], 0, ',', '.');
    
    $pdf->Cell(40, 4, $qty_harga, 0, 0, 'L');
    $pdf->Cell(32, 4, $total_item, 0, 1, 'R');
    
    // Baris Tambahan: Catatan Khusus
    if (!empty($item['catatan'])) {
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell($pw, 4, '  * ' . htmlspecialchars($item['catatan']), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 9);
    }
}

garisPutus($pdf, $pw);

// BLOK TOTAL
$pdf->Cell(30, 4, 'Subtotal', 0, 0);
$pdf->Cell(42, 4, 'Rp ' . number_format($subtotal, 0, ',', '.'), 0, 1, 'R');

$pdf->Cell(30, 4, 'PPN (10%)', 0, 0);
$pdf->Cell(42, 4, 'Rp ' . number_format($ppn, 0, ',', '.'), 0, 1, 'R');

$pdf->Cell(30, 4, 'Service Tax', 0, 0);
$pdf->Cell(42, 4, 'Rp ' . number_format($service_tax, 0, ',', '.'), 0, 1, 'R');

$pdf->Ln(1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 5, 'TOTAL', 0, 0);
$pdf->Cell(42, 5, 'Rp ' . number_format($grand_total, 0, ',', '.'), 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);

garisPutus($pdf, $pw);

// METODE BAYAR & STATUS
$pdf->Cell(30, 4, 'Metode Bayar', 0, 0);
$pdf->Cell(42, 4, strtoupper(str_replace('_', ' ', $pesanan['metode_bayar'])), 0, 1, 'R');

$status_label = ($pesanan['status_bayar'] == 'dikonfirmasi' || $pesanan['status_bayar'] == 'lunas') ? 'LUNAS' : 'BELUM';
$pdf->Cell(30, 4, 'Status Bayar', 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(42, 4, $status_label, 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);

$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 11);
if ($pesanan['status_bayar'] == 'dikonfirmasi' || $pesanan['status_bayar'] == 'lunas') {
    $pdf->Cell($pw, 6, '*** LUNAS ***', 0, 1, 'C');
} else {
    $pdf->Cell($pw, 6, '*** BELUM LUNAS ***', 0, 1, 'C');
}

// FOOTER
$pdf->Ln(2);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell($pw, 4, 'Terima kasih atas kunjungan Anda!', 0, 1, 'C');
$pdf->Cell($pw, 4, 'IG: @patpatcafe', 0, 1, 'C');

// BERSIHKAN BUFFER DAN TAMPILKAN
ob_end_clean();
$pdf->Output('I', 'Struk_PatPatCafe_#' . str_pad($pesanan['id_pesanan'], 4, '0', STR_PAD_LEFT) . '.pdf');
?>