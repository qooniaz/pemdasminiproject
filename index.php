<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Blog Sederhana-Artikel</title>

    <link rel="stylesheet" href="style.css"> 
    
</head>
<body>
    <div class="container">
        <h1>Aplikasi Blog Sederhana</h1>

        <?php
        $file_artikel = 'artikel.txt';
        $data_artikel = '';

        if (file_exists($file_artikel) && filesize($file_artikel) > 0) {
            $handle = fopen($file_artikel, 'r'); // Mode 'r' (read)
            
            if ($handle) {
                $file_size = filesize($file_artikel);
                // Mencegah fread() jika ukuran file 0
                if ($file_size > 0) {
                    $data_artikel = fread($handle, $file_size);
                }
                fclose($handle);
            }
        }
        
        $artikel_array = array_filter(explode("---BATAS-ARTIKEL---\n", $data_artikel));
        $artikel_array_reverse = array_reverse($artikel_array); // Artikel terbaru di awal

        // Cek apakah user ingin melihat artikel lengkap (READ DETAIL)
        if (isset($_GET['id'])) {
            $id_artikel = $_GET['id'];
            $index_target = (int)str_replace('artikel_', '', $id_artikel);
            $target_data = isset($artikel_array_reverse[$index_target]) ? $artikel_array_reverse[$index_target] : null;

            if ($target_data) {
                // Component 5: Pemecahan Masalah - Pastikan array memiliki 4 elemen
                $parts = explode("|", $target_data);
                if (count($parts) < 4) {
                    // Jika data rusak, skip dan tampilkan pesan error
                    echo '<p class="error">Data artikel ini rusak. Tidak dapat ditampilkan.</p>';
                    echo '<a href="index.php">Kembali ke Daftar Artikel</a>';
                    goto end_detail_view;
                }
                
                list($judul, $penulis, $kategori, $isi) = $parts;
                
                // --- TAMPILAN ARTIKEL LENGKAP ---
                echo '<a href="index.php">&laquo; Kembali ke Daftar Artikel</a>';
                echo '<div class="artikel-detail">';
                echo '<h2>' . htmlspecialchars(trim($judul)) . '</h2>';
                echo '<p style="font-style: italic;">Oleh: ' . htmlspecialchars(trim($penulis)) . ' | Kategori: ' . htmlspecialchars(trim($kategori)) . '</p>';
                echo '<hr>';
                echo '<div style="line-height: 1.6;">' . nl2br(htmlspecialchars(trim($isi))) . '</div>'; 
                echo '</div>';
                
                // --- BAGIAN KOMENTAR ---
                echo '<h4>Komentar Artikel Ini:</h4>';
                
                $file_komentar = 'komentar.txt';
                $komentar_data = '';
                // Component 4: File Handling - READ Komentar
                if (file_exists($file_komentar) && filesize($file_komentar) > 0) {
                    $handle_komen = fopen($file_komentar, 'r');
                    if ($handle_komen) {
                        $komentar_data = fread($handle_komen, filesize($file_komentar));
                        fclose($handle_komen);
                    }
                }
                
                $komentar_lines = explode("\n", $komentar_data);

                foreach ($komentar_lines as $line) {
                    @list($komen_id, $komen_nama, $komen_isi) = explode("|", $line);
                    
                    if (trim($komen_id) == $id_artikel && !empty($komen_nama)) {
                        // Component 3: Manipulasi String (ucwords)
                        echo '<div style="border-left: 3px solid #007bff; padding-left: 10px; margin-bottom: 5px; background: #f8f9fa;">';
                        echo '<strong>' . ucwords(strtolower(trim($komen_nama))) . ':</strong> '; 
                        echo htmlspecialchars(trim($komen_isi));
                        echo '</div>';
                    }
                }
                
                // Form Komentar
                echo '<form action="proses_komentar.php" method="POST" style="margin-top: 10px;" class="komentar-form">';
                echo '<input type="hidden" name="artikel_id" value="' . $id_artikel . '">';
                echo '<input type="text" name="nama_komentar" placeholder="Nama Anda" required size="15">';
                echo '<input type="text" name="isi_komentar" placeholder="Tulis Komentar..." required size="30">';
                echo '<input type="submit" name="submit_komentar" value="Kirim">';
                echo '</form>';
                
            } else {
                echo '<p>Artikel tidak ditemukan.</p>';
                echo '<a href="index.php">Kembali ke Daftar Artikel</a>';
            }

        end_detail_view: // Label untuk goto
        } else {
            // --- Logika TAMPILAN DAFTAR ARTIKEL, FILTER, DAN FORM INPUT (DEFAULT) ---
        ?>

        <?php
        if (isset($_GET['status']) && $_GET['status'] == 'gagal') {
            echo '<p class="error">Data Gagal Disimpan! Judul, Penulis, dan Isi harus diisi.</p>';
        }
        if (isset($_GET['status']) && $_GET['status'] == 'sukses') {
            echo '<p style="color: green;">Artikel Berhasil Dipublikasikan!</p>';
        }
        ?>

        <div class="artikel-form">
            <h2>Tulis Artikel Baru</h2>
            <form id="artikelForm" action="proses.php" method="POST"> 
                <label for="judul">Judul Artikel:</label><br>
                <input type="text" id="judul" name="judul" required><br>

                <label for="penulis">Nama Penulis:</label><br>
                <input type="text" id="penulis" name="penulis" required><br>

                <label>Kategori:</label><br>
                <input type="radio" id="studi_kasus" name="kategori" value="Studi Kasus" required>
                <label for="studi_kasus">Studi Kasus</label>
                <input type="radio" id="tinjauan" name="kategori" value="Tinjauan" required>
                <label for="tinjauan">Tinjauan</label>
                <input type="radio" id="metodologi" name="kategori" value="Metodologi" required>
                <label for="metodologi">Metodologi</label>
                <input type="radio" id="ilmiah/akademik" name="kategori" value="Ilmiah/Akademik" required>
                <label for="ilmiah/akademik">Ilmiah/Akademik</label>
                <br>

                <label for="isi">Isi Artikel:</label><br>
                <textarea id="isi" name="isi" rows="8" required></textarea><br>
                
                <div id="validationMessage" class="error" style="display:none; margin-top:10px;"></div>

                <input type="submit" name="submit_artikel" value="Publikasikan">
            </form>
        </div>

        <div class="filter-search">
            <h3>Filter & Pencarian</h3>
            <form method="GET" action="index.php">
                <?php
                $filter_kategori = isset($_GET['filter']) ? trim($_GET['filter']) : '';
                $kata_kunci = isset($_GET['q']) ? htmlspecialchars(trim($_GET['q'])) : '';
                ?>
                <label for="q">Cari Artikel:</label>
                <input type="text" id="q" name="q" placeholder="Masukkan kata kunci..." value="<?php echo $kata_kunci; ?>">
                
                <label for="kategori_filter" style="margin-left: 20px;">Filter Kategori:</label>
                <select id="kategori_filter" name="filter">
                    <option value="">Semua Kategori</option>
                    <option value="Studi Kasus" <?php echo ($filter_kategori == 'Studi Kasus') ? 'selected' : ''; ?>>Studi Kasus</option>
                    <option value="Tinjauan" <?php echo ($filter_kategori == 'Tinjauan') ? 'selected' : ''; ?>>Tinjauan</option>
                    <option value="Metodologi" <?php echo ($filter_kategori == 'Metodologi') ? 'selected' : ''; ?>>Metodologi</option>
                    <option value="Ilmiah/Akademik" <?php echo ($filter_kategori == 'Ilmiah/Akademik') ? 'selected' : ''; ?>>Ilmiah/Akademik</option>
                </select>
                
                <input type="submit" value="Terapkan Filter">
                <a href="index.php" style="margin-left: 10px;">Reset</a>
            </form>
        </div>

        <div class="daftar-artikel">
            <h2>Daftar Artikel</h2>
            <?php
            $artikel_ditemukan = false;

            foreach ($artikel_array_reverse as $index => $artikel) {
                @list($judul, $penulis, $kategori, $isi) = explode("|", $artikel);
                
                if (!empty($judul)) {
                    $tampil = true;
                    
                    // Logika Filtering & Pencarian
                    if (!empty($filter_kategori) && trim($kategori) != $filter_kategori) {
                        $tampil = false;
                    }
                    if ($tampil && !empty($kata_kunci)) {
                        // Component 3: Manipulasi String (stripos)
                        if (stripos($judul, $kata_kunci) === false && stripos($isi, $kata_kunci) === false) {
                            $tampil = false;
                        }
                    }

                    if ($tampil) {
                        $artikel_ditemukan = true;
                        $artikel_id = 'artikel_' . $index; 
                        
                        echo '<div class="artikel">';
                        echo '<h3><a href="index.php?id=' . $artikel_id . '">' . htmlspecialchars(trim($judul)) . '</a></h3>'; 
                        echo '<p><strong>Penulis:</strong> ' . htmlspecialchars(trim($penulis)) . ' | Kategori: ' . htmlspecialchars(trim($kategori)) . '</p>';
                        
                        $isi_pendek = substr(trim($isi), 0, 150);
                        echo '<p>' . nl2br(htmlspecialchars($isi_pendek)) . '... <a href="index.php?id=' . $artikel_id . '">[Baca Selengkapnya]</a></p>'; 
                        echo '</div>';
                    }
                }
            }
            
            if (!$artikel_ditemukan) {
                echo '<p>Tidak ada artikel yang ditemukan sesuai filter/pencarian Anda.</p>';
            }
        ?>
        </div>

        <?php
        } 
        ?>
    </div>
    <script src="script.js" defer></script> 
</body>
</html>