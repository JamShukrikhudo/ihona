                            @if($property->hasVirtualTour())
                            <div class="mt-6">
                                <div class="flex gap-4">
                                    {{-- The label says what happens, so the title
                                         attribute has nothing left to add. --}}
                                    <x-ui.button variant="secondary" class="flex-1"
                                        wire:click="toggleVirtualTour"
                                        aria-expanded="{{ $showVirtualTour ? 'true' : 'false' }}">
                                        <svg class="size-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        {{ $showVirtualTour ? __('Hide the 3D tour') : __('View the 3D tour') }}
                                    </x-ui.button>

                                    @if($property->live_tour_available)
                                    <x-ui.button variant="secondary" class="flex-1"
                                        wire:click="openScheduleLiveTourModal">
                                        <svg class="size-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M14 6H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1Zm7 11-6-2V9l6-2v10Z" />
                                        </svg>
                                        {{ __('Book a live tour with an agent') }}
                                    </x-ui.button>
                                    @endif
                                </div>

                                @if($showVirtualTour)
                                <div class="mt-4 overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-200" style="height: 480px;">
                                    <div class="w-full h-full">
                                        {!! $property->getVirtualTourEmbed() !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif
