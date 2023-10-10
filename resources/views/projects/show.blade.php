<x-app-layout>
    <x-slot name="header">
        {{ $project->name }}
    </x-slot>

    <x-block>
        <div class="space-y-3">

            <h2 class="text-xl">Project Details for "{{$project->name}}"</h2>
            <p>
                Ediarum Webdav Route: <code>/webdav/{{ $project->slug }}</code>
            </p>
            <p>
                Data Folder: <code>{{$project->data_folder_location}}</code>
            </p>
            <p>
                Pushes to Gitlab: <code>{{$gitlab_push ? "Yes" : "No" }}</code>
            </p>
            <p>
                Pushes to Ediarum Backend: <code>{{$ediarum_push ? "Yes" : "No" }}</code>
            </p>
            <p>
                Pushes to Existdb: <code>{{$exist_push ? "Yes" : "No" }}</code>
            </p>
            <a class="block" href="{{route('projects.edit',['project'=>$project->id])}}">
                <x-primary-button type="button">Edit</x-primary-button>
            </a>

        </div>
    </x-block>
    <x-block>
        <h2 class="text-xl py-4">Users for {{$project->name}}</h2>
        @foreach($project->users as $user)
            <div class="py-4 flex w-80">
                <div class="inline-block py-2 flex-grow">
                    {{$user->name}}
                </div>
                <form method="POST" action="{{route('projects.remove-user')}}">
                    @method('DELETE')
                    @csrf
                    <input type="hidden" id="project_id" name="project_id"
                           value="{{ request()->route()->parameter('project') }}"/>
                    <input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}"/>
                    <x-primary-button class="mt-1">Remove</x-primary-button>
                </form>
            </div>
        @endforeach
        <livewire:add-user :$users/>
    </x-block>
</x-app-layout>
