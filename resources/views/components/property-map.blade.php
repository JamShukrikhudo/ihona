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
    <div data-map
         data-properties="{{ json_encode($properties) }}"
         data-currency="{{ app(\App\Settings\GeneralSettings::class)->site_currency }}"
         class="h-[400px] w-full"
         role="application"
         aria-label="{{ __('Map of available properties') }}"></div>

    <div class="flex items-center justify-between gap-3 border-t border-sheet-300 bg-sheet-000 px-3 py-2">
        <p class="font-mono text-annotation uppercase text-ink-400">
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
                var element = document.querySelector('[data-map]');

                if (! element || typeof L === 'undefined') return;

                var map = null;

                function draw() {
                    if (map) return;

                    map = L.map(element, { doubleClickZoom: false }).setView([51.5, -0.09], 10);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    }).addTo(map);

                    var properties = JSON.parse(element.dataset.properties || '[]');
                    var currency = element.dataset.currency || '';
                    var bounds = [];

                    properties.forEach(function (property) {
                        if (! property.latitude || ! property.longitude) return;

                        var point = [property.latitude, property.longitude];
                        bounds.push(point);

                        L.marker(point)
                            .addTo(map)
                            .bindPopup(
                                '<strong>' + property.title + '</strong><br>' +
                                currency + Number(property.price).toLocaleString()
                            );
                    });

                    if (bounds.length) {
                        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                    }
                }

                if ('IntersectionObserver' in window) {
                    var observer = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                draw();
                                observer.disconnect();
                            }
                        });
                    }, { rootMargin: '200px' });

                    observer.observe(element);
                } else {
                    draw();
                }

                // Only on request: a page must not ask where someone is on load.
                var locate = document.querySelector('[data-map-locate]');

                if (locate) {
                    locate.addEventListener('click', function () {
                        draw();
                        map.locate({ setView: true, maxZoom: 15 });
                    });
                }
            })();
        </script>
    @endpush
@endonce
