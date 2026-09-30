<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KegiatanFasilitator extends Model
{
    protected $table = 'kegiatan_fasilitator';

    protected $fillable = ['kegiatan_id', 'nama_fasilitator', 'no_hp', 'token_fasilitator'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->token_fasilitator)) {
                $model->token_fasilitator = (string) Str::uuid();
            }
        });
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function nilaiSkill()
    {
        return $this->hasMany(NilaiSkill::class, 'fasilitator_id');
    }

    public function tandaTangan()
    {
        return $this->hasMany(TtdPenilaian::class, 'fasilitator_id');
    }
    public function materi()
{
    return $this->belongsToMany(KegiatanMateri::class, 'materi_fasilitator', 'fasilitator_id', 'materi_id');
}
}