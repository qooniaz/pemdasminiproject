<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Blog Sederhana - CRUD & File Handling</title>

    <link rel="stylesheet" href="style.css"> 
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Roboto:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="header-wrapper">
        <div class="container-content">
            <h1>Aplikasi Blog Sederhana</h1>
        </div>
    </div>
    
    <div class="container-content"> 

        <?php
        // --- LOGIKA UTAMA PHP UNTUK MEMBACA DATA ARTIKEL ---
        // (Logika PHP di sini tetap sama seperti kode asli Anda)
        $file_artikel = 'artikel.txt';
        $data_artikel = file_exists($file_artikel) ? file_get_contents($file_artikel) : '';
        $artikel_array = array_filter(explode("---BATAS-ARTIKEL---\n", $data_artikel));
        $artikel_array_reverse = array_reverse($artikel_array); // Artikel terbaru di awal

        // Cek apakah user ingin melihat artikel lengkap (READ DETAIL)
        if (isset($_GET['id'])) {
            // (Tampilan detail artikel di sini tetap sama)
            $id_artikel = $_GET['id'];
            $index_target = (int)str_replace('artikel_', '', $id_artikel);
            $target_data = isset($artikel_array_reverse[$index_target]) ? $artikel_array_reverse[$index_target] : null;

            if ($target_data) {
                list($judul, $penulis, $kategori, $isi) = explode("|", $target_data);
                
                // --- TAMPILAN ARTIKEL LENGKAP ---
                echo '<a href="index.php" style="font-weight: 600; display: inline-block; margin-bottom: 20px;">&laquo; Kembali ke Daftar Artikel</a>';
                echo '<div class="artikel-detail">';
                // Komponen Wajib 3: Manipulasi String (strtoupper untuk Judul)
                echo '<h2>' . strtoupper(htmlspecialchars(trim($judul))) . '</h2>'; 
                echo '<p style="font-style: italic; color: var(--soft-text); font-size: 0.9em;">Oleh: ' . htmlspecialchars(trim($penulis)) . ' | Kategori: ' . htmlspecialchars(trim($kategori)) . '</p>';
                echo '<hr style="border: none; border-top: 1px solid #dcdfe6; margin: 15px 0;">';
                echo '<div style="line-height: 1.8; color: var(--text);">' . nl2br(htmlspecialchars(trim($isi))) . '</div>'; 
                echo '</div>';
                
                // --- BAGIAN KOMENTAR ---
                echo '<h4>Komentar Artikel Ini:</h4>';
                
                // (Logika menampilkan komentar tetap sama)
                $file_komentar = 'komentar.txt';
                if (file_exists($file_komentar)) {
                    $komentar_data = file_get_contents($file_komentar);
                    $komentar_lines = array_reverse(array_filter(explode("\n", $komentar_data))); // Komentar terbaru di atas

                    $komentar_ditemukan = false;
                    foreach ($komentar_lines as $line) {
                        @list($komen_id, $komen_nama, $komen_isi) = explode("|", $line);
                        
                        if (trim($komen_id) == $id_artikel && !empty($komen_nama)) {
                            $komentar_ditemukan = true;
                            echo '<div style="border-left: 3px solid #007bff; padding-left: 10px; margin-bottom: 5px; background: #f8f9fa;">';
                            // Komponen Wajib 3: Manipulasi String (ucwords, strtolower)
                            echo '<strong>' . ucwords(strtolower(trim($komen_nama))) . ':</strong> '; 
                            echo htmlspecialchars(trim($komen_isi));
                            echo '</div>';
                        }
                    }
                    if (!$komentar_ditemukan) {
                        echo '<p style="color: var(--soft-text);">Belum ada komentar untuk artikel ini.</p>';
                    }
                }
                
                // Form Komentar (Component 1: Form Input HTML)
                echo '<h4>Tulis Komentar</h4>';
                echo '<form action="proses_komentar.php" method="POST" class="komentar-form">';
                echo '<input type="hidden" name="artikel_id" value="' . $id_artikel . '">';
                echo '<input type="text" name="nama_komentar" placeholder="Nama Anda" required>';
                echo '<input type="text" name="isi_komentar" placeholder="Tulis Komentar..." required>';
                echo '<input type="submit" name="submit_komentar" value="Kirim">';
                echo '</form>';
                
            } else {
                echo '<p class="error">Artikel tidak ditemukan.</p>';
                echo '<a href="index.php">Kembali ke Daftar Artikel</a>';
            }

        } else {
            // --- Logika TAMPILAN DAFTAR ARTIKEL, FILTER, DAN FORM INPUT (DEFAULT) ---
        ?>

        <?php
        if (isset($_GET['status']) && $_GET['status'] == 'gagal') {
            echo '<p class="error">Data Gagal Disimpan! Judul, Penulis, dan Isi harus diisi.</p>';
        }
        if (isset($_GET['status']) && $_GET['status'] == 'sukses') {
            echo '<p class="success">Artikel Berhasil Dipublikasikan!</p>';
        }
        ?>
        
        <div class="main-layout">
            
            <div class="articles-column">
                
                <div class="filter-search section-card">
                    <h3>Filter & Pencarian</h3>
                    <form method="GET" action="index.php">
                        <?php
                        $filter_kategori = isset($_GET['filter']) ? trim($_GET['filter']) : '';
                        $kata_kunci = isset($_GET['q']) ? htmlspecialchars(trim($_GET['q'])) : '';
                        ?>
                        
                        <input type="text" id="q" name="q" placeholder="Cari Judul/Isi..." value="<?php echo $kata_kunci; ?>">
                        
                        <select id="kategori_filter" name="filter">
                            <option value="">Semua Kategori</option>
                            <option value="Studi Kasus" <?php echo ($filter_kategori == 'Studi Kasus') ? 'selected' : ''; ?>>Studi Kasus</option>
                            <option value="Tinjauan" <?php echo ($filter_kategori == 'Tinjauan') ? 'selected' : ''; ?>>Tinjauan</option>
                            <option value="Metodologi" <?php echo ($filter_kategori == 'Metodologi') ? 'selected' : ''; ?>>Metodologi</option>
                            <option value="Ilmiah/Akademik" <?php echo ($filter_kategori == 'Ilmiah/Akademik') ? 'selected' : ''; ?>>Ilmiah/Akademik</option>
                        </select>
                        
                        <input type="submit" value="Terapkan">
                        <a href="index.php" style="text-decoration: underline;">Reset</a>
                    </form>
                </div>

                <div class="daftar-artikel">
                    <h2>Daftar Artikel Terbaru</h2>
                    <?php
                    $artikel_ditemukan = false;

                    foreach ($artikel_array_reverse as $index => $artikel) {
                        @list($judul, $penulis, $kategori, $isi) = explode("|", $artikel);
                        
                        if (!empty($judul)) {
                            $tampil = true;
                            
                            // Logika Filtering & Pencarian (SINKRON)
                            if (!empty($filter_kategori) && trim($kategori) != $filter_kategori) {
                                $tampil = false;
                            }
                            if ($tampil && !empty($kata_kunci)) {
                                // Component 3: Manipulasi String (stripos) untuk Pencarian Case-Insensitive
                                if (stripos($judul, $kata_kunci) === false && stripos($isi, $kata_kunci) === false) {
                                    $tampil = false;
                                }
                            }

                            if ($tampil) {
                                $artikel_ditemukan = true;
                                $artikel_id = 'artikel_' . $index; 
                                
                                echo '<div class="artikel">';
                                echo '<h3><a href="index.php?id=' . $artikel_id . '">' . htmlspecialchars(trim($judul)) . '</a></h3>'; 
                                echo '<p><strong>Penulis:</strong> ' . htmlspecialchars(trim($penulis)) . ' | <strong>Kategori:</strong> ' . htmlspecialchars(trim($kategori)) . '</p>';
                                
                                // Component 3: Manipulasi String (substr) untuk ringkasan
                                $isi_pendek = substr(trim($isi), 0, 200);
                                echo '<p>' . nl2br(htmlspecialchars($isi_pendek)) . '... <a href="index.php?id=' . $artikel_id . '">[Baca Selengkapnya]</a></p>'; 
                                echo '</div>';
                            }
                        }
                    }
                    
                    if (!$artikel_ditemukan) {
                        echo '<p class="section-card" style="color: var(--soft-text);">Tidak ada artikel yang ditemukan sesuai filter/pencarian Anda.</p>';
                    }
                    ?>
                </div>

            </div>
            
            <div class="form-column">
                <div class="artikel-form section-card">
                    <h2>Tulis Artikel Baru</h2>
                    <form action="proses.php" method="POST">
                        <label for="judul">Judul Artikel:</label>
                        <input type="text" id="judul" name="judul" required>

                        <label for="penulis">Nama Penulis:</label>
                        <input type="text" id="penulis" name="penulis" required>

                        <label>Kategori:</label>
                       
                        <div class="radio-group-category"> 
                            <input type="radio" id="studi_kasus" name="kategori" value="Studi Kasus" required>
                            <label for="studi_kasus" style="display: inline-block; font-weight: normal;">Studi Kasus</label>
                            
                            <input type="radio" id="tinjauan" name="kategori" value="Tinjauan">
                            <label for="tinjauan" style="display: inline-block; font-weight: normal;">Tinjauan</label>
                            
                            <input type="radio" id="metodologi" name="kategori" value="Metodologi">
                            <label for="metodologi" style="display: inline-block; font-weight: normal;">Metodologi</label>
                            
                            <input type="radio" id="ilmiah/akademik" name="kategori" value="Ilmiah/Akademik">
                            <label for="ilmiah/akademik" style="display: inline-block; font-weight: normal;">Ilmiah/Akademik</label>
                        </div>
                        

                        <label for="isi">Isi Artikel:</label>
                        <textarea id="isi" name="isi" rows="12" required></textarea>

                        <input type="submit" name="submit_artikel" value="Publikasikan">
                    </form>
                </div>
            </div>
            
        </div>
        
        <?php
        } 
        ?>
    </div>
    
    <div class="footer-wrapper" style="margin-top: 40px;">
        <div class="container-content" style="padding: 15px 40px; text-align: center;">
            <p style="color: var(--soft-text); font-size: 0.9em; margin: 0;">&copy; <?= date('Y') ?> Aplikasi Blog Sederhana. Implementasi File Handling PHP.</p>
        </div>
    </div>
</body>
</html>