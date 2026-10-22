<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    protected $fillable = ['mahasiswa_id', 'room_sequence_id', 'ruangan_id', 'tanggal', 'shift_type'];

    /** Hapus shift harian mahasiswa di dalam rentang sebuah periode rolling (RoomSequence). */
    public static function hapusDalamPeriode($sequence)
    {
        return static::where('mahasiswa_id', $sequence->mahasiswa_id)
            ->whereBetween('tanggal', [substr($sequence->start_date, 0, 10), substr($sequence->end_date, 0, 10)])
            ->delete();
    }

    /**
     * Setelah periode diubah: shift di tanggal yang keluar dari rentang baru dihapus;
     * bila ruangannya ganti, semua shift di rentang lama dihapus (nama shift beda per ruangan).
     */
    public static function bersihkanSetelahUbahPeriode($lama, $baru)
    {
        $q = static::where('mahasiswa_id', $lama->mahasiswa_id)
            ->whereBetween('tanggal', [substr($lama->start_date, 0, 10), substr($lama->end_date, 0, 10)]);

        if ((int) $lama->ruangan_id === (int) $baru->ruangan_id) {
            $mulai = substr($baru->start_date, 0, 10);
            $akhir = substr($baru->end_date, 0, 10);
            $q->where(function ($w) use ($mulai, $akhir) {
                $w->where('tanggal', '<', $mulai)->orWhere('tanggal', '>', $akhir);
            });
        }

        return $q->delete();
    }
}
