<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->text('suggestion')->nullable();
            $table->tinyInteger('overall_rating')->nullable();
        });
    }
    public function down()
    {
        Schema::table('responses', function (Blueprint $table) {
            if (Schema::hasColumn('responses', 'suggestion')) {
                $table->dropColumn('suggestion');
            }
            if (Schema::hasColumn('responses', 'overall_rating')) {
                $table->dropColumn('overall_rating');
            }
        });
    }
};
