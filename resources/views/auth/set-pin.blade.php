{{-- resources/views/auth/set-pin.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Buat PIN Anda') }}</div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5>Selamat datang, {{ $user->name }}</h5>
                        <p class="text-muted">Silakan buat PIN 6 digit untuk keamanan akun Anda</p>
                    </div>

                    <form method="POST" action="{{ route('token.set-pin') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="pin" class="form-label">{{ __('PIN (6 digit)') }}</label>
                            <input id="pin" type="password" class="form-control @error('pin') is-invalid @enderror"
                                   name="pin" required maxlength="6" pattern="\d{6}" inputmode="numeric"
                                   placeholder="Masukkan 6 digit PIN">
                            @error('pin')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pin_confirmation" class="form-label">{{ __('Konfirmasi PIN') }}</label>
                            <input id="pin_confirmation" type="password" class="form-control"
                                   name="pin_confirmation" required maxlength="6" pattern="\d{6}" inputmode="numeric"
                                   placeholder="Masukkan ulang PIN">
                        </div>

                        <div class="mb-0">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Simpan PIN') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
