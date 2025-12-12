document.addEventListener('DOMContentLoaded', function() {
    const formArtikel = document.querySelector('.artikel-form form');
    
    if (formArtikel) {
        formArtikel.addEventListener('submit', function(event) {
            
            const judul = document.getElementById('judul').value.trim();
            const penulis = document.getElementById('penulis').value.trim();
            const isi = document.getElementById('isi').value.trim();
            
            const kategoriRadios = document.getElementsByName('kategori');
            let kategoriTerpilih = false;
            
            for (let i = 0; i < kategoriRadios.length; i++) {
                if (kategoriRadios[i].checked) {
                    kategoriTerpilih = true;
                    break;
                }
            }

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

            if (errorMessages.length > 0) {
                event.preventDefault(); 
                alert('Peringatan Validasi (Client-side):\n\n' + errorMessages.join('\n'));
            }
        });
    }

    const textareaIsi = document.getElementById('isi');
    
    if (textareaIsi) {
        const charCountDisplay = document.createElement('p');
        charCountDisplay.style.fontSize = '0.85em';
        charCountDisplay.style.color = 'var(--text-soft)';
        charCountDisplay.style.marginTop = '-10px';
        charCountDisplay.style.marginBottom = '15px';
        charCountDisplay.id = 'char-counter';
        
        textareaIsi.parentNode.insertBefore(charCountDisplay, textareaIsi.nextSibling);

        const updateCharCount = () => {
            const count = textareaIsi.value.length;
            charCountDisplay.textContent = `Jumlah Karakter: ${count} (Min. 2000 Karakter)`;
            
            if (count < 50) {
                charCountDisplay.style.color = 'var(--secondary)';
            } else {
                charCountDisplay.style.color = 'var(--text-soft)';
            }
        };

        textareaIsi.addEventListener('input', updateCharCount);
        updateCharCount(); 
    }
});
