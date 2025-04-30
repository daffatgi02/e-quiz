{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('general.edit') }} {{ __('general.user') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('general.name') }}</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('general.email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nik" class="form-label">{{ __('general.nik') }}</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                                    name="nik" value="{{ old('nik', $user->nik) }}" required>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="position" class="form-label">{{ __('general.position') }}</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    id="position" name="position" value="{{ old('position', $user->position) }}" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="department" class="form-label">{{ __('general.department') }}</label>
                                <input type="text" class="form-control @error('department') is-invalid @enderror"
                                    id="department" name="department" value="{{ old('department', $user->department) }}"
                                    required>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="perusahaan" class="form-label">{{ __('general.company') }}</label>
                                <input type="text" class="form-control @error('perusahaan') is-invalid @enderror"
                                    id="perusahaan" name="perusahaan" value="{{ old('perusahaan', $user->perusahaan) }}">
                                @error('perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('general.password') }} <small
                                        class="text-muted">({{ __('general.leave_blank_to_keep_current') }})</small></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation"
                                    class="form-label">{{ __('general.confirm_password') }}</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin"
                                        value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_admin">
                                        {{ __('general.admin') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('general.active') }}
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                            <a href="{{ route('admin.users.index') }}"
                                class="btn btn-secondary">{{ __('general.cancel') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
