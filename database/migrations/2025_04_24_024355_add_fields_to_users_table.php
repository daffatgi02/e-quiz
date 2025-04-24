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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik')->unique()->after('name');
            $table->string('position')->after('nik');
            $table->string('department')->after('position');
            $table->boolean('is_admin')->default(false)->after('department');
            $table->boolean('is_active')->default(true)->after('is_admin');
            $table->string('language')->default('en')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'position', 'department', 'is_admin', 'is_active', 'language']);
        });
    }
};
