<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id')->unique()->nullable()->after('name');
            $table->string('governorate')->nullable()->after('phone');
            $table->string('university')->nullable()->after('governorate');
            $table->string('faculty')->nullable()->after('university');
            $table->year('graduation_year')->nullable()->after('faculty');
            $table->string('job_status')->nullable()->after('graduation_year');
            $table->json('id_photos')->nullable()->after('address');
            $table->json('residence_proof')->nullable()->after('id_photos');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->text('rejection_reason')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn([
                'national_id', 'governorate', 'university', 'faculty',
                'graduation_year', 'job_status', 'id_photos', 'residence_proof',
                'approval_status', 'rejection_reason', 'approved_at',
            ]);
        });
    }
};
