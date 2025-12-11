<?php
// ROLE 3: BACKEND DEVELOPER (Handler Artikel)

if (isset($_POST['submit_artikel'])) {
    $judul_input = $_POST['judul'];
    $penulis_input = $_POST['penulis'];
    $kategori_input = $_POST['kategori'];
    $isi_input = $_POST['isi'];

    // Component 2: Validasi Form dengan PHP
    $judul_valid = filter_var($judul_input, FILTER_SANITIZE_STRING);
    $penulis_valid = filter_var($penulis_input, FILTER_SANITIZE_STRING);
    $isi_trim = trim($isi_input);
    
    // Pemecahan Masalah & Debugging: Jika ada input wajib kosong
    if (empty($judul_valid) || empty($penulis_valid) || empty($isi_trim)) {
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

    // Component 4: File Handling (Menyimpan data)
    $file_artikel = 'artikel.txt';
    $simpan = file_put_contents($file_artikel, $data_artikel_baru, FILE_APPEND | LOCK_EX);

    if ($simpan !== false) {
        header('Location: index.php?status=sukses');
    } else {
        header('Location: index.php?status=gagal_simpan');
    }
    
    exit();

} else {
    header('Location: index.php');
    exit();
}
?>