/**
 * Ticket 14 of the Survey Sheet rollout: the submenu, driven in a browser.
 *
 * The markup assertions in NavigationSubmenuTest cannot see any of this. Two
 * bugs here passed those tests and failed in Chromium: opening on
 * `:focus-within` made Escape a no-op, because Escape returns focus to the
 * toggle and the toggle sits inside the group; and focus lands before click,
 * so the toggle opened on focus and the click that followed read as a close.
 *
 * Usage:
 *   1. Seed a parent with children and snapshot the home page:
 *
 *      php artisan tinker --execute='
 *        $p = App\Models\Menu::create(["name"=>"Buying","url"=>"/properties","order"=>1]);
 *        App\Models\Menu::create(["name"=>"Mortgages","url"=>"/calculators","parent_id"=>$p->id,"order"=>1]);
 *        App\Models\Menu::create(["name"=>"Surveys","url"=>"/services","parent_id"=>$p->id,"order"=>2]);
 *        $root = "file://".public_path();
 *        $res = app(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::create("/", "GET"));
 *        file_put_contents("tests/Browser/snapshots/menu-probe.html",
 *          str_replace(["\"/build/", "\"/fonts/"], ["\"".$root."/build/", "\"".$root."/fonts/"], $res->getContent()));'
 *
 *   2. node tests/Browser/submenu-check.mjs
 *   3. Delete the seeded rows and tests/Browser/snapshots/menu-probe.html.
 */
import { chromium } from 'playwright';
const b = await chromium.launch({ args:['--allow-file-access-from-files'] });
const p = await b.newPage({ viewport: { width: 1440, height: 900 } });
const errs = [];
p.on('console', m => m.type() === 'error' && errs.push(m.text()));
p.on('pageerror', e => errs.push(String(e)));
await p.goto('file://' + process.cwd() + '/tests/Browser/snapshots/menu-probe.html');

const sub = p.locator('#submenu-1');
const toggle = p.locator('[data-submenu-toggle]').first();
const parentItem = p.locator('li.group').first();
const out = [];
const check = async (label, expected) => {
  const visible = await sub.isVisible();
  out.push(`${visible === expected ? 'ok  ' : 'FAIL'} ${label}: visible=${visible} expected=${expected}`);
};

await check('at rest', false);
await parentItem.hover();
await check('after hover', true);
await p.mouse.move(0, 0);
await check('after leaving', false);

await toggle.click();
await p.mouse.move(0, 0);   // otherwise hover alone would keep it open and prove nothing
await check('after tapping the toggle', true);
out.push((await toggle.getAttribute('aria-expanded')) === 'true' ? 'ok   aria-expanded tracks the toggle' : 'FAIL aria-expanded did not update');
await p.keyboard.press('Escape');
await check('after Escape', false);
out.push(await p.evaluate(() => document.activeElement?.hasAttribute('data-submenu-toggle')) ? 'ok   Escape returns focus to the toggle' : 'FAIL Escape lost focus');

await toggle.click();
await p.mouse.move(0, 0);
await check('reopened', true);
await p.locator('h1, body').first().click({ position: { x: 5, y: 400 }, force: true });
await check('after clicking away', false);

// Keyboard alone: tab to the parent link, submenu opens on focus-within.
await p.evaluate(() => document.querySelector('li.group a').focus());
await check('with the parent link focused', true);

const box = await sub.boundingBox().catch(() => null);
out.push('submenu box @1440: ' + JSON.stringify(box));

// The collapsed panel: no hover there at all, so the toggle is the only way in.
await p.setViewportSize({ width: 390, height: 800 });
await p.locator('#menuToggleButton').click();
const mToggle = p.locator('#menuToggle [data-submenu-toggle]').first();
const mSub = p.locator('#menuToggle ul[id^=submenu-]').first();
out.push(await mSub.isVisible() ? 'FAIL collapsed submenu starts open' : 'ok   collapsed submenu starts closed');
const t44 = await mToggle.boundingBox();
out.push(t44 && t44.width >= 44 && t44.height >= 44 ? 'ok   toggle is a 44px touch target' : 'FAIL toggle too small: ' + JSON.stringify(t44));
await mToggle.click();
out.push(await mSub.isVisible() ? 'ok   opens on tap in the collapsed panel' : 'FAIL tap did not open it');
out.push(await p.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth) ? 'ok   no horizontal scroll at 390' : 'FAIL horizontal scroll at 390');
out.push('console errors: ' + (errs.length ? errs.join(' | ') : 'none'));
console.log(out.join('\n'));
await b.close();
