{{-- resources/views/quizzes/result.blade.php (updated) --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quiz.result') }}: {{ $attempt->quiz->title }}</h5>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>{{ __('quiz.status') }}:
                                <span class="badge bg-{{ $attempt->status === 'graded' ? 'success' : 'warning' }}">
                                    {{ __('quiz.' . $attempt->status) }}
                                </span>
                            </h6>
                            <h6>{{ __('quiz.score') }}: {{ $attempt->score ?? __('quiz.pending') }}</h6>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6>{{ __('quiz.started_at') }}: {{ $attempt->started_at->format('Y-m-d H:i') }}</h6>
                            <h6>{{ __('quiz.completed_at') }}: {{ $attempt->completed_at->format('Y-m-d H:i') }}</h6>
                        </div>
                    </div>

                    @foreach($attempt->quiz->questions as $index => $question)
                        @php
                            $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                            $earnedPoints = $userAnswer ? ($userAnswer->points_earned ?? 0) : 0;
                            $maxPoints = $question->points;
                            $percentScore = $maxPoints > 0 ? ($earnedPoints / $maxPoints) * 100 : 0;

                            // Determine score color based on percentage
                            if ($percentScore >= 80) {
                                $scoreColorClass = 'text-success';
                            } elseif ($percentScore >= 50) {
                                $scoreColorClass = 'text-warning';
                            } else {
                                $scoreColorClass = 'text-danger';
                            }
                        @endphp

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $index + 1 }}). {{ $question->question }}</strong>
                                </div>
                                @if($attempt->status === 'graded')
                                    <div class="points-display {{ $scoreColorClass }} fw-bold">
                                        {{ $earnedPoints }} / {{ $maxPoints }}
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($question->type === 'multiple_choice')
                                    @foreach($question->options as $optionIndex => $option)
                                        @php
                                            $letter = chr(65 + $optionIndex); // A, B, C, D, etc.
                                            $isUserAnswer = $userAnswer && $userAnswer->question_option_id == $option->id;
                                            $isCorrect = $option->is_correct;

                                            // Determine option display class
                                            $optionClass = '';
                                            if ($isUserAnswer && $isCorrect) {
                                                $optionClass = 'bg-success bg-opacity-10';
                                            } elseif ($isUserAnswer && !$isCorrect) {
                                                $optionClass = 'bg-danger bg-opacity-10';
                                            } elseif ($isCorrect) {
                                                $optionClass = 'bg-success bg-opacity-10';
                                            }
                                        @endphp

                                        <div class="form-check p-2 rounded mb-2 {{ $optionClass }}">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <strong>{{ $letter }}.</strong>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input class="form-check-input" type="radio" disabled {{ $isUserAnswer ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        {{ $option->option }}
                                                    </label>
                                                </div>
                                                <div>
                                                    @if ($isUserAnswer && !$isCorrect)
                                                        <i class="fas fa-times-circle text-danger ms-2"></i>
                                                    @elseif ($isUserAnswer && $isCorrect)
                                                        <i class="fas fa-check-circle text-success ms-2"></i>
                                                    @elseif ($isCorrect)
                                                        <i class="fas fa-check-circle text-success ms-2"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="essay-answer p-3 rounded {{ $earnedPoints > 0 ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                                                <p class="mb-2"><strong>{{ __('quiz.your_answer') }}:</strong></p>
                                                <div class="p-3 bg-white rounded">
                                                    {{ $userAnswer->essay_answer ?? __('quiz.no_answer') }}
                                                </div>

                                                @if($userAnswer && !$userAnswer->is_graded)
                                                    <div class="mt-2">
                                                        <span class="badge bg-warning">{{ __('quiz.pending_grading') }}</span>
                                                    </div>
                                                @elseif($userAnswer && $userAnswer->is_graded)
                                                    <div class="mt-2">
                                                        <span class="badge {{ $scoreColorClass === 'text-success' ? 'bg-success' : ($scoreColorClass === 'text-warning' ? 'bg-warning' : 'bg-danger') }}">
                                                            {{ $earnedPoints }} / {{ $maxPoints }} {{ __('quiz.points') }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-4">
                        <a href="{{ route('quiz.index') }}" class="btn btn-primary">{{ __('general.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom styles for the result page */
    .points-display {
        font-size: 1.1rem;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .text-success {
        color: #198754 !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .form-check.bg-success.bg-opacity-10 {
        border: 1px solid rgba(25, 135, 84, 0.3);
    }

    .form-check.bg-danger.bg-opacity-10 {
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .essay-answer {
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .essay-answer.bg-success.bg-opacity-10 {
        border-color: rgba(25, 135, 84, 0.3);
    }

    .essay-answer.bg-danger.bg-opacity-10 {
        border-color: rgba(220, 53, 69, 0.3);
    }
</style>
@endsection
