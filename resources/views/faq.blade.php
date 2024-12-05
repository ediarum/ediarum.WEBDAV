<x-app-layout>
    <x-slot name="header">
        {{ __('Ediarum.WEBDAV FAQ') }}
    </x-slot>

    <x-block>
        <div class="prose max-w-none">
            {!! $markdown !!}
        </div>
    </x-block>

</x-app-layout>
