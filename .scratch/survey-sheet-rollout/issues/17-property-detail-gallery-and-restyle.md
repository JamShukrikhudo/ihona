# 17 — Property detail: gallery, AI labelling and restyle

**What to build:** The rest of the detail page. A visitor sees the floor plan
and any site plan in the same gallery as the photographs rather than as a link
at the bottom; every image in a given context shares one crop ratio; and each
room is captioned by name rather than by filename. Anything a model generated —
a valuation, a description, a staged image — says so on its face and shows its
confidence range.

The page also still carries its old styling. The view is 1,115 lines, which is
why the ticket 07 work stopped at the disclosure panel: restyling it safely
means breaking it into sections first, so each can be moved onto the design
system and checked on its own.

**Blocked by:** 07

**Status:** done

- [x] The view is split into sections small enough to review, with no behaviour change in that step
- [x] Floor plan and site plan appear in the gallery beside the photographs
- [x] One crop ratio per context; each room captioned by name
- [x] Model-generated valuations, descriptions and staged images carry a visible label and, where applicable, a confidence range
- [x] The page renders in both themes and at 390px with no horizontal scroll

## Notes

The page showed exactly **one** photograph, and hid even that one on the dark
theme: the img carried `dark:hidden` with no dark counterpart.

Three separate stores held property pictures and the gallery had to read all
three, so `Property::gallery()` merges them into one ordered list:

- the `images` table (V1 media API, virtual staging) — the only one carrying a
  caption, a type and a staged flag;
- the media-library `images` collection (the admin panel);
- the `floor_plan_image` column, consulted only when nothing newer holds one.

Fixed along the way:

- The staff panel uploaded to a media collection called `property_images`. The
  model registers `images`, and every public view reads `images`. **A property
  photographed by staff showed no photograph anywhere on the site.** Migration
  `2026_08_15_170000` relabels what was already uploaded.
- `Image::$url` returned `asset('storage/…')` for every row regardless of disk.
  The V1 API stores to the private `local` disk, so every image uploaded
  through it produced a URL that 404s. A disk with no public address now
  returns null and the gallery skips what it cannot serve.
- The "Book valuation" button opened a dialog whose body was gated `@if(false)`
  — an empty box with a Decline button and a submit that posted nowhere. Both
  it and the equally dead `scheduleViewingModal` are gone.
- The Energy Efficiency block restated the band and score the disclosure panel
  already carries, in a circle that was not the statutory colour, beside a
  "learn more" link pointing at a modal this page has never rendered.
- The review stars were drawn once *per review*, so four reviews produced
  twenty stars and four "N Reviews" links in a row.
- Median neighbourhood income printed with a hard `$` whatever currency the
  listing was in.

`predicted_roi` now carries a band whose width is the model's own risk score.
A point estimate to two decimal places claims a precision no model has.

**Left for other tickets:** `immersive`, `tours`, `history`, `events` and
`live-tour-modal` are extracted but keep their old markup — they pass contrast
in both themes, and each can now be moved on its own. The valuation page's
"login" link at 3.36:1 is ticket 23.
