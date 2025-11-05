<x-app-layout>
    <x-slot name="header">
        Projects
    </x-slot>

    <a href="{{route('projects.create')}}">
        <x-primary-button class="mb-5">
            Create New Project
        </x-primary-button>
    </a>
    @foreach($projects as $project)
        <a class='block' href="{{route('projects.show',["project"=>$project->id])}}">
            <x-block>
                Name: {{$project->name}}
            </x-block>
        </a>
    @endforeach
</x-app-layout>
