<?php
// database/migrations/2025_04_26_add_kicked_to_quiz_attempts.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->boolean('kicked')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn('kicked');
        });
    }
};
