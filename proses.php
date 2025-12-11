<?php
// ROLE 3: BACKEND DEVELOPER (Handler Artikel)

// --- FUNGSI LOGGING AKTIVITAS (FILE HANDLING FOPEN/FWRITE) ---
function logActivity($activity) {
    $log_file = 'aktivitas.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[" . $timestamp . "] " . $activity . "\n";
    
    // Component 4: File Handling - WRITE Log
    $handle = fopen($log_file, 'a'); // Mode 'a' untuk append
    
    if ($handle) {
        fwrite($handle, $log_entry);
        fclose($handle);
        return true;
    }
    return false;
}
// --- AKHIR FUNGSI LOGGING ---


if (isset($_POST['submit_artikel'])) {
    $judul_input = $_POST['judul'];
    $penulis_input = $_POST['penulis'];
    $kategori_input = $_POST['kategori'];
    $isi_input = $_POST['isi'];

    // Component 2: Validasi Form dengan PHP filter_var()
    $judul_valid = filter_var($judul_input, FILTER_SANITIZE_STRING);
    $penulis_valid = filter_var($penulis_input, FILTER_SANITIZE_STRING);
    $isi_trim = trim($isi_input);
    
    // Component 5: Pemecahan Masalah - Validasi utama
    if (empty($judul_valid) || empty($penulis_valid) || empty($isi_trim) || empty($kategori_input)) {
        logActivity("GAGAL - Validasi input form kosong.");
        header('Location: index.php?status=gagal');
        exit();
    }

    // Component 3: Manipulasi String (strtoupper)
    $judul_upper = strtoupper($judul_valid);
    
    // Menggabungkan data dengan delimiter "|"
    $data_artikel_baru = 
        $judul_upper . "|" . 
        $penulis_valid . "|" . 
        $kategori_input . "|" . 
        $isi_trim . "\n" .
        "---BATAS-ARTIKEL---\n";

    // Component 4: File Handling - WRITE Artikel (fopen, fwrite, fclose)
    $file_artikel = 'artikel.txt';
    $handle = fopen($file_artikel, 'a'); // Mode 'a' (append)

    $simpan_sukses = false;

    if ($handle) {
        // Mengunci file untuk mencegah data race (pemecahan masalah)
        if (flock($handle, LOCK_EX)) { 
            fwrite($handle, $data_artikel_baru);
            flock($handle, LOCK_UN); 
            $simpan_sukses = true;
        }
        fclose($handle);
    }

    if ($simpan_sukses) {
        logActivity("Artikel baru sukses dibuat: " . $judul_input . " oleh " . $penulis_valid);
        header('Location: index.php?status=sukses');
    } else {
        logActivity("GAGAL - Artikel gagal disimpan ke file (File Permission?).");
        header('Location: index.php?status=gagal_simpan');
    }
    
    exit();

} else {
    header('Location: index.php');
    exit();
}
?>