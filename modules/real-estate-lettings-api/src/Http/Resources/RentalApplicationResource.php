<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class RentalApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'party_id', 'applicant_user_id', 'status', 'employment_status', 'annual_income', 'monthly_income', 'application_date', 'desired_move_in_date', 'lease_end_date', 'background_check_status', 'credit_report_status', 'rental_history_status', 'affordability_status', 'right_to_rent_status', 'guarantors', 'employer_reference', 'landlord_reference', 'screening_consent_at', 'submitted_at', 'decided_at', 'decided_by', 'decision_notes', 'ethereum_address', 'created_at', 'updated_at']);
    }
}
