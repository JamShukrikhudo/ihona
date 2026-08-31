<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Application\RequestViewingFeedback;
use Liberu\RealEstate\Viewings\Application\SubmitViewingFeedback;
use Liberu\RealEstate\Viewings\Application\SummarizeViewingFeedback;
use Liberu\RealEstate\Viewings\Models\Viewing;
use Liberu\RealEstate\Viewings\Models\ViewingFeedback;

uses(RefreshDatabase::class);

it('requests and submits token-based viewing feedback', function (): void {
    Mail::fake();
    $viewing = Viewing::query()->create(['team_id' => 1, 'property_id' => 7, 'subject' => 'Viewing', 'status' => 'completed', 'starts_at' => now()->addDay()]);

    $feedback = app(RequestViewingFeedback::class)->handle($viewing, 'viewer@example.com', 'Viewer');
    Mail::assertSentCount(1);
    expect($feedback->token)->toHaveLength(48)->and($feedback->hasBeenSubmitted())->toBeFalse();

    $submitted = app(SubmitViewingFeedback::class)->handle($feedback, ['overall_rating' => 4, 'price_rating' => 3, 'condition_rating' => 5, 'interest_level' => 'interested', 'would_make_offer' => true]);
    expect($submitted->feedback_submitted_at)->not->toBeNull()->and($submitted->getAverageRating())->toBe(4.0);
});

it('rejects invalid or duplicate feedback submissions', function (): void {
    $viewing = Viewing::query()->create(['team_id' => 1, 'property_id' => 7, 'subject' => 'Viewing', 'status' => 'completed', 'starts_at' => now()->addDay()]);
    expect(fn () => app(RequestViewingFeedback::class)->handle($viewing, 'invalid', 'Viewer'))->toThrow(ValidationException::class);
    $feedback = ViewingFeedback::query()->create(['team_id' => 1, 'viewing_id' => $viewing->id, 'property_id' => 7, 'viewer_name' => 'Viewer', 'viewer_email' => 'viewer@example.com', 'token' => 'feedback-token', 'feedback_submitted_at' => now()]);
    expect(fn () => app(SubmitViewingFeedback::class)->handle($feedback, ['overall_rating' => 5]))->toThrow(ValidationException::class);
});

it('summarizes submitted feedback by team and property', function (): void {
    foreach ([4, 3] as $rating) {
        ViewingFeedback::query()->create(['team_id' => 1, 'viewing_id' => $rating, 'property_id' => 7, 'viewer_name' => 'Viewer', 'viewer_email' => $rating.'@example.com', 'token' => 'feedback-'.$rating, 'feedback_submitted_at' => now(), 'overall_rating' => $rating, 'interest_level' => $rating === 4 ? 'interested' : 'not_interested', 'would_make_offer' => $rating === 4]);
    }

    $summary = app(SummarizeViewingFeedback::class)->handle(1, 7);
    expect($summary['total_viewings'])->toBe(2)->and($summary['average_overall_rating'])->toBe(3.5)->and($summary['would_make_offer_count'])->toBe(1)->and($summary['interested_viewers'])->toBe(1);
});
