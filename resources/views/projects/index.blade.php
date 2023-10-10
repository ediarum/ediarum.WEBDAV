<x-app-layout>
    <x-slot name="header">
        Projects
    </x-slot>

    <x-primary-button>
        <a href="{{route('projects.create')}}">
            Create New Project
        </a>
    </x-primary-button>
    @foreach($projects as $project)
        <a class='block' href="{{route('projects.show',["project"=>$project->id])}}">
            <x-block>
                Name: {{$project->name}}
            </x-block>
        </a>
    @endforeach
</x-app-layout>
