<?php

namespace App\Http\Resources\Site;

use App\Http\Resources\MediaResource;
use App\Models\Site\Block;
use App\Support\MarkdownParser;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class PageResource extends JsonResource
{
    use MarkdownParser;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'content' => $this->content,
            'content_html' => $this->parse($this->content),
            'assets' => [
                'cover' => $this->cover_image,
                'default_cover' => ! Arr::get($this->assets, 'cover') ? true : false,
                'og' => $this->og_image,
                'default_og' => ! Arr::get($this->assets, 'og') ? true : false,
            ],
            'seo' => [
                'robots' => (bool) Arr::get($this->seo, 'robots'),
                'meta_title' => Arr::get($this->seo, 'meta_title'),
                'meta_description' => Arr::get($this->seo, 'meta_description'),
                'meta_keywords' => Arr::get($this->seo, 'meta_keywords'),
            ],
            $this->mergeWhen($request->with_details, [
                'has_block' => (bool) $this->getMeta('has_block'),
                'blocks' => $this->getBlocks(),
            ]),
            'media_token' => $this->getMeta('media_token'),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }

    private function getBlocks()
    {
        if (! $this->getMeta('has_block')) {
            return [];
        }

        return BlockResource::collection(Block::query()
            ->whereIn('uuid', $this->getMeta('blocks', []))
            ->orderBy('position', 'asc')
            ->get());
    }
}
