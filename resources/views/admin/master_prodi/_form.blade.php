@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
    <div class="col-md-7">
        <label class="form-label">Nama Program Studi <span class="text-danger">*</span></label>
        <input type="text" name="nama_prodi" class="form-control" value="{{ old('nama_prodi', $prodi->nama_prodi ?? '') }}" required placeholder="mis. D3 KEPERAWATAN">
    </div>
    <div class="col-md-5">
        <label class="form-label">Jenjang</label>
        <select name="jenjang" class="form-select">
            @php $jenjangs = ['','SMK','D3','D4','S1','S2','PROFESI','PPDS']; $cur = old('jenjang', $prodi->jenjang ?? ''); @endphp
            @foreach($jenjangs as $j)
                <option value="{{ $j }}" {{ $cur===$j ? 'selected':'' }}>{{ $j ?: '-' }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Kategori / Rumpun</label>
        <input type="text" name="kategori" class="form-control" value="{{ old('kategori', $prodi->kategori ?? '') }}" placeholder="mis. KEPERAWATAN">
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="aktif" value="0">
            <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktif" {{ old('aktif', $prodi->aktif ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="aktif">Aktif (tampil di dropdown)</label>
        </div>
    </div>
</div>
