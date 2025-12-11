/**
 * File: script.js
 * Validasi sisi klien untuk form input artikel.
 */

document.addEventListener('DOMContentLoaded', function() {
    const artikelForm = document.getElementById('artikelForm');

    // Menambahkan event listener pada form untuk validasi
    if (artikelForm) {
        artikelForm.addEventListener('submit', function(event) {
            if (!validateForm()) {
                // Mencegah form disubmit jika validasi gagal
                event.preventDefault(); 
            }
        });
    }
});

/**
 * Fungsi validasi form.
 */
function validateForm() {
    const judul = document.getElementById('judul').value.trim();
    const penulis = document.getElementById('penulis').value.trim();
    const isi = document.getElementById('isi').value.trim();
    
    // Karena Anda kembali menggunakan Radio Button, kita cek status checked pada input radio
    const categories = document.querySelector('input[name="kategori"]:checked');
    
    const validationMessage = document.getElementById('validationMessage');

    let errors = [];

    // Validasi input wajib
    if (judul === "") { errors.push("Judul Artikel wajib diisi."); }
    if (penulis === "") { errors.push("Nama Penulis wajib diisi."); }
    
    // Validasi Radio Button (salah satu harus dipilih)
    if (!categories) {
        errors.push("Pilih salah satu Kategori (Radio Button).");
    }
    
    // Validasi Isi (minimal 50 karakter sebagai contoh)
    if (isi.length < 50) {
        errors.push(`Isi Artikel minimal 50 karakter (Saat ini: ${isi.length}).`);
    }

    if (errors.length > 0) {
        validationMessage.style.display = 'block';
        // Menggunakan class CSS lama (error)
        validationMessage.innerHTML = 
            '⚠️ <strong>Validasi Gagal:</strong><ul><li>' 
            + errors.join('</li><li>') 
            + '</li></ul>';
        
        return false; 
    } else {
        validationMessage.style.display = 'none';
        return true; 
    }
}