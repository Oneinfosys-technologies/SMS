<?php

namespace Mint\Service\Actions;

use Closure;

class GenerateSymlink
{
    public function handle($params, Closure $next)
    {
        if (\File::exists(public_path('storage'))) {
            \File::deleteDirectory(public_path('storage'));
        }

        if (!\File::exists(storage_path('app/public'))) {
            \Storage::makeDirectory('public');
        }

        try {
            \Artisan::call('storage:link');
        } catch (\Exception $e) {
            // do nothing
        }

        return $next($params);
    }
}
