<?php
declare(strict_types=1);
namespace Liberu\RealEstate\InstructionsLivewire\Components;
use Illuminate\Contracts\View\View; use Liberu\RealEstate\Instructions\Models\Instruction; use Livewire\Component;
final class InstructionList extends Component { public string $search=''; public function render():View{$teamId=auth()->user()?->current_team_id;$instructions=$teamId===null?collect():Instruction::query()->forTeam($teamId)->when($this->search!=='',fn($query)=>$query->where('subject','like','%'.$this->search.'%'))->latest()->paginate(20);return view('real-estate-instructions-livewire::instruction-list',['instructions'=>$instructions]);} }
