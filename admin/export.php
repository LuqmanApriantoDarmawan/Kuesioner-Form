<?php
/**
 * Export Data ke CSV
 * Download semua data responden dan jawaban
 */

session_start();

// Cek apakah sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Include koneksi database
require_once '../koneksi.php';

// Set headers untuk download CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_kuesioner_' . date('Y-m-d_H-i-s') . '.csv');

// Buat output stream
$output = fopen('php://output', 'w');

// Tambahkan BOM untuk Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header kolom
$headers = [
    'No',
    'Tanggal',
    'Umur',
    'Jenis Kelamin',
    'Semester',
    'Platform E-Learning',
    'Q1 - Ketertarikan Perkuliahan Daring',
    'Q2 - Ketertarikan Materi E-Learning',
    'Q3 - Semangat Pembelajaran Daring',
    'Q4 - Keterlibatan Pengerjaan Tugas',
    'Q5 - Kemandirian Waktu Belajar',
    'Q6 - Konsistensi Tanpa Pengawasan',
    'Q7 - Kepuasan Pembelajaran Daring',
    'Q8 - Kemudahan Memahami Materi',
    'Rata-rata Skor',
    'Kategori Motivasi'
];
fputcsv($output, $headers);

// Query data
$sql = "SELECT 
    r.id,
    r.created_at,
    r.umur,
    r.jenis_kelamin,
    r.semester,
    r.platform_elearning,
    j.q1, j.q2, j.q3, j.q4, j.q5, j.q6, j.q7, j.q8,
    ROUND((j.q1+j.q2+j.q3+j.q4+j.q5+j.q6+j.q7+j.q8)/8, 2) as skor_rata
    FROM responden r
    LEFT JOIN jawaban j ON r.id = j.responden_id
    ORDER BY r.created_at DESC";

$result = mysqli_query($koneksi, $sql);

// Fungsi untuk menentukan kategori motivasi
function getKategoriMotivasi($skor) {
    if ($skor >= 4.6) return 'Sangat Tinggi';
    if ($skor >= 3.6) return 'Tinggi';
    if ($skor >= 3.1) return 'Sedang';
    if ($skor >= 2.1) return 'Rendah';
    return 'Sangat Rendah';
}

// Output data
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $data = [
        $no++,
        date('d/m/Y H:i', strtotime($row['created_at'])),
        $row['umur'],
        $row['jenis_kelamin'],
        $row['semester'],
        $row['platform_elearning'],
        $row['q1'],
        $row['q2'],
        $row['q3'],
        $row['q4'],
        $row['q5'],
        $row['q6'],
        $row['q7'],
        $row['q8'],
        $row['skor_rata'],
        getKategoriMotivasi($row['skor_rata'])
    ];
    fputcsv($output, $data);
}

fclose($output);
mysqli_close($koneksi);
exit;
?>
