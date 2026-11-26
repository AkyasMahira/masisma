<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. FLASH MESSAGE (Notifikasi Sukses/Gagal dari Controller) ---
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: @json(session('success')),
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: @json(session('error')),
        });
    @endif

    // Error validasi: banyak halaman tidak menampilkan $errors -> tampilkan toast kecil (tidak menghalangi)
    @if($errors->any())
        Swal.fire({
            toast: true, position: 'top-end', icon: 'warning',
            title: 'Periksa kembali isian',
            html: @json('<div style="text-align:left;font-size:.85rem">' . collect($errors->all())->unique()->take(5)->map(fn ($e) => '• ' . e($e))->implode('<br>') . '</div>'),
            showConfirmButton: false, timer: 7000, timerProgressBar: true,
        });
    @endif

    // Menampilkan Akun Mahasiswa yang baru dibuat (Khusus Admin)
    @if(session('created_mahasiswa_credentials'))
        const creds = @json(session('created_mahasiswa_credentials'));
        Swal.fire({
            title: 'Akun Mahasiswa Terbuat',
            html: `
                <div style="text-align: left;">
                    <p>Silakan simpan data ini:</p>
                    <p><strong>Nama:</strong> ${creds.name}</p>
                    <p><strong>Email:</strong> ${creds.email}</p>
                    <p><strong>Password:</strong> ${creds.password}</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Tutup & Salin',
        });
    @endif


    // --- 2. KONFIRMASI TOMBOL DELETE (Form Method DELETE) ---
    // Mencari semua form yang punya input _method=DELETE
    document.querySelectorAll('form').forEach(function(form) {
        // Cek apakah ini form delete
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput && methodInput.value.toUpperCase() === 'DELETE' && !form.dataset.noConfirm) {
            
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop submit asli
                
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626', // Warna merah untuk hapus
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Lanjutkan submit jika user klik Ya
                    }
                });
            });
        }
    });


    // --- 3. KONFIRMASI LINK/TOMBOL UMUM (Non-Delete) ---
    // Gunakan class="btn-confirm" atau atribut data-confirm-text="Pesan..." pada link <a> atau button
    document.querySelectorAll('.btn-confirm, [data-confirm-text]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault(); // Tahan link/button
            
            // Ambil pesan custom atau default
            const message = el.getAttribute('data-confirm-text') || 'Lanjutkan tindakan ini?';
            const href = el.getAttribute('href'); // Jika itu link <a>

            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c1316',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika elemen adalah Link <a>, pindah halaman
                    if (href) {
                        window.location.href = href;
                    } 
                    // Jika elemen adalah tombol submit dalam form biasa (bukan delete)
                    else if (el.type === 'submit' || el.closest('form')) {
                        el.closest('form').submit();
                    }
                }
            });
        });
    });

});
</script>