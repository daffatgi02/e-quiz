{{-- resources/views/quizzes/take.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $quiz->title }}</h5>
                        <div id="timer" class="badge bg-primary fs-5"></div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="quiz-form" method="POST" action="{{ route('quiz.submit', $attempt) }}">
                        @csrf

                        @foreach($questions as $index => $question)
                            <div class="question-card mb-4" data-question-id="{{ $question->id }}">
                                <h6 class="mb-3">{{ __('quiz.question') }} {{ $index + 1 }}: {{ $question->question }}</h6>

                                @if($question->type === 'multiple_choice')
                                    @foreach($question->options as $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio"
                                                name="answers[{{ $question->id }}]"
                                                id="option-{{ $option->id }}"
                                                value="{{ $option->id }}">
                                            <label class="form-check-label" for="option-{{ $option->id }}">
                                                {{ $option->option }}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <textarea class="form-control"
                                        name="answers[{{ $question->id }}]"
                                        rows="4"
                                        placeholder="{{ __('quiz.essay_answer') }}"></textarea>
                                @endif
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">{{ __('quiz.submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const remainingSeconds = {{ $remainingSeconds }};
    const timerElement = document.getElementById('timer');
    let timeLeft = remainingSeconds;

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft <= 300) {
            timerElement.classList.remove('bg-primary');
            timerElement.classList.add('bg-danger');
        }

        if (timeLeft <= 0) {
            document.getElementById('quiz-form').submit();
        } else {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }
    }

    updateTimer();

    // Auto-save answers
    const form = document.getElementById('quiz-form');
    let saveTimeout;

    form.addEventListener('change', function(e) {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            const formData = new FormData(form);
            formData.append('attempt_id', '{{ $attempt->id }}');

            fetch('{{ route('quiz.save-answer') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            });
        }, 1000);
    });
});
</script>
@endpush
@endsection
