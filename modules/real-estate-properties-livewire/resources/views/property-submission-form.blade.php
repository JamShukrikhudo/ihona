<section aria-label="Submit a property">
    <h2>Submit a property</h2>

    @if (session('message'))
        <p role="status">{{ session('message') }}</p>
    @endif

    <form wire:submit="submit" enctype="multipart/form-data">
        <label for="property-submission-title">Title</label>
        <input id="property-submission-title" type="text" wire:model="title">
        @error('title') <p>{{ $message }}</p> @enderror

        <label for="property-submission-description">Description</label>
        <textarea id="property-submission-description" wire:model="description"></textarea>
        @error('description') <p>{{ $message }}</p> @enderror
        <button type="button" wire:click="generateAIDescription">Generate description</button>

        <label for="property-submission-location">Location</label>
        <input id="property-submission-location" type="text" wire:model="location">
        @error('location') <p>{{ $message }}</p> @enderror

        <label for="property-submission-price">Price</label>
        <input id="property-submission-price" type="number" wire:model="price" min="0">
        <label for="property-submission-bedrooms">Bedrooms</label>
        <input id="property-submission-bedrooms" type="number" wire:model="bedrooms" min="0">
        <label for="property-submission-bathrooms">Bathrooms</label>
        <input id="property-submission-bathrooms" type="number" wire:model="bathrooms" min="0">
        <label for="property-submission-area">Area (sq ft)</label>
        <input id="property-submission-area" type="number" wire:model="area_sqft" min="0">
        <label for="property-submission-year">Year built</label>
        <input id="property-submission-year" type="number" wire:model="year_built" min="1066">
        @error('year_built') <p>{{ $message }}</p> @enderror

        <label for="property-submission-type">Property type</label>
        <input id="property-submission-type" type="text" wire:model="property_type">

        <label for="property-submission-images">Images</label>
        <input id="property-submission-images" type="file" wire:model="images" accept="image/*" multiple>
        @error('images.*') <p>{{ $message }}</p> @enderror

        <label for="property-submission-video">Video tour</label>
        <input id="property-submission-video" type="file" wire:model="video" accept="video/mp4,video/quicktime">
        @error('video') <p>{{ $message }}</p> @enderror

        <label for="property-submission-custom-description">Additional notes</label>
        <textarea id="property-submission-custom-description" wire:model="customDescription"></textarea>
        <label for="property-submission-tone">Description tone</label>
        <select id="property-submission-tone" wire:model="descriptionTone">
            <option value="professional">Professional</option>
            <option value="casual">Casual</option>
            <option value="luxury">Luxury</option>
        </select>

        @error('submission') <p>{{ $message }}</p> @enderror
        <button type="button" wire:click="preview">Preview property</button>
        <button type="submit">Submit property</button>
    </form>
</section>
