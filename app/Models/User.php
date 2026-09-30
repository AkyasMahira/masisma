<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\ResetPasswordNotification;
class User extends Authenticatable
{
    use Notifiable;
    
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'mou_id',
        'device_id',
        'is_approved',
        'program_studi'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function mou()
    {
        return $this->belongsTo(Mou::class);
    }
public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id')->latest();
    }

    /**
     * Mengambil SELURUH riwayat magang user.
     * Berguna jika Anda ingin membuat halaman "Riwayat Magang" di frontend.
     */
    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswa::class, 'user_id');
    }
public function sendPasswordResetNotification($token)
{
    $this->notify(new ResetPasswordNotification($token, $this->email));
}
}
