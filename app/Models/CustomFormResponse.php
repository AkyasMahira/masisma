<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFormResponse extends Model {
    protected $fillable = ['custom_form_id', 'answers'];
    protected $casts = ['answers' => 'array'];
}