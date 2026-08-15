    <div class="">
        <div class="bg-white dark:bg-gray-900">
            <div class="grid max-w-(--breakpoint-xl) px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
                <div class="mr-auto place-self-center lg:col-span-7">
                    <h1
                        class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white">
                        Properties for Sale & Rent</h1>
                    <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                        Welcome to our comprehensive real estate platform, where you can explore an extensive range of
                        properties available for sale and rent. Our goal is to provide a seamless and efficient experience
                        for
                        buyers, sellers, landlords, and tenants alike. Whether you're looking for your dream home, an
                        investment
                        property, or a rental residence, we have a diverse selection to cater to your needs</p>
                    <a href="/contact"
                        class="inline-flex items-center justify-center px-5 py-3 mr-3 text-base font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                        Contact us
                        <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
                <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
                    <img src="https://static.vecteezy.com/system/resources/previews/025/442/517/original/house-for-rent-real-estate-business-concept-with-houses-tiny-real-estate-agent-or-broker-looking-for-house-in-website-modern-flat-cartoon-style-illustration-on-white-background-vector.jpg"
                        alt="mockup">
                </div>
            </div>


    <div class="container mx-auto px-4 py-8">
        <div class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-12">
            <div class="mx-auto max-w-(--breakpoint-xl) px-0 2xl:px-0">
                <div class="d-flex mb-5">
                    <div class="w-full md:w-1/2">
                        <form class="flex items-center">
                            <label for="simple-search" class="sr-only">Search</label>
                            <div class="relative w-full mr-2">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="simple-search" wire:model.live.debounce.300ms="search"
                                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Looking for a house, apartment, or commercial space? ...">
                            </div>

                            <button type="button"
                                class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                                <svg class="h-3.5 w-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                search
                            </button>
                        </form>
                    </div>
                </div>
                {{-- One card component, used here and on the home page, so a
                     change to the disclosure strip lands everywhere at once. --}}
                <div class="mb-4 grid gap-5 sm:grid-cols-2 md:mb-8 lg:grid-cols-3">
                    @forelse ($properties as $property)
                        <x-property-card :property="$property"
                                         saveable
                                         :saved="$this->isFavorited($property->id)" />
                    @empty
                        <div class="col-span-full rounded-sheet border border-dashed border-sheet-300 bg-sheet-000 p-10 text-center">
                            <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                                {{ __('No homes match these filters') }}
                            </p>
                            <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                                {{ __('Widening the price range or the radius is usually the quickest way back to a full page of results.') }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-12">
            <div class="flex items-center justify-between border-gray-100 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <a href="#"
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                    <a href="#"
                        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>

                    </div>
                    <div>
                        <div class="">
                            {{ $properties->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
