<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_applications', function (Blueprint $table) {
            $table->foreignId('applicant_id')->nullable()->after('tenant_id')->constrained('contacts')->nullOnDelete();
            $table->json('guarantors')->nullable()->after('desired_move_in_date');
            $table->json('employer_reference')->nullable()->after('guarantors');
            $table->json('landlord_reference')->nullable()->after('employer_reference');
            $table->string('affordability_status')->nullable()->after('rental_history_status');
            $table->string('right_to_rent_status')->nullable()->after('affordability_status');
            $table->timestamp('screening_consent_at')->nullable()->after('right_to_rent_status');
            $table->timestamp('submitted_at')->nullable()->after('screening_consent_at');
            $table->timestamp('decided_at')->nullable()->after('submitted_at');
            $table->foreignId('decided_by')->nullable()->after('decided_at')->constrained('users')->nullOnDelete();
            $table->text('decision_notes')->nullable()->after('decided_by');

            $table->index(['team_id', 'status', 'application_date'], 'rental_applications_team_status_date_index');
            $table->index(['team_id', 'applicant_id'], 'rental_applications_team_applicant_index');
        });
    }

    public function down(): void
    {
        Schema::table('rental_applications', function (Blueprint $table) {
            $table->dropIndex('rental_applications_team_status_date_index');
            $table->dropIndex('rental_applications_team_applicant_index');
            $table->dropConstrainedForeignId('decided_by');
            $table->dropConstrainedForeignId('applicant_id');
            $table->dropColumn([
                'guarantors',
                'employer_reference',
                'landlord_reference',
                'affordability_status',
                'right_to_rent_status',
                'screening_consent_at',
                'submitted_at',
                'decided_at',
                'decision_notes',
            ]);
        });
    }
};
