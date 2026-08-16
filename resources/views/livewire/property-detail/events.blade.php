                    @if($communityEvents->count() > 0)
                        <div class="w-full mt-8 mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Community Events Calendar
                            </h2>
                            
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <!-- Calendar Navigation -->
                                <div class="flex items-center justify-between mb-6">
                                    <button wire:click="changeMonth('prev')" 
                                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->format('F Y') }}
                                    </h3>
                                    <button wire:click="changeMonth('next')" 
                                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Events List -->
                                <div class="space-y-4">
                                    @foreach($communityEvents as $event)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <div class="flex items-start space-x-4">
                                                <!-- Event Date Badge -->
                                                <div class="flex-shrink-0 w-16 h-16 bg-blue-600 rounded-lg flex flex-col items-center justify-center text-white">
                                                    <span class="text-xs font-semibold uppercase">{{ $event->event_date->format('M') }}</span>
                                                    <span class="text-2xl font-bold">{{ $event->event_date->format('d') }}</span>
                                                </div>
                                                
                                                <!-- Event Details -->
                                                <div class="flex-1">
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                        {{ $event->title }}
                                                    </h4>
                                                    
                                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $event->event_date->format('g:i A') }}
                                                        @if($event->end_date)
                                                            - {{ $event->end_date->format('g:i A') }}
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        {{ $event->location }}
                                                        @if(isset($event->distance_from_property))
                                                            <span class="ml-2 text-xs text-blue-600 dark:text-blue-400">
                                                                ({{ number_format($event->distance_from_property, 1) }} km away)
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($event->category)
                                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mb-2">
                                                            {{ ucfirst($event->category) }}
                                                        </span>
                                                    @endif
                                                    
                                                    @if($event->description)
                                                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
                                                            {{ Str::limit($event->description, 150) }}
                                                        </p>
                                                    @endif
                                                    
                                                    @if($event->organizer)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                            Organized by: {{ $event->organizer }}
                                                        </p>
                                                    @endif
                                                    
                                                    @if($event->website_url)
                                                        <a href="{{ $event->website_url }}" target="_blank" 
                                                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mt-2">
                                                            Learn more
                                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                                        Showing {{ $communityEvents->count() }} upcoming events within 10 km of this property
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
