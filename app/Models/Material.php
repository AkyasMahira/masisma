<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
 
   protected $fillable = ['title', 'subtitle', 'description', 'external_link', 'order'];

    public function files() {
        return $this->hasMany(MaterialFile::class);
    }

    /**
     * Relasi ke progress (untuk ngecek siapa aja yang udah kelar)
     */
    public function progresses()
    {
        return $this->hasMany(MaterialProgress::class);
    }

    /**
     * Cek apakah user tertentu sudah kelar materi ini
     */
    public function isCompletedBy($userId)
    {
        return $this->progresses()->where('user_id', $userId)->where('is_completed', true)->exists();
    }
}