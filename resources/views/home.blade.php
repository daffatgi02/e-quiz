{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('general.dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h5>{{ __('general.welcome') }}, {{ Auth::user()->name }}!</h5>
                    <p>{{ __('You are logged in!') }}</p>

                    <div class="mt-4">
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                {{ __('general.admin_dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('quiz.index') }}" class="btn btn-primary">
                                {{ __('quiz.take_quiz') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
