<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mother_father_name')->nullable()->after('mother_name');
            $table->string('mother_grandfather_name')->nullable()->after('mother_father_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mother_father_name', 'mother_grandfather_name']);
        });
    }
};
