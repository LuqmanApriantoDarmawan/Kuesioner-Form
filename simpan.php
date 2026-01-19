<?php
/**
 * Simpan Data Kuesioner - Enhanced Version
 * Dengan animasi success dan confetti effect
 */

// Include koneksi database
require_once 'koneksi.php';

// Periksa apakah form disubmit dengan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil dan sanitasi data responden
$umur = filter_input(INPUT_POST, 'umur', FILTER_VALIDATE_INT);
$jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin'] ?? '');
$semester = filter_input(INPUT_POST, 'semester', FILTER_VALIDATE_INT);
$platform_elearning = mysqli_real_escape_string($koneksi, $_POST['platform_elearning'] ?? '');

// Ambil dan validasi jawaban kuesioner (q1 - q8)
$jawaban = [];
for ($i = 1; $i <= 8; $i++) {
    $nilai = filter_input(INPUT_POST, "q$i", FILTER_VALIDATE_INT);
    if ($nilai === false || $nilai < 1 || $nilai > 5) {
        $error = "Jawaban pertanyaan $i tidak valid.";
        break;
    }
    $jawaban["q$i"] = $nilai;
}

// Validasi data responden
if (!$umur || $umur < 17 || $umur > 60) {
    $error = "Umur tidak valid.";
}
if (empty($jenis_kelamin) || !in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
    $error = "Jenis kelamin tidak valid.";
}
if (!$semester || $semester < 1 || $semester > 8) {
    $error = "Semester tidak valid.";
}
if (empty($platform_elearning)) {
    $error = "Platform e-learning harus dipilih.";
}

// Jika tidak ada error, simpan ke database
if (!isset($error)) {
    // Mulai transaction
    mysqli_begin_transaction($koneksi);
    
    try {
        // Insert data responden
        $sql_responden = "INSERT INTO responden (umur, jenis_kelamin, semester, platform_elearning) 
                          VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql_responden);
        mysqli_stmt_bind_param($stmt, "isis", $umur, $jenis_kelamin, $semester, $platform_elearning);
        mysqli_stmt_execute($stmt);
        
        // Ambil ID responden yang baru diinsert
        $responden_id = mysqli_insert_id($koneksi);
        
        // Insert jawaban kuesioner
        $sql_jawaban = "INSERT INTO jawaban (responden_id, q1, q2, q3, q4, q5, q6, q7, q8) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql_jawaban);
        mysqli_stmt_bind_param($stmt, "iiiiiiiii", 
            $responden_id,
            $jawaban['q1'], $jawaban['q2'], $jawaban['q3'], $jawaban['q4'],
            $jawaban['q5'], $jawaban['q6'], $jawaban['q7'], $jawaban['q8']
        );
        mysqli_stmt_execute($stmt);
        
        // Hitung skor rata-rata
        $skor_rata = array_sum($jawaban) / 8;
        
        // Commit transaction
        mysqli_commit($koneksi);
        $success = true;
        
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($koneksi);
        $error = "Terjadi kesalahan saat menyimpan data: " . $e->getMessage();
    }
}

// Tutup koneksi
mysqli_close($koneksi);

// Interpretasi skor
function getMotivationLevel($skor) {
    if ($skor >= 4.6) return ['level' => 'Sangat Tinggi', 'emoji' => '🌟', 'color' => '#10b981'];
    if ($skor >= 3.6) return ['level' => 'Tinggi', 'emoji' => '😊', 'color' => '#10b981'];
    if ($skor >= 3.1) return ['level' => 'Sedang', 'emoji' => '😐', 'color' => '#f59e0b'];
    if ($skor >= 2.1) return ['level' => 'Rendah', 'emoji' => '😕', 'color' => '#ef4444'];
    return ['level' => 'Sangat Rendah', 'emoji' => '😔', 'color' => '#ef4444'];
}

$motivation = isset($skor_rata) ? getMotivationLevel($skor_rata) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($success) ? 'Berhasil' : 'Gagal' ?> - Kuesioner E-Learning</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <div class="card" style="text-align: center; max-width: 550px; margin: 50px auto;">
            <?php if (isset($success)): ?>
                <div class="success-icon">✅</div>
                <h1 style="background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 16px; font-size: 2rem;">Terima Kasih!</h1>
                <p style="color: var(--gray); margin-bottom: 28px; font-size: 1.1rem;">
                    Jawaban Anda telah berhasil disimpan.<br>
                    Terima kasih atas partisipasi Anda dalam kuesioner ini.
                </p>
                
                <!-- Score Result -->
                <div style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 16px; padding: 24px; margin-bottom: 28px;">
                    <p style="color: var(--gray); margin-bottom: 8px; font-size: 0.9rem;">Skor Motivasi Anda</p>
                    <div style="font-size: 3rem; font-weight: 800; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?= number_format($skor_rata, 1) ?>
                    </div>
                    <div style="margin-top: 12px;">
                        <span style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: 30px; font-weight: 700; background: <?= $motivation['color'] ?>20; color: <?= $motivation['color'] ?>;">
                            <?= $motivation['emoji'] ?> Motivasi <?= $motivation['level'] ?>
                        </span>
                    </div>
                </div>
                
                <a href="index.php" class="btn btn-primary">
                    ✨ Isi Kuesioner Lagi
                </a>
            <?php else: ?>
                <div style="font-size: 5rem; margin-bottom: 20px;">❌</div>
                <h1 style="color: var(--danger); margin-bottom: 16px; font-size: 1.75rem;">Gagal Menyimpan</h1>
                <div class="alert alert-error" style="text-align: left;">
                    <?= htmlspecialchars($error ?? 'Terjadi kesalahan yang tidak diketahui.') ?>
                </div>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    ← Kembali ke Form
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($success)): ?>
    <script>
        // Confetti Effect
        function createConfetti() {
            const colors = ['#6366f1', '#06b6d4', '#f472b6', '#10b981', '#f59e0b', '#ef4444'];
            
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = confetti.style.width;
                confetti.style.animationDuration = Math.random() * 2 + 2 + 's';
                confetti.style.animationDelay = Math.random() * 0.5 + 's';
                document.body.appendChild(confetti);
                
                // Remove after animation
                setTimeout(() => confetti.remove(), 4000);
            }
        }
        
        // Trigger confetti
        createConfetti();
    </script>
    <?php endif; ?>
</body>
</html>
