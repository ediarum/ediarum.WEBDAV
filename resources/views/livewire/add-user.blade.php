<div class="pt-10">
    <form method="POST" action="{{ route('projects.add-user') }}">
        @csrf
        <input type="hidden" id="project_id" name="project_id" value="{{ request()->route()->parameter('project') }}"/>
        <select id="user_id" name="user_id" class="mr-10">
            @foreach($users as $user)
                <option value="{{$user->id}}">{{$user->name}}</option>
            @endforeach
        </select>
        <x-primary-button>Add User</x-primary-button>
    </form>
</div>
