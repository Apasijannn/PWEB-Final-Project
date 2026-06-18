<div align="center">

<img src="https://readme-typing-svg.demolab.com?font=Fira+Code&weight=700&size=28&pause=1000&color=C8A96E&center=true&vCenter=true&width=600&lines=☕+Pat-Pat+Cafe;Order+%26+POS+System" alt="Typing SVG" />

<br/>

**Sistem Point of Sale & manajemen pesanan berbasis web untuk kafe modern.**  
Mendukung alur *Dine-in via QR Code* dan *Takeaway* — dari meja ke dapur, real-time.

<br/>

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-005C84?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![FPDF](https://img.shields.io/badge/FPDF-Receipt_Generator-C8A96E?style=flat-square)
![License](https://img.shields.io/badge/License-Academic-lightgrey?style=flat-square)

</div>

---

## 📖 Tentang Proyek

**Pat-Pat Cafe Order System** adalah aplikasi POS (*Point of Sale*) komersial yang dirancang untuk memodernisasi alur operasional kafe — mulai dari pemesanan pelanggan, manajemen kasir, hingga pelaporan keuangan harian. Dibangun dengan arsitektur PHP native yang ringan namun dirancang dengan prinsip keamanan dan akuntabilitas operasional.

> Proyek ini dikembangkan sebagai *Final Project* mata kuliah Pemrograman Web dan sekaligus sebagai portofolio rekayasa perangkat lunak profesional.

---

## ✨ Fitur Teknis Unggulan

### 🛒 Dynamic Cart & Transaksi Real-Time
Session-based cart di memori PHP dengan kalkulasi **PPN 11%** dan **Service Charge** otomatis. Dilengkapi timer kadaluarsa untuk pelepasan stok jika transaksi tidak diselesaikan.

### 🧾 Nota Digital via FPDF
Menghasilkan bukti nota dalam format *thermal receipt 80mm* langsung dari server menggunakan library FPDF dan *Output Buffering* — menghindari distorsi cetak dari browser rendering.

### 🔐 Keamanan Kriptografi
Seluruh kredensial pegawai diproteksi dengan algoritma **Bcrypt** (enkripsi satu arah), memastikan tidak ada sandi yang tersimpan dalam bentuk plaintext di database.

### 📊 Dashboard Analitik & Ekspor Data
Dashboard rekap keuangan dengan *Data Retention Policy* (3 tahun) untuk optimalisasi query, plus kapabilitas ekspor ke **CSV** untuk audit di Microsoft Excel.

### 📋 Audit Trail Kasir
Session tagging ketat pada setiap transaksi kasir untuk menjaga jejak audit operasional yang akuntabel.

### 📱 Dynamic QRIS Render
QR Code di-generate dan di-render langsung di RAM — tidak menyimpan file sementara ke disk untuk mencegah akumulasi file sampah di hosting.

---

## 📸 Screenshots

| Halaman Katalog | Dashboard Keuangan |
|:---:|:---:|
| ![Katalog](link_gambar_katalog_di_sini) | ![Keuangan](link_gambar_dashboard_di_sini) |
| **Nota Digital PDF** | **Panel Admin** |
| ![Nota](link_gambar_nota_di_sini) | ![Admin](link_gambar_admin_di_sini) |

---

## 🚀 Instalasi Lokal

### Prasyarat
- XAMPP / Laragon (PHP 8.x + MySQL)
- Browser modern

### Langkah-langkah

**1. Clone repositori**
```bash
git clone https://github.com/Apasijannn/PWEB-Final-Project.git
cd PWEB-Final-Project
```

**2. Pindahkan ke direktori server**

Salin folder proyek ke `htdocs` (XAMPP) atau direktori root lokal yang kamu gunakan.

**3. Buat & import database**

Buka `http://localhost/phpmyadmin`, buat database baru bernama `patpat_cafe`, lalu import file `patpat_cafe.sql` dari root folder proyek.

**4. Konfigurasi koneksi**

Buka `config/koneksi.php` dan sesuaikan kredensial:

```php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "patpat_cafe";
```

**5. Akses aplikasi**

| Antarmuka | URL |
|-----------|-----|
| 🧑 Pelanggan (Katalog & Pemesanan) | `http://localhost/PWEB-Final-Project/` |
| 🔧 Admin & Kasir (Back-Office) | `http://localhost/PWEB-Final-Project/admin/` |

---

## 🗂️ Struktur Direktori

```
PWEB-Final-Project/
├── admin/          # Panel back-office (kasir & manajemen)
├── assets/         # CSS, JS, gambar
├── config/         # Konfigurasi database
├── includes/       # Komponen reusable (header, footer, dll)
├── libs/           # Library pihak ketiga (FPDF, QR generator)
├── uploads/        # Media yang diunggah
├── index.php       # Halaman utama pelanggan
├── katalog.php     # Halaman menu & pemesanan
├── checkout.php    # Keranjang & konfirmasi pesanan
└── pembayaran.php  # Proses pembayaran & QRIS
```

---

## 👥 Tim Pengembang

<table>
  <tr>
    <td align="center">
      <b>Muhammad Dayyan Ghazanfar Latief</b><br/>
      <a href="https://github.com/Apasijannn">@Apasijannn</a>
    </td>
    <td align="center">
      <b>Sitti Aminah</b><br/>
    </td>
  </tr>
</table>

---

## 📄 Lisensi

Proyek ini dikembangkan sebagai bagian dari penyelesaian tugas akademik **Pemrograman Web** dan optimalisasi portofolio profesional rekayasa perangkat lunak.

---

<div align="center">
  <sub>Made with ☕ & PHP · Pat-Pat Cafe © 2025</sub>
</div>
