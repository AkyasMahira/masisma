<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Invoice - {{ $invoice->no_invoice }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --rs-maroon: #7c1316;
            --rs-maroon-subtle: #fcf0f1;
            --card-radius: 16px;
        }

        body { 
            background-color: #f8f9fa; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #333;
            padding: 20px 0 80px;
        }

        .main-container { max-width: 500px; margin: auto; padding: 0 15px; }

        .card-custom {
            background: #fff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border-top: 6px solid var(--rs-maroon);
            overflow: hidden;
        }

        /* Stepper */
        .stepper { display: flex; justify-content: space-between; margin-bottom: 25px; position: relative; padding: 0 10px; }
        .stepper::before { content: ""; position: absolute; top: 15px; left: 40px; right: 40px; height: 2px; background: #eee; z-index: 1; }
        .step { z-index: 2; text-align: center; width: 33.33%; }
        .step-icon { 
            width: 32px; height: 32px; border-radius: 50%; background: #fff; border: 2px solid #eee; 
            display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 14px; color: #ccc;
        }
        .step.active .step-icon { border-color: var(--rs-maroon); color: var(--rs-maroon); background: var(--rs-maroon-subtle); }
        .step.completed .step-icon { border-color: #28a745; background: #28a745; color: #fff; }
        .step-text { font-size: 9px; font-weight: 800; text-transform: uppercase; color: #adb5bd; }

        /* Preview PDF Button */
        .btn-preview {
            background-color: #fff;
            color: var(--rs-maroon);
            border: 2px solid var(--rs-maroon);
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        .btn-preview:hover { background-color: var(--rs-maroon); color: #fff; }

        /* Rincian Nota */
        .nota-box {
            background-color: #fcfcfc;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
        .nota-header { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 12px; font-weight: 800; font-size: 11px; color: #888; text-transform: uppercase; }
        .nota-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
        .nota-label { color: #666; }
        .nota-value { font-weight: 600; text-align: right; color: #1a1a1a; }

        .bank-info {
            background-color: var(--rs-maroon-subtle);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--rs-maroon);
            margin-bottom: 20px;
        }

        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 30px 15px;
            text-align: center;
            cursor: pointer;
            background: #fafafa;
            transition: 0.3s;
        }
        .upload-area:hover { border-color: var(--rs-maroon); background: #fff; }

        .btn-confirm {
            background-color: var(--rs-maroon);
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2);
        }

        .wa-float {
            position: fixed; bottom: 25px; right: 25px; background: #25d366;
            color: white; padding: 12px 20px; border-radius: 50px;
            text-decoration: none; font-weight: 700; font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; z-index: 1000;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="main-container">
        
        <div class="text-center mb-4">
            <img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" alt="Logo RSUD" style="height: 55px;">
            <p class="text-muted small fw-bold mt-2 mb-0">INVOICE {{ $invoice->no_invoice }}</p>
        </div>

        <div class="card-custom bg-white p-4">
            
            <div class="stepper">
                <div class="step {{ $invoice->status == 'Menunggu Pembayaran' ? 'active' : 'completed' }}">
                    <div class="step-icon"><i class="bi bi-wallet2"></i></div>
                    <div class="step-text">TAGIHAN</div>
                </div>
                <div class="step {{ $invoice->status == 'Proses' ? 'active' : ($invoice->status == 'Selesai' ? 'completed' : '') }}">
                    <div class="step-icon"><i class="bi bi-shield-check"></i></div>
                    <div class="step-text">VERIFIKASI</div>
                </div>
                <div class="step {{ $invoice->status == 'Selesai' ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-check2-all"></i></div>
                    <div class="step-text">SELESAI</div>
                </div>
            </div>

            <div class="text-center mb-4">
                <span class="badge bg-light text-muted border px-3 py-2 mb-2" style="border-radius: 50px;">NO: {{ $invoice->no_invoice }}</span>
                <h1 class="fw-bold mb-0" style="color: var(--rs-maroon);">Rp {{ number_format($invoice->jumlah_dibayarkan, 0, ',', '.') }}</h1>
            </div>

            <hr class="my-4 opacity-10">

            @if($invoice->status == 'Menunggu Pembayaran')
                
                <div class="mb-4">
                    <a href="{{ route('public.invoice.print', $invoice->payment_token) }}" target="_blank" class="btn-preview">
                        <i class="bi bi-file-earmark-pdf-fill h4 mb-0"></i>
                    UNDUH BILLING
                    </a>
                    <small class="text-muted d-block text-center mt-2" style="font-size: 11px;">
                        *Silakan periksa rincian tagihan pada dokumen PDF sebelum membayar.
                    </small>
                </div>

                <div class="nota-box">
                    <div class="nota-header">Rincian Kegiatan</div>
                    <div class="nota-row">
                        <span class="nota-label">Instansi</span>
                        <span class="nota-value">{{ $invoice->instansi }}</span>
                    </div>
                    <div class="nota-row">
                        <span class="nota-label">Program Studi</span>
                        <span class="nota-value">{{ $invoice->prodi }}</span>
                    </div>
                    <div class="nota-row">
                        <span class="nota-label">Unit / Ruangan</span>
                        <span class="nota-value text-truncate" style="max-width: 200px;">{{ $invoice->ruangan_ci }}</span>
                    </div>
                    
                    <div class="border-top my-2"></div>

                    {{-- Loop Semua Item Kegiatan --}}
                    @foreach($invoice->items as $item)
                    <div class="nota-row">
                        <span class="nota-label text-muted">{{ $item->deskripsi }}</span>
                        <span class="nota-value">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach

                    @if($invoice->biaya_konsumsi > 0)
                    <div class="nota-row">
                        <span class="nota-label text-muted">Biaya Konsumsi/Lainnya</span>
                        <span class="nota-value">Rp {{ number_format($invoice->biaya_konsumsi, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="bank-info text-center">
                    <p class="text-muted small mb-1 fw-bold">TRANSFER KE (BANK BNI):</p>
                    <h2 class="fw-bold mb-1" style="letter-spacing: 2px;">881669217</h2>
                    <p class="small mb-0">a.n <strong>RSUD Simpang Lima Gumul Kediri</strong></p>
                </div>

                {{-- PENGECEKAN JATUH TEMPO --}}
                @php
                    $isExpired = \Carbon\Carbon::parse($invoice->tgl_jatuh_tempo)->endOfDay()->isPast();
                @endphp

                @if($isExpired)
                    <div class="alert alert-danger text-center mt-4" style="border-radius: 12px; border: none; background-color: #fdf2f2;">
                        <i class="bi bi-x-circle-fill text-danger d-block mb-2" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold text-danger">Waktu Pembayaran Habis</h5>
                        <p class="mb-0 small text-muted">
                            Batas akhir pembayaran untuk invoice ini adalah <br>
                            <strong>{{ \Carbon\Carbon::parse($invoice->tgl_jatuh_tempo)->translatedFormat('d F Y') }}</strong>. <br>
                            Silakan hubungi admin SINDIKAT.
                        </p>
                    </div>
                @else
                    <form action="{{ route('public.invoice.submit', $invoice->payment_token) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">ASAL BANK ANDA</label>
                            <select name="bank" class="form-select border-0 bg-light py-2" required>
                                <option value="">-- Pilih Bank --</option>
                                <option value="BANK JATIM">BANK JATIM</option>
                                <option value="BRI">BRI</option>
                                <option value="BNI">BNI</option>
                                <option value="MANDIRI">MANDIRI</option>
                                <option value="BCA">BCA</option>
                                <option value="LAINNYA">LAINNYA</option>
                            </select>
                        </div>

                        <div class="upload-area mb-4" onclick="document.getElementById('bukti_file').click()">
                            <i class="bi bi-camera-fill text-muted h1"></i>
                            <p class="small text-muted mb-0 d-block" id="file_label">Klik untuk Unggah Bukti Bayar</p>
                            <small class="text-muted" style="font-size: 10px;">Format: JPG/PNG, <strong>Maks: 1MB</strong></small>
                            <input type="file" name="bukti_bayar" id="bukti_file" class="d-none" required accept="image/*" onchange="updateLabel(this)">
                        </div>

                        <button type="submit" class="btn-confirm">KONFIRMASI PEMBAYARAN</button>
                    </form>
                @endif

            @elseif($invoice->status == 'Proses')
                <div class="text-center py-4">
                    <div class="spinner-grow text-danger mb-3" style="width: 3rem; height: 3rem; color: var(--rs-maroon);"></div>
                    <h5 class="fw-bold">Sedang Diverifikasi</h5>
                    <p class="text-muted small px-3">Terima kasih. Bukti bayar telah kami terima. Admin sedang mencocokkan dengan mutasi bank kami.</p>
                    
                    <div class="mt-4 p-2 border rounded-3 bg-light">
                        <small class="text-muted d-block mb-2 fw-bold" style="font-size: 10px;">BUKTI YANG DIUNGGAH:</small>
                        <img src="{{ asset('storage/' . $invoice->bukti_bayar) }}" class="img-fluid rounded border shadow-sm" style="max-height: 250px;">
                    </div>
                </div>

            @elseif($invoice->status == 'Selesai')
                <div class="text-center py-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-check-lg display-4"></i>
                    </div>
                    <h4 class="fw-bold text-success">Pembayaran Lunas</h4>
                    <p class="text-muted small px-2">Pembayaran telah diverifikasi. Silakan unduh Invoice resmi Anda sebagai bukti bayar yang sah.</p>
                    
                    <a href="{{ route('public.invoice.print', $invoice->payment_token) }}" target="_blank" class="btn btn-dark w-100 py-3 mt-3 fw-bold" style="border-radius: 12px;">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i> DOWNLOAD INVOICE RESMI
                    </a>
                </div>
            @endif

        </div>

        <p class="text-center mt-4 small text-muted">
            &copy; {{ date('Y') }} SINDIKAT - RSUD Simpang Lima Gumul Kediri
        </p>
    </div>
</div>

<a href="https://wa.me/6282245415977?text=Halo%20Admin,%20saya%20konfirmasi%20invoice%20{{ $invoice->no_invoice }}" class="wa-float" target="_blank">
    <i class="bi bi-whatsapp"></i>
    <span>Bantuan</span>
</a>

<script>
    function updateLabel(input) {
        const label = document.getElementById('file_label');
        
        // 1. Cek apakah ada file yang dipilih
        if (!input.files || input.files.length === 0) {
            label.innerHTML = 'Klik untuk Unggah Bukti Bayar';
            return;
        }

        const file = input.files[0];
        const limitSize = 1024 * 1024; // Tepat 1MB dalam hitungan Bytes

        // 2. Validasi Ukuran (Pakai Bytes agar lebih presisi)
        if (file.size > limitSize) {
            const ukuranFileMB = (file.size / (1024 * 1024)).toFixed(2);
            
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: `Ukuran maksimal 1MB. File Anda: ${ukuranFileMB} MB. Silakan kompres foto atau gunakan file lain.`,
                confirmButtonColor: '#7c1316',
                confirmButtonText: 'Siap, saya ganti'
            });

            // Reset input supaya tidak error saat submit
            input.value = ""; 
            label.innerHTML = 'Klik untuk Unggah Bukti Bayar';
            return;
        }

        // 3. Validasi Tipe (Opsional tapi penting agar tidak upload PDF/Exe)
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'warning',
                title: 'Format Salah',
                text: 'Hanya menerima format foto (JPG/PNG).',
                confirmButtonColor: '#7c1316'
            });
            input.value = "";
            label.innerHTML = 'Klik untuk Unggah Bukti Bayar';
            return;
        }

        // 4. Jika LOLOS semua validasi
        label.innerHTML = `<span class="text-success fw-bold">
            <i class="bi bi-check-circle-fill me-1"></i> ${file.name} 
            (${ (file.size / 1024).toFixed(0) } KB)
        </span>`;
    }

    // Tampilkan notifikasi jika sukses/error dari backend
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            confirmButtonColor: '#7c1316'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            confirmButtonColor: '#7c1316'
        });
    @endif
</script>

</body>
</html>