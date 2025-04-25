{{-- resources/views/admin/users/history.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ __('general.history') }} - {{ $user->name }}</h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    {{ __('general.back') }}
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('general.user') }} {{ __('general.details') }}</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('general.name') }}:</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('general.nik') }}:</th>
                                    <td>{{ $user->nik }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('general.position') }}:</th>
                                    <td>{{ $user->position }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('general.department') }}:</th>
                                    <td>{{ $user->department }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('general.statistics') }}</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('general.total') }} {{ __('quiz.attempts') }}:</th>
                                    <td>{{ $statistics['total_attempts'] }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('quiz.completed') }}:</th>
                                    <td>{{ $statistics['completed'] }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('quiz.graded') }}:</th>
                                    <td>{{ $statistics['graded'] }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('general.average') }} {{ __('quiz.score') }}:</th>
                                    <td>{{ number_format($statistics['average_score'], 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quiz.history') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('quiz.title') }}</th>
                                <th>{{ __('quiz.started_at') }}</th>
                                <th>{{ __('quiz.completed_at') }}</th>
                                <th>{{ __('quiz.status') }}</th>
                                <th>{{ __('quiz.score') }}</th>
                                <th>{{ __('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td>{{ $attempt->quiz->title }}</td>
                                    <td>{{ $attempt->started_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'completed' ? 'warning' : 'info') }}">
                                            {{ __('quiz.' . $attempt->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attempt->score ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.reports.attempt.detail', $attempt) }}" class="btn btn-sm btn-info">
                                            {{ __('general.view') }} {{ __('general.details') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($attempts->hasPages())
                        {{ $attempts->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
