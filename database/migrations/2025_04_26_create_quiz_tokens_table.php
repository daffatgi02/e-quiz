<?php
// database/migrations/2025_04_26_create_quiz_tokens_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->boolean('requires_token')->default(false)->after('single_attempt');
            $table->string('quiz_token', 9)->nullable()->unique()->after('requires_token'); // format ABCD-EFGH
            $table->timestamp('token_expires_at')->nullable()->after('quiz_token');
        });

        // Tabel untuk tracking user yang sudah menggunakan token
        Schema::create('quiz_token_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('token_used_at')->nullable(); // Tambahkan nullable()
            $table->timestamps();

            $table->unique(['quiz_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_token_users');

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['requires_token', 'quiz_token', 'token_expires_at']);
        });
    }
};
