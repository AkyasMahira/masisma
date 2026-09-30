<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    protected $fillable = ['mahasiswa_id', 'ruangan_id', 'tanggal', 'shift_type'];
}