@props([
    'id',
    'description',
    'value',
])

<div>
    @if($value === null)
        <x-input-standard :id="$id" :description="$description" :hidden="true" :value="$value" />
    @else
        <div class="font-medium text-sm text-gray-700'">
        <x-input-standard :id="$id"
                          :description="'A '
                          . $description
                          . ' is currently saved for this project. You can enter a new '
                          . $description
                           . ':' " :hidden="true" :value="null" />
         <x-input-label class="mt-2 inline-block" for="{{$id}}_delete">Or delete the current {{$description}}:</x-input-label>
         <input type="checkbox" id="{{$id}}_delete" name="{{$id}}_delete">
        </div>

    @endif
</div>
