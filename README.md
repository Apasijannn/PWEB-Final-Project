<div align="center">
  <br/>
  <p>
    <img src="https://readme-typing-svg.demolab.com?font=Fira+Code&weight=600&size=14&pause=1000&color=888780&center=true&vCenter=true&width=300&lines=Final+Project+%C2%B7+Pemrograman+Web" alt="eyebrow" />
  </p>
  <h1>Pat-Pat Cafe</h1>
  <p><strong>Order &amp; Point of Sale System</strong></p>
  <br/>
  <p>
    <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white"/>
    <img src="https://img.shields.io/badge/MySQL-8.x-005C84?style=flat-square&logo=mysql&logoColor=white"/>
    <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white"/>
    <img src="https://img.shields.io/badge/FPDF-Receipt-C8A96E?style=flat-square"/>
    <img src="https://img.shields.io/badge/v1.0.0-Academic-lightgrey?style=flat-square"/>
    <a href="http://patpat-cafe.page.gd/index.php">
      <img src="https://img.shields.io/badge/Live-Demo-28a745?style=flat-square&logo=vercel&logoColor=white"/>
    </a>
  </p>
  <br/>
  <p>
    Sistem POS berbasis web untuk modernisasi operasional kafe —<br/>
    dari pelanggan scan QR di meja, hingga nota PDF tercetak di kasir.<br/>
    Dibangun dengan PHP native, dirancang dengan standar produk nyata.
  </p>
  <br/>
</div>

---

## Alur Sistem

```
 01 · Scan QR      →   02 · Browse & Order   →   03 · Checkout
 Pelanggan · meja       Katalog · cart             QRIS · konfirmasi

 04 · Dapur        →   05 · Nota PDF
 Order masuk            Kasir · cetak
```

---

## Engineering Highlights

<table>
<tr>
<td width="50%" valign="top">

**Dynamic Cart**
Session-based cart dengan kalkulasi PPN 11% + service charge otomatis. Timer kadaluarsa mencegah stok tertahan dari pesanan dibatalkan.

</td>
<td width="50%" valign="top">

**PDF Receipt**
Thermal receipt 80mm via FPDF + Output Buffering. Bebas distorsi cetak browser, kompatibel langsung dengan printer fisik.

</td>
</tr>
<tr>
<td valign="top">

**Bcrypt Auth**
Semua kredensial pegawai dienkripsi one-way. Tidak ada plaintext tersimpan di database — termasuk dari sisi admin.

</td>
<td valign="top">

**Audit Trail**
Setiap transaksi di-tag dengan session ID kasir. Rekam jejak penuh: siapa kasir, kapan, dan berapa nominal transaksi.

</td>
</tr>
<tr>
<td valign="top">

**Analytics + CSV Export**
Data retention policy 3 tahun untuk menjaga query tetap ringan. Export data mentah ke CSV untuk audit eksternal.

</td>
<td valign="top">

**QRIS In-Memory**
QR Code di-generate dan di-render langsung di RAM server. Tidak ada file temp yang menumpuk di disk hosting.

</td>
</tr>
</table>

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
│
├── admin/           ← back-office: kasir & manajemen
├── assets/          ← CSS · JS · gambar statis
├── config/          ← konfigurasi koneksi database
├── includes/        ← komponen reusable (header, footer)
├── libs/            ← FPDF · QR Generator
├── uploads/         ← media yang diunggah
│
├── index.php        ← landing page pelanggan
├── katalog.php      ← menu & pemesanan
├── checkout.php     ← keranjang & konfirmasi
└── pembayaran.php   ← proses pembayaran & QRIS
```

---

## Instalasi Lokal

**Prasyarat** — XAMPP / Laragon (PHP 8.x + MySQL) + browser modern.

**01 — Clone**
```bash
git clone https://github.com/Apasijannn/PWEB-Final-Project.git
cd PWEB-Final-Project
```

**02 — Pindah ke server lokal**

Salin folder ke `htdocs/` (XAMPP) atau root direktori server lokal yang digunakan.

**03 — Buat & import database**

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
      <br/>
      <img src="https://github.com/Apasijannn.png" width="60" style="border-radius:50%"/><br/>
      <br/>
      <b>Muhammad Dayyan Ghazanfar Latief</b><br/>
      <a href="https://github.com/Apasijannn"><code>@Apasijannn</code></a><br/>
      <sub>Lead Developer</sub>
      <br/><br/>
    </td>
    <td align="center" width="50%">
      <br/>
      <img src="https://github.com/ghost.png" width="60" style="border-radius:50%"/><br/>
      <br/>
      <b>Sitti Aminah</b><br/>
      <sub>Kolaborator</sub>
      <br/><br/>
    </td>
  </tr>
</table>

---

<div align="center">
  <br/>
  <a href="http://patpat-cafe.page.gd/index.php"><code>patpat-cafe.page.gd</code></a>
  <br/><br/>
  <sub>Pat-Pat Cafe © 2025 &nbsp;·&nbsp; PHP native &nbsp;·&nbsp; Academic License</sub>
  <br/><br/>
</div>
