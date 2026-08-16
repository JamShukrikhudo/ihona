<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Collection;
use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu as SpatieMenu;

class MenuService
{
    /**
     * Every public menu link is styled from here — the two navbar templates
     * both render this, so the Survey Sheet tokens go on once.
     */
    private const LINK = 'block rounded-tag px-3 py-2 text-body-s text-ink-700 transition-colors duration-[160ms]'
        .' hover:bg-sheet-200 hover:text-ink-900 lg:px-0 lg:py-0 lg:hover:bg-transparent';

    private const SUBMENU_LINK = 'block rounded-tag px-4 py-2 text-body-s text-ink-700 transition-colors'
        .' duration-[160ms] hover:bg-sheet-200 hover:text-ink-900';

    /**
     * The navbar renders the menu twice — the wide bar and the collapsed panel
     * — so the counter is static: a submenu id minted in the first render must
     * not be minted again in the second.
     */
    private static int $submenus = 0;

    /**
     * The navbar renders this twice, for the wide bar and the collapsed panel,
     * and each render walked the tree again: 2 x (1 + N) queries on every
     * public page. The tree is the same both times.
     */
    public function buildMenu()
    {
        $menu = SpatieMenu::new()
            ->addClass('lg:flex lg:items-center lg:gap-8')
            ->addItemClass(self::LINK);

        $this->tree()->each(function (Menu $item) use ($menu) {
            if ($item->children->isEmpty()) {
                $menu->add(Link::to($item->url, $item->name));

                return;
            }

            // Raw html, because the submenu has to be a *descendant* of the
            // element carrying `group`. Building it through nested Spatie menus
            // put it beside that element instead, where `group-hover:` can
            // never see it — so no submenu on the site had ever opened.
            // Flex on the wide bar: the caret button is `lg:static`, so beside a
            // `block` link it wrapped onto a second line and pushed the item taller
            // than its neighbours.
            $menu->html($this->parent($item), ['class' => 'group relative lg:flex lg:items-center lg:gap-1.5']);
        });

        return $menu;
    }

    /**
     * @return Collection<int, Menu>
     */
    private function tree(): Collection
    {
        return once(fn () => Menu::query()
            ->whereNull('parent_id')
            ->with('children.children.children')
            ->orderBy('order')
            ->get());
    }

    private function parent(Menu $item): string
    {
        $id = 'submenu-'.(++self::$submenus);

        return $this->link($item, self::LINK.' pr-14 lg:pr-0')
            .$this->toggle($item, $id)
            .$this->submenu($item->children->sortBy('order')->values(), $id);
    }

    /**
     * The parent keeps its own destination. It is a page in its own right, and
     * a toggle that swallows it strands anyone who wanted it.
     */
    private function link(Menu $item, string $class): string
    {
        return sprintf(
            '<a href="%s" class="%s">%s</a>',
            e($item->url),
            $class,
            e($item->name)
        );
    }

    /**
     * Hover alone strands anyone on a keyboard, and a touch screen has no
     * hover at all. `aria-expanded` is the single source of truth: the script
     * in the navbar keeps it honest, and the class below reads it.
     */
    private function toggle(Menu $item, string $id): string
    {
        return sprintf(
            '<button type="button" data-submenu-toggle aria-expanded="false" aria-controls="%s" aria-label="%s"'
            .' class="absolute right-1 top-1/2 inline-flex h-11 w-11 -translate-y-1/2 items-center justify-center'
            .' rounded-tag text-ink-400 transition-transform duration-[160ms] hover:text-ink-900'
            .' group-has-[[aria-expanded=true]]:rotate-180 lg:static lg:h-auto lg:w-auto lg:translate-y-0">'
            .'<svg viewBox="0 0 10 6" class="h-[6px] w-[10px]" fill="none" aria-hidden="true">'
            .'<path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/>'
            .'</svg></button>',
            $id,
            e(__('Show submenu for :name', ['name' => $item->name]))
        );
    }

    /**
     * Hidden until hovered, or until the toggle reports itself expanded.
     *
     * `aria-expanded` is deliberately the only state the CSS reads. Opening on
     * `:focus-within` as well looked equivalent and was not: Escape returns
     * focus to the toggle, the toggle sits inside the group, and the submenu
     * sprang straight back open. The navbar script sets the attribute on focus
     * instead, which keeps one source of truth and lets Escape win.
     *
     * Below lg it stacks in the flow — an absolutely positioned panel inside
     * the collapsed menu covers the items underneath it.
     *
     * `lg:top-full` is not optional: the parent li is a flex row, so an
     * abspos child with no `top` takes the flex container's corner as its
     * static position and the panel opened over the navbar instead of under it.
     *
     * ponytail: mt-6 is the row's half-leading plus the navbar's p-4, so the
     * panel lands on the bottom border. Retune it if the bar's height changes.
     * The ::before covers exactly that margin — without it the pointer left the
     * group crossing the gap, the panel closed, and no link was ever reachable.
     */
    private function submenu(Collection $children, string $id): string
    {
        return sprintf(
            '<ul id="%s" class="hidden group-hover:block'
            .' group-has-[[aria-expanded=true]]:block mt-1 w-full rounded-sheet border border-sheet-300'
            .' bg-sheet-000 py-1 lg:absolute lg:right-0 lg:top-full lg:z-20 lg:mt-6 lg:w-48 lg:shadow-lift-2'
            ." lg:before:absolute lg:before:inset-x-0 lg:before:-top-6 lg:before:h-6 lg:before:content-['']"
            .'">%s</ul>',
            $id,
            $this->submenuItems($children)
        );
    }

    /**
     * One flat panel, indented by depth, rather than a dropdown hanging off a
     * dropdown. A second nesting level would need its own group name to hover
     * against, and Tailwind's named group variant matches *any* ancestor of
     * that name, so a third level would open with the second.
     *
     * ponytail: flat past depth two — give each level its own reveal if a menu
     * ever genuinely needs cascading panels.
     */
    private function submenuItems(Collection $children, int $depth = 0): string
    {
        return $children->map(function (Menu $child) use ($depth) {
            // Written out, never assembled: Tailwind scans this file as text,
            // so a class built at runtime is a class that is never generated.
            $indent = ['', ' pl-8', ' pl-12'][min($depth, 2)];

            $item = '<li>'.$this->link($child, self::SUBMENU_LINK.$indent).'</li>';

            $nested = $child->children->sortBy('order')->values();

            return $nested->isEmpty()
                ? $item
                : $item.$this->submenuItems($nested, $depth + 1);
        })->implode('');
    }
}
