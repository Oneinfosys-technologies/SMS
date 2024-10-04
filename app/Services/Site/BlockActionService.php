<?php

namespace App\Services\Site;

use App\Concerns\HasStorage;
use App\Models\Site\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BlockActionService
{
    use HasStorage;

    public function reorder(Request $request): void
    {
        $blocks = $request->blocks ?? [];

        $allBlocks = Block::query()
            ->get();

        foreach ($blocks as $index => $blockItem) {
            $block = $allBlocks->firstWhere('uuid', Arr::get($blockItem, 'uuid'));

            if (! $block) {
                continue;
            }

            $block->position = $index + 1;
            $block->save();
        }
    }

    public function uploadAsset(Request $request, Block $block, string $type)
    {
        request()->validate([
            'image' => 'required|image',
        ]);

        $assets = $block->assets;
        $asset = Arr::get($assets, $type);

        $this->deleteImageFile(
            visibility: 'public',
            path: $asset,
        );

        $image = $this->uploadImageFile(
            visibility: 'public',
            path: 'site/block/assets/'.$type,
            input: 'image',
            url: false
        );

        $assets[$type] = $image;
        $block->assets = $assets;
        $block->save();
    }

    public function removeAsset(Request $request, Block $block, string $type)
    {
        $assets = $block->assets;
        $asset = Arr::get($assets, $type);

        $this->deleteImageFile(
            visibility: 'public',
            path: $asset,
        );

        unset($assets[$type]);
        $block->assets = $assets;
        $block->save();
    }
}
