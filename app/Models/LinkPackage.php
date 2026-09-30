<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LinkPackage extends Model {
    protected $fillable = ['title', 'slug', 'image', 'is_active'];
    public function items() { return $this->hasMany(LinkItem::class, 'package_id')->orderBy('sort_order'); }
}