// database/migrations/xxxx_xx_xx_add_image_to_questions_and_options.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('question');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('option');
            $table->integer('order')->default(0)->after('is_correct');
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'order']);
        });
    }
};
