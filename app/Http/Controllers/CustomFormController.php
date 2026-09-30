<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomForm;
use App\Models\CustomFormResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomFormController extends Controller {

    // --- ADMIN SIDE: DAFTAR SEMUA FORM ---
    public function index() {
        // Ambil form terbaru dengan hitungan respon pendaftar
        $forms = CustomForm::withCount('responses')->latest()->get();
        return view('admin.forms.index', compact('forms'));
    }
// CustomFormController.php

public function duplicate($id) {
    // 1. Cari form asli
    $originalForm = CustomForm::findOrFail($id);

    // 2. Replicate model (menyalin semua atribut kecuali ID dan timestamps)
    $newForm = $originalForm->replicate();

    // 3. Modifikasi judul dan slug agar tidak bentrok
    $newForm->title = $originalForm->title . ' (Copy)';
    $newForm->slug = Str::slug($newForm->title) . '-' . rand(1000, 9999);
    
    // 4. Set ulang jumlah respon ke 0 (karena ini form baru)
    // Jika is_active ingin otomatis non-aktif dulu bisa di-set false
    $newForm->is_active = false; 

    // 5. Simpan ke Database
    $newForm->save();

    return redirect()->route('admin.forms.index')
                     ->with('success', 'Formulir berhasil diduplikasi sebagai draft!');
}
    public function create() {
        return view('admin.forms.create');
    }

    // --- ADMIN SIDE: SIMPAN FORM BARU ---
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'fields' => 'required|array',
            'header_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        // Proses Sanitasi Fields (Checkbox & Nested Data)
        $fields = $request->fields;
        foreach ($fields as $key => $field) {
            // Paksa checkbox jadi boolean agar tidak eror di DB
            $fields[$key]['required'] = isset($field['required']) ? true : false;
            $fields[$key]['multiple'] = isset($field['multiple']) ? true : false;
            
            // Pastikan options masuk sebagai array dan bersihkan string kosong
            if (isset($field['options']) && is_array($field['options'])) {
                foreach ($field['options'] as $oIdx => $opt) {
                    // Pastikan setiap pilihan punya array children (untuk anak pertanyaan)
                    if (!isset($opt['children']) || !is_array($opt['children'])) {
                        $fields[$key]['options'][$oIdx]['children'] = [];
                    }
                }
            } else {
                $fields[$key]['options'] = [];
            }
        }

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . rand(1000, 9999),
            'description' => $request->description,
            'fields' => $fields,
            'is_active' => true
        ];

        // Handle Gambar Header
        if ($request->hasFile('header_image')) {
            $data['header_image'] = $request->file('header_image')->store('form_headers', 'public');
        }

        CustomForm::create($data);

        return redirect()->route('admin.forms.index')->with('success', 'Formulir berhasil dipublikasikan!');
    }

    public function edit($id) {
        $form = CustomForm::findOrFail($id);
        return view('admin.forms.edit', compact('form'));
    }

    // --- ADMIN SIDE: UPDATE DATA & GAMBAR ---
    public function update(Request $request, $id) {
        $form = CustomForm::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'fields' => 'required|array',
            'header_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);
        
        $fields = $request->fields;
        foreach ($fields as $key => $field) {
            $fields[$key]['required'] = isset($field['required']) ? true : false;
            $fields[$key]['multiple'] = isset($field['multiple']) ? true : false;
            
            // Mapping Nested Data (Pilihan & Anak Pertanyaan)
            if (isset($field['options']) && is_array($field['options'])) {
                foreach ($field['options'] as $oIdx => $opt) {
                    $fields[$key]['options'][$oIdx]['children'] = $opt['children'] ?? [];
                }
            }
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'fields' => $fields,
        ];

        // Ganti Gambar Header & Hapus yang Lama biar Storage Gak Penuh
        if ($request->hasFile('header_image')) {
            if ($form->header_image) {
                Storage::disk('public')->delete($form->header_image);
            }
            $data['header_image'] = $request->file('header_image')->store('form_headers', 'public');
        }

        $form->update($data);

        return redirect()->route('admin.forms.index')->with('success', 'Perubahan formulir berhasil disimpan!');
    }

    // --- ADMIN SIDE: TOGGLE BUKA/TUTUP FORM ---
    public function toggleStatus($id) {
        $form = CustomForm::findOrFail($id);
        $form->is_active = !$form->is_active;
        $form->save();

        $pesan = $form->is_active ? 'Pendaftaran Dibuka!' : 'Pendaftaran Ditutup!';
        return back()->with('success', $pesan);
    }

    public function destroy($id) {
        $form = CustomForm::findOrFail($id);
        
        if ($form->header_image) {
            Storage::disk('public')->delete($form->header_image);
        }
        
        $form->delete();
        return back()->with('success', 'Formulir telah dihapus permanen.');
    }

    // --- PUBLIC SIDE: TAMPILAN FORM ---
    public function showPublic($slug) {
        $form = CustomForm::where('slug', $slug)->firstOrFail();
        // View 'show' nanti akan mengecek is_active untuk menampilkan pesan "Ditutup"
        return view('public.forms.show', compact('form'));
    }

    // --- PUBLIC SIDE: SUBMIT JAWABAN ---
   // --- PUBLIC SIDE: SUBMIT JAWABAN ---
public function submitPublic(Request $request, $slug) {
    $form = CustomForm::where('slug', $slug)->firstOrFail();
    $answers = $request->except(['_token']);

    // Handle File Upload dari Jawaban User
    foreach ($form->fields as $index => $field) {
        $key = 'ans_' . $index;
        if ($field['type'] == 'file' && $request->hasFile($key)) {
            // Simpan file ke folder 'form_responses'
            $path = $request->file($key)->store('form_responses', 'public');
            $answers[$key] = $path; // Simpan path-nya ke JSON answers
        }
    }

    $response = CustomFormResponse::create([
        'custom_form_id' => $form->id,
        'answers' => $answers,
    ]);

    return redirect()->route('forms.public.success', $slug)->with('response_id', $response->id);
}

    public function showSuccess($slug) {
        $form = CustomForm::where('slug', $slug)->firstOrFail();
        $responseId = session('response_id');
        
        if (!$responseId) { 
            return redirect()->route('forms.public.show', $slug); 
        }

        $response = CustomFormResponse::findOrFail($responseId);
        return view('public.forms.success', compact('form', 'response'));
    }

    // --- EXPORT SIDE: DOWNLOAD PDF BUKTI INDIVIDU ---
    public function downloadPDF($slug, $responseId) {
        $form = CustomForm::where('slug', $slug)->firstOrFail();
        $response = CustomFormResponse::findOrFail($responseId);

        $logoPath = public_path('icon.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : "";

        $pdf = Pdf::loadView('public.forms.pdf_template', [
            'form' => $form,
            'response' => $response,
            'logo_base64' => $logoData
        ]);

        return $pdf->download('BUKTI-'.Str::slug($form->title).'.pdf');
    }

    // --- ADMIN SIDE: ANALISIS RESPONS ---
    public function responses($id) {
        $form = CustomForm::with('responses')->findOrFail($id);
        return view('admin.forms.responses', compact('form'));
    }
    
    // --- ADMIN SIDE: EXPORT SEMUA DATA (BATCH) ---
    public function exportResponsesPDF($id) {
        $form = CustomForm::with('responses')->findOrFail($id);
        
        $logoPath = public_path('icon.png'); 
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : "";

        $pdf = Pdf::loadView('admin.forms.pdf_rekap', compact('form', 'logoBase64'));
        $pdf->setPaper('a4', 'landscape'); 
        
        return $pdf->download('REKAP-'.Str::slug($form->title).'.pdf');
    }
    
    // Tambahkan di CustomFormController.php

public function destroyResponse($id) {
    $response = CustomFormResponse::findOrFail($id);
    $response->delete();

    return back()->with('success', 'Respon berhasil dihapus.');
}
}