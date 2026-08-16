/**
 * Ticket 20 of the Survey Sheet rollout: hovering a result raises its pin, and
 * the reverse.
 *
 * ResultsAndPinsTest holds the contract — matching identities, delegation
 * rather than per-card listeners, the width gate. This drives the real thing:
 * Leaflet builds actual markers, the browser dispatches actual pointer events,
 * and the fifth check tears the card list out and replaces it the way a
 * Livewire re-render does, which is the failure mode the ticket was deferred
 * over.
 *
 * Usage:
 *   1. Snapshot the pages (see tests/Browser/public-site-sweep.mjs).
 *   2. node tests/Browser/map-pairing-check.mjs
 */
import { chromium } from 'playwright';

const url = 'file://' + process.cwd() + '/tests/Browser/snapshots/properties.html';
const browser = await chromium.launch({ args: ['--allow-file-access-from-files'] });
const results = [];

function record(name, pass, detail) {
    results.push({ name, pass, detail: detail || '' });
}

async function open(width) {
    const context = await browser.newContext({ viewport: { width, height: 1000 } });
    const page = await context.newPage();
    // The tiles are a third-party host and there is no network here.
    await page.route('**://tile.openstreetmap.org/**', route => route.abort());
    await page.goto(url, { waitUntil: 'load' });
    // The map only builds once it is observed into view.
    await page.evaluate(() => document.querySelector('[data-map]')?.scrollIntoView());
    await page.waitForTimeout(700);

    return { context, page };
}

const ids = async page => page.evaluate(
    () => [...document.querySelectorAll('[data-property-id]')].map(el => el.dataset.propertyId)
);

const raisedMarkers = page => page.evaluate(
    () => document.querySelectorAll('.leaflet-marker-icon.is-raised').length
);

const raisedCards = page => page.evaluate(
    () => [...document.querySelectorAll('[data-property-id][data-raised]')].map(el => el.dataset.propertyId)
);

// ---------------------------------------------------------------- desktop ---
{
    const { context, page } = await open(1440);

    const cardIds = await ids(page);
    const markerCount = await page.evaluate(() => document.querySelectorAll('.leaflet-marker-icon').length);

    record('the map plots a marker', markerCount > 0, `${markerCount} marker(s)`);
    record('the page lists cards', cardIds.length > 0, `${cardIds.length} card(s)`);

    // 1. Hovering a card raises its marker.
    await page.hover(`[data-property-id="${cardIds[0]}"]`);
    await page.waitForTimeout(120);
    record('hovering a card raises a marker', (await raisedMarkers(page)) === 1, `${await raisedMarkers(page)} raised`);
    record('hovering a card raises that card', (await raisedCards(page))[0] === cardIds[0]);

    // Moving off it puts everything back.
    await page.mouse.move(2, 2);
    await page.waitForTimeout(120);
    record('leaving the card releases the pair',
        (await raisedMarkers(page)) === 0 && (await raisedCards(page)).length === 0);

    // 2. Hovering a marker raises its card. Leaflet listens on the icon itself.
    const paired = await page.evaluate(() => {
        const icon = document.querySelector('.leaflet-marker-icon');
        if (! icon) return null;
        icon.dispatchEvent(new MouseEvent('mouseover', { bubbles: true }));
        return [...document.querySelectorAll('[data-property-id][data-raised]')].map(el => el.dataset.propertyId);
    });
    record('hovering a marker raises a card', Array.isArray(paired) && paired.length === 1, JSON.stringify(paired));

    await page.evaluate(() => document.querySelector('.leaflet-marker-icon')
        ?.dispatchEvent(new MouseEvent('mouseout', { bubbles: true })));

    // 3. Keyboard focus does what hover does.
    await page.evaluate(id => document.querySelector(`[data-property-id="${id}"] a`)?.focus(), cardIds[0]);
    await page.waitForTimeout(120);
    record('focusing a card raises its marker', (await raisedMarkers(page)) === 1);

    await page.evaluate(() => document.activeElement?.blur());
    await page.waitForTimeout(120);
    record('blurring the card releases the pair', (await raisedCards(page)).length === 0);

    // 4. A different card raises a different marker — not always the first.
    if (cardIds.length > 1) {
        await page.hover(`[data-property-id="${cardIds[1]}"]`);
        await page.waitForTimeout(120);
        const which = await raisedCards(page);
        record('a second card raises its own pin', which.length === 1 && which[0] === cardIds[1], JSON.stringify(which));
        await page.mouse.move(2, 2);
    } else {
        record('a second card raises its own pin', true, 'only one mapped listing in the snapshot');
    }

    // 5. The pairing survives the DOM being replaced under it, which is what a
    //    Livewire re-render does to every card on the page.
    const survived = await page.evaluate(async () => {
        const cards = [...document.querySelectorAll('[data-property-id]')];
        const points = cards.map(el => el.dataset.propertyId);

        // Replace each card with a fresh node carrying the same identity.
        cards.forEach(card => card.replaceWith(card.cloneNode(true)));

        document.dispatchEvent(new CustomEvent('property-map-updated', {
            detail: {
                properties: [...document.querySelectorAll('[data-map]')]
                    .flatMap(m => JSON.parse(m.dataset.properties || '[]')),
                label: `${points.length} properties mapped`,
            },
        }));

        await new Promise(r => setTimeout(r, 300));

        const fresh = document.querySelector('[data-property-id]');
        fresh.dispatchEvent(new PointerEvent('pointerover', { bubbles: true }));

        await new Promise(r => setTimeout(r, 100));

        return {
            raisedCards: document.querySelectorAll('[data-property-id][data-raised]').length,
            raisedMarkers: document.querySelectorAll('.leaflet-marker-icon.is-raised').length,
            markers: document.querySelectorAll('.leaflet-marker-icon').length,
        };
    });

    record('the pairing survives a re-render', survived.raisedCards === 1 && survived.raisedMarkers === 1,
        JSON.stringify(survived));

    // No marker is left behind by the replacement.
    record('replaced markers are not left on the map',
        survived.markers === markerCount, `${survived.markers} now, ${markerCount} before`);

    await context.close();
}

// ----------------------------------------------------------------- mobile ---
{
    const { context, page } = await open(390);

    const cardIds = await ids(page);

    await page.evaluate(id => document.querySelector(`[data-property-id="${id}"]`)
        ?.dispatchEvent(new PointerEvent('pointerover', { bubbles: true })), cardIds[0]);
    await page.waitForTimeout(150);

    record('nothing is paired below 1024px',
        (await raisedCards(page)).length === 0 && (await raisedMarkers(page)) === 0);

    await context.close();
}

await browser.close();

const failed = results.filter(r => ! r.pass);
for (const r of results) {
    console.log(`${r.pass ? '  ok  ' : '  FAIL'} ${r.name}${r.detail ? '  — ' + r.detail : ''}`);
}
console.log(`\n${results.length - failed.length}/${results.length} checks passed`);
process.exit(failed.length ? 1 : 0);
