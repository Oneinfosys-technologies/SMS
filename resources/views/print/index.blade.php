<x-print.layout type="{{ Arr::get($meta, 'layout.type') }}">
    @include('print.config')

    <div :style="{ marginTop: `${marginTop}px` }">

        <div v-if="showHeader">
            @includeFirst([config('config.print.custom_path') . 'header', 'print.header'])
        </div>

        <h1 v-if="title" class="heading" v-text="title"></h1>
        <h1 v-if="subTitle" class="sub-heading" v-text="subTitle"></h1>
        <table class="table">
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $item)
                            <td>
                                @if (is_array($item) && array_key_exists('label', $item))
                                    <div>{{ Arr::get($item, 'label') }}</div>
                                    <span class="font-90pc block">{{ Arr::get($item, 'sub_label') }}</span>
                                @elseif(is_array($item))
                                    @foreach ($item as $rowItem)
                                        <div>{{ $rowItem }}</div>
                                    @endforeach
                                @else
                                    {{ $item }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            @if ($footer)
                <tfoot>
                    <tr>
                        @foreach ($footers as $footer)
                            <th>{{ $footer }}</th>
                        @endforeach
                    </tr>
                </tfoot>
            @endif
        </table>

        <div class="footer">
            <div v-text="footerNote"></div>
            <p class="timestamp" v-if="showPrintTime">
                {{ trans('print.printed_at', ['attribute' => Cal::dateTime(now())->formatted]) }}</p>
        </div>
    </div>
</x-print.layout>

<script>
    const {
        createApp
    } = Vue

    createApp({
        data() {
            return {
                isSidebarOpen: false,
                title: "{{ Arr::get($meta, 'title') }}",
                subTitle: "{{ Arr::get($meta, 'sub_title') }}",
                footerNote: "",
                showHeader: true,
                marginTop: 0,
                showPrintTime: true,
            }
        },
        methods: {
            toggleSidebar() {
                this.isSidebarOpen = !this.isSidebarOpen;
            }
        }
    }).mount('#app')
</script>
