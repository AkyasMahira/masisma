<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomForm extends Model {
    // Tambahkan 'header_image' di sini!
    protected $fillable = ['title', 'slug', 'description', 'fields', 'header_image', 'is_active'];
    
    protected $casts = [
        'fields' => 'array',
    ];

    public function responses() {
        return $this->hasMany(CustomFormResponse::class);
    }
}