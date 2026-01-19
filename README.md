# 📚 Kuesioner Motivasi Belajar E-Learning

Aplikasi web kuesioner berbasis **PHP dan MySQL** untuk mengumpulkan data motivasi belajar mahasiswa dalam pembelajaran daring berbasis e-learning.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)

---

## ✨ Fitur

### 📝 Form Kuesioner
- Form data responden (umur, jenis kelamin, semester, platform)
- 8 pertanyaan dengan skala Likert (1-5)
- Progress bar interaktif
- Animasi visual yang menarik
- Tampilan skor motivasi setelah submit

### 📊 Dashboard Admin
- Visualisasi data dengan **Chart.js** (bar, pie, doughnut)
- Statistik total responden & rata-rata skor
- **Kesimpulan otomatis** (interpretasi level motivasi)
- **Filter data** berdasarkan semester, gender, platform
- **Export data ke CSV/Excel**

### 🎨 UI/UX Premium
- Animated gradient background
- Glassmorphism card effects
- Floating particles animation
- Micro-animations & hover effects
- Confetti celebration effect
- Fully responsive design

---

## 🖼️ Preview

| Form Kuesioner | Dashboard Admin |
|----------------|-----------------|
| Progress bar, animasi visual | Chart.js, statistik, filter |

---

## 🛠️ Teknologi

- **Backend:** PHP Native
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Charts:** Chart.js
- **Server:** XAMPP / Laragon

---

## 📂 Struktur File

```
kuesioner/
├── index.php           # Form kuesioner
├── simpan.php          # Handler simpan data
├── koneksi.php         # Konfigurasi database
├── style.css           # Stylesheet premium
├── database.sql        # Schema database
├── PANDUAN_DEPLOY.md   # Panduan hosting
└── admin/
    ├── login.php       # Login admin
    ├── dashboard.php   # Dashboard + Chart.js
    ├── export.php      # Export CSV
    └── logout.php      # Logout
```

---

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/USERNAME/kuesioner-elearning.git
```

### 2. Setup Database
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Buat database baru: `kuesioner_elearning`
3. Import file `database.sql`

### 3. Konfigurasi Koneksi
Edit file `koneksi.php` sesuai kredensial database:
```php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kuesioner_elearning';
```

### 4. Jalankan Aplikasi
- **Form:** `http://localhost/kuesioner/`
- **Admin:** `http://localhost/kuesioner/admin/login.php`

---

## 🔐 Kredensial Default

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |

> ⚠️ **Penting:** Ganti password default setelah instalasi!

---

## 📋 Pertanyaan Kuesioner

1. Ketertarikan mengikuti perkuliahan daring
2. Ketertarikan terhadap materi e-learning
3. Semangat mengikuti pembelajaran daring
4. Keterlibatan dalam pengerjaan tugas
5. Kemandirian mengatur waktu belajar
6. Konsistensi belajar tanpa pengawasan langsung
7. Kepuasan terhadap pembelajaran daring
8. Kemudahan memahami materi melalui e-learning

**Skala:** 1 (Sangat Tidak Setuju) - 5 (Sangat Setuju)

---

## 📈 Interpretasi Skor

| Rentang Skor | Level Motivasi |
|--------------|----------------|
| 4.6 - 5.0 | 🌟 Sangat Tinggi |
| 3.6 - 4.5 | 😊 Tinggi |
| 3.1 - 3.5 | 😐 Sedang |
| 2.1 - 3.0 | 😕 Rendah |
| 1.0 - 2.0 | 😔 Sangat Rendah |

---

## 🌐 Deployment

Lihat [PANDUAN_DEPLOY.md](PANDUAN_DEPLOY.md) untuk panduan upload ke hosting.

Hosting gratis yang direkomendasikan:
- [InfinityFree](https://infinityfree.com)
- [000Webhost](https://000webhost.com)

---

## 📄 Lisensi

Project ini dibuat untuk keperluan **tugas akademik**.

---

## 👨‍💻 Author

Dibuat dengan ❤️ untuk Program Studi Informatika

---

⭐ **Jangan lupa kasih star jika project ini bermanfaat!**
