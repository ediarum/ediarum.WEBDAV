<x-app-layout>
    <x-slot name="header">
        {{ __('Ediarum.WEBDAV FAQ') }}
    </x-slot>

    <x-block>
        <div class="prose max-w-none">
            {!!  \Illuminate\Support\Str::markdown(file_get_contents(resource_path('markdown/faq.md'))) !!}
        </div>
    </x-block>

</x-app-layout>
