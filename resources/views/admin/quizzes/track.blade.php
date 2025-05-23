{{-- resources/views/admin/quizzes/track.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>{{ __('quiz.tracking') }}: {{ $quiz->title }}</h1>
                    <div>
                        @if($statistics['total_participants'] > 0)
                            <form action="{{ route('admin.quizzes.reset-all-attempts', $quiz) }}" method="POST" class="d-inline" id="resetAllForm">
                                @csrf
                                <button type="button" class="btn btn-danger" id="resetAllBtn">
                                    <i class="fas fa-trash"></i> {{ __('quiz.reset_all_attempts') }}
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary ms-2">
                            {{ __('general.back') }}
                        </a>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>{{ __('quiz.in_progress') }}</h6>
                                <h3>{{ $statistics['in_progress'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>{{ __('quiz.completed') }}</h6>
                                <h3>{{ $statistics['completed'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>{{ __('quiz.graded') }}</h6>
                                <h3>{{ $statistics['graded'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>{{ __('quiz.total_participants') }}</h6>
                                <h3>{{ $statistics['total_participants'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content - combined participants and token users -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('quiz.participants_status') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($attempts->isEmpty() && (!$quiz->requires_token || $quiz->tokenUsers->isEmpty()))
                            <div class="text-center py-5">
                                <h5>{{ __('quiz.no_participants_yet') }}</h5>
                                <p class="text-muted">{{ __('quiz.tracking_available_when_started') }}</p>
                            </div>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.name') }}</th>
                                        <th>{{ __('general.nik') }}</th>
                                        <th>{{ __('general.department') }}</th>
                                        @if($quiz->requires_token)
                                            <th>{{ __('quiz.token_used_at') }}</th>
                                        @endif
                                        <th>{{ __('quiz.status') }}</th>
                                        <th>{{ __('quiz.started_at') }}</th>
                                        <th>{{ __('quiz.remaining_time') }}</th>
                                        <th>{{ __('quiz.score') }}</th>
                                        <th>{{ __('general.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($quiz->requires_token)
                                        {{-- Display token users who haven't attempted yet --}}
                                        @foreach($quiz->tokenUsers as $user)
                                            @php
                                                $userAttempt = $attempts->where('user_id', $user->id)->first();
                                                if($userAttempt) continue; // Skip if user has attempt (will show in attempts loop)
                                            @endphp
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->nik }}</td>
                                                <td>{{ $user->department }}</td>
                                                <td>
                                                    @if (is_string($user->pivot->token_used_at))
                                                        {{ $user->pivot->token_used_at }}
                                                    @elseif($user->pivot->token_used_at)
                                                        {{ $user->pivot->token_used_at->format('Y-m-d H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ __('quiz.not_started') }}
                                                    </span>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>
                                                    <form action="{{ route('admin.quizzes.revoke-token', [$quiz, $user]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning"
                                                            onclick="return confirm('{{ __('quiz.revoke_token_confirm') }}')">
                                                            <i class="fas fa-ban"></i> {{ __('quiz.revoke_token') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    {{-- Display all attempts --}}
                                    @foreach ($attempts as $attempt)
                                        @php
                                            $remainingTime = null;
                                            if ($attempt->status === 'in_progress') {
                                                $endTime = $attempt->started_at->addMinutes($quiz->duration);
                                                $remainingTime = now()->diffInMinutes($endTime, false);
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $attempt->user->name }}</td>
                                            <td>{{ $attempt->user->nik }}</td>
                                            <td>{{ $attempt->user->department }}</td>
                                            @if($quiz->requires_token)
                                                <td>
                                                    @php
                                                        $tokenUser = $quiz->tokenUsers->where('id', $attempt->user->id)->first();
                                                    @endphp
                                                    @if ($tokenUser && isset($tokenUser->pivot))
                                                        @if (is_string($tokenUser->pivot->token_used_at))
                                                            {{ $tokenUser->pivot->token_used_at }}
                                                        @elseif($tokenUser->pivot->token_used_at)
                                                            {{ $tokenUser->pivot->token_used_at->format('Y-m-d H:i') }}
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
                                                <span
                                                    class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'completed' ? 'warning' : 'info') }}">
                                                    {{ __('quiz.' . $attempt->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $attempt->started_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if ($remainingTime !== null)
                                                    @if ($remainingTime > 0)
                                                        <span class="badge bg-info">{{ $remainingTime }}
                                                            {{ __('quiz.minutes') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ __('quiz.time_up') }}</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $attempt->score ?? '-' }}</td>
                                            <td>
                                                @if ($attempt->status === 'in_progress')
                                                    <form action="{{ route('admin.quizzes.kick-user', [$quiz, $attempt]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('{{ __('quiz.kick_user_confirm') }}')">
                                                            <i class="fas fa-user-times"></i> {{ __('quiz.kick') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.reports.attempt.detail', $attempt) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> {{ __('general.view') }}
                                                    </a>

                                                    <form action="{{ route('admin.quizzes.reset-attempt', ['quiz' => $quiz, 'user' => $attempt->user]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning"
                                                            onclick="return confirm('{{ __('quiz.confirm_reset_attempt') }}')">
                                                            <i class="fas fa-redo"></i>
                                                            {{ __('quiz.reset_attempt') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $attempts->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto refresh halaman setiap 30 detik
            setInterval(function() {
                location.reload();
            }, 30000);

            // Reset All confirmation
            document.addEventListener('DOMContentLoaded', function() {
                const resetAllBtn = document.getElementById('resetAllBtn');
                const resetAllForm = document.getElementById('resetAllForm');

                if (resetAllBtn) {
                    resetAllBtn.addEventListener('click', function() {
                        Swal.fire({
                            title: '{{ __("quiz.reset_all_attempts") }}',
                            text: '{{ __("quiz.confirm_reset_all_attempts") }}',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: '{{ __("general.yes") }}',
                            cancelButtonText: '{{ __("general.cancel") }}'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                resetAllForm.submit();
                            }
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection
