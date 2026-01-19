# 📤 Panduan Deploy ke InfinityFree

Panduan langkah-langkah untuk meng-upload aplikasi kuesioner ke hosting gratis InfinityFree.

---

## 🔧 Langkah 1: Daftar Akun InfinityFree

1. Buka [infinityfree.com](https://www.infinityfree.com)
2. Klik **Sign Up** → Daftar dengan email
3. Verifikasi email Anda

---

## 🌐 Langkah 2: Buat Hosting Baru

1. Login ke InfinityFree
2. Klik **Create Account**
3. Pilih subdomain gratis (contoh: `kuesioner-elearning.epizy.com`)
4. Tunggu hingga hosting aktif (~1-2 menit)

---

## 💾 Langkah 3: Buat Database MySQL

1. Di dashboard hosting, klik **MySQL Databases**
2. Buat database baru, catat:
   - **Database Name**: (contoh: `epiz_xxxxx_kuesioner`)
   - **Username**: (biasanya sama dengan database name)
   - **Password**: (generate atau buat sendiri)
   - **Host**: `sql.epizy.com` (atau sesuai yang tertera)

---

## 📥 Langkah 4: Import Database

1. Buka **phpMyAdmin** dari dashboard hosting
2. Pilih database yang baru dibuat
3. Klik tab **Import**
4. Upload file `database.sql`
5. Klik **Go** untuk menjalankan

---

## ⚙️ Langkah 5: Edit Konfigurasi Database

Sebelum upload, **edit file `koneksi.php`** sesuai dengan data hosting:

```php
$host = 'sql.epizy.com';           // Ganti sesuai host dari InfinityFree
$username = 'epiz_xxxxx_kuesioner'; // Username database Anda
$password = 'password_anda';        // Password database Anda
$database = 'epiz_xxxxx_kuesioner'; // Nama database Anda
```

---

## 📂 Langkah 6: Upload File

### Via File Manager:
1. Buka **File Manager** dari dashboard
2. Masuk ke folder `htdocs`
3. Upload semua file dari folder `kuesioner/`:
   - `index.php`
   - `simpan.php`
   - `koneksi.php`
   - `style.css`
   - Folder `admin/` (dengan isinya)

### Via FTP (Alternatif):
1. Download FileZilla
2. Gunakan kredensial FTP dari dashboard hosting
3. Upload ke folder `/htdocs/`

---

## ✅ Langkah 7: Testing

1. **Form Kuesioner**: `https://subdomain-anda.epizy.com/`
2. **Login Admin**: `https://subdomain-anda.epizy.com/admin/login.php`
3. **Kredensial Admin**: `admin` / `admin123`

---

## ⚠️ Catatan Penting

- **Gratis**: InfinityFree sepenuhnya gratis
- **Batasan**: Tidak mendukung `mail()` function
- **SSL**: HTTPS tersedia gratis
- **Uptime**: 99% uptime untuk hosting gratis

---

## 🔐 Tips Keamanan

> Setelah deploy, segera ganti password admin default melalui phpMyAdmin!

```sql
UPDATE admin SET password = '$2y$10$HASH_BARU' WHERE username = 'admin';
```

Gunakan [bcrypt-generator.com](https://bcrypt-generator.com/) untuk generate hash password baru.
