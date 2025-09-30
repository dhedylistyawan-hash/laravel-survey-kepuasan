<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->text('options')->nullable();
            $table->string('scale_label_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('questions', 'options')) {
                $table->dropColumn('options');
            }
            if (Schema::hasColumn('questions', 'scale_label_type')) {
                $table->dropColumn('scale_label_type');
            }
        });
    }
};
