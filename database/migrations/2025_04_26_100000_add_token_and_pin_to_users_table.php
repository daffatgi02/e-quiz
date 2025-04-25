<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login_token', 10)->unique()->nullable()->after('password');
            $table->string('pin', 60)->nullable()->after('login_token');
            $table->boolean('pin_set')->default(false)->after('pin');
            $table->timestamp('token_issued_at')->nullable()->after('pin_set');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['login_token', 'pin', 'pin_set', 'token_issued_at']);
        });
    }
};
