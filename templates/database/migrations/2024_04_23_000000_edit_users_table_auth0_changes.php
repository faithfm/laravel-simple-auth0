<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditUsersTableAuth0Changes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // NOTE: separate table transactions required for SQLite compatibility (can't perform multiple drop operations in one operation)

        // Add 'sub' column
        Schema::table('users', function (Blueprint $table) {
            $table->string('sub')->after('id')
                ->unique()
                ->nullable()
                ->default(null);
        });

        // Remove unique constraint from 'email' column
        Schema::table('users', function (Blueprint $table) {
            // $table->dropUnique('email');                 // required for MySQL?
            $table->dropUnique('users_email_unique');
        });

        // Drop 'password' column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password');
        });
        
        // Drop 'email_verified_at' column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
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
            $table->dropColumn('sub');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->after('email_verified_at');
        });
    }
}