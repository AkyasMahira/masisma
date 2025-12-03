<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddInstansiFieldsToMousTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mous', function (Blueprint $table) {
            // Add new columns for instansi and contact
            $table->string('nama_instansi')->nullable()->after('nama_universitas');
            $table->string('alamat_instansi')->nullable()->after('nama_instansi');
            $table->text('rencana_kerja_sama')->nullable()->after('alamat_instansi');
            $table->string('nama_pic_instansi')->nullable()->after('rencana_kerja_sama');
            $table->string('nomor_kontak_pic')->nullable()->after('nama_pic_instansi');
            $table->string('jenis_instansi')->nullable()->after('nomor_kontak_pic');
            $table->string('jenis_instansi_lainnya')->nullable()->after('jenis_instansi');
            // New document uploads for pengajuan kerjasama
            $table->string('surat_permohonan')->nullable()->after('jenis_instansi_lainnya');
            $table->string('sk_pengangkatan_pimpinan')->nullable()->after('surat_permohonan');
            $table->string('sertifikat_akreditasi_prodi')->nullable()->after('sk_pengangkatan_pimpinan');
            $table->string('draft_mou')->nullable()->after('sertifikat_akreditasi_prodi');
        });

        // Copy existing data from nama_universitas to nama_instansi (for migration)
        DB::table('mous')->whereNull('nama_instansi')->update([
            'nama_instansi' => DB::raw('nama_universitas')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mous', function (Blueprint $table) {
            $table->dropColumn([
                'nama_instansi',
                'alamat_instansi',
                'rencana_kerja_sama',
                'nama_pic_instansi',
                'nomor_kontak_pic',
                'jenis_instansi',
                'jenis_instansi_lainnya',
                'surat_permohonan',
                'sk_pengangkatan_pimpinan',
                'sertifikat_akreditasi_prodi',
                'draft_mou',
            ]);
        });
    }
}
