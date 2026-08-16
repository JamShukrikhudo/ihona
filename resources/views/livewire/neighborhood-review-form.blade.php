<x-review-form
    class="mt-6"
    prefix="neighbourhood-review"
    :rating="$rating"
    :heading="__('Review this neighbourhood')"
    :intro="__('What is it actually like to live here? Say what you would want to have been told.')"
    :title-placeholder="__('Summarise the neighbourhood')"
    :comment-placeholder="__('Noise, neighbours, parking, the walk to the station')"
/>
