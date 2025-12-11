<?php
// ROLE 3: BACKEND DEVELOPER (Handler Komentar)

if (isset($_POST['submit_komentar'])) {
    $artikel_id = $_POST['artikel_id'];
    $nama_komentar_input = $_POST['nama_komentar'];
    $isi_komentar_input = $_POST['isi_komentar'];

    // Component 2: Validasi Form dengan PHP
    $nama_valid = filter_var($nama_komentar_input, FILTER_SANITIZE_STRING);
    $isi_valid = filter_var($isi_komentar_input, FILTER_SANITIZE_STRING);
    
    if (empty($nama_valid) || empty($isi_valid) || empty($artikel_id)) {
        // Redirect kembali ke halaman detail artikel
        header('Location: index.php?id=' . $artikel_id . '&komen_status=gagal_validasi');
        exit();
    }

    // Component 3: Manipulasi String (strtolower)
    $nama_lowercase = strtolower($nama_valid);
    
    // Menggabungkan data komentar
    $data_komentar_baru = 
        trim($artikel_id) . "|" . 
        trim($nama_lowercase) . "|" . 
        trim($isi_valid) . "\n";

    // Component 4: File Handling (Menyimpan komentar)
    $file_komentar = 'komentar.txt';
    $simpan = file_put_contents($file_komentar, $data_komentar_baru, FILE_APPEND | LOCK_EX);

    // Redirect kembali ke halaman detail artikel
    header('Location: index.php?id=' . $artikel_id . '#komentar-section');
    exit();

} else {
    header('Location: index.php');
    exit();
}
?>
