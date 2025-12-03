<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show flash messages using SweetAlert if present
    @if(session('success'))
        Swal.fire({icon: 'success', title: 'Sukses', text: @json(session('success'))});
    @endif
    @if(session('error'))
        Swal.fire({icon: 'error', title: 'Gagal', text: @json(session('error'))});
    @endif

    // If admin created mahasiswa credentials exist, show them in a modal
    @if(session('created_mahasiswa_credentials'))
        const creds = @json(session('created_mahasiswa_credentials'));
        Swal.fire({
            title: 'Akun Mahasiswa Dibuat',
            html: `<p><strong>Nama:</strong> ${creds.name}</p><p><strong>Email:</strong> ${creds.email}</p><p><strong>Password:</strong> ${creds.password}</p>`,
            icon: 'info'
        });
    @endif

    // Intercept forms that perform DELETE (have _method=DELETE) and ask confirmation
    document.querySelectorAll('form').forEach(function(form) {
        try {
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput && methodInput.value.toUpperCase() === 'DELETE' && !form.dataset.noConfirm) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: 'Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        } catch (err) {
            // ignore
        }
    });

    // Add generic confirmation for elements with data-confirm attribute
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            const message = el.getAttribute('data-confirm') || 'Anda yakin?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});
</script>
