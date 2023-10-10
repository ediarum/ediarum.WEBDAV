<x-app-layout>
    <x-slot name="header">
        {{ __('Profile') }}
    </x-slot>

    <x-block>
        @include('profile.partials.update-profile-information-form')
    </x-block>

    <x-block>
        @include('profile.partials.update-password-form')
    </x-block>
</x-app-layout>
