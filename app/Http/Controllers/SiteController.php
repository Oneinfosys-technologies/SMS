<?php

namespace App\Http\Controllers;

use App\Services\SiteService;
use App\Support\MarkdownParser;

class SiteController extends Controller
{
    use MarkdownParser;

    public function home(SiteService $service)
    {
        return $service->getPage('Home');
    }

    public function page(string $slug, SiteService $service)
    {
        return $service->getPage($slug);
    }
}
