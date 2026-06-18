<?php
// Wajib dipanggil di setiap file yang menggunakan session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$valid_username = 'admin';
$valid_password_hash = '$2y$10$O573w/DVn4LAAde.H2OQMeAWmQt96DwGSpL1f0n0w8065qBDqNVaG'; 

// 2. Fungsi Verifikasi Login
function loginAdmin($input_username, $input_password) {
    global $valid_username, $valid_password_hash;

    // Cek username dan verifikasi kecocokan password asli dengan hash
    if ($input_username === $valid_username && password_verify($input_password, $valid_password_hash)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['role'] = 'admin';
        
        // Catat waktu login statis dan aktivitas pertama secara dinamis
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    return false;
}

// 3. Middleware: Fungsi Penjaga Halaman Admin
function cekLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Cek apakah identitas sesi admin ada
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }

    // ==========================================================
    // LOGIKA SESSION TIMEOUT (Absolute & Idle)
    // ==========================================================
    $waktu_sekarang = time();
    $idle_timeout = 15 * 60;      // 15 menit (900 detik)
    $absolute_timeout = 60 * 60; // 60 menit (3600 detik)

    // Cek Idle Timeout (Tidak ada pergerakan selama 15 menit)
    if (isset($_SESSION['last_activity'])) {
        if ($waktu_sekarang - $_SESSION['last_activity'] > $idle_timeout) {
            // Hancurkan sesi karena admin AFK/Meninggalkan layar
            session_unset();
            session_destroy();
            header("Location: login.php?msg=idle");
            exit;
        }
    }

    // Cek Absolute Timeout (Paksa keluar jika sudah 60 menit sejak login)
    if (isset($_SESSION['login_time'])) {
        if ($waktu_sekarang - $_SESSION['login_time'] > $absolute_timeout) {
            // Hancurkan sesi karena batas aman shift operasional habis
            session_unset();
            session_destroy();
            header("Location: login.php?msg=expired");
            exit;
        }
    }

    // Lolos dari semua jebakan? Perbarui catatan waktu aktivitas terakhir
    $_SESSION['last_activity'] = $waktu_sekarang;
}

// ====================================================================
// FUNGSI KEAMANAN TAMBAHAN (CSRF, XSS, & SANITASI)
// ====================================================================

// Fungsi Anti-CSRF (Cross-Site Request Forgery)
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

// Fungsi Anti-XSS (Cross-Site Scripting)
function escape($html) {
    return htmlspecialchars($html ?? '', ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}