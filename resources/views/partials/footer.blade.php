<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .footer-minimal {
        background-color: rgba(255,255,255,0.7);
        border-top: 1px solid rgba(0,0,0,0.06);
        padding: 1rem 0;
        color: #6c757d;
        font-size: 0.85rem;
        font-family: sans-serif;
        margin-top: auto;
    }

    .footer-content {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .footer-link {
        color: #6c757d;
        text-decoration: none;
    }

    .footer-link:hover {
        color: #7c1316; /* Maroon Hover */
    }

    .footer-dot {
        width: 4px;
        height: 4px;
        background-color: #999;
        border-radius: 50%;
    }

    /* ==== HELPDESK ==== */
    .helpdesk-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(37, 211, 102, 0.08);
        padding: 6px 12px;
        border-radius: 999px;
        border: 1px solid rgba(37, 211, 102, 0.25);
    }

    .helpdesk-label {
        font-size: 0.75rem;
        color: #6c757d;
        white-space: nowrap;
    }

    .wa-minimal {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #25D366;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .wa-minimal svg {
        fill: currentColor;
    }

    /* Custom Class untuk Swal agar font resmi */
    .swal-resmi {
        font-family: 'Times New Roman', Times, serif !important;
    }
</style>

@php
    use Carbon\Carbon;
    use Illuminate\Support\Collection;

    $mhs = $mahasiswa ?? null;

    if ($mhs instanceof Collection) {
        $mhs = $mhs->first();
    }

    $nama   = $mhs->nm_mahasiswa ?? '-';
    $kampus = $mhs->univ_asal ?? 'Belum diisi';
    $prodi  = $mhs->prodi ?? '-';

    $pesan = urlencode(
        "Halo Admin Sindikat,\n" .
        "Saya {$nama}\n" .
        "Prodi {$prodi}\n" .
        "Asal {$kampus}\n" .
        "\n" .
        "Saya ingin bertanya terkait:"
    );
@endphp

<footer class="footer-minimal">
    <div class="container">
        <div class="footer-content">

            <div>
                &copy; {{ date('Y') }}
                <a href="" target="_blank" class="footer-link fw-bold">
                    Sindikat
                </a>
                <span class="mx-1">·</span> All Rights Reserved
            </div>

            <div class="footer-dot d-none d-md-block"></div>

            <div class="helpdesk-wrap">
                <span class="helpdesk-label">Butuh bantuan?</span>

                <a href="javascript:void(0)" 
                   onclick="btnChatAdmin()"
                   class="wa-minimal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16">
                        <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326z"/>
                    </svg>
                    Chat Admin
                </a>
            </div>

        </div>
    </div>
</footer>

<script>
function btnChatAdmin() {
    Swal.fire({
        title: 'PERHATIAN!',
        html: `
            <div class="swal-resmi" style="text-align: left; font-size: 1rem; line-height: 1.5;">
                <p>Sebelum menghubungi Admin, pastikan Anda memahami aturan pengaduan berikut:</p>
                <ol>
                    <li><b>Wajib Screenshot:</b> Aduan tanpa bukti screenshoot valid <b>TIDAK AKAN DIBALAS</b>.</li>
                    <li><b>Masalah Absensi:</b> Bukti harus menunjukkan jam HP saat kejadian (Contoh: Masuk > 07:15 atau Pulang > 21:15). Jika tidak ada bukti jam, aduan diabaikan.</li>
                    <li><b>Masalah Jadwal:</b> Jika Anda belum mengisi <b>Periode & Shift</b> pada sistem, aduan tidak akan dilayani.</li>
                </ol>
                <p style="color: #7c1316; font-weight: bold; text-align: center; margin-top: 15px;">
                    Hanya aduan dengan bukti valid yang akan diproses.
                </p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c1316', // Maroon RSUD SLG
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Saya Mengerti, Chat Sekarang',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            container: 'swal-resmi'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.open("https://wa.me/6282245415977?text={{ $pesan }}", "_blank");
        }
    });
}
</script>