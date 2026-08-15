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

            // The visitor came to see a figure, not a button only staff can press.
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
    
    /**
     * Re-runs the model. Staff only: this supersedes the agency's current row.
     */
    public function generateValuation()
    {
        $user = Auth::user();

        if (! $user || ! $user->hasAnyRole(['staff', 'agent', 'admin', 'super_admin'])) {
            $this->errorMessage = __('Only the agency can re-run this estimate.');

            return;
        }

        if (! $this->property) {
            return;
        }

        $this->isLoading = true;
        $this->errorMessage = '';
        
        try {
            $nnService = app(NeuralNetworkValuationService::class);

            // The property's team, not a hardcoded 1.
            $this->valuation = $nnService->createValuation(
                $this->property,
                $user->id,
                $user->current_team_id ?? $user->teams()->first()->id ?? $this->property->team_id
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
     * Scoped to the property in the URL: the route is public and this took any
     * id, so any property's valuation history was reachable by counting.
     */
    public function viewValuation($valuationId)
    {
        if (! $this->property) {
            return;
        }

        // Type too: staff-entered valuations for the same property carry the
        // valuer's name and notes and are not the agency's to publish.
        $valuation = PropertyValuation::query()
            ->where('id', $valuationId)
            ->where('property_id', $this->property->id)
            ->where('valuation_type', 'neural_network')
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
