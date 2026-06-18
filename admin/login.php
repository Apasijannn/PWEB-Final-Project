<?php
require_once '../config/auth.php';

// Jika sudah login, tendang langsung ke halaman menu
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: menu.php");
    exit;
}

// Generate CSRF Token saat halaman login dimuat
$csrf_token = generateCsrfToken();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. PROTEKSI CSRF: Tolak mentah-mentah jika token tidak ada atau tidak cocok
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("Validasi keamanan (CSRF) gagal. Akses ditolak.");
    }

    // 2. SANITASI INPUT: Bersihkan data dari spasi berlebih dan tag HTML/Script jahat
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    
    // Password hanya di-trim, tidak di-filter HTML agar jika password aslinya 
    // mengandung simbol unik (seperti < atau &) tidak rusak saat dicocokkan.
    $password = trim($_POST['password'] ?? '');

    // Cek agar tidak ada yang mencoba bypass dengan mengirimkan input kosong berisi spasi
    if (empty($username) || empty($password)) {
        $error = 'Username dan Password tidak boleh kosong!';
    } else {
        // Panggil fungsi loginAdmin dari auth.php
        if (loginAdmin($username, $password)) {
            
            // 3. MENCEGAH SESSION FIXATION: Acak ulang ID Sesi setelah sukses login
            session_regenerate_id(true);
            
            header("Location: menu.php");
            exit;
        } else {
            $error = 'Username atau Password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Admin - Pat-Pat Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow border-0 rounded-4 p-4">
                    <h3 class="text-center fw-bold mb-4" style="color: #4e342e;">Login Kasir</h3>
                    
                    <?php if (isset($_GET['msg'])): ?>
                        <?php if ($_GET['msg'] === 'idle'): ?>
                            <div class="alert alert-warning text-center fw-bold shadow-sm rounded-4 mb-4">
                                <i class="fas fa-user-clock me-2"></i>Sesi berakhir karena tidak ada aktivitas selama 5 menit.
                            </div>
                        <?php elseif ($_GET['msg'] === 'expired'): ?>
                            <div class="alert alert-info text-center fw-bold shadow-sm rounded-4 mb-4">
                                <i class="fas fa-history me-2"></i>Batas waktu sesi (30 menit) tercapai.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 text-center"><?= escape($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= escape($csrf_token) ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
                        </div>
                        <button type="submit" class="btn w-100 fw-bold" style="background-color: #4e342e; color: white;">Masuk</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>