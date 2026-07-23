<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('media_path')->nullable()->after('content');
            $table->enum('media_type', ['image', 'video'])->nullable()->after('media_path');
            $table->string('thumbnail_path')->nullable()->after('media_type');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['media_path', 'media_type', 'thumbnail_path']);
        });
    }
};