-- ==============================================
-- Database: kuesioner_elearning
-- Untuk: Aplikasi Kuesioner Motivasi Belajar E-Learning
-- ==============================================

-- Buat database (jalankan manual jika hosting tidak mendukung CREATE DATABASE)
CREATE DATABASE IF NOT EXISTS kuesioner_elearning;
USE kuesioner_elearning;

-- ==============================================
-- Tabel: admin
-- ==============================================
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$YMVPwPKjLZ8aH.Q8B8Qn4uy1L7Xk3S9qXdJz5nZcNxRvw6KpYmHGi');

-- ==============================================
-- Tabel: responden
-- ==============================================
CREATE TABLE IF NOT EXISTS responden (
    id INT PRIMARY KEY AUTO_INCREMENT,
    umur INT NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    semester INT NOT NULL,
    platform_elearning VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================
-- Tabel: jawaban
-- Berisi jawaban skala Likert (1-5) untuk 8 pertanyaan
-- ==============================================
CREATE TABLE IF NOT EXISTS jawaban (
    id INT PRIMARY KEY AUTO_INCREMENT,
    responden_id INT NOT NULL,
    q1 TINYINT NOT NULL COMMENT 'Ketertarikan mengikuti perkuliahan daring',
    q2 TINYINT NOT NULL COMMENT 'Ketertarikan terhadap materi e-learning',
    q3 TINYINT NOT NULL COMMENT 'Semangat mengikuti pembelajaran daring',
    q4 TINYINT NOT NULL COMMENT 'Keterlibatan dalam pengerjaan tugas',
    q5 TINYINT NOT NULL COMMENT 'Kemandirian mengatur waktu belajar',
    q6 TINYINT NOT NULL COMMENT 'Konsistensi belajar tanpa pengawasan langsung',
    q7 TINYINT NOT NULL COMMENT 'Kepuasan terhadap pembelajaran daring',
    q8 TINYINT NOT NULL COMMENT 'Kemudahan memahami materi melalui e-learning',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (responden_id) REFERENCES responden(id) ON DELETE CASCADE
);

-- ==============================================
-- Index untuk performa query
-- ==============================================
CREATE INDEX idx_responden_semester ON responden(semester);
CREATE INDEX idx_jawaban_responden ON jawaban(responden_id);
