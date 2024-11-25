<x-app-layout>
    <x-slot name="header">
        Users
    </x-slot>

    @if (session('new_password'))
        <div class="bg-red-100 p-4">
            <h3>New Password for {{ session('user') }}: {{ session('new_password') }}</h3>
            <p>Copy this password. It will not be displayed again.</p>
        </div>
    @endif

    <a href="{{ route('register') }}">
        <x-primary-button>
            Create New User
        </x-primary-button>
    </a>
    @foreach ($users as $user)
        <x-block>
            Name: {{ $user->name }}, Email: {{ $user->email }}
            Role: {{ $user->is_admin ? 'Admin' : 'Regular User' }}



            <x-secondary-button data-modal-target="popup-modal-{{ $user->id }}"
                data-modal-toggle="popup-modal-{{ $user->id }}"
                class="float-right "
                type="button">
                Reset Password
            </x-secondary-button>

            <div id="popup-modal-{{ $user->id }}" tabindex="-1"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <button type="button"
                            class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="popup-modal-{{ $user->id }}">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-4 md:p-5 text-center">
                            <svg class="mx-auto mb-4 text-grey-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want
                                to change the password?</h3>
                            <form method="POST"
                                action="{{ route('password.admin-reset', ['id' => $user->id]) }}">
                                @method('PUT')
                                @csrf
                                <x-primary-button>
                                    Yes, I'm sure. Reset Password
                                </x-primary-button>
                            </form>
                            <button data-modal-hide="popup-modal-{{ $user->id }}" type="button"
                                class="mt-5 py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                                cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </x-block>
    @endforeach
</x-app-layout>
