# 10 — Content, news and legal pages

**What to build:** The pages a visitor reads rather than operates: about, services, contact, the news list and article, the calculators, and the legal pages.

Running copy gets a real measure — around 65 characters — and the type scale rather than default Tailwind prose. News items reuse the card treatment. The calculators are forms and follow 09. Legal pages are plain and readable, because that is the point of them.

**Blocked by:** 04, 09

**Status:** done

About, terms and privacy share one `x-prose-page` component rather than three
near-copies: the measure stops at roughly 65 characters whatever the viewport
does, and the type scale replaces default prose. Services and the news list
were rewritten onto the system. Calculators got tokens, tabular result figures
and — new — an assumptions line under every calculated number, because a
figure without its assumptions is a guess in a confident font.

The chatbot widget went with them. It is shared chrome appearing on all 60
sweep combinations and still carried `bg-white`, so it was a white panel on the
night ground.

Clears the calculators and news entries from ticket 24: white on `bg-blue-500`
is 3.8:1, under AA for a button label.

Caught while converting: my first pass at services extracted zero services,
because the copy is bullet lists rather than paragraphs and my pattern looked
for paragraphs. It would have shipped an empty page. All four services and all
twenty points were recovered from git and are asserted present.

- [ ] About, services, terms, privacy and the news article render in the system with a measured column of body copy
- [ ] News list uses the card treatment and shows date and category as mono annotations
- [ ] Contact page form follows 09; submitting it confirms in the interface's voice
- [ ] Calculators present results with tabular figures and label their assumptions
- [ ] Headings form a correct document outline on every page
- [ ] Renders correctly in both themes and at 390px
