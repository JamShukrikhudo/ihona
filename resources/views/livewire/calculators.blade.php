
<div class="container mx-auto px-4 py-8" x-data="{ calculatorType: 'mortgage' }">
    <h1 class="text-3xl font-bold mb-6">Property Calculators</h1>

    <div class="mb-6">
        <button x-on:click="calculatorType = 'mortgage'" class="px-4 py-2 mr-2 mb-2" x-bind:class="calculatorType === 'mortgage' ? 'bg-action text-white' : 'bg-gray-200'">Mortgage Calculator</button>
        <button x-on:click="calculatorType = 'costOfMoving'" class="px-4 py-2 mr-2 mb-2" x-bind:class="calculatorType === 'costOfMoving' ? 'bg-action text-white' : 'bg-gray-200'">Cost of Moving Calculator</button>
        <button x-on:click="calculatorType = 'stampDuty'" class="px-4 py-2 mr-2 mb-2" x-bind:class="calculatorType === 'stampDuty' ? 'bg-action text-white' : 'bg-gray-200'">Stamp Duty Calculator</button>
        <button x-on:click="calculatorType = 'homeValuation'" class="px-4 py-2 mb-2" x-bind:class="calculatorType === 'homeValuation' ? 'bg-action text-white' : 'bg-gray-200'">Home Valuation</button>
    </div>

    <div x-show="calculatorType === 'mortgage'" class="rounded-sheet border border-sheet-300 bg-sheet-000 p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">Mortgage Calculator</h2>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="propertyPrice">
                Property Price (£)
            </label>
            <input wire:model="propertyPrice" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="propertyPrice" type="number" placeholder="Enter property price">
        </div>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="loanAmount">
                Loan Amount (£)
            </label>
            <input wire:model="loanAmount" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="loanAmount" type="number" placeholder="Enter loan amount">
        </div>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="interestRate">
                Interest Rate (%)
            </label>
            <input wire:model="interestRate" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="interestRate" type="number" step="0.01" placeholder="Enter interest rate">
        </div>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="loanTerm">
                Loan Term (years)
            </label>
            <input wire:model="loanTerm" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="loanTerm" type="number" placeholder="Enter loan term">
        </div>
        <div class="flex items-center justify-between">
            <button wire:click="calculateMortgage" class="bg-action hover:bg-action-hover text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                Calculate
            </button>
        </div>
    </div>

    @if ($mortgageResult)
        <div x-show="calculatorType === 'mortgage'" class="rounded-sheet border border-verdigris-600 bg-verdigris-100 p-4 mt-4 text-verdigris-700" role="alert">
            <p class="font-display text-h5 font-bold tracking-tight text-ink-900">Mortgage Calculation Results:</p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Monthly Payment</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($mortgageResult['monthly_payment'], 2) }}</span></p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Total Payment</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($mortgageResult['total_payment'], 2) }}</span></p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Total Interest</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($mortgageResult['total_interest'], 2) }}</span></p>
        
            <p class="mt-3 border-t border-sheet-300 pt-3 text-caption text-ink-500">{{ __("Assumes: A capital-and-interest repayment over the full term, at a rate that does not change. A real offer will differ.") }}</p>
        </div>
    @endif

    <div x-show="calculatorType === 'costOfMoving'" class="rounded-sheet border border-sheet-300 bg-sheet-000 p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">Cost of Moving Calculator</h2>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="propertyValue">
                Property Value (£)
            </label>
            <input wire:model="propertyValue" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="propertyValue" type="number" placeholder="Enter property value">
        </div>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="isFirstTimeBuyer">
                First Time Buyer?
            </label>
            <select wire:model="isFirstTimeBuyer" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="isFirstTimeBuyer">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="movingDistance">
                Moving Distance (miles)
            </label>
            <input wire:model="movingDistance" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="movingDistance" type="number" placeholder="Enter moving distance">
        </div>
        <div class="flex items-center justify-between">
            <button wire:click="calculateCostOfMoving" class="bg-action hover:bg-action-hover text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                Calculate
            </button>
        </div>
    </div>

    @if ($costOfMovingResult)
        <div x-show="calculatorType === 'costOfMoving'" class="rounded-sheet border border-verdigris-600 bg-verdigris-100 p-4 mt-4 text-verdigris-700" role="alert">
            <p class="font-display text-h5 font-bold tracking-tight text-ink-900">Cost of Moving Calculation Results:</p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Estate Agent Fee</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($costOfMovingResult['estate_agent_fee'], 2) }}</span></p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Conveyancing Fee</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($costOfMovingResult['conveyancing_fee'], 2) }}</span></p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Survey Fee</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($costOfMovingResult['survey_fee'], 2) }}</span></p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Removals</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($costOfMovingResult['removals'], 2) }}</span></p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Energy Performance Certificate</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($costOfMovingResult['energy_performance_certificate'], 2) }}</span></p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Total Cost</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($costOfMovingResult['total_cost'], 2) }}</span></p>
        
            <p class="mt-3 border-t border-sheet-300 pt-3 text-caption text-ink-500">{{ __("Assumes: Typical fees for a sale of this value. Your solicitor, surveyor and remover will quote their own.") }}</p>
        </div>
    @endif

    <div x-show="calculatorType === 'stampDuty'" class="rounded-sheet border border-sheet-300 bg-sheet-000 p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">Stamp Duty Calculator</h2>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="propertyValue">
                Property Value (£)
            </label>
            <input wire:model="propertyValue" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="propertyValue" type="number" placeholder="Enter property value">
        </div>
        <div class="mb-4">
            <label class="block text-ink-700 text-sm font-bold mb-2" for="isFirstTimeBuyer">
                First Time Buyer?
            </label>
            <select wire:model="isFirstTimeBuyer" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="isFirstTimeBuyer">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <div class="flex items-center justify-between">
            <button wire:click="calculateStampDuty" class="bg-action hover:bg-action-hover text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                Calculate
            </button>
        </div>
    </div>

    @if ($stampDutyResult)
        <div x-show="calculatorType === 'stampDuty'" class="rounded-sheet border border-verdigris-600 bg-verdigris-100 p-4 mt-4 text-verdigris-700" role="alert">
            <p class="font-display text-h5 font-bold tracking-tight text-ink-900">Stamp Duty Calculation Results:</p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Stamp Duty</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($stampDutyResult['stamp_duty'], 2) }}</span></p>
            <p class="flex justify-between gap-6 py-0.5"><span class="text-ink-500">Effective Tax Rate</span><span class="font-mono font-medium tabular-nums text-ink-900">{{ number_format($stampDutyResult['effective_tax_rate'], 2) }}%</span></p>
        
            <p class="mt-3 border-t border-sheet-300 pt-3 text-caption text-ink-500">{{ __("Assumes: England and Northern Ireland rates for the current year. Scotland and Wales charge differently.") }}</p>
        </div>
    @endif

    <div x-show="calculatorType === 'homeValuation'" class="rounded-sheet border border-sheet-300 bg-sheet-000 p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">Home Valuation Tool</h2>
        <p class="text-ink-500 mb-4">Enter your property details to get an estimated value</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-ink-700 text-sm font-bold mb-2" for="valuationPropertySize">
                    Property Size (sq ft)
                </label>
                <input wire:model="valuationPropertySize" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="valuationPropertySize" type="number" placeholder="e.g., 1500">
            </div>
            
            <div class="mb-4">
                <label class="block text-ink-700 text-sm font-bold mb-2" for="valuationBedrooms">
                    Number of Bedrooms
                </label>
                <input wire:model="valuationBedrooms" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="valuationBedrooms" type="number" placeholder="e.g., 3">
            </div>
            
            <div class="mb-4">
                <label class="block text-ink-700 text-sm font-bold mb-2" for="valuationBathrooms">
                    Number of Bathrooms
                </label>
                <input wire:model="valuationBathrooms" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="valuationBathrooms" type="number" placeholder="e.g., 2">
            </div>
            
            <div class="mb-4">
                <label class="block text-ink-700 text-sm font-bold mb-2" for="valuationYearBuilt">
                    Year Built
                </label>
                <input wire:model="valuationYearBuilt" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="valuationYearBuilt" type="number" placeholder="e.g., 2010">
            </div>
            
            <div class="mb-4">
                <label class="block text-ink-700 text-sm font-bold mb-2" for="valuationPropertyType">
                    Property Type
                </label>
                <select wire:model="valuationPropertyType" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="valuationPropertyType">
                    <option value="detached">Detached</option>
                    <option value="semi-detached">Semi-Detached</option>
                    <option value="terraced">Terraced</option>
                    <option value="apartment">Apartment</option>
                    <option value="bungalow">Bungalow</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-ink-700 text-sm font-bold mb-2" for="valuationCondition">
                    Property Condition
                </label>
                <select wire:model="valuationCondition" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="valuationCondition">
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-ink-700 text-sm font-bold mb-2" for="valuationLocation">
                    Location Quality
                </label>
                <select wire:model="valuationLocation" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="valuationLocation">
                    <option value="prime">Prime</option>
                    <option value="good">Good</option>
                    <option value="average">Average</option>
                    <option value="below-average">Below Average</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-ink-700 text-sm font-bold mb-2" for="valuationBasePrice">
                    Base Price per sq ft (£)
                </label>
                <input wire:model="valuationBasePrice" class="shadow appearance-none border rounded w-full py-2 px-3 text-ink-700 leading-tight focus:outline-none focus:shadow-outline" id="valuationBasePrice" type="number" placeholder="e.g., 3000">
                <p class="text-xs text-ink-500 mt-1">Average price per square foot in your area</p>
            </div>
        </div>
        
        <div class="flex items-center justify-between mt-6">
            <button wire:click="calculateHomeValuation" class="bg-action hover:bg-action-hover text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                Get Home Valuation
            </button>
        </div>
    </div>

    @if ($homeValuationResult)
        <div x-show="calculatorType === 'homeValuation'" class="bg-sheet-000 shadow-md rounded px-8 pt-6 pb-8 mt-4">
            <h3 class="text-xl font-bold mb-4 text-blue-600">Home Valuation Results</h3>
            
            <div class="bg-sheet-100 border-l-4 border-blue-500 p-4 mb-4">
                <p class="font-bold text-2xl text-blue-800">Estimated Value: {{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($homeValuationResult['estimated_value'], 2) }}</p>
                <p class="text-sm text-ink-500 mt-2">Value Range: {{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($homeValuationResult['min_value'], 2) }} - {{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($homeValuationResult['max_value'], 2) }}</p>
                <p class="text-sm text-ink-500">Confidence Level: {{ $homeValuationResult['confidence_level'] }}%</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-sm font-semibold text-ink-700">Property Details</p>
                    <p class="text-sm text-ink-500">Size: {{ number_format($homeValuationResult['property_size']) }} sq ft</p>
                    <p class="text-sm text-ink-500">Bedrooms: {{ $homeValuationResult['bedrooms'] }}</p>
                    <p class="text-sm text-ink-500">Bathrooms: {{ $homeValuationResult['bathrooms'] }}</p>
                    <p class="text-sm text-ink-500">Year Built: {{ $homeValuationResult['year_built'] }} ({{ $homeValuationResult['property_age'] }} years old)</p>
                </div>
                
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-sm font-semibold text-ink-700">Property Characteristics</p>
                    <p class="text-sm text-ink-500">Type: {{ ucfirst($homeValuationResult['property_type']) }}</p>
                    <p class="text-sm text-ink-500">Condition: {{ ucfirst($homeValuationResult['condition']) }}</p>
                    <p class="text-sm text-ink-500">Location: {{ ucfirst($homeValuationResult['location']) }}</p>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm font-semibold text-ink-700 mb-2">Valuation Breakdown</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <p class="text-ink-500">Base Value:</p>
                    <p class="text-ink-900 font-medium">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($homeValuationResult['breakdown']['base_value'], 2) }}</p>
                    
                    <p class="text-ink-500">Type Multiplier:</p>
                    <p class="text-ink-900 font-medium">{{ $homeValuationResult['breakdown']['type_multiplier'] }}x</p>
                    
                    <p class="text-ink-500">Condition Multiplier:</p>
                    <p class="text-ink-900 font-medium">{{ $homeValuationResult['breakdown']['condition_multiplier'] }}x</p>
                    
                    <p class="text-ink-500">Location Multiplier:</p>
                    <p class="text-ink-900 font-medium">{{ $homeValuationResult['breakdown']['location_multiplier'] }}x</p>
                    
                    <p class="text-ink-500">Age Adjustment:</p>
                    <p class="text-ink-900 font-medium">{{ $homeValuationResult['breakdown']['age_adjustment'] }}x</p>
                    
                    <p class="text-ink-500">Room Bonus:</p>
                    <p class="text-ink-900 font-medium">{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}{{ number_format($homeValuationResult['breakdown']['room_bonus'], 2) }}</p>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400">
                <p class="text-xs text-ink-700">
                    <strong>Note:</strong> This is an automated estimate based on the information provided. 
                    For a precise valuation, please contact a professional surveyor or estate agent.
                </p>
            </div>
        
            <p class="mt-3 border-t border-sheet-300 pt-3 text-caption text-ink-500">{{ __("Assumes: An estimate from comparable local sales, not a survey or a formal valuation.") }}</p>
        </div>
    @endif
</div>