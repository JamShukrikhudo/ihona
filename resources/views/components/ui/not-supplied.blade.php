{{--
    A fact the record does not hold.

    Inside the disclosure strip a cell is roughly 60px wide at 11.5px mono, so
    the words "Not supplied" truncate to "Not supp…" and wrap, which breaks the
    column alignment the strip exists for. An em dash is the table convention
    for an absent value, holds the row height, and carries its meaning to
    screen readers and on hover through the label.

    Still never a 0: that would claim the property is free, brand new, or has no
    floor area.
--}}
<span {{ $attributes->class('text-ink-400') }}
      role="img"
      aria-label="{{ __('Not supplied') }}"
      title="{{ __('Not supplied') }}">&mdash;</span>
