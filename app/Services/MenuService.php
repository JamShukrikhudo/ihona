<?php

namespace App\Services;

use App\Models\Menu;
use Spatie\Menu\Laravel\Menu as SpatieMenu;
use Spatie\Menu\Laravel\Link;

class MenuService
{
    public function buildMenu()
    {
        $menuItems = Menu::whereNull('parent_id')->orderBy('order')->get();

        // Every public menu link is styled from here — the two navbar templates
        // both render this, so the Survey Sheet tokens go on once.
        $menu = SpatieMenu::new()
            ->addClass('lg:flex lg:items-center lg:gap-6')
            ->addItemClass('block rounded-tag px-3 py-2 text-body-s text-ink-700 transition-colors duration-[160ms] hover:bg-sheet-200 hover:text-ink-900 lg:px-0 lg:py-0 lg:hover:bg-transparent');

        $this->createMenuItems($menuItems)->each(function ($item) use ($menu) {
            $menu->add($item);
        });

        return $menu;
    }

    private function createMenuItems($items)
    {
        return $items->map(function ($item) {
            if ($item->children->count() > 0) {
                $submenu = SpatieMenu::new()
                    ->addClass('absolute right-0 mt-2 w-48 rounded-sheet border border-sheet-300 bg-sheet-000 py-1 shadow-lift-2')
                    ->addItemClass('block px-4 py-2 text-body-s text-ink-700 hover:bg-sheet-200 hover:text-ink-900');
    
                $this->createMenuItems($item->children)->each(function ($subItem) use ($submenu) {
                    $submenu->add($subItem);
                });
    
                return SpatieMenu::new()
                    ->add(Link::to($item->url, $item->name)->addClass('relative group'))
                    ->add($submenu->addClass('hidden group-hover:block'));
            }
    
            return Link::to($item->url, $item->name);
        });
    }
}
