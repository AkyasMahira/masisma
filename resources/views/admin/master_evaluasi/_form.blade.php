@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Kode</label>
        <input type="text" name="kode" class="form-control" value="{{ old('kode', $unsur->kode ?? '') }}" placeholder="mis. U1">
        <small class="text-muted">Opsional. Dipakai di laporan IKM.</small>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tipe <span class="text-danger">*</span></label>
        <select name="tipe" class="form-select" required>
            <option value="rating" {{ old('tipe', $unsur->tipe ?? 'rating')==='rating' ? 'selected' : '' }}>Rating (1-4, masuk IKM)</option>
            <option value="text" {{ old('tipe', $unsur->tipe ?? '')==='text' ? 'selected' : '' }}>Isian teks</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Urutan</label>
        <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $unsur->urutan ?? 0) }}" min="0">
    </div>
    <div class="col-12">
        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
        <textarea name="pertanyaan" class="form-control" rows="3" required placeholder="Tulis pertanyaan / pernyataan unsur...">{{ old('pertanyaan', $unsur->pertanyaan ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="aktif" value="0">
            <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktifSwitch" {{ old('aktif', $unsur->aktif ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="aktifSwitch">Aktif (tampil di form publik & dihitung IKM)</label>
        </div>
    </div>
</div>
