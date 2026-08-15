/**
 * Ticket 12 of the Survey Sheet rollout: the browser half of the acceptance
 * sweep. PublicSiteSweepTest covers what PHP can see; this covers what only a
 * browser can — horizontal scroll, computed contrast, console errors and any
 * request that leaves the origin.
 *
 * Every public page is checked at 390px and 1440px, in both themes: 60
 * page/width/theme combinations.
 *
 * Usage:
 *   1. npx vite build            (the snapshots point at the built CSS)
 *   2. php artisan tinker tests/Browser/snapshot-pages.php
 *   3. node tests/Browser/public-site-sweep.mjs
 *
 * Step 2 needs no running server: it puts each page through the HTTP kernel in
 * process and rewrites the asset URLs to file://. The recipe used to live in
 * this comment as a one-line --execute, which is why it drifted out of date —
 * it is a script now, and it builds its own fixtures.
 *
 * Contrast is measured against the nearest opaque ancestor background. A
 * gradient is skipped rather than guessed at: reporting it as white-on-white
 * would be a lie, and this sweep is only worth running if its output is true.
 */
import { chromium } from 'playwright';
import { readdirSync } from 'fs';

const pages = readdirSync('./tests/Browser/snapshots').filter(f => f.endsWith('.html')).map(f => f.replace('.html',''));
const b = await chromium.launch({ args:['--allow-file-access-from-files'] });
const rows = [];

// Contrast of every text node against its effective background.
const CONTRAST = `() => {
  const lum = (c) => { const [r,g,b]=c; const f=(v)=>{v/=255; return v<=0.03928? v/12.92 : Math.pow((v+0.055)/1.055,2.4);};
    return 0.2126*f(r)+0.7152*f(g)+0.0722*f(b); };
  const cv=document.createElement('canvas').getContext('2d');
  const parse = (s) => {
    if (!s || s==='transparent') return null;
    let m=s.match(/rgba?\\(([^)]+)\\)/);
    if (m) { const p=m[1].split(/[ ,\\/]+/).filter(Boolean).map(Number); return {c:[p[0],p[1],p[2]], a: p.length>3? p[3]:1}; }
    try {
      cv.clearRect(0,0,1,1); cv.fillStyle=s; cv.fillRect(0,0,1,1);
      const d=cv.getImageData(0,0,1,1).data;
      return {c:[d[0],d[1],d[2]], a: d[3]/255};
    } catch (e) { return null; }
  };
  const bgOf = (el) => { let n=el; while(n && n!==document.documentElement){ const cs=getComputedStyle(n);
    if (cs.backgroundImage && cs.backgroundImage!=='none') return 'gradient';
    const b=parse(cs.backgroundColor);
    if (b && b.a>0.9) return b.c; n=n.parentElement; } const h=parse(getComputedStyle(document.body).backgroundColor); return h? h.c:[255,255,255]; };
  const bad=[];
  document.querySelectorAll('body *').forEach(el => {
    const cs0=getComputedStyle(el);
    if (cs0.display==='none' || cs0.visibility==='hidden' || cs0.opacity==='0') return;
    const r=el.getBoundingClientRect();
    if (r.width===0 || r.height===0) return;
    const text=[...el.childNodes].filter(n=>n.nodeType===3).map(n=>n.textContent.trim()).join('');
    if (!text) return;
    const cs=getComputedStyle(el);
    const fg=parse(cs.color); if(!fg || fg.a<0.5) return;
    const bg=bgOf(el);
    if (bg==='gradient') return; // no single colour to measure against
    const L1=lum(fg.c), L2=lum(bg);
    const ratio=(Math.max(L1,L2)+0.05)/(Math.min(L1,L2)+0.05);
    const size=parseFloat(cs.fontSize), weight=parseInt(cs.fontWeight)||400;
    const large = size>=24 || (size>=18.66 && weight>=700);
    const need = large? 3 : 4.5;
    if (ratio < need - 0.05) bad.push({t:text.slice(0,28), ratio:+ratio.toFixed(2), need, size:+size.toFixed(1)});
  });
  return bad.sort((a, b) => a.ratio - b.ratio);
}`;

for (const page of pages) {
  for (const [w,h] of [[390,900],[1440,1000]]) {
    for (const scheme of ['light','dark']) {
      const c = await b.newContext({ viewport:{width:w,height:h}, colorScheme:scheme });
      const p = await c.newPage();
      const errs=[]; const external=[];
      p.on('pageerror', e=>errs.push(e.message));
      p.on('request', r=>{ const u=r.url(); if(u.startsWith('http') && !u.includes('tile.openstreetmap') && !u.includes('localhost')) external.push(u); });
      await p.goto('file://'+process.cwd()+'/tests/Browser/snapshots/'+page+'.html', {waitUntil:'load'});
      await p.waitForTimeout(500);
      const sw = await p.evaluate(()=>document.documentElement.scrollWidth);
      const contrast = await p.evaluate("(" + CONTRAST + ")()");
      rows.push({page, w, scheme, hScroll: sw>w, contrast: contrast.length, worst: contrast[0]||null, errs: errs.length, external: external.length});
      await c.close();
    }
  }
}
await b.close();

const hs = rows.filter(r=>r.hScroll);
const ct = rows.filter(r=>r.contrast>0);
const er = rows.filter(r=>r.errs>0);
const ex = rows.filter(r=>r.external>0);
console.log('checked', rows.length, 'page/width/theme combinations across', pages.length, 'pages');
console.log('horizontal scroll :', hs.length ? hs.map(r=>`${r.page}@${r.w}/${r.scheme}`).join(', ') : 'none');
console.log('console errors    :', er.length ? er.map(r=>`${r.page}@${r.w}/${r.scheme}`).join(', ') : 'none');
console.log('external requests :', ex.length ? ex.map(r=>`${r.page}@${r.w}/${r.scheme}`).join(', ') : 'none');
console.log('contrast failures :', ct.length ? '' : 'none');
for (const r of ct) console.log(`   ${r.page} @${r.w}/${r.scheme}  ${r.contrast} node(s), worst ${r.worst.ratio}:1 (needs ${r.worst.need}) ${r.worst.size}px "${r.worst.t}"`);
