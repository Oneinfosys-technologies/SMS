<?php

namespace App\Services\Site;

use App\Models\Site\Block;
use Illuminate\Http\Request;

class BlockService
{
    public function preRequisite(Request $request): array
    {
        return [];
    }

    public function create(Request $request): Block
    {
        \DB::beginTransaction();

        $block = Block::forceCreate($this->formatParams($request));

        \DB::commit();

        return $block;
    }

    private function formatParams(Request $request, ?Block $block = null): array
    {
        $formatted = [
            'name' => $request->name,
            'title' => $request->title,
            'sub_title' => $request->sub_title,
            'content' => $request->content,
        ];

        $meta = $block?->meta ?? [];
        $meta['url'] = $request->url;

        $formatted['meta'] = $meta;

        if (! $block) {
            //
        }

        return $formatted;
    }

    public function update(Request $request, Block $block): void
    {
        \DB::beginTransaction();

        $block->forceFill($this->formatParams($request, $block))->save();

        \DB::commit();
    }

    public function deletable(Block $block): void {}
}
