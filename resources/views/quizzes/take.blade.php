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

                            @foreach ($questions as $index => $question)
                                <div class="question-card mb-4" data-question-id="{{ $question->id }}">
                                    <h6 class="mb-3">{{ $index + 1 }}). {{ $question->question }}</h6>

                                    @if($question->image_path)
                                        <div class="question-image mb-3">
                                            <img src="{{ Storage::url($question->image_path) }}" class="img-fluid" alt="Question Image" style="max-height: 300px; object-fit: contain;">
                                        </div>
                                    @endif

                                    @if ($question->type === 'multiple_choice')
                                        @foreach ($question->options->sortBy('order') as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio"
                                                    name="answers[{{ $question->id }}]" id="option-{{ $option->id }}"
                                                    value="{{ $option->id }}">
                                                <label class="form-check-label" for="option-{{ $option->id }}">
                                                    {{ $option->option }}
                                                    @if($option->image_path)
                                                        <div class="option-image mt-2">
                                                            <img src="{{ Storage::url($option->image_path) }}" class="img-fluid" alt="Option Image" style="max-height: 150px; object-fit: contain;">
                                                        </div>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <textarea class="form-control" name="answers[{{ $question->id }}]" rows="4"
                                            placeholder="{{ __('quiz.essay_answer') }}"></textarea>
                                    @endif
                                </div>
                            @endforeach

                            <div class="progress mb-4">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" style="width: 0%" id="quiz-progress">0%</div>
                            </div>

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
            function updateProgress() {
                const totalQuestions = {{ $questions->count() }};
                let answeredQuestions = 0;

                // Check untuk multiple choice
                document.querySelectorAll('input[type="radio"]:checked').forEach(() => answeredQuestions++);

                // Check untuk essay
                document.querySelectorAll('textarea').forEach(textarea => {
                    if (textarea.value.trim() !== '') {
                        answeredQuestions++;
                    }
                });

                const progress = (answeredQuestions / totalQuestions) * 100;
                const progressBar = document.getElementById('quiz-progress');
                progressBar.style.width = progress + '%';
                progressBar.textContent = Math.round(progress) + '%';

                // Update badge colors
                document.querySelectorAll('.question-badge').forEach((badge, index) => {
                    const question = document.querySelectorAll('.question-card')[index];
                    const isAnswered =
                        question.querySelector('input[type="radio"]:checked') ||
                        (question.querySelector('textarea')?.value.trim() !== '');

                    badge.classList.remove('bg-secondary', 'bg-success');
                    badge.classList.add(isAnswered ? 'bg-success' : 'bg-secondary');
                });
            }

            // Event listeners
            document.querySelectorAll('input[type="radio"], textarea').forEach(element => {
                element.addEventListener('change', updateProgress);
                element.addEventListener('input', updateProgress);
            });

            // Initial call
            updateProgress();
        </script>
    @endpush

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
