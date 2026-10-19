{{-- Hari tanpa sesi (pelatihan dengan hari tidak berurutan). Param: $awal = array tanggal Y-m-d --}}
<div class="col-12">
    <label class="form-label-custom">Hari tanpa sesi <span class="text-muted fw-normal">(opsional — untuk pelatihan yang harinya tidak berurutan)</span></label>
    <div id="hariLiburBox" class="d-flex flex-wrap gap-2" style="min-height:38px;"></div>
    <div class="small text-muted mt-1" id="hariLiburInfo"><i class="bi bi-info-circle me-1"></i>Isi tanggal mulai & selesai dulu. Centang tanggal yang <b>tidak ada sesi</b>: absensi otomatis tertutup di hari itu dan sertifikat menulis tanggal sesuai hari sesi.</div>
</div>
<style>
    .hl-chip { position:relative; }
    .hl-chip input { position:absolute; opacity:0; }
    .hl-chip label { display:inline-flex; flex-direction:column; align-items:center; min-width:62px; padding:6px 10px; border:1.5px solid #e2e8f0; border-radius:10px; cursor:pointer; font-size:.78rem; font-weight:700; color:#334155; background:#fff; line-height:1.15; user-select:none; }
    .hl-chip label small { font-weight:600; color:#94a3b8; font-size:.66rem; }
    .hl-chip input:checked + label { background:#fee2e2; border-color:#fca5a5; color:#b91c1c; text-decoration:line-through; }
    .hl-chip input:checked + label small { color:#b91c1c; text-decoration:none; }
    .hl-chip.ujung label { background:#f1f5f9; color:#94a3b8; cursor:not-allowed; }
</style>
<script>
    (function () {
        var awal = @json(array_values((array) ($awal ?? [])));
        var box = document.getElementById('hariLiburBox');
        var info = document.getElementById('hariLiburInfo');
        var fM = document.querySelector('[name="tanggal_mulai"]');
        var fS = document.querySelector('[name="tanggal_selesai"]');
        if (!box || !fM || !fS) return;
        var HARI = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        var BLN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        function ymd(d) { return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2); }
        function tgl(v) { if (!v) return null; var p = v.slice(0, 10).split('-'); return new Date(+p[0], +p[1] - 1, +p[2]); }
        function terpilih() { return Array.prototype.map.call(box.querySelectorAll('input:checked'), function (i) { return i.value; }); }

        function render() {
            var pilih = box.children.length ? terpilih() : awal;
            var a = tgl(fM.value), b = tgl(fS.value);
            box.innerHTML = '';
            if (!a || !b || b <= a) { return; }
            var n = Math.round((b - a) / 864e5) + 1;
            if (n > 120) { box.innerHTML = '<span class="small text-muted">Rentang terlalu panjang untuk diatur per hari.</span>'; return; }
            for (var i = 0; i < n; i++) {
                var d = new Date(a.getFullYear(), a.getMonth(), a.getDate() + i), v = ymd(d);
                var ujung = (i === 0 || i === n - 1);
                var w = document.createElement('div');
                w.className = 'hl-chip' + (ujung ? ' ujung' : '');
                w.innerHTML = '<input type="checkbox" name="hari_libur[]" id="hl' + v + '" value="' + v + '"' + (!ujung && pilih.indexOf(v) >= 0 ? ' checked' : '') + (ujung ? ' disabled' : '') + '>'
                    + '<label for="hl' + v + '" title="' + (ujung ? 'Hari pertama/terakhir selalu ada sesi' : 'Klik untuk tandai tidak ada sesi') + '"><small>' + HARI[d.getDay()] + '</small>' + d.getDate() + ' ' + BLN[d.getMonth()] + '</label>';
                box.appendChild(w);
            }
            hitung();
        }
        function hitung() {
            var total = box.querySelectorAll('input').length, libur = terpilih().length;
            if (total) info.innerHTML = '<i class="bi bi-calendar-check me-1"></i><b>' + (total - libur) + ' hari sesi</b>' + (libur ? ', ' + libur + ' hari tanpa sesi (dicoret)' : '') + '. Klik tanggal untuk menandai tidak ada sesi. Hari pertama & terakhir selalu ada sesi.';
        }
        box.addEventListener('change', hitung);
        fM.addEventListener('change', render);
        fS.addEventListener('change', render);
        render();
    })();
</script>
