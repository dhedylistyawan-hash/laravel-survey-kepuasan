<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable();
            $table->string('umur')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('unit_kerja')->nullable();
            $table->string('jabatan_fungsional')->nullable();
        });
    }

    public function down()
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin',
                'umur',
                'pendidikan',
                'unit_kerja',
                'jabatan_fungsional'
            ]);
        });
    }
};
