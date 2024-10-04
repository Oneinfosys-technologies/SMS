<?php

namespace App\Services;

use App\Enums\Site\MenuPlacement;
use App\Models\Site\Block;
use App\Models\Site\Menu;
use App\Support\MarkdownParser;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SiteService
{
    use MarkdownParser;

    public function getPage(?string $slug = '')
    {
        $slug = $slug ?? 'Home';

        $menu = Menu::query()
            ->whereSlug($slug)
            ->first();

        if (! $menu) {
            abort(404);
        }

        if (! $menu->page_id) {
            return redirect()->route('app');
        }

        $headerMenus = Menu::query()
            ->wherePlacement(MenuPlacement::HEADER)
            ->with(['children' => function ($query) {
                $query->whereNotNull('page_id')
                    ->orderBy('position', 'asc');
            }])
            ->whereNull('parent_id')
            ->orderBy('position', 'asc')
            ->get();

        $footerMenus = Menu::query()
            ->wherePlacement(MenuPlacement::FOOTER)
            ->whereNull('parent_id')
            ->orderBy('position', 'asc')
            ->get();

        $page = $menu->page;

        $metaTitle = Arr::get($page->seo, 'meta_title');
        $metaDescription = Arr::get($page->seo, 'meta_description');
        $metaKeywords = Arr::get($page->seo, 'meta_keywords');

        $address = Arr::toAddress([
            'address_line1' => config('config.general.app_address_line1'),
            'address_line2' => config('config.general.app_address_line2'),
            'city' => config('config.general.app_city'),
            'state' => config('config.general.app_state'),
            'zipcode' => config('config.general.app_zipcode'),
            'country' => config('config.general.app_country'),
        ]);

        config([
            'config.general.app_address' => $address,
        ]);

        $content = $page->content;

        $content = $this->parse($content);

        $blocks = $page->getMeta('has_block') ? Block::query()
            ->whereIn('uuid', $page->getMeta('blocks', []))
            ->orderBy('position', 'asc')
            ->get() : collect([]);

        $blocks = $blocks->map(function ($block) {
            $block->content = Str::limit(strip_tags($this->parse($block->content)), 100);

            return $block;
        });

        return view(config('config.site.view').'page', compact('menu', 'page', 'content', 'headerMenus', 'footerMenus', 'metaTitle', 'metaDescription', 'metaKeywords', 'blocks'));
    }
}
