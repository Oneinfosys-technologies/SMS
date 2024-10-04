<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasStorage
{
    private function getDisk(string $visibility = 'private'): string
    {
        $disk = config('filesystems.default', 'local');

        if (in_array($disk, ['local', 'vol']) && $visibility == 'public') {
            $disk = 'public';
        }

        return $disk;
    }

    public function uploadFile(
        string $path = 'files',
        string $input = 'file',
        bool $url = false
    ): string {
        $path = config('config.system.upload_prefix').$path;

        $filename = Storage::putFile($path, request()->file($input));

        return $url ? Storage::url($filename) : $filename;
    }

    public function uploadImageFile(
        string $path = 'files',
        string $input = 'file',
        bool|int $maxWidth = false,
        bool|int $thumbnail = false,
        string $visibility = 'private',
        bool $url = false
    ): string {
        $disk = $this->getDisk($visibility);

        $path = config('config.system.upload_prefix').$path;

        if ($maxWidth) {
            $img = \Image::make(request()->file($input))->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $filename = $path.'/'.uniqid().'_'.time().'.'.request()->file($input)->getClientOriginalExtension();

            Storage::disk($disk)->put($filename, $img->stream(), $visibility);
        } else {
            $filename = Storage::disk($disk)->putFile($path, request()->file($input), $visibility);
        }

        if ($thumbnail) {
            $thumbnail = is_bool($thumbnail) ? 400 : $thumbnail;

            $img = \Image::make(request()->file($input))->resize($thumbnail, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $thumbFilename = Str::of($filename)->replaceLast('.', '-thumb.');

            Storage::disk($disk)->put($thumbFilename, $img->stream(), $visibility);
        }

        return $url ? Storage::disk($disk)->url($filename) : $filename;
    }

    public function deleteFile(
        ?string $path = null,
    ): void {
        if (! $path) {
            return;
        }

        try {
            Storage::delete($path);
        } catch (\Exception $e) {
        }
    }

    public function deleteImageFile(
        string $disk = 'local',
        bool $thumbnail = false,
        string $visibility = 'private',
        ?string $path = null,
    ): void {
        if (! $path) {
            return;
        }

        $disk = $this->getDisk($visibility);

        try {
            Storage::disk($disk)->delete($path);

            if ($thumbnail) {
                $thumbFilename = Str::of($path)->replaceLast('.', '-thumb.');
                Storage::disk($disk)->delete($thumbFilename);
            }

        } catch (\Exception $e) {
        }
    }

    public function makeDirectory(string $path): void
    {
        if (Storage::exists($path)) {
            return;
        }

        Storage::makeDirectory($path);
    }

    public function getFile(
        ?string $path = '',
        string $default = '',
        string $visibility = 'private',
    ): string {
        $disk = $this->getDisk($visibility);

        if (! $path) {
            return $default;
        }

        if (! Storage::disk($disk)->exists($path)) {
            return $default;
        }

        return Storage::disk($disk)->url($path);
    }

    public function getImageFile(
        ?string $path = '',
        string $default = '',
        string $visibility = 'private',
        int $liftime = 5,
    ): string {
        $disk = $this->getDisk($visibility);

        if (! $path) {
            return url($default);
        }

        if (in_array($disk, ['s3', 'r2'])) {
            return Storage::temporaryUrl($path, now()->addMinutes($liftime));
        }

        if (! Storage::disk($disk)->exists($path)) {
            return url($default);
        }

        return Storage::disk($disk)->url($path);
    }
}
