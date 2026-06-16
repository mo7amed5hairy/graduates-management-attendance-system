<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mother_name')->nullable()->after('name');
            $table->string('social_status')->nullable()->after('job_status');
            $table->unsignedTinyInteger('children_count')->nullable()->after('social_status');
            $table->integer('date_of_birth')->nullable()->after('age');
            $table->foreignId('qualification_id')->nullable()->constrained()->nullOnDelete()->after('date_of_birth');
            $table->foreignId('qualification_faculty_id')->nullable()->constrained('qualification_faculties')->nullOnDelete()->after('qualification_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('qualification_faculty_id');
            $table->dropConstrainedForeignId('qualification_id');
            $table->dropColumn(['mother_name', 'social_status', 'children_count', 'date_of_birth']);
        });
    }
};
