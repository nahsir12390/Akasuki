<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('password');
            $table->string('location')->nullable()->after('bio');
            $table->string('website')->nullable()->after('location');
            $table->boolean('email_course_updates')->default(true)->after('website');
            $table->boolean('email_messages')->default(true)->after('email_course_updates');
            $table->boolean('public_profile')->default(true)->after('email_messages');
            $table->boolean('show_online_status')->default(true)->after('public_profile');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'location',
                'website',
                'email_course_updates',
                'email_messages',
                'public_profile',
                'show_online_status'
            ]);
        });
    }
};