                            @if($property->hasMedia('3d_models'))
                                <div class="mt-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            <svg class="w-5 h-5 inline mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            3D Property Model
                                        </h3>
                                        @if($arTourAvailable)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                                </svg>
                                                AR Available
                                            </span>
                                        @endif
                                    </div>
                                    <model-viewer
                                        loading="lazy"
                                        reveal="auto"
                                        src="{{ $property->getFirstMediaUrl('3d_models') }}"
                                        alt="3D model of {{ $property->title }}"
                                        @if($arTourAvailable && $arTourConfig)
                                            ar
                                            ar-modes="{{ implode(' ', $arTourConfig['ar_modes'] ?? ['webxr', 'scene-viewer', 'quick-look']) }}"
                                            ar-scale="{{ $arTourConfig['placement_guide'] ?? 'auto' }}"
                                            camera-orbit="{{ $arTourConfig['camera_orbit'] ?? '0deg 75deg 2.5m' }}"
                                            min-camera-orbit="{{ $arTourConfig['min_camera_orbit'] ?? 'auto auto 1m' }}"
                                            max-camera-orbit="{{ $arTourConfig['max_camera_orbit'] ?? 'auto auto 10m' }}"
                                            @if($arTourConfig['enable_controls'] ?? true)
                                                camera-controls
                                            @endif
                                            @if($arTourConfig['auto_rotate'] ?? true)
                                                auto-rotate
                                            @endif
                                            shadow-intensity="{{ $arTourConfig['shadow_intensity'] ?? 1 }}"
                                            interaction-prompt="{{ $arTourConfig['interaction_prompt'] ?? 'auto' }}"
                                            @php
                                                $scale = $property->ar_model_scale ?? 1.0;
                                            @endphp
                                            scale="{{ $scale }} {{ $scale }} {{ $scale }}"
                                        @else
                                            camera-controls
                                            touch-action="pan-y"
                                            auto-rotate
                                            shadow-intensity="1"
                                        @endif
                                        class="w-full h-96 bg-gray-100 dark:bg-gray-800 rounded-lg"
                                        style="--poster-color: #f3f4f6;">
                                    </model-viewer>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-center">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                        </svg>
                                        @if($arTourAvailable)
                                            Drag to rotate • Pinch to zoom • <strong>Tap AR icon for immersive augmented reality tour</strong>
                                        @else
                                            Drag to rotate • Pinch to zoom • Tap AR icon for augmented reality
                                        @endif
                                    </p>
                                    @if($arTourAvailable)
                                        <div class="mt-3 p-4 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-lg border border-blue-200 dark:border-gray-600">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">AR Tour Instructions</h4>
                                                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                                        <ul class="list-disc list-inside space-y-1">
                                                            <li>Tap the AR icon (cube with camera) in the bottom right corner</li>
                                                            <li>Point your camera at a flat surface like a floor or table</li>
                                                            <li>The property model will appear in your real space</li>
                                                            <li>Walk around to view the property from all angles</li>
                                                            <li>Use pinch gestures to scale the model</li>
                                                        </ul>
                                                    </div>
                                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                        <strong>Note:</strong> AR features require a compatible mobile device with AR support (ARCore for Android, ARKit for iOS)
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Holographic Tour Section --}}
                            @if($holographicTourAvailable || $property->hasMedia('3d_models') || $property->model_3d_url)
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                        <svg class="w-5 h-5 inline mr-2 text-ink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Holographic Property Tour
                                    </h3>
                                    
                                    @if($holographicTourAvailable)
                                        <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg p-6 border-2 border-purple-200 dark:border-purple-800">
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                                        Experience this property in stunning holographic detail
                                                    </p>
                                                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            360° Interactive View
                                                        </span>
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Multi-Device Support
                                                        </span>
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            4K Resolution
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    Premium Feature
                                                </span>
                                            </div>
                                            
                                            <x-ui.button variant="secondary" class="w-full" wire:click="toggleHolographicViewer">
                                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ __('Launch the holographic tour') }}
                                            </x-ui.button>

                                            <div class="mt-4 pt-4 border-t border-purple-200 dark:border-purple-800">
                                                <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
                                                    Supported on: Looking Glass Display • HoloFan • HoloLamp • Web Viewer
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                Holographic tour not yet available for this property. Generate one now to provide an immersive viewing experience.
                                            </p>
                                            <x-ui.button variant="secondary" wire:click="generateHolographicTour">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                {{ __('Generate a holographic tour') }}
                                            </x-ui.button>
                                        </div>
                                    @endif
                                </div>
                            @endif
