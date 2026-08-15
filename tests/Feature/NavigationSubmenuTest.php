<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 14 of the Survey Sheet rollout: menu dropdowns never open.
 *
 * The submenu was rendered as a *sibling* of the link carrying `group`, and
 * `group-hover:` needs `.group` on an ancestor. Nothing revealed it, so every
 * child page in the menu was unreachable from the navigation — and `absolute`
 * had no positioned ancestor either, so it would have landed in the wrong
 * place had it ever shown.
 */
class NavigationSubmenuTest extends TestCase
{
    use RefreshDatabase;

    private function seedParentWithChildren(): Menu
    {
        $parent = Menu::create(['name' => 'Buying', 'url' => '/buying', 'order' => 1]);

        Menu::create(['name' => 'Mortgages', 'url' => '/mortgages', 'parent_id' => $parent->id, 'order' => 1]);
        Menu::create(['name' => 'Surveys', 'url' => '/surveys', 'parent_id' => $parent->id, 'order' => 2]);

        return $parent;
    }

    private function xpath(string $html): \DOMXPath
    {
        $document = new \DOMDocument();

        // The menu is a fragment, and libxml complains about HTML5 either way.
        libxml_use_internal_errors(true);
        $document->loadHTML('<!DOCTYPE html><html><body>'.$html.'</body></html>');
        libxml_clear_errors();

        return new \DOMXPath($document);
    }

    private function menuHtml(): string
    {
        return (string) app(MenuService::class)->buildMenu();
    }

    public function test_the_submenu_nests_inside_the_element_that_carries_group(): void
    {
        $this->seedParentWithChildren();

        $xpath = $this->xpath($this->menuHtml());

        $submenus = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' group ')]//ul");

        $this->assertGreaterThan(
            0,
            $submenus->length,
            'the submenu has to be a descendant of the group, or group-hover can never reveal it'
        );

        // Anything the submenu is positioned against has to be positioned
        // itself, or `absolute` escapes to whichever ancestor happens to be.
        $positioned = $xpath->query(
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' group ')"
            ." and contains(concat(' ', normalize-space(@class), ' '), ' relative ')]"
        );

        $this->assertGreaterThan(0, $positioned->length, 'the group is not a positioning context');
    }

    public function test_the_children_are_reachable_in_the_markup(): void
    {
        $this->seedParentWithChildren();

        $xpath = $this->xpath($this->menuHtml());

        foreach (['/mortgages' => 'Mortgages', '/surveys' => 'Surveys'] as $url => $label) {
            $links = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' group ')]//a[@href='{$url}']");

            $this->assertSame(1, $links->length, "[{$label}] is not inside the parent's submenu");
        }

        // The parent is still its own destination, not just a toggle.
        $this->assertSame(1, $xpath->query("//a[@href='/buying']")->length);
    }

    /**
     * Hover alone strands anyone on a keyboard, and on a touch screen there is
     * no hover at all, so there has to be a control that opens it.
     */
    public function test_the_submenu_has_a_toggle_that_names_what_it_controls(): void
    {
        $this->seedParentWithChildren();

        $xpath = $this->xpath($this->menuHtml());

        $toggles = $xpath->query('//button[@aria-expanded]');

        $this->assertSame(1, $toggles->length, 'a submenu needs a control that opens it without hover');

        $controls = $toggles->item(0)->getAttribute('aria-controls');

        $this->assertNotSame('', $controls, 'the toggle does not say what it opens');
        $this->assertSame(
            1,
            $xpath->query("//ul[@id='{$controls}']")->length,
            "aria-controls points at [{$controls}], which is not a submenu on the page"
        );

        $this->assertStringContainsString(
            'Buying',
            $toggles->item(0)->getAttribute('aria-label'),
            'the toggle has to say which menu it opens'
        );
    }

    public function test_the_submenu_opens_without_a_pointer(): void
    {
        $this->seedParentWithChildren();

        $html = $this->menuHtml();

        $this->assertStringContainsString('group-hover:block', $html, 'hover does not open it');

        // aria-expanded is the only state the stylesheet reads, so that the
        // navbar script can close the submenu with Escape while focus is still
        // inside it. Opening on :focus-within too made Escape a no-op.
        $this->assertStringContainsString(
            'group-has-[[aria-expanded=true]]:block',
            $html,
            'nothing but hover can open the submenu'
        );
        $this->assertStringNotContainsString(
            'group-focus-within:block',
            $html,
            'focus-within reopens the submenu the instant Escape returns focus to the toggle'
        );
    }

    /**
     * The navbar renders the menu twice — the wide bar and the collapsed panel
     * — so ids minted per submenu must not collide across the two.
     */
    public function test_two_renders_do_not_mint_the_same_id(): void
    {
        $this->seedParentWithChildren();

        $xpath = $this->xpath($this->menuHtml().$this->menuHtml());

        $ids = [];

        foreach ($xpath->query('//ul[@id]') as $node) {
            $ids[] = $node->getAttribute('id');
        }

        $this->assertSame($ids, array_unique($ids), 'the same submenu id was minted twice on one page');
    }

    /**
     * A third level used to hang a dropdown off a dropdown, keyed to the
     * *outer* group, so it opened whenever the top-level item was hovered.
     * It now sits in the same panel, indented — still one hover from reach.
     */
    public function test_a_grandchild_is_still_reachable(): void
    {
        $parent = $this->seedParentWithChildren();
        $child = Menu::where('name', 'Mortgages')->sole();

        Menu::create(['name' => 'Remortgage', 'url' => '/remortgage', 'parent_id' => $child->id, 'order' => 1]);

        $xpath = $this->xpath($this->menuHtml());

        $links = $xpath->query(
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' group ')]//a[@href='/remortgage']"
        );

        $this->assertSame(1, $links->length, 'a third-level page is unreachable from the navigation');
        $this->assertStringContainsString(
            'pl-8',
            $links->item(0)->getAttribute('class'),
            'a third-level page reads as a sibling of the second'
        );
    }

    public function test_a_menu_item_without_children_stays_a_plain_link(): void
    {
        Menu::create(['name' => 'Contact', 'url' => '/contact', 'order' => 1]);

        $xpath = $this->xpath($this->menuHtml());

        $this->assertSame(0, $xpath->query('//button')->length);
        $this->assertSame(1, $xpath->query("//a[@href='/contact']")->length);
    }

    public function test_the_navigation_carries_the_submenu_onto_the_page(): void
    {
        $this->seedParentWithChildren();

        $this->get('/')
            ->assertOk()
            ->assertSee('Mortgages')
            ->assertSee('aria-expanded', false);
    }
}
