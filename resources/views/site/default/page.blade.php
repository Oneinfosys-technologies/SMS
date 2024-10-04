<x-site.default.layout :header-menus="$headerMenus" :footer-menus="$footerMenus" :meta-title="$metaTitle" :meta-description="$metaDescription" :meta-keywords="$metaKeywords">

    <section>
        @if (Arr::get($page->assets, 'cover'))
            <img src="{{ asset(Arr::get($page->assets, 'cover')) }}" alt="{{ $page->title }}" class="w-full h-auto">
        @endif
        <div class="container py-20">
            <h1 class="text-3xl text-gray-800 font-bold">{{ $page->title }}</h1>
            @if ($page->sub_title)
                <h2 class="text-xl text-gray-700">{{ $page->sub_title }}</h2>
            @endif

            <div class="mt-10 text-gray-700">
                {!! $content !!}
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($blocks as $block)
                    <div class="border-gray-100 border-2 rounded-md">
                        @if ($block->cover_image)
                            <img src="{{ $block->cover_image }}" alt="{{ $block->title }}"
                                class="w-full h-auto rounded-t-md">
                        @endif
                        <div class="px-4 py-2 text-gray-700">
                            <h2 class="text-xl font-bold">{{ $block->title }}</h2>
                            @if ($block->sub_title)
                                <p class="text-sm text-gray-700">{{ $block->sub_title }}</p>
                            @endif
                            <p class="mt-2 text-gray-700">{{ Str::limit($block->content, 100) }}</p>

                            @if ($block->getMeta('url'))
                                <a href="{{ $block->getMeta('url') }}" class="text-xs text-gray-700">Read More</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-site.default.layout>
