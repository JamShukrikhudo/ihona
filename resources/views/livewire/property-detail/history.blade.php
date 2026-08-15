                    @if($propertyHistory->count() > 0 || $priceHistory->count() > 0 || $salesHistory->count() > 0)
                        <div class="w-full mt-8 mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Property History</h2>
                            
                            {{-- Price History --}}
                            @if($priceHistory->count() > 0)
                                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                        </svg>
                                        Price Changes
                                    </h3>
                                    <div class="space-y-3">
                                        @foreach($priceHistory as $history)
                                            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $history->event_date->format('M d, Y') }}</p>
                                                    <p class="text-gray-900 dark:text-white font-medium">
                                                        {{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }} {{ number_format($history->old_price, 2) }}
                                                        <svg class="w-4 h-4 inline mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                        </svg>
                                                        {{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }} {{ number_format($history->new_price, 2) }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    @php
                                                        $change = $history->getPriceChangePercentage();
                                                        $isIncrease = $change >= 0;
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isIncrease ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                        {{ $isIncrease ? '+' : '' }}{{ number_format($change, 2) }}%
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Sales History --}}
                            @if($salesHistory->count() > 0)
                                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Past Sales
                                    </h3>
                                    <div class="space-y-3">
                                        @foreach($salesHistory as $sale)
                                            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                                <div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $sale->event_date->format('M d, Y') }}</p>
                                                    <p class="text-gray-900 dark:text-white">{{ $sale->description }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-bold text-green-600">
                                                        {{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }} {{ number_format($sale->new_price, 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- General History Timeline --}}
                            @if($propertyHistory->count() > 0)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Activity Timeline
                                    </h3>
                                    <div class="space-y-4">
                                        @foreach($propertyHistory as $event)
                                            <div class="flex gap-4">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                                        {{ $event->event_type === 'sale' ? 'bg-green-100 dark:bg-green-800' : 
                                                           ($event->event_type === 'price_change' ? 'bg-blue-100 dark:bg-blue-800' : 
                                                           ($event->event_type === 'status_change' ? 'bg-yellow-100 dark:bg-yellow-800' : 'bg-gray-100 dark:bg-gray-700')) }}">
                                                        @if($event->event_type === 'sale')
                                                            <svg class="w-5 h-5 text-green-600 dark:text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @elseif($event->event_type === 'price_change')
                                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                                            </svg>
                                                        @elseif($event->event_type === 'status_change')
                                                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $event->event_date->format('M d, Y') }}</p>
                                                    <p class="text-gray-900 dark:text-white font-medium">{{ $event->description }}</p>
                                                    @if($event->event_type === 'update' && $event->changes)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            Updated fields: {{ implode(', ', array_keys($event->changes)) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
