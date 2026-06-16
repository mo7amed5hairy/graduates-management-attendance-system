<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('date_of_birth_year')->nullable()->after('date_of_birth');
        });

        // Convert existing date strings to years
        DB::statement("UPDATE users SET date_of_birth_year = YEAR(STR_TO_DATE(date_of_birth, '%Y-%m-%d')) WHERE date_of_birth IS NOT NULL AND date_of_birth REGEXP '^[0-9]{4}-'");

        // For any remaining non-null values (already numbers converted to dates by MySQL)
        // MySQL stores date '1995-01-01' as string in date column, but YEAR() on it works
        DB::statement("UPDATE users SET date_of_birth_year = YEAR(date_of_birth) WHERE date_of_birth IS NOT NULL AND date_of_birth_year IS NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('date_of_birth_year', 'date_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth_new')->nullable()->after('date_of_birth');
        });

        DB::statement("UPDATE users SET date_of_birth_new = DATE(CONCAT(date_of_birth, '-01-01')) WHERE date_of_birth IS NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('date_of_birth_new', 'date_of_birth');
        });
    }
};
