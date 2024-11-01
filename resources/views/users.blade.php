<x-app-layout>
    <x-slot name="header">
        Users
    </x-slot>

    @if(session('new_password'))
        <div class="bg-red-100 p-4">
            <h3>New Password for {{session('user')}}: {{session('new_password')}}</h3>
            <p>Copy this password. It will not be displayed again.</p>
        </div>
    @endif

    <a href="{{route('register')}}">
        <x-primary-button>
            Create New User
        </x-primary-button>
    </a>
    @foreach($users as $user)
        <x-block>
            Name: {{$user->name}}, Email: {{$user->email}}
            Role: {{ $user->is_admin ? "Admin" : "Regular User" }}
            <form class="float-right" method="POST"
                  action="{{ route('password.admin-reset', ["id"=>$user->id]) }}">
                <x-primary-button>
                    @method('PUT')
                    @csrf
                    Reset Password
                </x-primary-button>
            </form>
        </x-block>
    @endforeach
</x-app-layout>
