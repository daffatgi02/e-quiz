{{-- resources/views/admin/reports/quiz.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ __('quiz.report') }}: {{ $quiz->title }}</h1>
                <div>
                    <a href="{{ route('admin.reports.export.quiz', $quiz) }}" class="btn btn-success">
                        {{ __('general.export') }}
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        {{ __('general.back') }}
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>{{ __('general.total') }} {{ __('quiz.attempts') }}</h6>
                            <h3>{{ $statistics['total_attempts'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>{{ __('general.average') }} {{ __('quiz.score') }}</h6>
                            <h3>{{ number_format($statistics['average_score'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>{{ __('general.highest') }} {{ __('quiz.score') }}</h6>
                            <h3>{{ $statistics['highest_score'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>{{ __('general.lowest') }} {{ __('quiz.score') }}</h6>
                            <h3>{{ $statistics['lowest_score'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quiz.attempts') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('general.user') }}</th>
                                <th>{{ __('general.nik') }}</th>
                                <th>{{ __('general.department') }}</th>
                                <th>{{ __('quiz.started_at') }}</th>
                                <th>{{ __('quiz.completed_at') }}</th>
                                <th>{{ __('quiz.status') }}</th>
                                <th>{{ __('quiz.score') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td>{{ $attempt->user->name }}</td>
                                    <td>{{ $attempt->user->nik }}</td>
                                    <td>{{ $attempt->user->department }}</td>
                                    <td>{{ $attempt->started_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i') : '-' }}</td>
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
