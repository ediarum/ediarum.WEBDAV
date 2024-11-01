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
    <x-block>
        <h2 class="text-xl py-4">Webdav Locks</h2>
        <p class="text-sm mb-4">(Warning: Unlocking a file by force will generate an error when the user tries to save
            that file.)</p>
        <table class="w-full border-separate">
            <thead class="text-left">
            <tr>
                <th>Owner</th>
                <th>File</th>
                <th>Locked Since</th>
                <th>Locked Time</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($locks as $lock)
                <tr>
                    <td class="border">
                        {{$lock['owner']}}
                    </td>
                    <td class="border">
                        {{$lock['file']}}
                    </td>
                    <td class="border">
                        {{$lock['created']}}
                    </td>
                    <td class="border">
                        {{$lock['timeElapsed']}}
                    </td>
                    <td>
                        <form method="POST"
                              action="{{ route('projects.remove-lock',["projectId"=>$project->id, "lockId"=>$lock['id']]) }}">
                            @csrf
                            @method('DELETE')
                            <x-primary-button>Force Unlock</x-primary-button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(sizeof($locks) == 0)
            <div class="w-full text-center">
                (No Locks)
            </div>
        @endif
    </x-block>
    @if($exist_push)
        <x-block>
            <h2 class="text-xl py-4">Failed Pushes to Existdb</h2>
            @foreach($failed_jobs as $f)
                Failed at: {{ $f['time'] }}. File(s): {{ $f["file"] }}.
            @endforeach
            @if(sizeof($failed_jobs) == 0)
                <div class="w-full text-center">
                    (No Failed Pushes to ExistDB)
                </div>
            @endif

        </x-block>
    @endif
</x-app-layout>
