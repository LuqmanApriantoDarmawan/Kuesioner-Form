<?php
/**
 * Koneksi Database
 * File ini berisi konfigurasi koneksi ke database MySQL
 */

// ============================================
// KONFIGURASI DATABASE
// Ubah sesuai dengan hosting Anda
// ============================================
$host = 'localhost';           // Host database (biasanya localhost)
$username = 'root';            // Username database
$password = '';                // Password database (kosong untuk XAMPP default)
$database = 'kuesioner_elearning';  // Nama database

// ============================================
// KONEKSI KE DATABASE
// ============================================
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke utf8 untuk mendukung karakter Indonesia
mysqli_set_charset($koneksi, "utf8");
?>
