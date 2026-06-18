<?php
session_start();
require_once 'config/koneksi.php';

// ==========================================
// 1. IMPORT LIBRARY QR CODE
// ==========================================
require_once 'libs/phpqrcode/qrlib.php';

// Amankan data dari halaman checkout sebelumnya
if (isset($_POST['lanjut_pembayaran'])) {
    $_SESSION['checkout_data'] = [
        'metode' => $_POST['metode'],
        'nama_pelanggan' => trim($_POST['nama_pelanggan']),
        'nomor_meja' => ($_POST['metode'] === 'takeaway') ? null : $_POST['nomor_meja']
    ];
}

// Logika pembatalan pesanan (reset sesi)
if (isset($_GET['action']) && $_GET['action'] === 'batal') {
    unset($_SESSION['keranjang']);
    unset($_SESSION['checkout_data']);
    unset($_SESSION['payment_start_time']);
    
    $redirect_url = "index.php";
    if (isset($_GET['reason']) && $_GET['reason'] === 'timeout') {
        $redirect_url .= "?msg=payment_timeout";
    }
    header("Location: " . $redirect_url);
    exit;
}

// Validasi sesi: Tendang jika keranjang kosong atau data checkout tidak ada
if (empty($_SESSION['keranjang']) || empty($_SESSION['checkout_data'])) {
    header("Location: index.php");
    exit;
}

// ==================================================================
// LOGIKA BACKEND: VALIDASI TIMER 15 MENIT
// ==================================================================
if (!isset($_SESSION['payment_start_time'])) {
    $_SESSION['payment_start_time'] = time(); // Kunci waktu awal masuk halaman pembayaran
}

$waktu_sekarang = time();
$durasi_maksimal = 15 * 60; // 15 menit dalam satuan detik (900 detik)
$selisih_waktu = $waktu_sekarang - $_SESSION['payment_start_time'];
$sisa_detik = $durasi_maksimal - $selisih_waktu;

// Jika waktu habis di sisi server, hancurkan pesanan secara paksa
if ($sisa_detik <= 0) {
    unset($_SESSION['keranjang']);
    unset($_SESSION['checkout_data']);
    unset($_SESSION['payment_start_time']);
    header("Location: index.php?msg=payment_timeout");
    exit;
}

$keranjang = $_SESSION['keranjang'];
$data_pelanggan = $_SESSION['checkout_data'];
$items = [];
$subtotal = 0;

// Kalkulasi Harga dari Database
$placeholders = implode(',', array_fill(0, count($keranjang), '?'));
$stmt = $pdo->prepare("SELECT * FROM menu WHERE id_menu IN ($placeholders)");
$stmt->execute(array_keys($keranjang));
$items = $stmt->fetchAll();

foreach ($items as $item) {
    $subtotal += $item['harga'] * $keranjang[$item['id_menu']];
}

$ppn = $subtotal * 0.10; 
$service_tax = 3000; 
$grand_total = $subtotal + $ppn + $service_tax;


// ==================================================================
// 2. LOGIKA GENERATE QRIS SECARA AMAN (MENCEGAH KERUSAKAN HEADER)
// ==================================================================
$qris_gateway_url = "https://patpatcafe.com/verify-payment.php?data=" . base64_encode($grand_total . '|' . time());

// Tentukan nama file sementara (temp file) di direktori yang sama
$file_sementara = 'temp_qris_' . time() . '.png';

// Generate QR Code dan paksa library MENYIMPAN KE FILE, bukan mencetak ke layar
QRcode::png($qris_gateway_url, $file_sementara, QR_ECLEVEL_Q, 6, 2);

// Baca file fisik yang baru saja dibuat, lalu ubah ke format Base64
$image_data = file_get_contents($file_sementara);
$qris_image_src = 'data:image/png;base64,' . base64_encode($image_data);

// Segera hapus file sementaranya agar tidak menjadi sampah di hard drive server
if (file_exists($file_sementara)) {
    unlink($file_sementara);
}


$pesan_sukses = '';
$error = '';

// 3. EKSEKUSI FINAL: SIMPAN KE DATABASE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_final'])) {
    $metode_bayar = $_POST['metode_bayar']; 
    $bukti_transfer = null;

    if (empty($metode_bayar)) {
        $error = "Pilih metode pembayaran terlebih dahulu!";
    } else {
        if ($metode_bayar === 'transfer') {
            if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] === 0) {
                $ext = strtolower(pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $bukti_transfer = time() . '_transfer_' . rand(100, 999) . '.' . $ext;
                    move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], 'uploads/bukti_transfer/' . $bukti_transfer);
                } else {
                    $error = "Format bukti transfer harus JPG atau PNG.";
                }
            } else {
                $error = "Kamu memilih QRIS, wajib mengunggah bukti pembayaran!";
            }
        }

        if (empty($error)) {
            try {
                $pdo->beginTransaction();

                $stmt_pesanan = $pdo->prepare("INSERT INTO pesanan (nomor_meja, nama_pelanggan, metode, total, metode_bayar, bukti_transfer, status, status_bayar) VALUES (?, ?, ?, ?, ?, ?, 'proses', 'belum')");
                $stmt_pesanan->execute([
                    $data_pelanggan['nomor_meja'], 
                    $data_pelanggan['nama_pelanggan'], 
                    $data_pelanggan['metode'], 
                    $grand_total,
                    $metode_bayar, 
                    $bukti_transfer
                ]);
                $id_pesanan_baru = $pdo->lastInsertId();

                $stmt_detail = $pdo->prepare("INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
                foreach ($items as $item) {
                    $stmt_detail->execute([$id_pesanan_baru, $item['id_menu'], $keranjang[$item['id_menu']], $item['harga']]);
                }

                $pdo->commit();
                
                unset($_SESSION['keranjang']);
                unset($_SESSION['checkout_data']);
                unset($_SESSION['payment_start_time']); // Bersihkan timer karena transaksi sukses
                
                $pesan_sukses = "Pesanan berhasil diproses! Silakan tunggu konfirmasi dari kasir.";

            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Kegagalan sistem database: " . $e->getMessage();
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container mt-5 mb-5" style="max-width: 800px;">
    <?php if ($pesan_sukses): ?>
        <div class="card shadow-sm border-0 rounded-4 p-5 text-center">
            <i class="fas fa-check-circle fa-4x mb-3 text-success"></i>
            <h3 class="fw-bold text-success mb-3">Pesanan Diterima!</h3>
            <p class="fs-5 mb-4 text-muted"><?= $pesan_sukses ?></p>
            <a href="index.php" class="btn btn-success rounded-pill px-5 fw-bold py-3">Kembali ke Beranda</a>
        </div>
    <?php else: ?>
    
    <div class="alert alert-danger d-flex align-items-center justify-content-between rounded-3 shadow-sm mb-4 p-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-clock fa-lg me-2 text-danger animate-pulse"></i>
            <span class="fw-bold text-dark">Selesaikan pembayaran sebelum waktu habis:</span>
        </div>
        <div id="countdownTimer" class="badge bg-danger fs-6 px-3 py-2 rounded-pill fw-bold">15:00</div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-light">
                <h4 class="fw-bold mb-4" style="color: var(--primary-color);"><i class="fas fa-receipt me-2"></i>Invoice</h4>
                
                <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                    <p class="mb-1 text-muted small">Atas Nama :</p>
                    <h5 class="fw-bold"><?= htmlspecialchars($data_pelanggan['nama_pelanggan']) ?></h5>
                    <p class="mb-0 text-muted small">Layanan : 
                        <span class="fw-bold text-dark">
                            <?= ($data_pelanggan['metode'] == 'table_service') ? 'Dine In (Meja ' . htmlspecialchars($data_pelanggan['nomor_meja']) . ')' : 'Takeaway' ?>
                        </span>
                    </p>
                </div>

                <ul class="list-unstyled mb-4">
                    <?php foreach ($items as $item): 
                        $qty = $keranjang[$item['id_menu']];
                    ?>
                    <li class="d-flex justify-content-between mb-2">
                        <span><?= $qty ?>x <?= htmlspecialchars($item['nama_menu']) ?></span>
                        <span>Rp <?= number_format($item['harga'] * $qty, 0, ',', '.') ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="border-top border-secondary border-opacity-25 pt-3">
                    <div class="d-flex justify-content-between mb-1 text-muted">
                        <span>Subtotal</span>
                        <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-muted">
                        <span>PPN (10%)</span>
                        <span>Rp <?= number_format($ppn, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Service Tax</span>
                        <span>Rp <?= number_format($service_tax, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm">
                        <span class="fw-bold fs-5">TOTAL BAYAR</span>
                        <span class="fs-4 fw-bold text-success">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                <h4 class="fw-bold mb-4" style="color: var(--primary-color);"><i class="fas fa-wallet me-2"></i>Pembayaran</h4>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-4">
                        <div class="mb-3">
                            <label class="d-flex align-items-center border rounded-3 p-3 w-100 shadow-sm" style="cursor: pointer; background-color: #f8f9fa;" for="metodeTunai">
                                <input class="form-check-input m-0 me-3" type="radio" name="metode_bayar" id="metodeTunai" value="tunai" required onchange="togglePanduan()" style="width: 1.5em; height: 1.5em;">
                                <span class="fw-bold text-dark mb-0" style="font-size: 1.1rem;">Bayar Tunai di Kasir</span>
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="d-flex align-items-center border rounded-3 p-3 w-100 shadow-sm" style="cursor: pointer; background-color: #f8f9fa;" for="metodeTransfer">
                                <input class="form-check-input m-0 me-3" type="radio" name="metode_bayar" id="metodeTransfer" value="transfer" required onchange="togglePanduan()" style="width: 1.5em; height: 1.5em;">
                                <span class="fw-bold text-dark mb-0" style="font-size: 1.1rem;">Bayar via QRIS</span>
                            </label>
                        </div>
                    </div>

                    <div id="panduanKasir" class="alert alert-warning text-center d-none mb-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                        <h6 class="fw-bold text-dark mb-1">Menunggu Pembayaran</h6>
                        <p class="small mb-0 text-dark">Silakan bawa perangkatmu dan tunjukkan halaman ini ke meja kasir untuk melakukan pembayaran secara tunai.</p>
                    </div>

                    <div id="panduanQRIS" class="bg-light p-3 rounded-3 border text-center d-none mb-4">
                        <p class="fw-bold mb-2 text-danger">Silakan Scan QR Code Berikut :</p>
                        
                        <img src="<?= $qris_image_src ?>" alt="QRIS Pat-Pat Cafe" class="img-fluid rounded shadow-sm mb-3" style="max-width: 200px;">
                        
                        <div class="text-start mt-2">
                            <label class="form-label fw-bold small text-primary"><i class="fas fa-upload me-1"></i>Upload Bukti Bayar</label>
                            <input type="file" name="bukti_transfer" id="inputBukti" class="form-control" accept=".jpg,.jpeg,.png">
                            <div class="form-text small">Wajib diisi jika menggunakan QRIS.</div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-4">
                        <button type="submit" name="proses_final" id="btnSubmit" class="btn btn-primary w-100 fw-bold rounded-pill py-3 disabled" style="background-color: var(--primary-color);">
                            Konfirmasi Pesanan
                        </button>
                        
                        <a href="pembayaran.php?action=batal" class="btn btn-outline-danger w-100 fw-bold rounded-pill py-2" onclick="return confirm('Apakah kamu yakin ingin membatalkan pesanan ini? Seluruh isi keranjangmu akan dihapus.');">
                            Batalkan Pesanan
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    function togglePanduan() {
        var tunai = document.getElementById('metodeTunai').checked;
        var transfer = document.getElementById('metodeTransfer').checked;
        
        var boxKasir = document.getElementById('panduanKasir');
        var boxQRIS = document.getElementById('panduanQRIS');
        var inputBukti = document.getElementById('inputBukti');
        var btnSubmit = document.getElementById('btnSubmit');
        
        btnSubmit.classList.remove('disabled');

        if (tunai) {
            boxKasir.classList.remove('d-none');
            boxQRIS.classList.add('d-none');
            inputBukti.removeAttribute('required');
        } else if (transfer) {
            boxKasir.classList.add('d-none');
            boxQRIS.classList.remove('d-none');
            inputBukti.setAttribute('required', 'required');
        }
    }

    // ==================================================================
    // LOGIKA FRONTEND: JS COUNTDOWN TIMER
    // ==================================================================
    // Ambil sisa detik langsung dari perhitungan server PHP di atas
    var sisaWaktuDetik = <?= $sisa_detik ?>; 
    var displayTimer = document.getElementById('countdownTimer');

    var intervalTimer = setInterval(function() {
        var menit = Math.floor(sisaWaktuDetik / 60);
        var detik = sisaWaktuDetik % 60;

        // Beri format padding angka 0 jika di bawah nilai 10 (contoh: 09:05)
        menit = menit < 10 ? '0' + menit : menit;
        detik = detik < 10 ? '0' + detik : detik;

        displayTimer.textContent = menit + ':' + detik;

        // Jika hitungan mundur menyentuh angka 0, paksa lempar ke link pembatalan otomatis
        if (--sisaWaktuDetik < 0) {
            clearInterval(intervalTimer);
            window.location.href = 'pembayaran.php?action=batal&reason=timeout';
        }
    }, 1000);
</script>

<?php require_once 'includes/footer.php'; ?>