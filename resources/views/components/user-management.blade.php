@props(['user'])

@if ($user !== null && Auth::user()->isAdmin())

    @if ($user->active)
    <form
        action="{{ route('activated-users.destroy', ['user' => $user]) }}"
        method="POST"
    >
        @method('DELETE')
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <button
            type="submit"
            class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-red-600 hover:bg-red-500 hover:text-red-100 rounded-md shadown"
        >
            desativar usuário
        </button>
    </form>
    @else
    <form
        action="{{ route('activated-users.store') }}"
        method="POST"
    >
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <button
            type="submit"
            class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-red-600 hover:bg-red-500 hover:text-red-100 rounded-md shadown"
        >
            ativar usuário
        </button>
    </form>
    @endif

@endif
