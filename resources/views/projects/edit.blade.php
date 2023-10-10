<x-app-layout>
    <x-slot name="header">
        {{ $new ? "(New Project)" : $project->name }}
    </x-slot>

    <x-block>
        <form method="post"
              action="{{ $new ? route('projects.store') : route('projects.update', ["project" => $project->id]) }}"
              class="mt-6 space-y-6"
        >
            @csrf
            @if(!$new)
                @method('put')
            @endif
            <x-input-standard id="name" description="Project Name" :value="$project->name"/>
            <x-input-standard id="slug"
                              description="Slug (only lowercase alphanumeric characters and hyphens allowed)"
                              :value="$project->slug"
            />
            <x-input-standard id="data_folder_location" description="Data Folder Location (full path)"
                              :value="$project->data_folder_location"
            />
            <x-input-standard id="gitlab_url" description="Gitlab Url"
                              :value="$project->gitlab_url"
            />
            <x-input-standard id="gitlab_username" description="Gitlab Username"
                              :value="$project->gitlab_username"
            />
            <x-input-standard id="gitlab_personal_access_token" description="Gitlab PAT" :hidden="true"
                              :value="$project->gitlab_personal_access_token"
            />
            <x-input-standard id="ediarum_backend_url" description="Ediarum Backend Url"
                              :value="$project->ediarum_backend_url"
            />
            <x-input-standard id="ediarum_backend_api_key" description="Ediarum Backend Api Key" :hidden="true"
                              :value="$project->ediarum_backend_api_key"
            />
            <x-input-standard id="exist_base_url" description="eXist-db Base Url"
                              :value="$project->exist_base_url"
            />
            <x-input-standard id="exist_data_path" description="eXist-db Data Path (for example: '/db/apps/project-name/data'"
                              :value="$project->exist_data_path"
            />
            <x-input-standard id="exist_username" description="eXist-db Username"
                              :value="$project->exist_username"
            />
            <x-input-standard id="exist_password" description="eXist-db Password" :hidden="true"
                              :value="$project->exist_password"
            />

            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </form>
    </x-block>
</x-app-layout>
