@props(['properties' => []])

{{--
    Nothing is fetched until the reader scrolls the map into view. Leaflet pulls
    a tile per screenful from a third-party host, which is the heaviest thing on
    any page carrying it, and the map used to sit above the fold.

    The automatic .locate() call is gone with it: asking for someone's location
    the moment a page loads is a permission prompt nobody asked for. It is a
    button now, so the visitor chooses.
--}}
<div class="overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-200">
    <div wire:ignore
         data-map
         data-properties="{{ json_encode($properties) }}"
         data-currency="{{ app(\App\Settings\GeneralSettings::class)->site_currency }}"
         class="h-[400px] w-full"
         role="application"
         aria-label="{{ __('Map of available properties') }}"></div>

    <div class="flex items-center justify-between gap-3 border-t border-sheet-300 bg-sheet-000 px-3 py-2">
        <p data-map-count class="font-mono text-annotation uppercase text-ink-400">
            {{ trans_choice(':count property mapped|:count properties mapped', count($properties), ['count' => count($properties)]) }}
        </p>
        <button type="button" data-map-locate
                class="rounded-tag border border-sheet-300 px-2.5 py-1.5 font-mono text-annotation uppercase text-ink-500 transition-colors duration-[160ms] hover:border-ink-900 hover:text-ink-900">
            {{ __('Show my location') }}
        </button>
    </div>
</div>

@once
    @push('scripts')
        <script type="module">
            (function () {
                if (typeof L === 'undefined') return;

                document.querySelectorAll('[data-map]').forEach(setUp);

                function setUp(element) {
                    var map = null;
                    var markers = [];

                    function draw() {
                        if (map) return;

                        map = L.map(element, { doubleClickZoom: false }).setView([51.5, -0.09], 10);

                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        }).addTo(map);

                        plot(JSON.parse(element.dataset.properties || '[]'));
                    }

                    function plot(properties) {
                        var currency = element.dataset.currency || '';
                        var bounds = [];

                        properties.forEach(function (property) {
                            if (! property.latitude || ! property.longitude) return;

                            var point = [property.latitude, property.longitude];
                            bounds.push(point);

                            // Built as nodes, never concatenated into HTML: a
                            // title is staff-entered and would otherwise be
                            // stored XSS for everyone who opens the marker.
                            var popup = document.createElement('div');
                            var name = document.createElement('strong');
                            name.textContent = property.title || '';
                            popup.appendChild(name);

                            if (property.price !== null && property.price !== undefined && property.price !== '') {
                                popup.appendChild(document.createElement('br'));
                                popup.appendChild(document.createTextNode(
                                    (property.currency || currency) + Number(property.price).toLocaleString()
                                ));
                            }

                            markers.push(L.marker(point).addTo(map).bindPopup(popup));
                        });

                        if (bounds.length) {
                            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                        }
                    }

                    if ('IntersectionObserver' in window) {
                        var observer = new IntersectionObserver(function (entries) {
                            entries.forEach(function (entry) {
                                if (! entry.isIntersecting) return;

                                draw();
                                observer.disconnect();
                            });
                        }, { rootMargin: '200px' });

                        observer.observe(element);
                    } else {
                        draw();
                    }

                    // wire:ignore keeps Leaflet's DOM out of Livewire's hands,
                    // which also means a re-render cannot update the pins. The
                    // component pushes the new set instead, or the map would
                    // keep showing the pre-filter results beside a filtered list.
                    document.addEventListener('property-map-updated', function (event) {
                        var points = (event.detail && event.detail.properties) || [];

                        element.dataset.properties = JSON.stringify(points);

                        var counter = element.parentElement.querySelector('[data-map-count]');

                        if (counter) {
                            counter.textContent = event.detail.label || '';
                        }

                        if (! map) return;

                        markers.forEach(function (marker) { map.removeLayer(marker); });
                        markers = [];
                        plot(points);
                    });

                    // Only on request: a page must not ask where someone is on load.
                    var locate = element.parentElement.querySelector('[data-map-locate]');

                    if (locate) {
                        locate.addEventListener('click', function () {
                            draw();
                            map.locate({ setView: true, maxZoom: 15 });
                        });
                    }
                }
            })();
        </script>
    @endpush
@endonce
