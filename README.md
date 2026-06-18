<div align="center">

<br/>

# PAT-PAT CAFE
### Order & Point of Sale System

<sub>v1.0.0 · Academic License</sub>

<br/>

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-005C84?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![FPDF](https://img.shields.io/badge/FPDF-Receipt-C8A96E?style=flat-square)
[![Live Demo](https://img.shields.io/badge/Live-Demo-28a745?style=flat-square&logo=vercel&logoColor=white)](http://patpat-cafe.page.gd/index.php)

</div>

---

Sistem POS berbasis web untuk modernisasi operasional kafe — dari scan QR di meja hingga nota PDF tercetak di kasir. Dibangun dengan PHP native, dirancang dengan standar produk nyata.

> Final Project — Mata Kuliah Pemrograman Web

---

## Engineering Highlights

```
Dynamic Cart      Session-based cart, PPN 11% + service charge otomatis.
                  Timer kadaluarsa untuk pelepasan stok dari pesanan dibatalkan.

PDF Receipt       Thermal receipt 80mm via FPDF + Output Buffering.
                  Bebas distorsi cetak browser, siap kirim ke printer fisik.

Bcrypt Auth       Kredensial pegawai dienkripsi one-way.
                  Tidak ada plaintext di database — termasuk dari sisi admin.

Audit Trail       Setiap transaksi di-tag dengan session ID kasir.
                  Rekam jejak penuh: siapa kasir, kapan, berapa nominal.

Analytics + CSV   Data retention policy 3 tahun untuk optimalisasi query.
                  Export data mentah ke CSV untuk audit di Excel.

QRIS In-Memory    QR Code di-generate dan di-render langsung di RAM.
                  Tidak ada file temp yang menumpuk di disk hosting.
```

---

## Screenshots

| Katalog Menu | Dashboard Keuangan |
|:---:|:---:|
| ![Katalog](assets/screenshots/katalog.png) | ![Dashboard](assets/screenshots/dashboard.png) |
| **Nota Digital PDF** | **Panel Admin** |
| ![Nota](assets/screenshots/nota.png) | ![Admin](assets/screenshots/admin.png) |

---

## Struktur Direktori

```
PWEB-Final-Project/
├── admin/           → Panel back-office (kasir & manajemen)
├── assets/          → CSS, JS, gambar statis
├── config/          → Konfigurasi koneksi database
├── includes/        → Komponen reusable (header, footer, dll)
├── libs/            → Library pihak ketiga (FPDF, QR generator)
├── uploads/         → Media yang diunggah
├── index.php        → Landing page pelanggan
├── katalog.php      → Menu & pemesanan
├── checkout.php     → Keranjang & konfirmasi
└── pembayaran.php   → Proses pembayaran & QRIS
```

---

## Instalasi Lokal

**Prasyarat:** XAMPP / Laragon (PHP 8.x + MySQL) + browser modern.

**01 — Clone**
```bash
git clone https://github.com/Apasijannn/PWEB-Final-Project.git
cd PWEB-Final-Project
```

**02 — Pindah ke server lokal**

Salin folder ke `htdocs/` (XAMPP) atau root direktori server lokal yang kamu gunakan.

**03 — Import database**

Buka `http://localhost/phpmyadmin`, buat database `patpat_cafe`, lalu import `patpat_cafe.sql` dari root proyek.

**04 — Konfigurasi koneksi**

Edit `config/koneksi.php`:
```php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "patpat_cafe";
```

**05 — Akses aplikasi**

| Role | URL |
|---|---|
| Pelanggan | `http://localhost/PWEB-Final-Project/` |
| Admin & Kasir | `http://localhost/PWEB-Final-Project/admin/` |

---

## Tim Pengembang

<table>
  <tr>
    <td align="center" width="50%">
      <img src="https://github.com/Apasijannn.png" width="64" style="border-radius:50%"/><br/><br/>
      <b>Muhammad Dayyan Ghazanfar Latief</b><br/>
      <a href="https://github.com/Apasijannn">@Apasijannn</a>
    </td>
    <td align="center" width="50%">
      <img src="https://github.com/ghost.png" width="64" style="border-radius:50%"/><br/><br/>
      <b>Sitti Aminah</b><br/>
      <sub>Kolaborator</sub>
    </td>
  </tr>
</table>

---

<div align="center">
<sub>Pat-Pat Cafe © 2025 &nbsp;·&nbsp; PHP native &nbsp;·&nbsp; <a href="http://patpat-cafe.page.gd/index.php">patpat-cafe.page.gd</a></sub>
</div>
