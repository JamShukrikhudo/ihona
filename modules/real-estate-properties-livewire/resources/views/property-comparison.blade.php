<div>
    <section aria-label="Property comparison">
        <h1>Compare properties</h1>

        @if ($properties === [])
            <p>Select two to four properties to compare.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">Feature</th>
                        @foreach ($properties as $property)
                            <th scope="col">
                                {{ $property['title'] }}
                                <button type="button" wire:click="removeProperty('{{ $property['id'] }}')">Remove</button>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comparisonFields as $field)
                        <tr wire:key="comparison-{{ $field }}">
                            <th scope="row">{{ str_replace('_', ' ', ucfirst($field)) }}</th>
                            @foreach ($properties as $property)
                                <td>{{ $property[$field] ?? 'Not supplied' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if (count($propertyIds) < 4)
            <label for="comparison-search">Add a property</label>
            <input id="comparison-search" type="search" wire:model.live="searchTerm" minlength="3" autocomplete="off">
            @if ($searchResults !== [])
                <ul aria-label="Property search results">
                    @foreach ($searchResults as $result)
                        <li wire:key="comparison-result-{{ $result['id'] }}">
                            {{ $result['title'] }}
                            <button type="button" wire:click="addProperty('{{ $result['id'] }}')">Add</button>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
    </section>
</div>
