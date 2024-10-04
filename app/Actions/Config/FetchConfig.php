<?php

namespace App\Actions\Config;

use App\Models\Config\Config;
use App\Support\BuildConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FetchConfig
{
    use BuildConfig;

    public function execute(Request $request)
    {
        $config = $this->generate(
            config: Config::listAll(),
            params: [
                'mask' => true,
                'show_public' => false,
            ],
        );

        $config['system']['currencies'] = explode(',', Arr::get($config, 'system.currencies'));

        return Arr::get($config, $request->query('type'));
    }
}
