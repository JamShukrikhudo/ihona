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
         data-currency="{{ app(\App\Settings\GeneralSettings::class)->currencySymbol() }}"
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
                    // Keyed by listing id rather than a bare list: a card and
                    // its pin have to find each other, and the id is the only
                    // thing both sides hold.
                    var markers = {};
                    var raisedId = null;

                    // Below 1024px the map is a collapsed <details> the reader
                    // opens, and a "hover" on a touch screen is a tap on the
                    // way to the listing. Nothing is paired there.
                    var wide = window.matchMedia('(min-width: 1024px)');

                    function cardFor(id) {
                        var selector = window.CSS && CSS.escape ? CSS.escape(String(id)) : String(id);

                        return document.querySelector('[data-property-id="' + selector + '"]');
                    }

                    function clear() {
                        if (raisedId === null) return;

                        var marker = markers[raisedId];

                        if (marker) {
                            marker.setZIndexOffset(0);

                            var icon = marker.getElement();

                            if (icon) icon.classList.remove('is-raised');
                        }

                        var card = cardFor(raisedId);

                        if (card) card.removeAttribute('data-raised');

                        raisedId = null;
                    }

                    function raise(id) {
                        if (! wide.matches || id === null || id === undefined) return;
                        if (String(id) === String(raisedId)) return;

                        clear();

                        raisedId = id;

                        var marker = markers[id];

                        if (marker) {
                            // Above every other pin, or a raised marker can sit
                            // behind the ones plotted after it.
                            marker.setZIndexOffset(1000);

                            var icon = marker.getElement();

                            if (icon) icon.classList.add('is-raised');
                        }

                        var card = cardFor(id);

                        if (card) card.setAttribute('data-raised', '');
                    }

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

                            var marker = L.marker(point).addTo(map).bindPopup(popup);

                            if (property.id !== null && property.id !== undefined) {
                                markers[property.id] = marker;
                                marker.on('mouseover', function () { raise(property.id); });
                                marker.on('mouseout', clear);
                            }
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

                        if (counter && event.detail && event.detail.label) {
                            counter.textContent = event.detail.label;
                        }

                        if (! map) return;

                        // Released while the marker it points at still exists.
                        // Afterwards there is nothing to take the raised class
                        // off, and the card stays lit with no pin to match it.
                        clear();

                        Object.keys(markers).forEach(function (id) {
                            // off() before removeLayer: the handlers close over
                            // this marker, and a marker that is no longer on the
                            // map must not still be listening for a hover.
                            markers[id].off();
                            map.removeLayer(markers[id]);
                        });

                        markers = {};
                        plot(points);
                    });

                    // Delegated from the document, never bound to a card. Cards
                    // live inside Livewire's DOM and are replaced wholesale on
                    // every filter change, page change and debounced keystroke,
                    // so a listener bound to one dies with it — silently, which
                    // is the failure this ticket exists to avoid.
                    document.addEventListener('pointerover', function (event) {
                        var card = event.target.closest && event.target.closest('[data-property-id]');

                        if (card) {
                            raise(card.dataset.propertyId);
                        } else if (! (event.target.closest && event.target.closest('.leaflet-marker-icon'))) {
                            clear();
                        }
                    });

                    // Focus does exactly what hover does. The whole card is one
                    // link, so the event fires on the anchor inside it.
                    document.addEventListener('focusin', function (event) {
                        var card = event.target.closest && event.target.closest('[data-property-id]');

                        if (card) raise(card.dataset.propertyId);
                    });

                    document.addEventListener('focusout', function (event) {
                        var card = event.target.closest && event.target.closest('[data-property-id]');

                        if (card) clear();
                    });

                    // A narrowed window must not leave a pair lit that the
                    // reader can no longer see either half of.
                    if (wide.addEventListener) {
                        wide.addEventListener('change', clear);
                    }

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
