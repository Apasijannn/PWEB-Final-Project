<div align="center">

```
██████╗  █████╗ ████████╗      ██████╗  █████╗ ████████╗
██╔══██╗██╔══██╗╚══██╔══╝      ██╔══██╗██╔══██╗╚══██╔══╝
██████╔╝███████║   ██║   █████╗██████╔╝███████║   ██║   
██╔═══╝ ██╔══██║   ██║   ╚════╝██╔═══╝ ██╔══██║   ██║   
██║     ██║  ██║   ██║         ██║     ██║  ██║   ██║   
╚═╝     ╚═╝  ╚═╝   ╚═╝         ╚═╝     ╚═╝  ╚═╝   ╚═╝   
```

### ☕ Order & Point of Sale System

*Dari meja ke dapur — tanpa kertas, tanpa tunda.*

<br/>

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-005C84?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![FPDF](https://img.shields.io/badge/FPDF-Receipt-C8A96E?style=for-the-badge)](http://www.fpdf.org/)
[![Live Demo](https://img.shields.io/badge/Live-Demo-28a745?style=for-the-badge&logo=vercel&logoColor=white)](http://patpat-cafe.page.gd/index.php)

</div>

---

## Tentang Proyek

**Pat-Pat Cafe** adalah sistem POS (*Point of Sale*) berbasis web yang dirancang untuk memodernisasi alur kerja kafe secara menyeluruh — dari pelanggan scan QR di meja, hingga nota PDF tercetak di kasir. Dibangun di atas PHP native tanpa framework berat, dengan arsitektur yang mengutamakan kecepatan dan akuntabilitas operasional.

> Dikembangkan sebagai *Final Project* mata kuliah **Pemrograman Web** — dan dirancang dengan standar produk nyata.

<br/>

## Engineering Highlights

<table>
  <tr>
    <td>🛒</td>
    <td><strong>Dynamic Cart & Transaksi Real-Time</strong></td>
    <td>Session-based cart di PHP dengan kalkulasi PPN 11% + service charge otomatis. Timer kadaluarsa mencegah stok tertahan dari pesanan yang dibatalkan.</td>
  </tr>
  <tr>
    <td>🧾</td>
    <td><strong>Nota PDF via FPDF</strong></td>
    <td>Thermal receipt 80mm di-generate langsung dari server menggunakan FPDF + Output Buffering. Bebas distorsi cetak browser, siap kirim ke printer fisik.</td>
  </tr>
  <tr>
    <td>🔐</td>
    <td><strong>Bcrypt Auth</strong></td>
    <td>Semua kredensial pegawai dienkripsi one-way dengan Bcrypt. Tidak ada plaintext tersimpan — bahkan admin tidak bisa membaca password orang lain.</td>
  </tr>
  <tr>
    <td>📊</td>
    <td><strong>Dashboard Analitik + CSV Export</strong></td>
    <td>Data Retention Policy 3 tahun untuk menjaga query tetap ringan. Export data mentah ke CSV untuk audit di Excel dengan sekali klik.</td>
  </tr>
  <tr>
    <td>🔍</td>
    <td><strong>Audit Trail Kasir</strong></td>
    <td>Setiap transaksi di-tag dengan session ID kasir. Rekam jejak operasional penuh — siapa kasir, kapan, berapa — tersimpan otomatis.</td>
  </tr>
  <tr>
    <td>📱</td>
    <td><strong>Dynamic QRIS In-Memory</strong></td>
    <td>QR Code di-generate dan di-render langsung di RAM server. Tidak ada file temp yang menumpuk di disk hosting.</td>
  </tr>
</table>

<br/>

## Screenshots

| Katalog Menu | Dashboard Keuangan |
|:---:|:---:|
| ![Katalog](assets/screenshots/katalog.png) | ![Dashboard](assets/screenshots/dashboard.png) |
| **Nota Digital PDF** | **Panel Admin** |
| ![Nota](assets/screenshots/nota.png) | ![Admin](assets/screenshots/admin.png) |

<br/>

## Struktur Direktori

```
PWEB-Final-Project/
│
├── 📁 admin/           → Panel back-office (kasir & manajemen)
├── 📁 assets/          → CSS, JS, gambar statis
├── 📁 config/          → Konfigurasi koneksi database
├── 📁 includes/        → Komponen reusable (header, footer, dll)
├── 📁 libs/            → Library pihak ketiga (FPDF, QR generator)
├── 📁 uploads/         → Media yang diunggah
│
├── 📄 index.php        → Landing page pelanggan
├── 📄 katalog.php      → Menu & pemesanan
├── 📄 checkout.php     → Keranjang & konfirmasi
└── 📄 pembayaran.php   → Proses pembayaran & QRIS
```

<br/>

## Instalasi Lokal

**Prasyarat:** XAMPP / Laragon (PHP 8.x + MySQL) + browser modern.

**1 — Clone**
```bash
git clone https://github.com/Apasijannn/PWEB-Final-Project.git
cd PWEB-Final-Project
```

**2 — Pindah ke server lokal**

Salin folder ke `htdocs/` (XAMPP) atau root direktori server lokal kamu.

**3 — Buat & import database**

Buka `http://localhost/phpmyadmin`, buat database `patpat_cafe`, lalu import `patpat_cafe.sql` dari root proyek.

**4 — Konfigurasi koneksi**

Edit `config/koneksi.php`:
```php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "patpat_cafe";
```

**5 — Akses aplikasi**

| Role | URL |
|---|---|
| 🧑 Pelanggan | `http://localhost/PWEB-Final-Project/` |
| 🔧 Admin & Kasir | `http://localhost/PWEB-Final-Project/admin/` |

<br/>

## Tim Pengembang

<table>
  <tr>
    <td align="center" width="50%">
      <img src="https://github.com/Apasijannn.png" width="80" style="border-radius:50%"/><br/><br/>
      <b>Muhammad Dayyan Ghazanfar Latief</b><br/>
      <a href="https://github.com/Apasijannn">@Apasijannn</a>
    </td>
    <td align="center" width="50%">
      <img src="https://github.com/ghost.png" width="80" style="border-radius:50%"/><br/><br/>
      <b>Sitti Aminah</b><br/>
      <sub>Kolaborator</sub>
    </td>
  </tr>
</table>

<br/>

---

<div align="center">

**🔗 [patpat-cafe.page.gd](http://patpat-cafe.page.gd/index.php)**

<sub>Pat-Pat Cafe © 2025 &nbsp;·&nbsp; Made with ☕ & PHP native &nbsp;·&nbsp; Academic License</sub>

</div>
