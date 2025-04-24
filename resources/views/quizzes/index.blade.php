{{-- resources/views/quizzes/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <style>
        /* Tambahkan di bagian styles */
        .blink-animation {
            animation: blink 1s linear infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0.5;
            }
        }

        /* Pastikan responsif di mobile */
        @media (max-width: 375px) {
            .countdown-value {
                font-size: 18px;
                padding: 2px 6px;
            }

            .countdown-item {
                min-width: 30px;
            }
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">{{ __('quiz.title') }}</h1>

                <!-- Upcoming Quiz Alert (Mobile Friendly) -->
                @if ($upcomingQuizzes->isNotEmpty())
                    <div class="alert alert-info shadow-sm">
                        <h5 class="alert-heading">📅 {{ __('quiz.upcoming_quizzes') }}</h5>
                        @foreach ($upcomingQuizzes->take(2) as $quiz)
                            <div class="upcoming-quiz-item mb-3 p-3 bg-white rounded shadow-sm">
                                <div class="d-flex flex-column">
                                    <h6 class="mb-2">
                                        <i class="fas fa-clipboard-list me-2"></i>
                                        {{ $quiz->title }}
                                    </h6>
                                    <small class="text-muted mb-2">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $quiz->start_date->format('d M Y H:i') }}
                                    </small>
                                    <div class="countdown-container mt-2"
                                        data-start-date="{{ $quiz->start_date->toISOString() }}">
                                        <div class="countdown-items">
                                            <div class="countdown-item">
                                                <div class="countdown-value days">00</div>
                                                <div class="countdown-label">{{ __('quiz.days') }}</div>
                                            </div>
                                            <div class="countdown-separator">:</div>
                                            <div class="countdown-item">
                                                <div class="countdown-value hours">00</div>
                                                <div class="countdown-label">{{ __('quiz.hours') }}</div>
                                            </div>
                                            <div class="countdown-separator">:</div>
                                            <div class="countdown-item">
                                                <div class="countdown-value minutes">00</div>
                                                <div class="countdown-label">{{ __('quiz.minutes') }}</div>
                                            </div>
                                            <div class="countdown-separator">:</div>
                                            <div class="countdown-item">
                                                <div class="countdown-value seconds">00</div>
                                                <div class="countdown-label">{{ __('quiz.seconds') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Quiz yang Aktif -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">🚀 {{ __('quiz.active_quizzes') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        @forelse($activeQuizzes as $quiz)
                            <div class="quiz-card p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="quiz-info">
                                        <h6 class="mb-1">{{ $quiz->title }}</h6>
                                        <p class="mb-2 text-muted small">{{ Str::limit($quiz->description, 60) }}</p>
                                        <div class="quiz-meta">
                                            <span class="badge bg-light text-dark me-2">
                                                <i class="fas fa-clock"></i>
                                                {{ $quiz->duration }} {{ __('quiz.minutes') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="quiz-action mt-2">
                                        @if (
                                            $quiz->single_attempt &&
                                                $quiz->attempts()->where('user_id', auth()->id())->exists())
                                            <span class="badge bg-secondary">{{ __('quiz.already_attempted') }}</span>
                                        @else
                                            <a href="{{ route('quiz.start', $quiz) }}" class="btn btn-primary btn-sm">
                                                {{ __('quiz.start') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <img src="{{ asset('images/no-quiz.svg') }}" alt="No Quiz" class="mb-3"
                                    style="max-width: 150px;">
                                <h6>{{ __('quiz.no_active_quizzes') }}</h6>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quiz yang Akan Datang -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">⏳ {{ __('quiz.upcoming_quizzes') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        @forelse($upcomingQuizzes as $quiz)
                            <div class="quiz-card p-3 border-bottom">
                                <div class="quiz-info">
                                    <h6 class="mb-1">{{ $quiz->title }}</h6>
                                    <p class="mb-2 text-muted small">{{ Str::limit($quiz->description, 60) }}</p>
                                    <div class="quiz-meta">
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="fas fa-calendar"></i>
                                            {{ $quiz->start_date->format('d M Y H:i') }}
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-clock"></i>
                                            {{ $quiz->duration }} {{ __('quiz.minutes') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <h6>{{ __('quiz.no_upcoming_quizzes') }}</h6>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- History -->
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">📜 {{ __('quiz.history') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        @forelse($quizHistory as $attempt)
                            <div class="quiz-card p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="quiz-info">
                                        <h6 class="mb-1">{{ $attempt->quiz->title }}</h6>
                                        <div class="quiz-meta">
                                            <span class="badge bg-light text-dark me-2">
                                                <i class="fas fa-calendar-check"></i>
                                                {{ $attempt->started_at->format('d M Y H:i') }}
                                            </span>
                                            <span
                                                class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'completed' ? 'warning' : 'info') }}">
                                                {{ __('quiz.' . $attempt->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="quiz-action">
                                        @if ($attempt->status !== 'in_progress')
                                            <div class="text-center">
                                                <div class="score-badge mb-2">
                                                    <span class="h4">{{ $attempt->score ?? '-' }}</span>
                                                    <small class="d-block">Score</small>
                                                </div>
                                                <a href="{{ route('quiz.result', $attempt) }}" class="btn btn-info btn-sm">
                                                    {{ __('general.view') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <h6>{{ __('quiz.no_quiz_history') }}</h6>
                            </div>
                        @endforelse
                    </div>
                    @if ($quizHistory->hasPages())
                        <div class="card-footer">
                            {{ $quizHistory->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Mobile-first styles */
            .upcoming-quiz-item {
                transition: all 0.3s ease;
            }

            .upcoming-quiz-item:hover {
                transform: translateY(-2px);
            }

            .countdown-container {
                background-color: #f8f9fa;
                border-radius: 8px;
                padding: 15px;
            }

            .countdown-items {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 5px;
            }

            .countdown-item {
                text-align: center;
                min-width: 45px;
            }

            .countdown-value {
                font-size: 24px;
                font-weight: bold;
                color: #007bff;
                background-color: white;
                border-radius: 8px;
                padding: 5px 10px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .countdown-label {
                font-size: 11px;
                color: #6c757d;
                margin-top: 5px;
                text-transform: uppercase;
            }

            .countdown-separator {
                font-size: 24px;
                font-weight: bold;
                color: #007bff;
            }

            .quiz-card {
                transition: all 0.3s ease;
            }

            .quiz-card:hover {
                background-color: #f8f9fa;
            }

            .quiz-meta .badge {
                font-weight: normal;
            }

            .score-badge {
                background-color: #f8f9fa;
                border-radius: 50%;
                width: 80px;
                height: 80px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                margin: 0 auto;
            }

            /* Responsive adjustments */
            @media (max-width: 576px) {
                .countdown-value {
                    font-size: 20px;
                    padding: 3px 8px;
                }

                .countdown-item {
                    min-width: 35px;
                }

                .quiz-action {
                    margin-top: 10px;
                }

                .quiz-card {
                    padding: 15px !important;
                }
            }
        </style>
        <!-- Add Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const countdowns = document.querySelectorAll('.countdown-container');

                countdowns.forEach(countdown => {
                    const startDate = new Date(countdown.dataset.startDate);

                    function updateCountdown() {
                        const now = new Date();
                        const diff = startDate - now;

                        if (diff <= 0) {
                            // Quiz sudah dimulai, reload halaman
                            location.reload();
                            return;
                        }

                        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                        countdown.querySelector('.days').textContent = String(days).padStart(2, '0');
                        countdown.querySelector('.hours').textContent = String(hours).padStart(2, '0');
                        countdown.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
                        countdown.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');

                        // Animasi berkedip untuk kurang dari 1 menit
                        if (diff < 60000) {
                            countdown.classList.add('blink-animation');
                        }
                    }

                    updateCountdown();
                    setInterval(updateCountdown, 1000);
                });
            });
        </script>
    @endpush
@endsection
