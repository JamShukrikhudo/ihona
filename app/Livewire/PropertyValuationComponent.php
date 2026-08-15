<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PropertyValuation;
use App\Services\NeuralNetworkValuationService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PropertyValuationComponent extends Component
{
    public $propertyId;
    public $property;
    public $valuation;
    public $valuationHistory = [];
    public $isLoading = false;
    public $showReport = false;
    public $errorMessage = '';
    
    protected $listeners = ['refreshValuation' => 'loadValuation'];
    
    public function mount($propertyId = null)
    {
        if ($propertyId) {
            $this->propertyId = $propertyId;
            $this->loadProperty();
            $this->loadValuationHistory();

            // Show the most recent estimate rather than an empty page behind a
            // button only a signed-in agent can press. A visitor arriving here
            // asked what the home is worth; the answer already exists.
            $this->valuation = collect($this->valuationHistory)->first();
            $this->showReport = (bool) $this->valuation;
        }
    }
    
    public function loadProperty()
    {
        $this->property = Property::find($this->propertyId);
        
        if (!$this->property) {
            $this->errorMessage = 'Property not found';
        }
    }
    
    public function loadValuationHistory()
    {
        if ($this->property) {
            $this->valuationHistory = PropertyValuation::where('property_id', $this->property->id)
                ->where('valuation_type', 'neural_network')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
    }
    
    public function generateValuation()
    {
        if (!Auth::check()) {
            $this->errorMessage = 'Please login to generate valuations';
            return;
        }
        
        $this->isLoading = true;
        $this->errorMessage = '';
        
        try {
            $nnService = app(NeuralNetworkValuationService::class);
            $user = Auth::user();
            
            $this->valuation = $nnService->createValuation(
                $this->property,
                $user->id,
                $user->current_team_id ?? $user->teams()->first()->id ?? 1
            );
            
            $this->loadValuationHistory();
            $this->showReport = true;
            
            $this->dispatch('valuation-generated', ['success' => true]);
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to generate valuation: ' . $e->getMessage();
            $this->dispatch('valuation-generated', ['success' => false]);
        } finally {
            $this->isLoading = false;
        }
    }
    
    /**
     * Scoped to the property in the URL.
     *
     * This took any id and rendered whatever came back. The route is public, so
     * that handed every visitor the valuation history of every property — and
     * the report carries the valuer's notes and the model's workings — by
     * counting integers.
     */
    public function viewValuation($valuationId)
    {
        if (! $this->property) {
            return;
        }

        $valuation = PropertyValuation::query()
            ->where('id', $valuationId)
            ->where('property_id', $this->property->id)
            ->first();

        if (! $valuation) {
            return;
        }

        $this->valuation = $valuation;
        $this->showReport = true;
    }
    
    public function render()
    {
        return view('livewire.property-valuation');
    }
}
