<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MatchingLivewire\Components;
use Illuminate\Contracts\View\View; use Liberu\RealEstate\Matching\Models\MatchProfile; use Livewire\Component;
final class MatchProfileList extends Component { public string $search=''; public function render():View{$teamId=auth()->user()?->current_team_id;$profiles=$teamId===null?collect():MatchProfile::query()->forTeam($teamId)->when($this->search!=='',fn($query)=>$query->where('subject','like','%'.$this->search.'%'))->latest()->paginate(20);return view('real-estate-matching-livewire::match-profile-list',['profiles'=>$profiles]);} }
