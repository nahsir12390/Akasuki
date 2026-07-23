<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateGroups = DB::table('likes')
            ->select('user_id', 'post_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('user_id', 'post_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            DB::table('likes')
                ->where('user_id', $group->user_id)
                ->where('post_id', $group->post_id)
                ->where('id', '!=', $group->keep_id)
                ->delete();
        }

        Schema::table('likes', function (Blueprint $table) {
            $table->unique(['user_id', 'post_id'], 'likes_user_post_unique');
        });
    }

    public function down(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropUnique('likes_user_post_unique');
        });
    }
};
