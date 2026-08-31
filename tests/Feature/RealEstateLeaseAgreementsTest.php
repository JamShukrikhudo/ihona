<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Application\CreateLeaseAgreement;
use Liberu\RealEstate\Lettings\Application\RenewLeaseAgreement;
use Liberu\RealEstate\Lettings\Application\ServeLeaseNotice;
use Liberu\RealEstate\Lettings\Domain\LeaseAgreementStatus;
use Liberu\RealEstate\Lettings\Models\LeaseAgreement;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('persists team-scoped tenancy agreements and signing state', function (): void {
    expect(Schema::hasTable('real_estate_lease_agreements'))->toBeTrue();
    $agreement = app(CreateLeaseAgreement::class)->handle(10, ['property_id' => 1, 'tenant_party_id' => 2, 'start_date' => '2026-09-01', 'end_date' => '2027-08-31', 'monthly_rent' => 1500, 'landlord_signed' => true, 'tenant_signed' => true]);

    expect($agreement->status)->toBe(LeaseAgreementStatus::Draft)->and($agreement->isFullySigned())->toBeTrue()->and(LeaseAgreement::query()->forTeam(11)->count())->toBe(0);
});

it('supports renewals and notices through explicit lifecycle actions', function (): void {
    $agreement = app(CreateLeaseAgreement::class)->handle(10, ['property_id' => 1, 'start_date' => '2026-09-01', 'end_date' => '2027-08-31', 'monthly_rent' => 1500, 'status' => 'active']);
    $renewal = app(RenewLeaseAgreement::class)->handle($agreement, 10, ['start_date' => '2027-09-01', 'end_date' => '2028-08-31', 'monthly_rent' => 1600]);
    expect($agreement->refresh()->status)->toBe(LeaseAgreementStatus::Renewed)->and($renewal->renewal_of_id)->toBe($agreement->getKey());
    $noticed = app(ServeLeaseNotice::class)->handle($renewal->forceFill(['status' => 'active']), 10, ['notice_type' => 'tenant', 'notice_served_at' => '2027-06-01', 'notice_expires_at' => '2027-08-31', 'end_reason' => 'Moving']);
    expect($noticed->status)->toBe(LeaseAgreementStatus::NoticeServed);
    expect(fn () => app(RenewLeaseAgreement::class)->handle($agreement, 11, ['start_date' => '2027-09-01', 'end_date' => '2028-08-31', 'monthly_rent' => 1600]))->toThrow(NotFoundHttpException::class);
    expect(fn () => app(CreateLeaseAgreement::class)->handle(10, ['property_id' => 1, 'start_date' => '2027-09-01', 'end_date' => '2027-08-31', 'monthly_rent' => 100]))->toThrow(ValidationException::class);
});

it('exposes tenancy agreement lifecycle endpoints with team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/tenancy-agreements', ['property_id' => 1, 'start_date' => '2026-09-01', 'end_date' => '2027-08-31', 'monthly_rent' => 1500, 'status' => 'active']);
    $create->assertCreated()->assertJsonPath('data.status', 'active');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/tenancy-agreements/'.$id.'/notice', ['notice_type' => 'mutual', 'notice_served_at' => '2027-06-01', 'notice_expires_at' => '2027-08-31'])->assertOk()->assertJsonPath('data.status', 'notice_served');
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->getJson('/api/v1/real-estate/tenancy-agreements/'.$id)->assertNotFound();
});
