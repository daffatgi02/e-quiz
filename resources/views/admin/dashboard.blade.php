{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">{{ __('general.dashboard') }}</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('quiz.title') }}</h5>
                        <p class="card-text display-4">{{ $statistics['total_quizzes'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('general.users') }}</h5>
                        <p class="card-text display-4">{{ $statistics['total_users'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('general.total') }} {{ __('quiz.attempts') }}</h5>
                        <p class="card-text display-4">{{ $statistics['total_attempts'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('quiz.pending_grading') }}</h5>
                        <p class="card-text display-4">{{ $statistics['pending_grading'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('general.recent') }} {{ __('quiz.attempts') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('general.user') }}</th>
                                    <th>{{ __('quiz.title') }}</th>
                                    <th>{{ __('quiz.status') }}</th>
                                    <th>{{ __('quiz.score') }}</th>
                                    <th>{{ __('general.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAttempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->user->name }}</td>
                                        <td>{{ $attempt->quiz->title }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'completed' ? 'warning' : 'info') }}">
                                                {{ __('quiz.' . $attempt->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $attempt->score ? $attempt->score : '-' }}</td>
                                        <td>{{ $attempt->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
