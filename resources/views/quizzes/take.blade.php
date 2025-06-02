@extends('layouts.app')

@section('content')
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
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .question-card {
            min-height: 300px;
        }

        .mark-question.active {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
        
        /* Option styling */
        .option-letter {
            font-weight: bold;
            min-width: 25px;
            display: inline-block;
        }

        .option-content {
            flex: 1;
        }

        .form-check-label {
            display: flex !important;
            align-items: flex-start !important;
            width: 100%;
        }
        
        /* Auto-save indicator styles */
        .autosave-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1050;
        }
        
        .autosave-indicator.show {
            opacity: 1;
        }
        
        .autosave-indicator.saving {
            background-color: #17a2b8;
            color: white;
        }
        
        .autosave-indicator.saved {
            background-color: #28a745;
            color: white;
        }
        
        .autosave-indicator.error {
            background-color: #dc3545;
            color: white;
        }
    </style>
    
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
                                        <h6 class="mb-0">{{ __('quiz.question') }} {{ $index + 1 }}
                                            {{ __('quiz.of') }} {{ $questions->count() }}</h6>
                                        <button type="button" class="btn btn-sm btn-outline-warning mark-question"
                                            data-question="{{ $index }}">
                                            <i class="fas fa-bookmark"></i>
                                            <span>{{ __('quiz.mark') }}</span>
                                        </button>
                                    </div>

                                    <p class="mb-3">{{ $question->question }}</p>

                                    @if ($question->image_path)
                                        <div class="question-image mb-3">
                                            <img src="{{ Storage::url($question->image_path) }}" class="img-fluid"
                                                alt="Question Image" style="max-height: 300px; object-fit: contain;">
                                        </div>
                                    @endif

                                    @if ($question->type === 'multiple_choice')
                                        @foreach ($question->options->sortBy('order') as $optionIndex => $option)
                                            @php
                                                $savedAnswer = isset($savedAnswers) ? $savedAnswers->where('question_id', $question->id)->first() : null;
                                                $isChecked = $savedAnswer && $savedAnswer->question_option_id == $option->id;
                                            @endphp
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio"
                                                    name="answers[{{ $question->id }}]" id="option-{{ $option->id }}"
                                                    value="{{ $option->id }}" {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label d-flex align-items-start"
                                                    for="option-{{ $option->id }}">
                                                    <div class="option-letter me-2">{{ chr(65 + $optionIndex) }}.</div>
                                                    <div class="option-content">
                                                        <span>{{ $option->option }}</span>
                                                        @if ($option->image_path)
                                                            <div class="option-image ms-3">
                                                                <img src="{{ Storage::url($option->image_path) }}"
                                                                    class="img-fluid" alt="Option Image"
                                                                    style="max-height: 150px; object-fit: contain;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        @php
                                            $savedAnswer = isset($savedAnswers) ? $savedAnswers->where('question_id', $question->id)->first() : null;
                                            $essayValue = $savedAnswer ? $savedAnswer->essay_answer : '';
                                        @endphp
                                        <textarea class="form-control" name="answers[{{ $question->id }}]" rows="4"
                                            placeholder="{{ __('quiz.essay_answer') }}">{{ $essayValue }}</textarea>
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
        
        <!-- Auto-save indicator -->
        <div class="autosave-indicator" id="autosave-indicator"></div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize variables
                const questions = {{ $questions->count() }};
                let currentQuestion = 0;
                let answeredQuestions = new Set();
                let markedQuestions = new Set();
                const autosaveIndicator = document.getElementById('autosave-indicator');

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

                // Auto-save functionality
                let saveTimeout;
                
                // Initialize input event listeners for auto-save
                initializeAutoSave();
                
                function initializeAutoSave() {
                    // For multiple choice questions (radio buttons)
                    document.querySelectorAll('input[type="radio"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            const questionCard = this.closest('.question-card');
                            const questionId = questionCard.dataset.questionId;
                            const questionIndex = parseInt(questionCard.dataset.questionIndex);
                            
                            // Mark as answered in UI
                            checkAndMarkAnswered(questionIndex);
                            
                            // Save the answer
                            autoSaveAnswer(questionId, this.value);
                        });
                    });
                    
                    // For essay questions (textareas)
                    document.querySelectorAll('textarea').forEach(textarea => {
                        textarea.addEventListener('input', function() {
                            const questionCard = this.closest('.question-card');
                            const questionId = questionCard.dataset.questionId;
                            const questionIndex = parseInt(questionCard.dataset.questionIndex);
                            
                            clearTimeout(saveTimeout);
                            saveTimeout = setTimeout(() => {
                                // Only save if there's actual content
                                if (this.value.trim() !== '') {
                                    checkAndMarkAnswered(questionIndex);
                                    autoSaveAnswer(questionId, this.value);
                                } else {
                                    // If textarea is emptied, update the answered state
                                    answeredQuestions.delete(questionIndex);
                                    const navBtn = document.querySelector(`.question-nav-btn[data-question="${questionIndex}"]`);
                                    navBtn.classList.remove('answered');
                                    updateSummary();
                                }
                            }, 1000); // Save after 1 second of typing pause
                        });
                    });
                }
                
                function autoSaveAnswer(questionId, value) {
                    showAutosaveIndicator('saving');
                    
                    const formData = new FormData();
                    formData.append('attempt_id', '{{ $attempt->id }}');
                    formData.append('question_id', questionId);
                    formData.append('answer', value);
                    
                    fetch('{{ route('quiz.save-answer') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAutosaveIndicator('saved');
                        } else {
                            showAutosaveIndicator('error');
                        }
                    })
                    .catch(error => {
                        console.error('Error saving answer:', error);
                        showAutosaveIndicator('error');
                    });
                }
                
                function showAutosaveIndicator(status) {
                    autosaveIndicator.className = 'autosave-indicator';
                    
                    if (status === 'saving') {
                        autosaveIndicator.textContent = '⏳ {{ __("quiz.saving") }}...';
                        autosaveIndicator.classList.add('saving');
                    } else if (status === 'saved') {
                        autosaveIndicator.textContent = '✓ {{ __("quiz.saved") }}';
                        autosaveIndicator.classList.add('saved');
                    } else if (status === 'error') {
                        autosaveIndicator.textContent = '❌ {{ __("quiz.answer_not_saved") }}';
                        autosaveIndicator.classList.add('error');
                    }
                    
                    autosaveIndicator.classList.add('show');
                    
                    // Hide indicator after 2 seconds
                    setTimeout(() => {
                        autosaveIndicator.classList.remove('show');
                    }, 2000);
                }

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
                    document.getElementById('next-btn').style.display = (index === questions - 1) ? 'none' :
                        'inline-block';
                    document.getElementById('submit-btn').style.display = (index === questions - 1) ? 'inline-block' :
                        'none';

                    // Update navigation button styles
                    document.querySelectorAll('.question-nav-btn').forEach(btn => {
                        btn.classList.remove('current');
                    });
                    document.querySelector(`.question-nav-btn[data-question="${index}"]`).classList.add('current');
                    
                    // Update mark button state
                    const markBtn = document.querySelector(`.mark-question[data-question="${index}"]`);
                    if (markedQuestions.has(index)) {
                        markBtn.classList.add('active');
                    } else {
                        markBtn.classList.remove('active');
                    }
                }

                function checkAndMarkAnswered(questionIndex) {
                    const questionCard = document.querySelector(
                        `.question-card[data-question-index="${questionIndex}"]`);
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
                
                // Initialize saved answers
                // FIX: Perbaikan syntax error - definisi questionIndex ganda
                @if(isset($savedAnswers) && $savedAnswers->count() > 0)
                    @foreach($savedAnswers as $answer)
                        @if(($answer->question_option_id || ($answer->essay_answer && !empty($answer->essay_answer))))
                            try {
                                const questionElement = document.querySelector(`.question-card[data-question-id="{{ $answer->question_id }}"]`);
                                if (questionElement) {
                                    const qIndex = parseInt(questionElement.dataset.questionIndex);
                                    answeredQuestions.add(qIndex);
                                    document.querySelector(`.question-nav-btn[data-question="${qIndex}"]`).classList.add('answered');
                                }
                            } catch(e) {
                                console.error('Error processing saved answer:', e);
                            }
                        @endif
                    @endforeach
                    updateSummary();
                    
                    // Show a toast notification that answers were loaded
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    
                    Toast.fire({
                        icon: 'info',
                        title: '{{ __("quiz.continue_from_saved") }}'
                    });
                @endif
                
                // Add "before unload" warning to prevent accidental closing
                window.addEventListener('beforeunload', function(e) {
                    // If all questions are answered, don't show warning
                    if (answeredQuestions.size === questions) return;
                    
                    // Otherwise show warning
                    e.preventDefault();
                    e.returnValue = ''; // Chrome requires returnValue to be set
                    return ''; // For older browsers
                });
                
                // Check for kicked status periodically
                function checkKickStatus() {
                    fetch('/quiz/check-attempt/{{ $attempt->id }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.kicked === true) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Anda Dikeluarkan',
                                    text: 'Anda telah dikeluarkan dari quiz ini oleh administrator.',
                                    confirmButtonText: 'OK',
                                    allowOutsideClick: false
                                }).then(() => {
                                    window.location.href = '{{ route('quiz.index') }}';
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error checking kick status:', error);
                        });
                }
                
                // Check kick status every 5 seconds
                checkKickStatus();
                setInterval(checkKickStatus, 5000);
            });
        </script>
    @endpush
@endsection