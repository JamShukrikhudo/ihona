<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Viewings\Application;
use Illuminate\Support\Facades\Mail; use Illuminate\Support\Str; use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Viewings\Models\{Viewing,ViewingFeedback}; use Liberu\RealEstate\Viewings\Mail\ViewingFeedbackRequested;
final class RequestViewingFeedback { public function handle(Viewing $viewing,string $email,string $name):ViewingFeedback { if(!filter_var($email,FILTER_VALIDATE_EMAIL))throw ValidationException::withMessages(['email'=>'A valid email is required.']); if($viewing->status->value!=='completed')throw ValidationException::withMessages(['viewing'=>'Viewing must be completed.']); $feedback=ViewingFeedback::create(['viewing_id'=>$viewing->id,'team_id'=>$viewing->team_id,'property_id'=>$viewing->property_id,'viewer_name'=>$name,'viewer_email'=>$email,'token'=>Str::random(48)]); Mail::to($email)->send(new ViewingFeedbackRequested($feedback)); return $feedback; } }
