{{-- resources/views/auth/token-login.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Login dengan ID-Trainer') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('token.login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="login_token" class="form-label">{{ __('ID-Trainer') }}</label>
                            <input id="login_token" type="text" class="form-control @error('login_token') is-invalid @enderror"
                                   name="login_token" value="{{ old('login_token') }}" required autofocus maxlength="8"
                                   style="text-transform: uppercase;" placeholder="Masukkan ID-Trainer">
                            @error('login_token')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Lanjutkan') }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">
                        <a href="{{ route('admin.login') }}">Login sebagai Admin</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
