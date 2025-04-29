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

        /* Floating button styles */
        .btn.rounded-circle {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn.rounded-circle i {
            margin: 0 !important;
        }

        /* Posisi default (kanan) untuk desktop */
        .floating-refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1030;
            transition: all 0.3s ease;
        }

        /* Posisi kiri untuk mobile */
        @media (max-width: 768px) {
            .floating-refresh-btn {
                right: auto;
                /* Reset posisi kanan */
                left: 20px;
                /* Atur posisi kiri */
            }
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">SELAMAT DATANG!</h1>

                {{-- Alert untuk quiz yang sedang dikerjakan --}}
                @if ($inProgressAttempts && $inProgressAttempts->count() > 0)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ __('quiz.in_progress_alert') }}
                        </h5>

                        @foreach ($inProgressAttempts as $attempt)
                            <p>{{ __('quiz.you_have_quiz_in_progress') }}: <strong>{{ $attempt->quiz->title }}</strong></p>
                        @endforeach

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

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
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tokenModal">
                                <i class="fas fa-key"></i> Masukkan Token
                            </button>
                        </div>
                    </div>
                </div>
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
                                        @php
                                            $userInProgressAttempt = $inProgressAttempts
                                                ->where('quiz_id', $quiz->id)
                                                ->first();
                                            $hasCompletedAttempt = $quiz
                                                ->attempts()
                                                ->where('user_id', auth()->id())
                                                ->where('status', '!=', 'in_progress')
                                                ->exists();
                                        @endphp

                                        @if ($userInProgressAttempt)
                                            <a href="{{ route('quiz.take', $userInProgressAttempt) }}"
                                                class="btn btn-warning btn-sm">
                                                {{ __('quiz.continue') }}
                                            </a>
                                        @elseif($quiz->single_attempt && $hasCompletedAttempt)
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
                                <h6>{{ __('quiz.no_active_quizzes') }}</h6>
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
        <!-- Floating Refresh Button -->
        <div class="floating-refresh-btn">
            <button class="btn btn-primary rounded-circle shadow" onclick="location.reload();"
                title="{{ __('general.refresh') }}">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <!-- Token Modal -->
    <div class="modal fade" id="tokenModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Masukkan Token Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('quiz.token.validate') }}" method="POST" id="tokenForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quiz_token" class="form-label">Token Quiz</label>
                            <input type="text" class="form-control" id="quiz_token" name="quiz_token"
                                placeholder="Format: ABCD-EFGH" maxlength="9" required>
                            <small class="form-text text-muted">
                                Masukkan token yang diberikan oleh admin
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Validasi Token</button>
                    </div>
                </form>
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
        <script>
            // Token validation with SweetAlert2
            document.getElementById('tokenForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const token = document.getElementById('quiz_token').value;
                const tokenRegex = /^[A-Z]{4}-[A-Z]{4}$/;

                // Validasi format token
                if (!tokenRegex.test(token)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Token Salah',
                        text: 'Format token yang benar adalah: ABCD-EFGH (8 huruf kapital dengan tanda - di tengah)',
                        confirmButtonText: 'Coba Lagi'
                    });
                    return;
                }

                // Jika format benar, kirim form
                const formData = new FormData(this);

                fetch('{{ route('quiz.token.validate') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => {
                        if (response.redirected) {
                            // Jika redirect, berarti token valid
                            window.location.href = response.url;
                            return null;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data === null) return; // Sudah di-redirect

                        if (data.errors && data.errors.quiz_token) {
                            // Tampilkan error
                            Swal.fire({
                                icon: 'error',
                                title: 'Token Tidak Valid',
                                text: data.errors.quiz_token,
                                confirmButtonText: 'Coba Lagi'
                            });
                        } else {
                            // Fallback jika ada error lain
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Silakan coba lagi nanti atau hubungi administrator.',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal memproses token. Silakan coba lagi nanti.',
                            confirmButtonText: 'OK'
                        });
                    });
            });

            // Tampilkan SweetAlert jika ada error atau success dari session
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'OK'
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: "{{ session('error') }}",
                    confirmButtonText: '{{ __('general.ok') }}'
                });
            @endif
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: "{{ session('info') }}",
                    confirmButtonText: 'OK'
                });
            @endif

            @if ($errors->has('quiz_token'))
                Swal.fire({
                    icon: 'error',
                    title: 'Token Tidak Valid',
                    text: "{{ $errors->first('quiz_token') }}",
                    confirmButtonText: 'Coba Lagi'
                });
            @endif
        </script>
        <script>
            // Show kicked notification if redirected from take page
            @if (session('error') && session('error') == 'Anda telah dikeluarkan dari quiz ini oleh administrator.')
                Swal.fire({
                    icon: 'warning',
                    title: 'Anda Dikeluarkan',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK'
                });
            @endif
        </script>
    @endpush
@endsection
