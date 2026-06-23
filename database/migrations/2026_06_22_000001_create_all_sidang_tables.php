<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 6. T_prodi
        Schema::create('T_prodi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_prodi', 50);
            $table->string('nama_prodi', 250);
            $table->string('status_aktif', 50);
            $table->date('tgl_create')->useCurrent();
            $table->date('tgl_update')->useCurrent();
        });

        // 1. T_User
        Schema::create('T_User', function (Blueprint $table) {
            $table->id();
            $table->string('nip_nim', 50);
            $table->string('nama_lengkap', 500);
            $table->string('email', 250);
            $table->string('akun_ina', 250)->nullable();
            $table->string('Username', 50)->nullable();
            $table->string('Password', 250)->nullable();
            $table->string('status_pegawai', 250)->nullable();
            $table->string('jenis_user', 250);
            $table->string('kode_prodi', 50)->nullable();
            $table->string('nama_prodi', 250)->nullable();
            $table->string('kode_fs', 50);
            $table->string('nama_fs', 250);
            $table->string('strata', 10)->nullable();
            $table->integer('thn_angkatan')->nullable();
            $table->string('status_aktif', 50);
            $table->char('status_approve', 1); // y/t
            $table->timestamp('tgl_create')->useCurrent();
            $table->timestamp('tgl_update')->useCurrent();
        });

        // 11. T_tahapan
        Schema::create('T_tahapan', function (Blueprint $table) {
            $table->id();
            $table->string('Tahapan', 250);
            $table->string('Kode_tahap', 50);
            $table->string('strata', 10)->nullable();
            $table->timestamp('tgl_buat')->useCurrent();
            $table->timestamp('tgl_update')->useCurrent();
        });

        // 2. T_Syarat_sidang
        Schema::create('T_Syarat_sidang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_persyaratan', 500);
            $table->unsignedBigInteger('id_prodi');
            $table->string('kode_prodi', 50);
            $table->string('nama_prodi', 250);
            $table->string('tahapan_sidang', 100);
            $table->string('status_aktif', 50);
            $table->string('strata', 10);
            $table->date('tgl_create')->useCurrent();
            $table->date('tgl_update')->useCurrent();

            $table->foreign('id_prodi')->references('id')->on('T_prodi')->onDelete('cascade');
        });

        // 3. T_point_penilaian
        Schema::create('T_point_penilaian', function (Blueprint $table) {
            $table->id();
            $table->string('Penilaian', 500);
            $table->unsignedBigInteger('id_prodi');
            $table->string('kode_prodi', 50);
            $table->string('nama_prodi', 250);
            $table->string('tahapan_sidang', 250);
            $table->string('status_aktif', 50);
            $table->string('strata', 10);
            $table->date('tgl_create')->useCurrent();
            $table->date('tgl_update')->useCurrent();

            $table->foreign('id_prodi')->references('id')->on('T_prodi')->onDelete('cascade');
        });

        // 4. T_judul
        Schema::create('T_judul', function (Blueprint $table) {
            $table->id();
            $table->string('Judul', 500);
            $table->unsignedBigInteger('id_user_mhs');
            $table->string('Nim', 50);
            $table->integer('thn_create');
            $table->date('tgl_create')->useCurrent();
            $table->date('tgl_update')->useCurrent();

            $table->foreign('id_user_mhs')->references('id')->on('T_User')->onDelete('cascade');
        });

        // 5. T_judul_temp
        Schema::create('T_judul_temp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_judul');
            $table->string('Judul', 500);
            $table->unsignedBigInteger('id_user_mhs');
            $table->string('Nim', 50);
            $table->string('judul_baru', 500);
            $table->string('tahap_perubahan', 250);
            $table->string('alasan_perubahan', 500);
            $table->date('tgl_create')->useCurrent();
            $table->date('tgl_update')->useCurrent();

            $table->foreign('id_user_mhs')->references('id')->on('T_User')->onDelete('cascade');
        });

        // 7. T_ajuan_sidang
        Schema::create('T_ajuan_sidang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->string('Nim', 50);
            $table->string('nama_mhs', 250);
            $table->integer('angkatan');
            $table->unsignedBigInteger('id_judul');
            $table->string('Judul', 500);
            $table->string('tahapan_sidang', 100);
            $table->string('Strata', 10);
            $table->date('tgl_sidang')->nullable();
            $table->time('waktu_sidang')->nullable();
            $table->string('ruang_sidang', 250)->nullable();
            $table->string('status_lulus', 50)->nullable();
            $table->string('sk_pembimbing', 250)->nullable();
            $table->char('status_ajukan_mhs', 1)->nullable(); // y/t
            $table->string('sk_penguji', 250)->nullable();
            $table->string('no_undangan', 250)->nullable();
            $table->char('status_ajukan_prodi', 1)->nullable(); // y/t
            $table->string('no_BA_sidang', 250)->nullable();
            $table->string('sk_lulus', 250)->nullable();
            $table->timestamp('tgl_create')->useCurrent();
            $table->timestamp('tgl_update')->useCurrent();
            $table->unsignedBigInteger('id_user_create');
            $table->string('nama_user_create', 250);
            $table->integer('thn_create');
            $table->unsignedBigInteger('id_prodi');
            $table->string('kode_prodi', 50);
            $table->string('nama_prodi', 250);

            $table->foreign('id_user')->references('id')->on('T_User')->onDelete('cascade');
            $table->foreign('id_judul')->references('id')->on('T_judul')->onDelete('cascade');
            $table->foreign('id_user_create')->references('id')->on('T_User')->onDelete('cascade');
            $table->foreign('id_prodi')->references('id')->on('T_prodi')->onDelete('cascade');
        });

        // 8. T_cek_persyaratan
        Schema::create('T_cek_persyaratan', function (Blueprint $table) {
            $table->id();
            $table->string('tahapan_sidang', 100);
            $table->unsignedBigInteger('id_judul');
            $table->unsignedBigInteger('id_syarat_sidang');
            $table->string('Persyaratan', 250);
            $table->char('status_lengkap', 1); // y/t
            $table->string('link_file', 2000)->nullable();
            $table->timestamp('tgl_buat')->useCurrent();
            $table->timestamp('tgl_update')->useCurrent();

            $table->foreign('id_judul')->references('id')->on('T_judul')->onDelete('cascade');
            $table->foreign('id_syarat_sidang')->references('id')->on('T_Syarat_sidang')->onDelete('cascade');
        });

        // 9. T_tim_sidang
        Schema::create('T_tim_sidang', function (Blueprint $table) {
            $table->id();
            $table->string('tahapan_sidang', 100);
            $table->unsignedBigInteger('id_judul');
            $table->string('status_tim_sidang', 250);
            $table->unsignedBigInteger('id_user_penilai');
            $table->string('nip', 50);
            $table->string('Nama', 250);
            $table->timestamp('tgl_create')->useCurrent();
            $table->timestamp('tgl_update')->useCurrent();

            $table->foreign('id_judul')->references('id')->on('T_judul')->onDelete('cascade');
            $table->foreign('id_user_penilai')->references('id')->on('T_User')->onDelete('cascade');
        });

        // 10. T_penilaian
        Schema::create('T_penilaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ajuan');
            $table->unsignedBigInteger('id_judul');
            $table->string('Judul', 500);
            $table->string('Nim', 50);
            $table->string('nama_mhs', 250);
            $table->string('tahapan_sidang', 100);
            $table->unsignedBigInteger('id_tim_sidang');
            $table->unsignedBigInteger('id_user_penilai');
            $table->string('status_tim_sidang', 250);
            $table->string('nip', 50);
            $table->string('Nama', 250);
            $table->unsignedBigInteger('id_penilaian'); // references T_point_penilaian
            $table->string('nama_penilaian', 250);
            $table->decimal('Nilai', 5, 2)->nullable();
            $table->string('catatan', 500)->nullable();
            $table->char('status_submit', 1); // y/t
            $table->timestamp('tgl_create')->useCurrent();
            $table->timestamp('tgl_update')->useCurrent();
            $table->unsignedBigInteger('id_user_create');
            $table->string('nama_user_create', 250);

            $table->foreign('id_ajuan')->references('id')->on('T_ajuan_sidang')->onDelete('cascade');
            $table->foreign('id_judul')->references('id')->on('T_judul')->onDelete('cascade');
            $table->foreign('id_tim_sidang')->references('id')->on('T_tim_sidang')->onDelete('cascade');
            $table->foreign('id_user_penilai')->references('id')->on('T_User')->onDelete('cascade');
            $table->foreign('id_penilaian')->references('id')->on('T_point_penilaian')->onDelete('cascade');
            $table->foreign('id_user_create')->references('id')->on('T_User')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('T_penilaian');
        Schema::dropIfExists('T_tim_sidang');
        Schema::dropIfExists('T_cek_persyaratan');
        Schema::dropIfExists('T_ajuan_sidang');
        Schema::dropIfExists('T_judul_temp');
        Schema::dropIfExists('T_judul');
        Schema::dropIfExists('T_point_penilaian');
        Schema::dropIfExists('T_Syarat_sidang');
        Schema::dropIfExists('T_tahapan');
        Schema::dropIfExists('T_User');
        Schema::dropIfExists('T_prodi');
    }
};
