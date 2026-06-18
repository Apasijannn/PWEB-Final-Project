<?php
// Mulai atau panggil session yang sedang berjalan
session_start();

// 1. Kosongkan semua variabel yang ada di dalam session
$_SESSION = [];

// 2. Hancurkan seluruh sesi dari server
session_destroy();

// 3. Tendang pengguna keluar menuju halaman login
// (Catatan: Pastikan nama file halaman login milikmu benar 'login.php', ubah jika berbeda)
header("Location: login.php");
exit;
?>