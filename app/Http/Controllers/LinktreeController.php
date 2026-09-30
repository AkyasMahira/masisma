<?php

namespace App\Http\Controllers;

use App\Models\LinkPackage;
use App\Models\LinkItem;
use App\Models\LinkContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LinktreeController extends Controller
{
    /**
     * 1. TAMPILAN PUBLIK (Client Side)
     * Menggunakan Caching untuk efisiensi server RSUD SLG.
     */
    public function showPublic($slug)
    {
        // Caching selama 1 jam (3600 detik)
        // Jika ada perubahan data, cache akan otomatis dihapus lewat method update/store
        $package = Cache::remember("linktree_v2_{$slug}", 3600, function () use ($slug) {
            return LinkPackage::where('slug', $slug)
                ->where('is_active', true)
                ->with(['items.contents'])
                ->firstOrFail();
        });

        return view('linktree.public', compact('package'));
    }

 public function index(Request $request)
{
    $query = LinkPackage::query();

    // Fitur Search (Cari berdasarkan judul atau slug)
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('slug', 'like', '%' . $request->search . '%');
    }

    // Fitur Filter Status
    if ($request->filled('status')) {
        $status = $request->status == 'active' ? 1 : 0;
        $query->where('is_active', $status);
    }

    // Pagination 9 data per halaman
    $packages = $query->latest()->paginate(9)->withQueryString();

    return view('linktree.index', compact('packages'));
}
    /**
     * 3. STORE PAKET (Wadah Utama)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('linktree', 'public');
        }

        LinkPackage::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . rand(100, 999), // Unique slug
            'image' => $imagePath,
            'is_active' => true
        ]);

        return back()->with('success', 'Paket Digital Folder berhasil dibuat!');
    }

    /**
     * 4. EDIT HALAMAN (Management Room)
     * Tempat mengatur Item dan Konten di dalam paket.
     */
    public function edit($id)
    {
        // Load relasi agar tidak terjadi N+1 query (Performance Optimization)
        $package = LinkPackage::with(['items.contents'])->findOrFail($id);
        return view('linktree.edit', compact('package'));
    }

    /**
     * 5. UPDATE PAKET (Nama & Gambar)
     */
    public function update(Request $request, $id)
    {
        $package = LinkPackage::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
            $package->image = $request->file('image')->store('linktree', 'public');
        }

        $package->title = $request->title;
        $package->save();

        // PENTING: Hapus cache agar perubahan langsung terlihat di publik
        Cache::forget("linktree_v2_{$package->slug}");

        return back()->with('success', 'Informasi paket berhasil diperbarui!');
    }

    /**
     * 6. MANAJEMEN ITEM (GRUP JUDUL)
     */
    public function storeItem(Request $request, $packageId)
    {
        $request->validate(['title' => 'required']);

        LinkItem::create([
            'package_id' => $packageId,
            'title' => $request->title,
            'sort_order' => LinkItem::where('package_id', $packageId)->count() + 1
        ]);

        // Clear Cache
        $package = LinkPackage::find($packageId);
        Cache::forget("linktree_v2_{$package->slug}");

        return back()->with('success', 'Grup kategori berhasil ditambah!');
    }

    public function destroyItem($id)
    {
        $item = LinkItem::findOrFail($id);
        $package = LinkPackage::find($item->package_id);
        
        $item->delete(); // Karena pakai cascade di migration, konten di dalamnya ikut terhapus
        Cache::forget("linktree_v2_{$package->slug}");

        return back()->with('success', 'Grup berhasil dihapus!');
    }

    /**
     * 7. MANAJEMEN KONTEN (LINK / FILE)
     */
    public function storeContent(Request $request, $itemId)
    {
        $request->validate([
            'label' => 'required',
            'type' => 'required|in:link,file,image',
            'file' => 'nullable|file|max:5120', // Maksimal 5MB
        ]);

        $value = $request->value; // Default untuk link

        // Jika user upload file
        if ($request->type !== 'link' && $request->hasFile('file')) {
            $path = $request->file('file')->store('linktree_assets', 'public');
            $value = asset('storage/' . $path);
        }

        LinkContent::create([
            'item_id' => $itemId,
            'type' => $request->type,
            'label' => $request->label,
            'value' => $value
        ]);

        // Clear Cache via Package Slug
        $item = LinkItem::find($itemId);
        $package = LinkPackage::find($item->package_id);
        Cache::forget("linktree_v2_{$package->slug}");

        return back()->with('success', 'Konten berhasil ditambahkan!');
    }

    public function destroyContent($id)
    {
        $content = LinkContent::findOrFail($id);
        
        // Cari slug paket untuk hapus cache
        $item = LinkItem::find($content->item_id);
        $package = LinkPackage::find($item->package_id);
        
        // Jika konten berupa file, hapus dari storage
        if ($content->type !== 'link') {
            $path = str_replace(asset('storage/'), '', $content->value);
            Storage::disk('public')->delete($path);
        }

        $content->delete();
        Cache::forget("linktree_v2_{$package->slug}");

        return back()->with('success', 'Konten dihapus!');
    }

    /**
     * 8. DELETE PAKET TOTAL
     */
    public function destroy($id)
    {
        $package = LinkPackage::findOrFail($id);
        
        // Hapus Gambar Header
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }

        // Hapus Cache
        Cache::forget("linktree_v2_{$package->slug}");
        
        $package->delete();

        return redirect()->route('admin.linktree.index')->with('success', 'Paket berhasil dihapus permanen!');
    }
    
    public function toggleStatus($id) {
    $package = LinkPackage::findOrFail($id);
    $package->is_active = !$package->is_active;
    $package->save();
    
    Cache::forget("linktree_v2_{$package->slug}");
    return back()->with('success', 'Status paket berhasil diubah!');
}
}