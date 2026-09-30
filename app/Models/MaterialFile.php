<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialFile extends Model
{

    protected $fillable = [
        'material_id', 
        'file_name', 
        'file_path', 
        'file_type', 
        'description'
    ];

    public function material() {
        return $this->belongsTo(Material::class);
    }
}