<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuesioner Motivasi Belajar E-Learning</title>
    <meta name="description" content="Kuesioner untuk mengukur motivasi belajar mahasiswa dalam pembelajaran daring berbasis e-learning">
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
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <form action="simpan.php" method="POST" id="kuesionerForm">
            <div class="card">
                <!-- Header -->
                <div class="header">
                    <h1>📚 Kuesioner Motivasi Belajar</h1>
                    <p>Pembelajaran Daring Berbasis E-Learning</p>
                </div>

                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                    </div>
                    <div class="progress-steps">
                        <div class="progress-step">
                            <div class="step-dot active" id="step1">1</div>
                            <span class="step-label">Data Diri</span>
                        </div>
                        <div class="progress-step">
                            <div class="step-dot" id="step2">2</div>
                            <span class="step-label">Pertanyaan</span>
                        </div>
                        <div class="progress-step">
                            <div class="step-dot" id="step3">✓</div>
                            <span class="step-label">Selesai</span>
                        </div>
                    </div>
                </div>

                <!-- Bagian Data Responden -->
                <h2 class="section-title">Data Responden</h2>
                
                <div class="form-group">
                    <label for="umur" class="required">Umur</label>
                    <input type="number" id="umur" name="umur" class="form-control" 
                           placeholder="Masukkan umur Anda" min="17" max="60" required>
                </div>

                <div class="form-group">
                    <label for="jenis_kelamin" class="required">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="semester" class="required">Semester</label>
                    <select id="semester" name="semester" class="form-control" required>
                        <option value="">-- Pilih Semester --</option>
                        <?php for($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>">Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="platform_elearning" class="required">Platform E-Learning yang Digunakan</label>
                    <select id="platform_elearning" name="platform_elearning" class="form-control" required>
                        <option value="">-- Pilih Platform --</option>
                        <option value="Google Classroom">Google Classroom</option>
                        <option value="Zoom + LMS Kampus">Zoom + LMS Kampus</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <hr class="section-divider">

                <!-- Bagian Kuesioner -->
                <h2 class="section-title">Kuesioner Motivasi Belajar</h2>
                <p style="color: var(--gray); margin-bottom: 28px; font-size: 0.95rem;">
                    Berikan penilaian Anda untuk setiap pernyataan berikut dengan skala 1-5.<br>
                    <span style="display: inline-flex; gap: 16px; margin-top: 8px; flex-wrap: wrap;">
                        <span><strong>1</strong> = Sangat Tidak Setuju</span>
                        <span><strong>5</strong> = Sangat Setuju</span>
                    </span>
                </p>

                <?php
                $pertanyaan = [
                    1 => "Saya tertarik mengikuti perkuliahan daring",
                    2 => "Saya tertarik terhadap materi yang disajikan melalui e-learning",
                    3 => "Saya bersemangat mengikuti pembelajaran daring",
                    4 => "Saya aktif terlibat dalam pengerjaan tugas yang diberikan",
                    5 => "Saya mampu mengatur waktu belajar secara mandiri",
                    6 => "Saya konsisten belajar meskipun tanpa pengawasan langsung",
                    7 => "Saya merasa puas dengan pembelajaran daring yang dijalani",
                    8 => "Saya mudah memahami materi melalui e-learning"
                ];

                $likertLabels = [
                    1 => "STS",
                    2 => "TS",
                    3 => "N",
                    4 => "S",
                    5 => "SS"
                ];

                foreach ($pertanyaan as $no => $teks):
                ?>
                <div class="question-card" id="question-<?= $no ?>">
                    <div class="question-text">
                        <span class="question-number"><?= $no ?></span>
                        <span><?= $teks ?></span>
                    </div>
                    <div class="likert-scale">
                        <?php foreach ($likertLabels as $nilai => $label): ?>
                        <div class="likert-option">
                            <input type="radio" id="q<?= $no ?>_<?= $nilai ?>" 
                                   name="q<?= $no ?>" value="<?= $nilai ?>" required
                                   onchange="handleAnswer(<?= $no ?>)">
                            <label for="q<?= $no ?>_<?= $nilai ?>">
                                <span class="likert-value"><?= $nilai ?></span>
                                <span class="likert-label"><?= $label ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                    🚀 Kirim Jawaban
                </button>
            </div>
        </form>

        <footer class="footer">
            <p>© 2026 Kuesioner Motivasi Belajar E-Learning | Program Studi Informatika</p>
        </footer>
    </div>

    <script>
        const totalQuestions = 8;
        const totalFields = 4; // Data responden fields
        let answeredQuestions = new Set();
        let filledFields = new Set();

        // Track data responden fields
        document.querySelectorAll('.form-control').forEach((field, index) => {
            field.addEventListener('change', function() {
                if (this.value) {
                    filledFields.add(index);
                } else {
                    filledFields.delete(index);
                }
                updateProgress();
            });
        });

        // Handle question answered
        function handleAnswer(questionNo) {
            answeredQuestions.add(questionNo);
            document.getElementById('question-' + questionNo).classList.add('answered');
            updateProgress();
        }

        // Update progress bar
        function updateProgress() {
            const totalItems = totalFields + totalQuestions;
            const completedItems = filledFields.size + answeredQuestions.size;
            const percentage = (completedItems / totalItems) * 100;
            
            document.getElementById('progressFill').style.width = percentage + '%';
            
            // Update step dots
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');
            const step3 = document.getElementById('step3');
            
            if (filledFields.size === totalFields) {
                step1.classList.remove('active');
                step1.classList.add('completed');
                step1.textContent = '✓';
                step2.classList.add('active');
            }
            
            if (answeredQuestions.size === totalQuestions) {
                step2.classList.remove('active');
                step2.classList.add('completed');
                step2.textContent = '✓';
            }
            
            if (filledFields.size === totalFields && answeredQuestions.size === totalQuestions) {
                step3.classList.add('active');
            }
        }

        // Form validation with animation
        document.getElementById('kuesionerForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check data responden
            document.querySelectorAll('.form-control').forEach(field => {
                if (!field.value) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger)';
                    field.style.animation = 'shake 0.5s ease';
                    setTimeout(() => {
                        field.style.animation = '';
                    }, 500);
                }
            });
            
            // Check questions
            for (let i = 1; i <= totalQuestions; i++) {
                if (!document.querySelector(`input[name="q${i}"]:checked`)) {
                    isValid = false;
                    const card = document.getElementById('question-' + i);
                    card.style.borderColor = 'var(--danger)';
                    card.style.animation = 'shake 0.5s ease';
                    setTimeout(() => {
                        card.style.animation = '';
                        card.style.borderColor = '';
                    }, 500);
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first unanswered
                const firstUnanswered = document.querySelector('.question-card:not(.answered)') || 
                                        document.querySelector('.form-control:invalid');
                if (firstUnanswered) {
                    firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Add shake animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px); }
                75% { transform: translateX(10px); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
