// script.js

document.addEventListener('DOMContentLoaded', function() {
    // Target form artikel baru (Form yang dikirim ke proses.php)
    const formArtikel = document.querySelector('.artikel-form form');
    
    if (formArtikel) {
        formArtikel.addEventListener('submit', function(event) {
            
            // Ambil nilai dari input
            const judul = document.getElementById('judul').value.trim();
            const penulis = document.getElementById('penulis').value.trim();
            const isi = document.getElementById('isi').value.trim();
            
            // Ambil status radio button kategori
            const kategoriRadios = document.getElementsByName('kategori');
            let kategoriTerpilih = false;
            
            for (let i = 0; i < kategoriRadios.length; i++) {
                if (kategoriRadios[i].checked) {
                    kategoriTerpilih = true;
                    break;
                }
            }

            // --- VALIDASI SISI KLIEN (CLIENT-SIDE VALIDATION) ---
            let errorMessages = [];

            if (judul.length === 0) {
                errorMessages.push("Judul Artikel tidak boleh kosong.");
            }
            if (penulis.length === 0) {
                errorMessages.push("Nama Penulis tidak boleh kosong.");
            }
            if (!kategoriTerpilih) {
                errorMessages.push("Harap pilih salah satu Kategori.");
            }
            if (isi.length < 50) {
                errorMessages.push("Isi Artikel minimal 50 karakter.");
            }

            // Jika ada kesalahan, hentikan pengiriman form dan tampilkan pesan
            if (errorMessages.length > 0) {
                event.preventDefault(); // Mencegah pengiriman form
                
                // Tampilkan pesan error kepada pengguna
                alert('Peringatan Validasi (Client-side):\n\n' + errorMessages.join('\n'));

                // Anda bisa menambahkan DOM manipulation untuk menampilkan error lebih bagus
                // Contoh: Judul.style.border = '2px solid red';
            }
        });
    }


    // --- Interaktivitas (Contoh: Menghitung Karakter Isi Artikel) ---
    const textareaIsi = document.getElementById('isi');
    
    if (textareaIsi) {
        // Buat elemen untuk menampilkan hitungan karakter
        const charCountDisplay = document.createElement('p');
        charCountDisplay.style.fontSize = '0.85em';
        charCountDisplay.style.color = 'var(--text-soft)';
        charCountDisplay.style.marginTop = '-10px';
        charCountDisplay.style.marginBottom = '15px';
        charCountDisplay.id = 'char-counter';
        
        // Sisipkan elemen di bawah textarea
        textareaIsi.parentNode.insertBefore(charCountDisplay, textareaIsi.nextSibling);

        const updateCharCount = () => {
            const count = textareaIsi.value.length;
            charCountDisplay.textContent = `Jumlah Karakter: ${count} (Min. 2000 Karakter)`;
            
            // Beri warna peringatan jika kurang dari 50
            if (count < 50) {
                charCountDisplay.style.color = 'var(--secondary)';
            } else {
                charCountDisplay.style.color = 'var(--text-soft)';
            }
        };

        // Panggil saat input atau load
        textareaIsi.addEventListener('input', updateCharCount);
        updateCharCount(); // Panggil pertama kali untuk inisialisasi
    }
});

// Catatan: Validasi server-side di proses.php tetap wajib sebagai lapisan keamanan utama.