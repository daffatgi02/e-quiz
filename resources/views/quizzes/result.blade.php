{{-- resources/views/quizzes/result.blade.php --}}
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
                        <div class="card mb-3">
                            <div class="card-header">
                                {{ $index + 1 }}). {{ $question->question }}
                                @if($attempt->status === 'graded')
                                    <span class="float-end">
                                        {{ __('quiz.points') }}:
                                        {{ $attempt->answers->where('question_id', $question->id)->first()->points_earned ?? 0 }} /
                                        {{ $question->points }}
                                    </span>
                                @endif
                            </div>
                            <div class="card-body">
                                @php
                                    $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                                @endphp

                                @if($question->type === 'multiple_choice')
                                    @foreach($question->options as $option)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" disabled
                                                {{ $userAnswer && $userAnswer->question_option_id == $option->id ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                {{ $option->option }}
                                                @if($option->is_correct)
                                                    <span class="badge bg-success ms-2">{{ __('quiz.correct_answer') }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p><strong>{{ __('quiz.your_answer') }}:</strong></p>
                                    <p>{{ $userAnswer->essay_answer ?? __('quiz.no_answer') }}</p>
                                    @if($userAnswer && !$userAnswer->is_graded)
                                        <span class="badge bg-warning">{{ __('quiz.pending_grading') }}</span>
                                    @endif
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
@endsection
