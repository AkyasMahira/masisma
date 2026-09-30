<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LinkItem extends Model {
    protected $fillable = ['package_id', 'title', 'sort_order'];
    public function contents() { return $this->hasMany(LinkContent::class, 'item_id'); }
}