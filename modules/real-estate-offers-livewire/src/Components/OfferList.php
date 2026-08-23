<?php
declare(strict_types=1);
namespace Liberu\RealEstate\OffersLivewire\Components;
use Illuminate\Contracts\View\View; use Liberu\RealEstate\Offers\Models\Offer; use Livewire\Component;
final class OfferList extends Component { public string $search=''; public function render():View{$teamId=auth()->user()?->current_team_id;$offers=$teamId===null?collect():Offer::query()->forTeam($teamId)->when($this->search!=='',fn($query)=>$query->where('subject','like','%'.$this->search.'%'))->latest()->paginate(20);return view('real-estate-offers-livewire::offer-list',['offers'=>$offers]);} }
