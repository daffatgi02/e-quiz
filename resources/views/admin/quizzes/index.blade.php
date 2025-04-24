{{-- resources/views/quizzes/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">{{ __('quiz.title') }}</h1>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quiz.active_quizzes') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('quiz.title') }}</th>
                                <th>{{ __('quiz.description') }}</th>
                                <th>{{ __('quiz.duration') }}</th>
                                <th>{{ __('quiz.start_date') }}</th>
                                <th>{{ __('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeQuizzes as $quiz)
                                <tr>
                                    <td>{{ $quiz->title }}</td>
                                    <td>{{ Str::limit($quiz->description, 50) }}</td>
                                    <td>{{ $quiz->duration }} {{ __('quiz.minutes') }}</td>
                                    <td>{{ $quiz->start_date->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($quiz->single_attempt && $quiz->attempts()->where('user_id', auth()->id())->exists())
                                            <span class="badge bg-secondary">{{ __('quiz.already_attempted') }}</span>
                                        @else
                                            <a href="{{ route('quiz.start', $quiz) }}" class="btn btn-primary btn-sm">
                                                {{ __('quiz.start') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('quiz.no_active_quizzes') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                            @forelse($quizHistory as $attempt)
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
                                        @if($attempt->status !== 'in_progress')
                                            <a href="{{ route('quiz.result', $attempt) }}" class="btn btn-info btn-sm">
                                                {{ __('general.view') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('quiz.no_quiz_history') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $quizHistory->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
