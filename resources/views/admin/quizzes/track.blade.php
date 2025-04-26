{{-- resources/views/admin/quizzes/track.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>{{ __('quiz.tracking') }}: {{ $quiz->title }}</h1>
                    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
                        {{ __('general.back') }}
                    </a>
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

                <!-- Users List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('quiz.participants_status') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($attempts->isEmpty())
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
                                        <th>{{ __('quiz.status') }}</th>
                                        <th>{{ __('quiz.started_at') }}</th>
                                        <th>{{ __('quiz.remaining_time') }}</th>
                                        <th>{{ __('quiz.score') }}</th>
                                        <th>{{ __('general.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                                    <form
                                                        action="{{ route('admin.quizzes.kick-user', [$quiz, $attempt]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Apakah Anda yakin ingin kick peserta ini?')">
                                                            <i class="fas fa-user-times"></i> Kick
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.reports.attempt.detail', $attempt) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> {{ __('general.view') }}
                                                    </a>

                                                    <form
                                                        action="{{ route('admin.quizzes.reset-attempt', ['quiz' => $quiz, 'user' => $attempt->user]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning"
                                                            onclick="return confirm('{{ __('quiz.confirm_reset_attempt') }}')">
                                                            <i class="fas fa-redo"></i> {{ __('quiz.reset_attempt') }}
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
        </script>
    @endpush
@endsection
