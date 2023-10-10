@props([
    'id',
    'description',
    'value',
    'hidden' => false,
])

<div>
    <x-input-label for="{{$id}}" value="{{$description}}"/>
    <x-text-input id="{{$id}}" name="{{$id}}" type="{{$hidden ? 'password' : 'text' }}" class="mt-1 block w-full"
                  :value="old($id, $value)" />
    <x-input-error class="mt-2" :messages="$errors->get($id)"/>
</div>
