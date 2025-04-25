{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ __('general.users') }}</h1>
                <div>
                    <a href="{{ route('admin.tokens.download') }}" class="btn btn-secondary">
                        <i class="fas fa-download"></i> Download Daftar ID-Trainer
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        {{ __('general.create') }} {{ __('general.user') }}
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <select name="department" class="form-select">
                                <option value="">Semua Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="position" class="form-select">
                                <option value="">Semua Posisi</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos }}">{{ $pos }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('general.name') }}</th>
                                <th>{{ __('general.nik') }}</th>
                                <th>ID-Trainer</th>
                                <th>PIN Status</th>
                                <th>{{ __('general.department') }}</th>
                                <th>{{ __('general.position') }}</th>
                                <th>{{ __('general.status') }}</th>
                                <th>{{ __('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->nik }}</td>
                                    <td>
                                        @if($user->login_token)
                                            <code>{{ $user->login_token }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->pin_set)
                                            <span class="badge bg-success">PIN Set</span>
                                        @else
                                            <span class="badge bg-warning">PIN Not Set</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->department }}</td>
                                    <td>{{ $user->position }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ __('general.' . ($user->is_active ? 'active' : 'inactive')) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if(!$user->login_token)
                                                <form action="{{ route('admin.tokens.generate', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-info">
                                                        Generate Token
                                                    </button>
                                                </form>
                                            @endif

                                            @if($user->pin_set)
                                                <form action="{{ route('admin.tokens.reset-pin', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning"
                                                            onclick="return confirm('Reset PIN untuk {{ $user->name }}?')">
                                                        Reset PIN
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                                                {{ __('general.edit') }}
                                            </a>

                                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $user->is_active ? 'danger' : 'success' }}">
                                                    {{ $user->is_active ? __('general.deactivate') : __('general.activate') }}
                                                </button>
                                            </form>
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
