<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ViewingsLivewire\Components;
use Illuminate\Contracts\View\View; use Liberu\RealEstate\Viewings\Models\Viewing; use Livewire\Component;
final class ViewingList extends Component { public string $search=''; public function render():View{$teamId=auth()->user()?->current_team_id;$viewings=$teamId===null?collect():Viewing::query()->forTeam($teamId)->when($this->search!=='',fn($query)=>$query->where('subject','like','%'.$this->search.'%'))->latest('starts_at')->paginate(20);return view('real-estate-viewings-livewire::viewing-list',['viewings'=>$viewings]);} }
