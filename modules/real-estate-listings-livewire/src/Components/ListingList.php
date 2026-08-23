<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ListingsLivewire\Components;
use Illuminate\Contracts\View\View; use Liberu\RealEstate\Listings\Models\Listing; use Livewire\Component;
final class ListingList extends Component { public string $search=''; public function render():View{$teamId=auth()->user()?->current_team_id;$listings=$teamId===null?collect():Listing::query()->forTeam($teamId)->when($this->search!=='',fn($query)=>$query->where('title','like','%'.$this->search.'%'))->latest()->paginate(20);return view('real-estate-listings-livewire::listing-list',['listings'=>$listings]);} }
