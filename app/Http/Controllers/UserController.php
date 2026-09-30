<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mou;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
public function index(Request $request)
{
    $search = $request->input('search');
    $mouId = $request->input('mou_id'); 
    $role = $request->input('role'); // Filter Role
    $sortBy = $request->input('sort', 'created_at');
    $direction = $request->input('direction', 'desc');

    $instansis = Mou::orderBy('nama_universitas', 'asc')->get();

    $query = User::with('mou')->where('id', '!=', auth()->id());

    // Search Nama/Email
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    // Filter Instansi
    if ($mouId) {
        $query->where('mou_id', $mouId);
    }

    // Filter Role
    if ($role) {
        $query->where('role', $role);
    }

    // Logic Sorting
    if ($sortBy == 'name') {
        $query->orderBy('name', $direction);
    } elseif ($sortBy == 'role') {
        $query->orderBy('role', $direction);
    } else {
        $query->orderBy('created_at', $direction);
    }

    $allUsers = $query->paginate(10)->withQueryString();

    return view('users.index', compact('allUsers', 'instansis'));
}
public function update(Request $request, $id)
{
    // 1. Cari user yang akan diupdate
    $user = User::findOrFail($id);
    
    // 2. Validasi input
    // email,'.$id bertujuan agar validasi unique mengabaikan email milik user ini sendiri
    $request->validate([
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email,'.$id,
        'role'          => 'required|in:admin,ruangan,user',
        'program_studi' => 'nullable|string|max:255',
    ], [
        'name.required'  => 'Nama wajib diisi.',
        'email.unique'   => 'Email sudah digunakan oleh pengguna lain.',
        'role.required'  => 'Role harus dipilih.',
    ]);

    try {
        // 3. Eksekusi Update
        $user->update([
            'name'          => strtoupper($request->name), // Biar rapi pakai huruf besar
            'email'         => $request->email,
            'role'          => $request->role,
            'program_studi' => $request->program_studi,
            // Jika ingin menambahkan mou_id juga bisa:
            // 'mou_id'     => $request->mou_id, 
        ]);

        return redirect()->back()->with('success', 'Informasi user ' . $user->name . ' berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
    }
}

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Akun ' . $user->name . ' berhasil disetujui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Opsional: Hapus data MOU terkait jika perlu
        // if($user->mou) { $user->mou->delete(); }
        
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

   
}