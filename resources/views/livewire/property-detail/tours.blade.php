                            @if($property->hasVirtualTour())
                            <div class="mt-6">
                                <div class="flex gap-4">
                                    <button wire:click="toggleVirtualTour" 
                                        class="flex-1 flex items-center justify-center py-2.5 px-5 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 focus:ring-4 focus:ring-purple-300 rounded-lg dark:bg-purple-600 dark:hover:bg-purple-700 focus:outline-none dark:focus:ring-purple-800"
                                        title="View 3D virtual tour">
                                        <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        {{ $showVirtualTour ? 'Hide' : 'View' }} Virtual Tour
                                    </button>

                                    @if($property->live_tour_available)
                                    <button wire:click="openScheduleLiveTourModal" 
                                        class="flex-1 flex items-center justify-center py-2.5 px-5 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 focus:ring-4 focus:ring-green-300 rounded-lg focus:outline-none"
                                        title="Schedule a live virtual tour with an agent">
                                        <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M14 6H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1Zm7 11-6-2V9l6-2v10Z" />
                                        </svg>
                                        Schedule Live Tour
                                    </button>
                                    @endif
                                </div>

                                @if($showVirtualTour)
                                <div class="mt-4 bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden" style="height: 480px;">
                                    <div class="w-full h-full">
                                        {!! $property->getVirtualTourEmbed() !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif
