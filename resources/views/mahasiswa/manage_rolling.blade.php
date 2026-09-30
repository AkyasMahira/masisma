@extends('layouts.app')

@section('content')

{{-- 1. LIBRARY PENDUKUNG --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --primary: #7c1316;
        --bg-soft: #f8fafc;
        --border-color: #e2e8f0;
    }

    /* Card Styling */
    .master-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid var(--border-color);
        margin-bottom: 25px;
        overflow: visible; 
    }

    .card-header-modern {
        padding: 20px 25px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border-radius: 16px 16px 0 0;
    }

    /* Form Elements */
    .form-label-sm {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 5px;
        display: block;
    }

    .form-control-modern {
        background: var(--bg-soft);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px;
        font-size: 0.9rem;
        min-height: 44px; /* Nyaman untuk di-tap di HP */
    }
    
    /* Choices.js Override */
    .choices__inner {
        background-color: var(--bg-soft) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 8px !important;
        min-height: 44px;
    }
    .choices__list--dropdown { z-index: 9999; }

    /* Preview Section */
    .preview-container {
        background: #f1f5f9;
        border-top: 1px dashed #cbd5e1;
        padding: 20px 25px;
        border-radius: 0 0 16px 16px;
        display: none; 
    }
    .preview-active {
        display: block;
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Grid Responsif Harian */
    .daily-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .daily-card {
        background: white;
        padding: 12px 15px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .date-badge { font-size: 0.85rem; font-weight: 700; color: #334155; }
    .day-badge { font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; }

    /* Tombol Hapus */
    .btn-delete-period {
        color: #ef4444;
        background: #fef2f2;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .btn-delete-period:hover { background: #fee2e2; transform: scale(1.1); }

    /* ==========================================
       MEDIA QUERIES KHUSUS RESPONSIVE (MOBILE & TABLET)
       ========================================== */
    @media (max-width: 768px) {
        .card-header-modern { padding: 15px; }
        .preview-container { padding: 15px; }
        .master-card { margin-bottom: 15px; }
        
        /* Grid harian menjadi 2 kolom di tablet/HP Landscape */
        .daily-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        /* Tombol aksi jadi memanjang (full width) */
        .action-buttons {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px !important;
        }
        .action-buttons .btn { width: 100%; justify-content: center; }
    }

    @media (max-width: 480px) {
        /* Grid harian menjadi 1 kolom penuh di HP Layar Kecil */
        .daily-grid { grid-template-columns: 1fr; }
    }
</style>

<form action="{{ route('mahasiswa.rolling.update', $mahasiswa->id) }}" method="POST" id="rollingForm">
    @csrf
    @method('PUT')

    <div class="container-fluid py-3 py-md-4">
        
        {{-- HEADER (Dibuat Responsive Flexbox) --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger fw-bold fs-4 flex-shrink-0" 
                     style="width: 55px; height: 55px; border: 2px solid #7c1316;">
                    {{ substr($mahasiswa->nm_mahasiswa, 0, 1) }}
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="font-size: clamp(1.1rem, 2.5vw, 1.5rem);">{{ $mahasiswa->nm_mahasiswa }}</h4>
                    <span class="badge bg-light text-secondary border mt-1">Manajemen Rolling & Shift</span>
                </div>
            </div>
            
            {{-- Action Buttons --}}
            <div class="action-buttons d-flex gap-2">
                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}" class="btn btn-light border fw-bold px-4 rounded-pill">Batal</a>
                <button type="button" onclick="submitForm()" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm d-flex align-items-center">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>

        {{-- AREA PERIODE (DYNAMIC) --}}
        <div id="periodsContainer">
            {{-- Card Periode akan di-render di sini oleh JS --}}
        </div>

        {{-- TOMBOL TAMBAH PERIODE --}}
        <div class="text-center mt-4 mb-5">
            <button type="button" class="btn btn-dark rounded-pill px-4 px-md-5 fw-bold py-2 shadow-sm w-100 w-md-auto" onclick="addPeriod()">
                <i class="bi bi-plus-lg me-2"></i>Tambah Periode Rolling
            </button>
        </div>

    </div>

    {{-- HIDDEN INPUTS --}}
    <div id="hiddenInputsArea"></div>
</form>

<script>
    // ==========================================
    // 1. DATA & STATE MANAGEMENT
    // ==========================================
    const ruangans = @json($ruangans);
    let periodsData = @json($mahasiswa->roomSequences); 
    
    let shiftsDB = {}; 
    @foreach($mahasiswa->shiftSchedules as $s)
        shiftsDB['{{ $s->tanggal }}'] = '{{ $s->shift_type }}';
    @endforeach

    // ==========================================
    // 2. RENDER LOGIC
    // ==========================================
    document.addEventListener('DOMContentLoaded', () => {
        if (periodsData.length === 0) {
            addPeriod(); 
        } else {
            periodsData.forEach((p, index) => renderPeriodCard(index, p));
        }
    });

    function addPeriod() {
        const newIndex = periodsData.length;
        const newObj = { ruangan_id: '', start_date: '', end_date: '' };
        periodsData.push(newObj);
        renderPeriodCard(newIndex, newObj);
    }

    function renderPeriodCard(index, data) {
        const container = document.getElementById('periodsContainer');
        
        let roomOptions = '<option value="">Pilih Ruangan</option>';
        ruangans.forEach(r => {
            const selected = data.ruangan_id == r.id ? 'selected' : '';
            roomOptions += `<option value="${r.id}" ${selected}>${r.nm_ruangan}</option>`;
        });

        // Menggunakan p-3 p-md-4 untuk padding yang lebih ramah mobile
        const html = `
        <div class="master-card" id="card-${index}">
            <div class="card-header-modern">
                <h6 class="fw-bold m-0 text-secondary">Periode #${index + 1}</h6>
                <button type="button" class="btn-delete-period" onclick="removePeriod(${index})" title="Hapus Periode">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
            <div class="p-3 p-md-4">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label-sm">Ruangan</label>
                        <select class="form-control choices-init" id="room-${index}" onchange="updateData(${index}, 'ruangan_id', this.value)">
                            ${roomOptions}
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label-sm">Tanggal Mulai</label>
                        <input type="date" class="form-control-modern w-100" id="start-${index}" 
                               value="${data.start_date || ''}" onchange="updateData(${index}, 'start_date', this.value)">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label-sm">Tanggal Selesai</label>
                        <input type="date" class="form-control-modern w-100" id="end-${index}" 
                               value="${data.end_date || ''}" onchange="updateData(${index}, 'end_date', this.value)">
                    </div>
                </div>
            </div>
            
            {{-- PREVIEW SECTION --}}
            <div class="preview-container" id="preview-${index}">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-1">
                    <h6 class="fw-bold m-0 text-dark"><i class="bi bi-calendar-week me-2"></i>Detail Shift Harian</h6>
                    <small class="text-muted">Muncul sesuai tanggal & ruangan</small>
                </div>
                <div class="daily-grid" id="grid-${index}">
                    {{-- Grid items generated by JS --}}
                </div>
            </div>
        </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

        const selectEl = document.getElementById(`room-${index}`);
        new Choices(selectEl, {
            searchEnabled: true,
            itemSelectText: '',
            placeholderValue: 'Cari Ruangan...',
            shouldSort: false
        });

        if (data.start_date && data.end_date) {
            renderDailyShifts(index);
        }
    }

    // ==========================================
    // 3. LOGIC UPDATE & PREVIEW
    // ==========================================
    function updateData(index, field, value) {
        periodsData[index][field] = value;
        if (field === 'start_date' || field === 'end_date' || field === 'ruangan_id') {
            renderDailyShifts(index);
        }
    }

    function renderDailyShifts(index) {
        const p = periodsData[index];
        const previewBox = document.getElementById(`preview-${index}`);
        const gridBox = document.getElementById(`grid-${index}`);
        
        gridBox.innerHTML = '';
        
        if (!p.start_date || !p.end_date) {
            previewBox.classList.remove('preview-active');
            return;
        }

        const start = moment(p.start_date);
        const end = moment(p.end_date);
        const diff = end.diff(start, 'days');

        if (diff < 0) {
            previewBox.classList.remove('preview-active');
            return;
        }
        if (diff > 366) {
            Swal.fire('Info', 'Rentang tanggal terlalu panjang (Max 1 tahun).', 'warning');
            return;
        }

        let optionsHtml = '';
        const selectedRoom = ruangans.find(r => r.id == p.ruangan_id);
        
        if (selectedRoom && selectedRoom.room_shifts && selectedRoom.room_shifts.length > 0) {
            selectedRoom.room_shifts.forEach(shift => {
                let jamM = shift.jam_masuk.substring(0,5);
                let jamK = shift.jam_keluar.substring(0,5);
                optionsHtml += `<option value="${shift.nama_shift}" data-raw="${shift.nama_shift}">${shift.nama_shift} (${jamM} - ${jamK})</option>`;
            });
            optionsHtml += `<option value="Libur" data-raw="Libur">❌ Libur</option>`;
        } else {
            const isGizi = selectedRoom && selectedRoom.nm_ruangan.toLowerCase().includes('gizi');
            if (isGizi) {
                optionsHtml = `
                    <option value="Pagi" data-raw="Pagi">🌞 Pagi (04:30)</option>
                    <option value="Siang" data-raw="Siang">🌤 Siang (11:20)</option>
                    <option value="Reguler" data-raw="Reguler">🏢 Reguler</option>
                    <option value="Libur" data-raw="Libur">❌ Libur</option>
                `;
            } else {
                optionsHtml = `
                    <option value="Pagi" data-raw="Pagi">🌞 Pagi</option>
                    <option value="Siang" data-raw="Siang">🌤 Siang</option>
                    <option value="Malam" data-raw="Malam">🌙 Malam</option>
                    <option value="Libur" data-raw="Libur">❌ Libur</option>
                `;
            }
        }

        previewBox.classList.add('preview-active');

        let loop = start.clone();
        while (loop.isSameOrBefore(end)) {
            const dateStr = loop.format('YYYY-MM-DD');
            const dayName = loop.format('dddd');
            const datePretty = loop.format('DD MMM');
            
            let val = 'Pagi';
            if (shiftsDB[dateStr]) {
                val = shiftsDB[dateStr];
            } else {
                const dayIso = loop.isoWeekday();
                if (dayIso === 6 || dayIso === 7) val = 'Libur';
            }

            // Replace the selected attribute logic (using regex/DOM parser workaround)
            let currentOptions = optionsHtml.replace(`data-raw="${val}"`, `data-raw="${val}" selected`);

            const itemHtml = `
                <div class="daily-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="date-badge">${datePretty}</span>
                        <span class="day-badge">${dayName}</span>
                    </div>
                    <select class="form-select form-select-sm fw-bold border-0 bg-light shift-input" 
                            data-index="${index}" data-date="${dateStr}"
                            style="cursor:pointer; min-height: 38px;">
                        ${currentOptions}
                    </select>
                </div>
            `;
            gridBox.insertAdjacentHTML('beforeend', itemHtml);

            loop.add(1, 'days');
        }
    }

    function removePeriod(index) {
        Swal.fire({
            title: 'Hapus Periode?',
            text: "Data shift di dalamnya juga akan hilang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`card-${index}`).remove();
                periodsData[index] = null; 
            }
        });
    }

    // ==========================================
    // 4. SUBMIT LOGIC
    // ==========================================
    function submitForm() {
        const hiddenArea = document.getElementById('hiddenInputsArea');
        hiddenArea.innerHTML = ''; 

        let seqCounter = 0;
        let shiftCounter = 0;

        try {
            periodsData.forEach((p, idx) => {
                if (p === null) return; 

                const roomId = document.getElementById(`room-${idx}`).value;
                const start = document.getElementById(`start-${idx}`).value;
                const end = document.getElementById(`end-${idx}`).value;

                if (!roomId || !start || !end) {
                    Swal.fire('Gagal', `Periode #${idx+1} belum lengkap!`, 'error');
                    throw new Error("Validation Failed");
                }

                hiddenArea.innerHTML += `
                    <input type="hidden" name="sequences[${seqCounter}][ruangan_id]" value="${roomId}">
                    <input type="hidden" name="sequences[${seqCounter}][start_date]" value="${start}">
                    <input type="hidden" name="sequences[${seqCounter}][end_date]" value="${end}">
                `;
                seqCounter++;

                const shiftInputs = document.querySelectorAll(`.shift-input[data-index="${idx}"]`);
                shiftInputs.forEach(input => {
                    const date = input.getAttribute('data-date');
                    const type = input.value;

                    hiddenArea.innerHTML += `
                        <input type="hidden" name="shifts[${shiftCounter}][tanggal]" value="${date}">
                        <input type="hidden" name="shifts[${shiftCounter}][ruangan_id]" value="${roomId}">
                        <input type="hidden" name="shifts[${shiftCounter}][shift_type]" value="${type}">
                    `;
                    shiftCounter++;
                });
            });

            document.getElementById('rollingForm').submit();
        } catch (e) {
            console.log(e);
        }
    }
</script>
@endsection