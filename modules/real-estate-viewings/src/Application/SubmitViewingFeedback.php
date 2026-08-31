<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Viewings\Application;
use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Viewings\Models\ViewingFeedback;
final class SubmitViewingFeedback { public function handle(ViewingFeedback $feedback,array $data):ViewingFeedback { if($feedback->hasBeenSubmitted())throw ValidationException::withMessages(['feedback'=>'Feedback has already been submitted.']); foreach(['overall_rating','price_rating','condition_rating'] as $k)if(isset($data[$k])&&($data[$k]<1||$data[$k]>5))throw ValidationException::withMessages([$k=>'Rating must be between 1 and 5.']); $feedback->update([...$data,'feedback_submitted_at'=>now()]); return $feedback->refresh(); } }
