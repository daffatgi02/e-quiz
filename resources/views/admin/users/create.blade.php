{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('general.create') }} {{ __('general.user') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('general.name') }}</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nik" class="form-label">{{ __('general.nik') }}</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                                    name="nik" value="{{ old('nik') }}" required>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="position" class="form-label">{{ __('general.position') }}</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    id="position" name="position" value="{{ old('position') }}" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="department" class="form-label">{{ __('general.department') }}</label>
                                <input type="text" class="form-control @error('department') is-invalid @enderror"
                                    id="department" name="department" value="{{ old('department') }}" required>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="perusahaan" class="form-label">{{ __('general.company') }}</label>
                                <input type="text" class="form-control @error('perusahaan') is-invalid @enderror"
                                    id="perusahaan" name="perusahaan" value="{{ old('perusahaan') }}">
                                @error('perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
                            <a href="{{ route('admin.users.index') }}"
                                class="btn btn-secondary">{{ __('general.cancel') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
