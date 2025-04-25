{{-- resources/views/admin/reports/attempt-detail.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>{{ __('quiz.attempt_detail') }}</h1>
                    <div>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-file-pdf"></i> {{ __('general.export') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.export.attempt', ['attempt' => $attempt, 'lang' => 'id']) }}">
                                        🇮🇩 Bahasa Indonesia
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.export.attempt', ['attempt' => $attempt, 'lang' => 'en']) }}">
                                        🇺🇸 English
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('admin.reports.quiz', $attempt->quiz) }}" class="btn btn-secondary">
                            {{ __('general.back') }}
                        </a>
                    </div>
                </div>

                <!-- User & Quiz Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('general.user') }} {{ __('general.details') }}</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>{{ __('general.name') }}:</th>
                                        <td>{{ $attempt->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('general.nik') }}:</th>
                                        <td>{{ $attempt->user->nik }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('general.department') }}:</th>
                                        <td>{{ $attempt->user->department }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('general.position') }}:</th>
                                        <td>{{ $attempt->user->position }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('quiz.details') }}</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>{{ __('quiz.title') }}:</th>
                                        <td>{{ $attempt->quiz->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.started_at') }}:</th>
                                        <td>{{ $attempt->started_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.completed_at') }}:</th>
                                        <td>{{ $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.score') }}:</th>
                                        <td>{{ $attempt->score ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.status') }}:</th>
                                        <td>
                                            <span class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'completed' ? 'warning' : 'info') }}">
                                                {{ __('quiz.' . $attempt->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions & Answers -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('quiz.questions_answers') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($attempt->quiz->questions as $index => $question)
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>{{ __('quiz.question') }} {{ $index + 1 }}: {{ $question->question }}</span>
                                    @php
                                        $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                                    @endphp
                                    <span class="badge bg-{{ $userAnswer && $userAnswer->points_earned == $question->points ? 'success' : 'danger' }}">
                                        {{ $userAnswer ? $userAnswer->points_earned : 0 }} / {{ $question->points }} pts
                                    </span>
                                </div>
                                <div class="card-body">
                                    @if ($question->type === 'multiple_choice')
                                        @foreach ($question->options as $option)
                                            <div class="form-check {{ $option->is_correct ? 'text-success fw-bold' : '' }}">
                                                <input class="form-check-input" type="radio" disabled
                                                    {{ $userAnswer && $userAnswer->question_option_id == $option->id ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    {{ $option->option }}
                                                    @if ($option->is_correct)
                                                        <i class="fas fa-check-circle text-success ms-2"></i>
                                                        <span class="badge bg-success ms-2">{{ __('quiz.correct_answer') }}</span>
                                                    @endif
                                                    @if ($userAnswer && $userAnswer->question_option_id == $option->id)
                                                        @if (!$option->is_correct)
                                                            <i class="fas fa-times-circle text-danger ms-2"></i>
                                                            <span class="badge bg-danger ms-2">{{ __('quiz.user_answer') }} ({{ __('quiz.incorrect') }})</span>
                                                        @else
                                                            <span class="badge bg-primary ms-2">{{ __('quiz.user_answer') }}</span>
                                                        @endif
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="mb-3">
                                            <h6>{{ __('quiz.user_answer') }}:</h6>
                                            <div class="p-3 bg-light rounded">
                                                {{ $userAnswer->essay_answer ?? __('quiz.no_answer') }}
                                            </div>
                                        </div>
                                        @if ($userAnswer && $userAnswer->is_graded)
                                            <div class="alert alert-info">
                                                <strong>{{ __('quiz.score') }}:</strong>
                                                {{ $userAnswer->points_earned }} / {{ $question->points }} points
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                {{ __('quiz.pending_grading') }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
