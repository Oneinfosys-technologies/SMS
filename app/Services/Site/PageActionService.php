<?php

namespace App\Services\Site;

use App\Concerns\HasStorage;
use App\Models\Site\Block;
use App\Models\Site\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PageActionService
{
    use HasStorage;

    public function updateMeta(Request $request, Page $page)
    {
        $request->validate([
            'seo.robots' => 'boolean',
            'seo.meta_title' => 'nullable|min:3|max:255',
            'seo.meta_description' => 'nullable|min:3|max:255',
            'seo.meta_keywords' => 'nullable|min:3|max:255',
        ], [], [
            'seo.robots' => trans('site.page.props.seo.robots'),
            'seo.meta_title' => trans('site.page.props.seo.meta_title'),
            'seo.meta_description' => trans('site.page.props.seo.meta_description'),
            'seo.meta_keywords' => trans('site.page.props.seo.meta_keywords'),
        ]);

        $page->seo = [
            'robots' => $request->boolean('seo.robots'),
            'meta_title' => $request->input('seo.meta_title'),
            'meta_description' => $request->input('seo.meta_description'),
            'meta_keywords' => $request->input('seo.meta_keywords'),
        ];
        $page->save();

        $page->updateMedia($request);
    }

    public function updateBlocks(Request $request, Page $page)
    {
        $request->validate([
            'has_block' => 'boolean',
            'blocks' => 'array|required_if:has_block,true',
        ], [], [
            'has_block' => trans('site.block.block'),
            'blocks' => trans('site.block.block'),
        ]);

        if (! $request->boolean('has_block')) {
            $page->setMeta([
                'has_block' => false,
            ]);
            $page->save();

            return;
        }

        $blocks = Block::query()
            ->whereIn('uuid', $request->blocks)
            ->get();

        $page->setMeta([
            'has_block' => true,
            'blocks' => $blocks->pluck('uuid'),
        ]);
        $page->save();
    }

    public function uploadAsset(Request $request, Page $page, string $type)
    {
        request()->validate([
            'image' => 'required|image',
        ]);

        $assets = $page->assets;
        $asset = Arr::get($assets, $type);

        $this->deleteImageFile(
            visibility: 'public',
            path: $asset,
        );

        $image = $this->uploadImageFile(
            visibility: 'public',
            path: 'site/page/assets/'.$type,
            input: 'image',
            url: false
        );

        $assets[$type] = $image;
        $page->assets = $assets;
        $page->save();
    }

    public function removeAsset(Request $request, Page $page, string $type)
    {
        $assets = $page->assets;
        $asset = Arr::get($assets, $type);

        $this->deleteImageFile(
            visibility: 'public',
            path: $asset,
        );

        unset($assets[$type]);
        $page->assets = $assets;
        $page->save();
    }
}
