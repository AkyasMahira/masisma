<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateIndikatorPenilaiansTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('indikator_penilaians')) {
            Schema::create('indikator_penilaians', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ruangan_id')->index();
                $table->string('nama');
                $table->string('kategori')->nullable();   // grup, mis. "Sikap & Kedisiplinan"
                $table->integer('urutan')->default(0);
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });
        }

        // Seed 5 indikator default untuk tiap ruangan yang sudah ada
        $defaults = [
            ['Sikap & Kedisiplinan', 'Kedisiplinan Waktu'],
            ['Sikap & Kedisiplinan', 'Komunikasi & Etika'],
            ['Sikap & Kedisiplinan', 'Kepatuhan SOP/PPI'],
            ['Keahlian & Kompetensi', 'Pemahaman Teoritis'],
            ['Keahlian & Kompetensi', 'Keterampilan Tindakan'],
        ];

        foreach (DB::table('ruangans')->pluck('id') as $rid) {
            $sudahAda = DB::table('indikator_penilaians')->where('ruangan_id', $rid)->exists();
            if ($sudahAda) continue;
            $u = 0;
            foreach ($defaults as [$kat, $nama]) {
                DB::table('indikator_penilaians')->insert([
                    'ruangan_id' => $rid,
                    'nama'       => $nama,
                    'kategori'   => $kat,
                    'urutan'     => $u++,
                    'aktif'      => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('indikator_penilaians');
    }
}
