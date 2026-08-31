<div>
    @if ($submitted)
        <p role="status">Thank you. We will be in touch soon.</p>
    @endif

    <form wire:submit="submit">
        <label for="contact-name">Name</label>
        <input id="contact-name" wire:model="name" autocomplete="name" required>
        @error('name') <p role="alert">{{ $message }}</p> @enderror

        <label for="contact-email">Email</label>
        <input id="contact-email" type="email" wire:model="email" autocomplete="email" required>
        @error('email') <p role="alert">{{ $message }}</p> @enderror

        <label for="contact-phone">Phone</label>
        <input id="contact-phone" type="tel" wire:model="phone" autocomplete="tel">

        <label for="contact-interest">I am interested in</label>
        <select id="contact-interest" wire:model="interest">
            <option value="">Please select</option>
            <option value="buying">Buying</option>
            <option value="selling">Selling</option>
            <option value="renting">Renting</option>
            <option value="letting">Letting</option>
            <option value="other">Other</option>
        </select>

        <label for="contact-message">Message</label>
        <textarea id="contact-message" wire:model="message" required></textarea>
        @error('message') <p role="alert">{{ $message }}</p> @enderror

        <button type="submit" wire:loading.attr="disabled">Send message</button>
    </form>
</div>
