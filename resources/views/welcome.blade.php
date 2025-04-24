{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Quiz Management') }}</title>

        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-light">
            <div class="text-center">
                <h1 class="display-4 mb-4">{{ config('app.name', 'Quiz Management') }}</h1>
                <p class="lead mb-4">{{ __('quiz.welcome_message') }}</p>

                @if (Route::has('login'))
                    <div class="mt-4">
                        @auth
                            <a href="{{ url('/home') }}" class="btn btn-primary btn-lg">
                                {{ __('general.dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                {{ __('general.login') }}
                            </a>
                        @endauth
                    </div>
                @endif

                <div class="mt-5">
                    <div class="btn-group">
                        <a href="{{ route('language.switch', 'en') }}" class="btn btn-outline-secondary {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                            {{ __('general.english') }}
                        </a>
                        <a href="{{ route('language.switch', 'id') }}" class="btn btn-outline-secondary {{ app()->getLocale() == 'id' ? 'active' : '' }}">
                            {{ __('general.indonesian') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
