{{-- resources/views/admin/reports/pending-grading.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ __('quiz.pending_grading') }}</h1>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    {{ __('general.back') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    @forelse($pendingAnswers as $answer)
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ __('general.user') }}:</strong> {{ $answer->quizAttempt->user->name }} ({{ $answer->quizAttempt->user->nik }})
                                        <br>
                                        <strong>{{ __('quiz.title') }}:</strong> {{ $answer->quizAttempt->quiz->title }}
                                    </div>
                                    <div>
                                        <strong>{{ __('general.date') }}:</strong> {{ $answer->created_at->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5>{{ __('quiz.question') }}:</h5>
                                <p>{{ $answer->question->question }}</p>

                                <h5>{{ __('quiz.answer') }}:</h5>
                                <p>{{ $answer->essay_answer }}</p>

                                <form action="{{ route('admin.reports.grade', $answer) }}" method="POST" class="mt-3">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <input type="number" name="points_earned" class="form-control"
                                                    min="0" max="{{ $answer->question->points }}" step="0.5" required>
                                                <span class="input-group-text">/ {{ $answer->question->points }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <h4>{{ __('quiz.no_pending_grading') }}</h4>
                        </div>
                    @endforelse

                    {{ $pendingAnswers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
