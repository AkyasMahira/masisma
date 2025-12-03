<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomSchedule extends Model
{

    protected $fillable = [
        'mahasiswa_id',
        'ruangan_id',
        'start_date',
        'end_date',
        'status',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
