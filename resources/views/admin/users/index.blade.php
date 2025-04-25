{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ __('general.users') }}</h1>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    {{ __('general.create') }} {{ __('general.user') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('general.name') }}</th>
                                <th>{{ __('general.nik') }}</th>
                                <th>{{ __('general.position') }}</th>
                                <th>{{ __('general.department') }}</th>
                                <th>{{ __('general.status') }}</th>
                                <th>{{ __('general.role') }}</th>
                                <th>{{ __('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->nik }}</td>
                                    <td>{{ $user->position }}</td>
                                    <td>{{ $user->department }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ __('general.' . ($user->is_active ? 'active' : 'inactive')) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_admin ? 'primary' : 'secondary' }}">
                                            {{ __('general.' . ($user->is_admin ? 'admin' : 'user')) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if(!$user->is_admin)
                                                <a href="{{ route('admin.users.history', $user) }}" class="btn btn-sm btn-info">
                                                    {{ __('general.history') }}
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                                {{ __('general.edit') }}
                                            </a>
                                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $user->is_active ? 'danger' : 'success' }}">
                                                    {{ $user->is_active ? __('general.deactivate') : __('general.activate') }}
                                                </button>
                                            </form>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('general.confirm_delete') }}')">
                                                        {{ __('general.delete') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
