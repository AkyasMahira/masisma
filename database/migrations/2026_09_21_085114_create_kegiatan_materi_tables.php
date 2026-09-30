<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Kegiatan;
use App\Models\KegiatanPeserta;
use App\Models\KegiatanFasilitator;
use App\Models\KegiatanPenilaianSetting;
use App\Models\ItemPenilaianSkill;
use App\Models\TtdPenilaian;

class CreateKegiatanMateriTables extends Migration
{
    public function up()
    {
        // Nama tabel diambil dari model supaya aman kalau ada $table custom
        $tKegiatan = (new Kegiatan)->getTable();
        $tPeserta  = (new KegiatanPeserta)->getTable();
        $tFas      = (new KegiatanFasilitator)->getTable();
        $tSetting  = (new KegiatanPenilaianSetting)->getTable();
        $tItem     = (new ItemPenilaianSkill)->getTable();
        $tTtd      = (new TtdPenilaian)->getTable();

        Schema::create('kegiatan_materi', function (Blueprint $table) use ($tKegiatan) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained($tKegiatan)->cascadeOnDelete();
            $table->string('nama_materi');
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('materi_fasilitator', function (Blueprint $table) use ($tFas) {
            $table->id();
            $table->foreignId('materi_id')->constrained('kegiatan_materi')->cascadeOnDelete();
            $table->foreignId('fasilitator_id')->constrained($tFas)->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['materi_id', 'fasilitator_id']);
        });

        Schema::table($tItem, function (Blueprint $table) {
            $table->unsignedBigInteger('materi_id')->nullable();
            $table->index('materi_id');
        });

        Schema::table($tTtd, function (Blueprint $table) {
            $table->unsignedBigInteger('materi_id')->nullable();
            $table->index('materi_id');
        });

        Schema::table($tSetting, function (Blueprint $table) {
            $table->unsignedTinyInteger('batas_posttest')->default(80);
        });

        // ---- Backfill: data lama dimasukkan ke satu materi default ----
        $kegiatanIds = DB::table($tItem)->distinct()->pluck('kegiatan_id');

        foreach ($kegiatanIds as $kid) {
            $materiId = DB::table('kegiatan_materi')->insertGetId([
                'kegiatan_id' => $kid,
                'nama_materi' => 'Materi Utama',
                'urutan'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::table($tItem)->where('kegiatan_id', $kid)->update(['materi_id' => $materiId]);

            foreach (DB::table($tFas)->where('kegiatan_id', $kid)->pluck('id') as $fid) {
                DB::table('materi_fasilitator')->insert([
                    'materi_id'      => $materiId,
                    'fasilitator_id' => $fid,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            $pesertaIds = DB::table($tPeserta)->where('kegiatan_id', $kid)->pluck('id');
            DB::table($tTtd)->whereIn('kegiatan_peserta_id', $pesertaIds)->update(['materi_id' => $materiId]);
        }
    }

    public function down()
    {
        $tSetting = (new KegiatanPenilaianSetting)->getTable();
        $tItem    = (new ItemPenilaianSkill)->getTable();
        $tTtd     = (new TtdPenilaian)->getTable();

        Schema::table($tSetting, function (Blueprint $table) {
            $table->dropColumn('batas_posttest');
        });

        Schema::table($tTtd, function (Blueprint $table) {
            $table->dropIndex(['materi_id']);
            $table->dropColumn('materi_id');
        });

        Schema::table($tItem, function (Blueprint $table) {
            $table->dropIndex(['materi_id']);
            $table->dropColumn('materi_id');
        });

        Schema::dropIfExists('materi_fasilitator');
        Schema::dropIfExists('kegiatan_materi');
    }
}