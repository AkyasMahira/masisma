<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialProgress extends Model
{
  
    
    protected $table = 'material_progress';

 protected $fillable = [
    'user_id',
    'mahasiswa_id', // Pastikan ini ditambahkan jika menggunakan $fillable
    'material_id',
    'is_completed',
    'completed_at'
];
    /**
     * Relasi balik ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi balik ke Materi
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}