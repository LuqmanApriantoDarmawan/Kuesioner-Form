<?php
/**
 * Admin Login Page - Premium Version
 * Halaman login untuk admin dengan UI menarik
 */

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Include koneksi database
require_once '../koneksi.php';

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi.';
    } else {
        // Cari user di database
        $sql = "SELECT id, username, password FROM admin WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($admin = mysqli_fetch_assoc($result)) {
            // Verifikasi password
            if (password_verify($password, $admin['password'])) {
                // Set session
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                // Redirect ke dashboard
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Password salah.';
            }
        } else {
            $error = 'Username tidak ditemukan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kuesioner E-Learning</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-container">
        <div class="card login-card">
            <div class="login-logo">
                <div class="icon">🔐</div>
                <h1 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 8px; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Login Admin</h1>
                <p style="color: var(--gray);">Kuesioner Motivasi Belajar E-Learning</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Masukkan username" required 
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Masukkan password" required
                           autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    🚀 Login
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 28px;">
                <a href="../index.php" style="color: var(--gray); text-decoration: none; font-weight: 500; transition: color 0.2s;">
                    ← Kembali ke Form Kuesioner
                </a>
            </div>
        </div>
    </div>
</body>
</html>
