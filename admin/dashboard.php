<?php
/**
 * Admin Dashboard - Enhanced Version
 * Dengan Chart.js, Filter, Kesimpulan, dan UI Premium
 */

session_start();

// Cek apakah sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Include koneksi database
require_once '../koneksi.php';

// Ambil filter dari GET
$filter_semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;
$filter_gender = isset($_GET['gender']) ? mysqli_real_escape_string($koneksi, $_GET['gender']) : '';
$filter_platform = isset($_GET['platform']) ? mysqli_real_escape_string($koneksi, $_GET['platform']) : '';

// Build WHERE clause
$where = [];
if ($filter_semester > 0) $where[] = "r.semester = $filter_semester";
if ($filter_gender) $where[] = "r.jenis_kelamin = '$filter_gender'";
if ($filter_platform) $where[] = "r.platform_elearning = '$filter_platform'";
$where_clause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// Ambil statistik total
$sql_count = "SELECT COUNT(*) as total FROM responden r $where_clause";
$result_count = mysqli_query($koneksi, $sql_count);
$total_responden = mysqli_fetch_assoc($result_count)['total'];

// Hitung rata-rata skor motivasi
$sql_avg = "SELECT 
    ROUND(AVG((j.q1+j.q2+j.q3+j.q4+j.q5+j.q6+j.q7+j.q8)/8), 2) as rata_rata
    FROM responden r
    LEFT JOIN jawaban j ON r.id = j.responden_id
    $where_clause";
$result_avg = mysqli_query($koneksi, $sql_avg);
$rata_rata = mysqli_fetch_assoc($result_avg)['rata_rata'] ?? 0;

// Rata-rata per pertanyaan (untuk chart)
$sql_per_q = "SELECT 
    ROUND(AVG(j.q1), 2) as q1,
    ROUND(AVG(j.q2), 2) as q2,
    ROUND(AVG(j.q3), 2) as q3,
    ROUND(AVG(j.q4), 2) as q4,
    ROUND(AVG(j.q5), 2) as q5,
    ROUND(AVG(j.q6), 2) as q6,
    ROUND(AVG(j.q7), 2) as q7,
    ROUND(AVG(j.q8), 2) as q8
    FROM responden r
    LEFT JOIN jawaban j ON r.id = j.responden_id
    $where_clause";
$result_per_q = mysqli_query($koneksi, $sql_per_q);
$avg_per_q = mysqli_fetch_assoc($result_per_q);

// Distribusi jenis kelamin
$sql_gender = "SELECT jenis_kelamin, COUNT(*) as jumlah FROM responden r $where_clause GROUP BY jenis_kelamin";
$result_gender = mysqli_query($koneksi, $sql_gender);
$gender_data = [];
while ($row = mysqli_fetch_assoc($result_gender)) {
    $gender_data[$row['jenis_kelamin']] = $row['jumlah'];
}

// Distribusi platform
$sql_platform = "SELECT platform_elearning, COUNT(*) as jumlah FROM responden r $where_clause GROUP BY platform_elearning";
$result_platform = mysqli_query($koneksi, $sql_platform);
$platform_data = [];
while ($row = mysqli_fetch_assoc($result_platform)) {
    $platform_data[$row['platform_elearning']] = $row['jumlah'];
}

// Distribusi semester
$sql_semester = "SELECT semester, COUNT(*) as jumlah FROM responden r $where_clause GROUP BY semester ORDER BY semester";
$result_semester = mysqli_query($koneksi, $sql_semester);
$semester_data = [];
while ($row = mysqli_fetch_assoc($result_semester)) {
    $semester_data[$row['semester']] = $row['jumlah'];
}

// Ambil daftar platform unik untuk filter
$sql_platforms = "SELECT DISTINCT platform_elearning FROM responden ORDER BY platform_elearning";
$result_platforms = mysqli_query($koneksi, $sql_platforms);
$platforms = [];
while ($row = mysqli_fetch_assoc($result_platforms)) {
    $platforms[] = $row['platform_elearning'];
}

// Ambil semua data responden dan jawaban
$sql_data = "SELECT 
    r.id,
    r.umur,
    r.jenis_kelamin,
    r.semester,
    r.platform_elearning,
    r.created_at,
    j.q1, j.q2, j.q3, j.q4, j.q5, j.q6, j.q7, j.q8,
    ROUND((j.q1+j.q2+j.q3+j.q4+j.q5+j.q6+j.q7+j.q8)/8, 2) as skor_rata
    FROM responden r
    LEFT JOIN jawaban j ON r.id = j.responden_id
    $where_clause
    ORDER BY r.created_at DESC";
$result_data = mysqli_query($koneksi, $sql_data);

// Fungsi interpretasi skor
function getInterpretasi($skor) {
    if ($skor >= 4.6) return ['level' => 'Sangat Tinggi', 'class' => 'high', 'emoji' => '🌟', 'desc' => 'Mahasiswa memiliki motivasi yang sangat tinggi dalam pembelajaran daring.'];
    if ($skor >= 3.6) return ['level' => 'Tinggi', 'class' => 'high', 'emoji' => '😊', 'desc' => 'Mahasiswa memiliki motivasi yang tinggi dan antusias dalam pembelajaran daring.'];
    if ($skor >= 3.1) return ['level' => 'Sedang', 'class' => 'medium', 'emoji' => '😐', 'desc' => 'Mahasiswa memiliki motivasi sedang, perlu sedikit dorongan untuk lebih aktif.'];
    if ($skor >= 2.1) return ['level' => 'Rendah', 'class' => 'low', 'emoji' => '😕', 'desc' => 'Mahasiswa memiliki motivasi rendah, perlu perhatian dan dukungan lebih.'];
    return ['level' => 'Sangat Rendah', 'class' => 'low', 'emoji' => '😔', 'desc' => 'Mahasiswa memiliki motivasi sangat rendah, perlu intervensi khusus.'];
}

$interpretasi = getInterpretasi($rata_rata);

// Label pertanyaan
$labels_pertanyaan = [
    'Q1' => 'Ketertarikan Perkuliahan',
    'Q2' => 'Ketertarikan Materi',
    'Q3' => 'Semangat Belajar',
    'Q4' => 'Keterlibatan Tugas',
    'Q5' => 'Kemandirian Waktu',
    'Q6' => 'Konsistensi Belajar',
    'Q7' => 'Kepuasan Pembelajaran',
    'Q8' => 'Kemudahan Materi'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kuesioner E-Learning</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <div class="container" style="max-width: 1300px;">
        <div class="card">
            <!-- Header -->
            <div class="dashboard-header">
                <div>
                    <h1>📊 Dashboard Admin</h1>
                    <p style="color: var(--gray);">Selamat datang, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></p>
                </div>
                <div class="dashboard-actions">
                    <a href="export.php?<?= http_build_query($_GET) ?>" class="btn btn-success btn-sm">
                        📥 Export CSV
                    </a>
                    <a href="../index.php" class="btn btn-secondary btn-sm">📝 Form</a>
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-title">🔍 Filter Data</div>
                <form method="GET" class="filter-row">
                    <div class="filter-group">
                        <label>Semester</label>
                        <select name="semester" onchange="this.form.submit()">
                            <option value="">Semua Semester</option>
                            <?php for($i = 1; $i <= 8; $i++): ?>
                            <option value="<?= $i ?>" <?= $filter_semester == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Jenis Kelamin</label>
                        <select name="gender" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            <option value="Laki-laki" <?= $filter_gender == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $filter_gender == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Platform</label>
                        <select name="platform" onchange="this.form.submit()">
                            <option value="">Semua Platform</option>
                            <?php foreach($platforms as $p): ?>
                            <option value="<?= htmlspecialchars($p) ?>" <?= $filter_platform == $p ? 'selected' : '' ?>><?= htmlspecialchars($p) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group" style="flex: 0;">
                        <label>&nbsp;</label>
                        <a href="dashboard.php" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?= $total_responden ?></div>
                    <div class="stat-label">Total Responden</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-value"><?= $rata_rata ?: '0' ?></div>
                    <div class="stat-label">Rata-rata Skor</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><?= $interpretasi['emoji'] ?></div>
                    <div class="stat-value" style="font-size: 1.5rem;"><?= $interpretasi['level'] ?></div>
                    <div class="stat-label">Level Motivasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">❓</div>
                    <div class="stat-value">8</div>
                    <div class="stat-label">Jumlah Pertanyaan</div>
                </div>
            </div>
            
            <?php if ($total_responden > 0): ?>
            
            <!-- Kesimpulan -->
            <div class="conclusion-card">
                <div class="conclusion-title">📝 Kesimpulan & Interpretasi</div>
                <div class="conclusion-content">
                    <p style="margin-bottom: 16px;">
                        Berdasarkan <strong><?= $total_responden ?> responden</strong> yang telah mengisi kuesioner, 
                        diperoleh rata-rata skor motivasi sebesar <strong><?= $rata_rata ?></strong> dari skala 5.
                    </p>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <span class="motivation-level <?= $interpretasi['class'] ?>">
                            <?= $interpretasi['emoji'] ?> Motivasi <?= $interpretasi['level'] ?>
                        </span>
                        <span style="color: #6b21a8;"><?= $interpretasi['desc'] ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="charts-row">
                <!-- Bar Chart - Rata-rata per Pertanyaan -->
                <div class="chart-container">
                    <div class="chart-title">📊 Rata-rata Skor per Pertanyaan</div>
                    <canvas id="barChart" height="250"></canvas>
                </div>
                
                <!-- Pie Chart - Gender Distribution -->
                <div class="chart-container">
                    <div class="chart-title">👤 Distribusi Jenis Kelamin</div>
                    <canvas id="genderChart" height="250"></canvas>
                </div>
            </div>
            
            <div class="charts-row">
                <!-- Doughnut Chart - Platform -->
                <div class="chart-container">
                    <div class="chart-title">💻 Distribusi Platform E-Learning</div>
                    <canvas id="platformChart" height="250"></canvas>
                </div>
                
                <!-- Bar Chart - Semester -->
                <div class="chart-container">
                    <div class="chart-title">📚 Distribusi per Semester</div>
                    <canvas id="semesterChart" height="250"></canvas>
                </div>
            </div>
            
            <?php endif; ?>
            
            <!-- Data Table -->
            <h2 class="section-title">Data Responden & Jawaban</h2>
            
            <?php if ($total_responden > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Umur</th>
                            <th>JK</th>
                            <th>Smt</th>
                            <th>Platform</th>
                            <th>Q1</th>
                            <th>Q2</th>
                            <th>Q3</th>
                            <th>Q4</th>
                            <th>Q5</th>
                            <th>Q6</th>
                            <th>Q7</th>
                            <th>Q8</th>
                            <th>Rata²</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result_data)): 
                            $skor = $row['skor_rata'];
                            $badge_class = $skor >= 3.6 ? 'badge-success' : ($skor >= 3.0 ? 'badge-warning' : 'badge-danger');
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                            <td><?= $row['umur'] ?></td>
                            <td><?= substr($row['jenis_kelamin'], 0, 1) ?></td>
                            <td><?= $row['semester'] ?></td>
                            <td><?= htmlspecialchars($row['platform_elearning']) ?></td>
                            <td><?= $row['q1'] ?></td>
                            <td><?= $row['q2'] ?></td>
                            <td><?= $row['q3'] ?></td>
                            <td><?= $row['q4'] ?></td>
                            <td><?= $row['q5'] ?></td>
                            <td><?= $row['q6'] ?></td>
                            <td><?= $row['q7'] ?></td>
                            <td><?= $row['q8'] ?></td>
                            <td><span class="badge <?= $badge_class ?>"><?= $skor ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                📭 Belum ada data responden<?= ($filter_semester || $filter_gender || $filter_platform) ? ' dengan filter yang dipilih' : '' ?>. 
                <?php if (!$filter_semester && !$filter_gender && !$filter_platform): ?>
                Silakan isi kuesioner terlebih dahulu.
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Keterangan Skala -->
            <div style="margin-top: 24px; padding: 20px; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 16px;">
                <strong style="color: var(--dark);">📋 Keterangan Pertanyaan:</strong>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 8px; margin-top: 12px;">
                    <?php foreach($labels_pertanyaan as $q => $label): ?>
                    <div style="color: var(--gray); font-size: 0.875rem;">
                        <strong><?= $q ?>:</strong> <?= $label ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p style="margin-top: 16px; font-size: 0.875rem; color: var(--gray);">
                    <strong>Skala Likert:</strong> 1 = Sangat Tidak Setuju, 2 = Tidak Setuju, 3 = Netral, 4 = Setuju, 5 = Sangat Setuju
                </p>
            </div>
        </div>
        
        <footer class="footer">
            <p>© 2026 Kuesioner Motivasi Belajar E-Learning | Dashboard Admin</p>
        </footer>
    </div>
    
    <?php if ($total_responden > 0): ?>
    <script>
        // Chart.js default config
        Chart.defaults.font.family = 'Inter, sans-serif';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        
        // Gradient colors
        const gradient1 = 'rgba(99, 102, 241, 0.8)';
        const gradient2 = 'rgba(6, 182, 212, 0.8)';
        const gradient3 = 'rgba(244, 114, 182, 0.8)';
        
        // Bar Chart - Rata-rata per Pertanyaan
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_values($labels_pertanyaan)) ?>,
                datasets: [{
                    label: 'Rata-rata Skor',
                    data: [
                        <?= $avg_per_q['q1'] ?? 0 ?>,
                        <?= $avg_per_q['q2'] ?? 0 ?>,
                        <?= $avg_per_q['q3'] ?? 0 ?>,
                        <?= $avg_per_q['q4'] ?? 0 ?>,
                        <?= $avg_per_q['q5'] ?? 0 ?>,
                        <?= $avg_per_q['q6'] ?? 0 ?>,
                        <?= $avg_per_q['q7'] ?? 0 ?>,
                        <?= $avg_per_q['q8'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(244, 114, 182, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(250, 204, 21, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
                    ],
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 45, minRotation: 45 }
                    }
                }
            }
        });
        
        // Pie Chart - Gender
        new Chart(document.getElementById('genderChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_keys($gender_data)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($gender_data)) ?>,
                    backgroundColor: ['rgba(99, 102, 241, 0.8)', 'rgba(244, 114, 182, 0.8)'],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
        
        // Doughnut Chart - Platform
        new Chart(document.getElementById('platformChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($platform_data)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($platform_data)) ?>,
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(244, 114, 182, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '60%'
            }
        });
        
        // Bar Chart - Semester
        new Chart(document.getElementById('semesterChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function($s) { return 'Smt ' . $s; }, array_keys($semester_data))) ?>,
                datasets: [{
                    label: 'Jumlah Responden',
                    data: <?= json_encode(array_values($semester_data)) ?>,
                    backgroundColor: 'rgba(6, 182, 212, 0.8)',
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
<?php
mysqli_close($koneksi);
?>
