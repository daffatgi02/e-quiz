{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">{{ __('general.reports') }}</h1>
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
                    <h5 class="card-title">{{ __('general.average') }} {{ __('quiz.score') }}</h5>
                    <p class="card-text display-4">{{ number_format($statistics['average_score'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quiz.pending_grading') }}</h5>
                </div>
                <div class="card-body">
                    <h3 class="text-center">{{ $statistics['pending_grading'] }}</h3>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.reports.pending-grading') }}" class="btn btn-primary">
                            {{ __('quiz.grade_essay') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('general.recent') }} {{ __('quiz.attempts') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('general.user') }}</th>
                                <th>{{ __('quiz.title') }}</th>
                                <th>{{ __('quiz.status') }}</th>
                                <th>{{ __('quiz.score') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAttempts as $attempt)
                                <tr>
                                    <td>{{ $attempt->user->name }}</td>
                                    <td>{{ $attempt->quiz->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'completed' ? 'warning' : 'info') }}">
                                            {{ __('quiz.' . $attempt->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attempt->score ?? '-' }}</td>
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
