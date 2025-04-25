{{-- resources/views/auth/verify-pin.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Verifikasi PIN') }}</div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5>Selamat datang kembali, {{ $user->name }}</h5>
                        <p class="text-muted">Masukkan PIN Anda untuk melanjutkan</p>
                    </div>

                    <form method="POST" action="{{ route('token.verify-pin') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="pin" class="form-label">{{ __('PIN') }}</label>
                            <input id="pin" type="password" class="form-control @error('pin') is-invalid @enderror"
                                   name="pin" required maxlength="6" pattern="\d{6}" inputmode="numeric"
                                   placeholder="Masukkan 6 digit PIN">
                            @error('pin')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Verifikasi') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
