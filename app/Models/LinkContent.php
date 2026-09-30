<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LinkContent extends Model {
    protected $fillable = ['item_id', 'type', 'label', 'value'];
}