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
                        <!-- Question Navigation -->
                        <div class="question-navigation mb-4">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($questions as $index => $question)
                                    <button type="button" class="btn btn-sm question-nav-btn"
                                            data-question="{{ $index }}"
                                            title="{{ __('quiz.question') }} {{ $index + 1 }}">
                                        {{ $index + 1 }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <form id="quiz-form" method="POST" action="{{ route('quiz.submit', $attempt) }}">
                            @csrf

                            @foreach ($questions as $index => $question)
                                <div class="question-card" data-question-id="{{ $question->id }}"
                                     data-question-index="{{ $index }}" style="display: none;">

                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h6 class="mb-0">{{ __('quiz.question') }} {{ $index + 1 }} {{ __('of') }} {{ $questions->count() }}</h6>
                                        <button type="button" class="btn btn-sm btn-outline-warning mark-question"
                                                data-question="{{ $index }}">
                                            <i class="fas fa-bookmark"></i>
                                            <span>{{ __('quiz.mark') }}</span>
                                        </button>
                                    </div>

                                    <p class="mb-3">{{ $question->question }}</p>

                                    @if($question->image_path)
                                        <div class="question-image mb-3">
                                            <img src="{{ Storage::url($question->image_path) }}" class="img-fluid"
                                                 alt="Question Image" style="max-height: 300px; object-fit: contain;">
                                        </div>
                                    @endif

                                    @if ($question->type === 'multiple_choice')
                                        @foreach ($question->options->sortBy('order') as $option)
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio"
                                                    name="answers[{{ $question->id }}]"
                                                    id="option-{{ $option->id }}"
                                                    value="{{ $option->id }}">
                                                <label class="form-check-label d-flex align-items-start"
                                                       for="option-{{ $option->id }}">
                                                    <span>{{ $option->option }}</span>
                                                    @if($option->image_path)
                                                        <div class="option-image ms-3">
                                                            <img src="{{ Storage::url($option->image_path) }}"
                                                                 class="img-fluid" alt="Option Image"
                                                                 style="max-height: 150px; object-fit: contain;">
                                                        </div>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <textarea class="form-control" name="answers[{{ $question->id }}]"
                                                  rows="4" placeholder="{{ __('quiz.essay_answer') }}"></textarea>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" id="prev-btn" disabled>
                                    <i class="fas fa-chevron-left"></i> {{ __('quiz.previous') }}
                                </button>
                                <div>
                                    <button type="button" class="btn btn-primary" id="next-btn">
                                        {{ __('quiz.next') }} <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <button type="button" class="btn btn-success" id="submit-btn" style="display: none;">
                                        <i class="fas fa-paper-plane"></i> {{ __('quiz.submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Question Status Summary -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="row text-center">
                                <div class="col">
                                    <span class="badge bg-success answered-count">0</span>
                                    <small class="d-block">{{ __('quiz.answered') }}</small>
                                </div>
                                <div class="col">
                                    <span class="badge bg-danger unanswered-count">{{ $questions->count() }}</span>
                                    <small class="d-block">{{ __('quiz.unanswered') }}</small>
                                </div>
                                <div class="col">
                                    <span class="badge bg-warning marked-count">0</span>
                                    <small class="d-block">{{ __('quiz.marked') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .question-nav-btn {
            width: 40px;
            height: 40px;
            padding: 0;
            font-weight: bold;
            border: 1px solid #dee2e6;
            background-color: #fff;
        }
        .question-nav-btn.answered {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }
        .question-nav-btn.marked {
            background-color: #ffc107;
            color: #000;
            border-color: #ffc107;
        }
        .question-nav-btn.current {
            border: 2px solid #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .question-card {
            min-height: 300px;
        }
        .mark-question.active {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables
            const questions = {{ $questions->count() }};
            let currentQuestion = 0;
            let answeredQuestions = new Set();
            let markedQuestions = new Set();

            // Show first question
            showQuestion(0);

            // Navigation button click handlers
            document.getElementById('prev-btn').addEventListener('click', () => {
                if (currentQuestion > 0) {
                    showQuestion(currentQuestion - 1);
                }
            });

            document.getElementById('next-btn').addEventListener('click', () => {
                if (currentQuestion < questions - 1) {
                    showQuestion(currentQuestion + 1);
                }
            });

            // Question navigation buttons
            document.querySelectorAll('.question-nav-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    showQuestion(parseInt(btn.dataset.question));
                });
            });

            // Mark question buttons
            document.querySelectorAll('.mark-question').forEach(btn => {
                btn.addEventListener('click', () => {
                    const questionIndex = parseInt(btn.dataset.question);
                    toggleMark(questionIndex);
                });
            });

            // Answer change handlers
            document.querySelectorAll('input[type="radio"], textarea').forEach(element => {
                element.addEventListener('change', () => {
                    const questionCard = element.closest('.question-card');
                    const questionIndex = parseInt(questionCard.dataset.questionIndex);
                    checkAndMarkAnswered(questionIndex);
                });

                element.addEventListener('input', () => {
                    if (element.tagName === 'TEXTAREA') {
                        const questionCard = element.closest('.question-card');
                        const questionIndex = parseInt(questionCard.dataset.questionIndex);
                        checkAndMarkAnswered(questionIndex);
                    }
                });
            });

            // Submit button handler
            document.getElementById('submit-btn').addEventListener('click', () => {
                checkAndSubmit();
            });

            function showQuestion(index) {
                // Hide all questions
                document.querySelectorAll('.question-card').forEach(card => {
                    card.style.display = 'none';
                });

                // Show selected question
                const questionCard = document.querySelector(`.question-card[data-question-index="${index}"]`);
                questionCard.style.display = 'block';

                currentQuestion = index;

                // Update navigation buttons
                document.getElementById('prev-btn').disabled = (index === 0);
                document.getElementById('next-btn').style.display = (index === questions - 1) ? 'none' : 'inline-block';
                document.getElementById('submit-btn').style.display = (index === questions - 1) ? 'inline-block' : 'none';

                // Update navigation button styles
                document.querySelectorAll('.question-nav-btn').forEach(btn => {
                    btn.classList.remove('current');
                });
                document.querySelector(`.question-nav-btn[data-question="${index}"]`).classList.add('current');
            }

            function checkAndMarkAnswered(questionIndex) {
                const questionCard = document.querySelector(`.question-card[data-question-index="${questionIndex}"]`);
                const isAnswered = checkIfQuestionAnswered(questionCard);

                if (isAnswered) {
                    answeredQuestions.add(questionIndex);
                    const navBtn = document.querySelector(`.question-nav-btn[data-question="${questionIndex}"]`);
                    navBtn.classList.add('answered');
                } else {
                    answeredQuestions.delete(questionIndex);
                    const navBtn = document.querySelector(`.question-nav-btn[data-question="${questionIndex}"]`);
                    navBtn.classList.remove('answered');
                }

                updateSummary();
            }

            function checkIfQuestionAnswered(questionCard) {
                const radioInputs = questionCard.querySelectorAll('input[type="radio"]');
                const textareaInput = questionCard.querySelector('textarea');

                if (radioInputs.length > 0) {
                    // Check if any radio button is selected
                    return Array.from(radioInputs).some(input => input.checked);
                } else if (textareaInput) {
                    // Check if textarea has content
                    return textareaInput.value.trim() !== '';
                }

                return false;
            }

            function toggleMark(questionIndex) {
                const markBtn = document.querySelector(`.mark-question[data-question="${questionIndex}"]`);
                const navBtn = document.querySelector(`.question-nav-btn[data-question="${questionIndex}"]`);

                if (markedQuestions.has(questionIndex)) {
                    markedQuestions.delete(questionIndex);
                    markBtn.classList.remove('active');
                    navBtn.classList.remove('marked');
                } else {
                    markedQuestions.add(questionIndex);
                    markBtn.classList.add('active');
                    navBtn.classList.add('marked');
                }
                updateSummary();
            }

            function updateSummary() {
                document.querySelector('.answered-count').textContent = answeredQuestions.size;
                document.querySelector('.unanswered-count').textContent = questions - answeredQuestions.size;
                document.querySelector('.marked-count').textContent = markedQuestions.size;
            }

            function checkAndSubmit() {
                // Check all questions are answered
                let allAnswered = true;
                let unansweredCount = 0;
                let unansweredQuestions = [];

                document.querySelectorAll('.question-card').forEach((card, index) => {
                    if (!checkIfQuestionAnswered(card)) {
                        allAnswered = false;
                        unansweredCount++;
                        unansweredQuestions.push(index + 1);
                    }
                });

                if (!allAnswered) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pertanyaan Belum Lengkap',
                        html: `Anda harus menjawab semua pertanyaan sebelum submit.<br><br>` +
                              `<strong>Pertanyaan yang belum dijawab:</strong><br>` +
                              `${unansweredQuestions.join(', ')}<br><br>` +
                              `<strong>Total: ${unansweredCount} pertanyaan</strong>`,
                        confirmButtonText: 'Periksa Kembali',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                // All questions answered, confirm submission
                Swal.fire({
                    title: '{{ __('quiz.confirm_submit') }}',
                    text: '{{ __('quiz.confirm_submit_message') }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{ __('quiz.submit_quiz') }}',
                    cancelButtonText: '{{ __('quiz.review_answers') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('quiz-form').submit();
                    }
                });
            }

            // Timer functionality
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
                    Swal.fire({
                        icon: 'info',
                        title: 'Waktu Habis!',
                        text: 'Waktu pengerjaan quiz telah habis. Quiz akan disubmit otomatis.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        document.getElementById('quiz-form').submit();
                    });
                } else {
                    timeLeft--;
                    setTimeout(updateTimer, 1000);
                }
            }

            updateTimer();

            // Auto-save functionality
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
                    }).then(response => {
                        if (response.ok) {
                            // Optional: Show auto-save success toast
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'bottom-end',
                                showConfirmButton: false,
                                timer: 1000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: 'success',
                                title: 'Jawaban tersimpan otomatis'
                            });
                        }
                    });
                }, 1000);
            });

            // Initial check for answered questions
            document.querySelectorAll('.question-card').forEach((card, index) => {
                checkAndMarkAnswered(index);
            });
        });
    </script>
    @endpush
@endsection
