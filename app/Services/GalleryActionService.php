<?php

namespace App\Services;

use App\Concerns\HasStorage;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryActionService
{
    use HasStorage;

    public function upload(Request $request, Gallery $gallery)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:10240'],
        ]);

        $image = $this->uploadImageFile(
            visibility: 'public',
            path: 'gallery',
            input: 'file',
            maxWidth: 1280,
            thumbnail: true,
        );

        $imageCount = GalleryImage::query()
            ->where('gallery_id', $gallery->id)
            ->count();

        $galleryImage = GalleryImage::forceCreate([
            'gallery_id' => $gallery->id,
            'path' => $image,
            // 'is_cover' => $imageCount === 0 ? true : false,
            // 'position' => $imageCount + 1,
        ]);

        return $galleryImage;
    }

    public function makeCover(Request $request, Gallery $gallery, string $image)
    {
        $galleryImage = GalleryImage::query()
            ->where('gallery_id', $gallery->id)
            ->where('uuid', $image)
            ->getOrFail(trans('gallery.props.image'));

        GalleryImage::query()
            ->where('gallery_id', $gallery->id)
            ->where('uuid', '!=', $image)
            ->update([
                'is_cover' => false,
            ]);

        $galleryImage->update([
            'is_cover' => true,
        ]);
    }

    public function deleteImage(Request $request, Gallery $gallery, string $image)
    {
        $galleryImage = GalleryImage::query()
            ->where('gallery_id', $gallery->id)
            ->where('uuid', $image)
            ->getOrFail(trans('gallery.props.image'));

        $this->deleteImageFile(
            visibility: 'public',
            path: $galleryImage->path,
        );

        $thumbFilename = Str::of($galleryImage->path)->replaceLast('.', '-thumb.');

        $this->deleteImageFile(
            visibility: 'public',
            path: $thumbFilename,
        );

        // if ($galleryImage->is_cover) {
        //     GalleryImage::query()
        //         ->where('gallery_id', $gallery->id)
        //         ->where('uuid', '!=', $image)
        //         ->update([
        //             'is_cover' => true,
        //         ]);
        // }

        $galleryImage->delete();
    }
}
