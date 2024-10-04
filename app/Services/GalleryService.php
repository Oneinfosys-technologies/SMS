<?php

namespace App\Services;

use App\Enums\GalleryType;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryService
{
    public function preRequisite(Request $request): array
    {
        $types = GalleryType::getOptions();

        return compact('types');
    }

    public function create(Request $request): Gallery
    {
        \DB::beginTransaction();

        $gallery = Gallery::forceCreate($this->formatParams($request));

        \DB::commit();

        return $gallery;
    }

    private function formatParams(Request $request, ?Gallery $gallery = null): array
    {
        $formatted = [
            'type' => $request->type,
            'title' => $request->title,
            'date' => $request->date,
            'description' => $request->description,
        ];

        if (! $gallery) {
            $formatted['team_id'] = auth()->user()?->current_team_id;
        }

        return $formatted;
    }

    public function update(Request $request, Gallery $gallery): void
    {
        \DB::beginTransaction();

        $gallery->forceFill($this->formatParams($request, $gallery))->save();

        \DB::commit();
    }

    public function deletable(Gallery $gallery): void {}
}
