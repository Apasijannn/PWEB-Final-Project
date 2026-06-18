<div align="center">

<img src="https://readme-typing-svg.demolab.com?font=Fira+Code&weight=700&size=30&pause=1000&color=C8A96E&center=true&vCenter=true&width=500&lines=☕+Pat-Pat+Cafe;Order+%26+POS+System" alt="Pat-Pat Cafe" />

<br/>

> **Sistem Point of Sale & Order Management berbasis web.**
> Mendukung *Dine-in via QR Code* dan *Takeaway* — dari meja ke dapur, real-time.

<br/>

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-005C84?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![FPDF](https://img.shields.io/badge/FPDF-Receipt_Generator-C8A96E?style=flat-square)
![License](https://img.shields.io/badge/License-Academic-lightgrey?style=flat-square)

</div>

---

## 📖 Tentang Proyek

**Pat-Pat Cafe Order System** adalah aplikasi POS (*Point of Sale*) komersial yang dirancang untuk memodernisasi alur operasional kafe — mulai dari pemesanan pelanggan, manajemen kasir, hingga pelaporan keuangan harian. Dibangun dengan arsitektur PHP native yang ringan namun menerapkan prinsip keamanan dan akuntabilitas operasional yang serius.

> Dikembangkan sebagai *Final Project* mata kuliah **Pemrograman Web** dan sekaligus portofolio rekayasa perangkat lunak profesional.

---

## ✨ Engineering Highlights

| | Fitur | Deskripsi |
|---|---|---|
| 🛒 | **Dynamic Cart & Transaksi Real-Time** | Session-based cart di memori PHP dengan kalkulasi PPN 11% & service charge otomatis. Dilengkapi timer kadaluarsa untuk pelepasan stok. |
| 🧾 | **Nota Digital via FPDF** | Thermal receipt 80mm di-generate langsung dari server menggunakan FPDF + Output Buffering — bebas distorsi cetak browser. |
| 🔐 | **Keamanan Bcrypt** | Seluruh kredensial pegawai dienkripsi one-way dengan Bcrypt — tidak ada plaintext tersimpan di database. |
| 📊 | **Dashboard Analitik & CSV Export** | Data Retention Policy 3 tahun untuk optimalisasi query, plus ekspor data mentah ke CSV untuk audit di Excel. |
| 🧾 | **Audit Trail Kasir** | Session tagging ketat pada setiap transaksi kasir untuk jejak audit operasional yang akuntabel. |
| 📱 | **Dynamic QRIS Render** | QR Code di-generate dan di-render langsung di RAM — tidak ada file temp yang menumpuk di hosting. |

---

## 📸 Screenshots

| Halaman Katalog | Dashboard Keuangan |
|:---:|:---:|
| ![Katalog](assets/screenshots/katalog.png) | ![Keuangan](assets/screenshots/dashboard.png) |
| **Nota Digital PDF** | **Panel Admin** |
| ![Nota](assets/screenshots/nota.png) | ![Admin](assets/screenshots/admin.png) |

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

Salin folder proyek ke `htdocs/` (XAMPP) atau direktori root server lokal yang kamu gunakan.

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
|---|---|
| 🧑 Pelanggan (Katalog & Pemesanan) | `http://localhost/PWEB-Final-Project/` |
| 🔧 Admin & Kasir (Back-Office) | `http://localhost/PWEB-Final-Project/admin/` |

---

## 🗂️ Struktur Direktori

```
PWEB-Final-Project/
├── admin/           # Panel back-office (kasir & manajemen)
├── assets/          # CSS, JS, gambar
├── config/          # Konfigurasi database
├── includes/        # Komponen reusable (header, footer, dll)
├── libs/            # Library pihak ketiga (FPDF, QR generator)
├── uploads/         # Media yang diunggah
├── index.php        # Halaman utama pelanggan
├── katalog.php      # Halaman menu & pemesanan
├── checkout.php     # Keranjang & konfirmasi pesanan
└── pembayaran.php   # Proses pembayaran & QRIS
```

---

## 👥 Tim Pengembang

<table>
  <tr>
    <td align="center" width="50%">
      <img src="https://github.com/Apasijannn.png" width="72" style="border-radius:50%"/><br/>
      <b>Muhammad Dayyan Ghazanfar Latief</b><br/>
      <a href="https://github.com/Apasijannn">@Apasijannn</a>
    </td>
    <td align="center" width="50%">
      <img src="https://github.com/ghost.png" width="72" style="border-radius:50%"/><br/>
      <b>Sitti Aminah</b><br/>
      <sub>Kolaborator</sub>
    </td>
  </tr>
</table>

---

## 🌐 Live Demo

> 🔗 [patpat-cafe.page.gd](http://patpat-cafe.page.gd/index.php)

---

<div align="center">
  <sub>Made with ☕ & PHP &nbsp;·&nbsp; Pat-Pat Cafe © 2025</sub>
</div>
