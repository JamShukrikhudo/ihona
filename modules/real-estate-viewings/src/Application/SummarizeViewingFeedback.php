<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Viewings\Application;
use Liberu\RealEstate\Viewings\Models\ViewingFeedback;
final class SummarizeViewingFeedback { public function handle(int|string $teamId,int|string $propertyId):array { $rows=ViewingFeedback::query()->where('team_id',$teamId)->where('property_id',$propertyId)->whereNotNull('feedback_submitted_at')->get(); return ['total_viewings'=>$rows->count(),'average_overall_rating'=>(float)$rows->avg('overall_rating'),'would_make_offer_count'=>$rows->where('would_make_offer',true)->count(),'interested_viewers'=>$rows->where('interest_level','interested')->count()]; } }
