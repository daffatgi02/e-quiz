{{-- resources/views/admin/quizzes/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('quiz.edit') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">{{ __('quiz.title') }}</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $quiz->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('general.description') }}</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description', $quiz->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="start_date" class="form-label">{{ __('quiz.start_date') }}</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date"
                                    value="{{ old('start_date', $quiz->start_date->format('Y-m-d\TH:i')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="duration" class="form-label">{{ __('quiz.duration') }}
                                    ({{ __('quiz.minutes') }})</label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror"
                                    id="duration" name="duration" value="{{ old('duration', $quiz->duration) }}"
                                    min="1" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="question_type" class="form-label">{{ __('quiz.question_type') }}</label>
                                <select class="form-select @error('question_type') is-invalid @enderror" id="question_type"
                                    name="question_type" required>
                                    <option value="multiple_choice"
                                        {{ old('question_type', $quiz->question_type) == 'multiple_choice' ? 'selected' : '' }}>
                                        {{ __('quiz.multiple_choice') }}</option>
                                    <option value="essay"
                                        {{ old('question_type', $quiz->question_type) == 'essay' ? 'selected' : '' }}>
                                        {{ __('quiz.essay') }}</option>
                                    <option value="mixed"
                                        {{ old('question_type', $quiz->question_type) == 'mixed' ? 'selected' : '' }}>
                                        {{ __('quiz.mixed') }}</option>
                                </select>
                                @error('question_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="single_attempt"
                                        name="single_attempt" value="1"
                                        {{ old('single_attempt', $quiz->single_attempt) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="single_attempt">
                                        {{ __('quiz.single_attempt') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('quiz.active') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="requires_token"
                                        name="requires_token" value="1"
                                        {{ old('requires_token', $quiz->requires_token) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_token">
                                        {{ __('Aktifkan Token Quiz') }}
                                    </label>
                                </div>
                                <small class="text-muted">Jika diaktifkan, peserta harus memasukkan token untuk mengakses
                                    quiz</small>
                            </div>

                            @if ($quiz->requires_token)
                                <div class="mb-3">
                                    <label class="form-label">Current Token</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $quiz->quiz_token }}"
                                            readonly>
                                        <button type="button" class="btn btn-outline-secondary" id="regenerateTokenBtn">
                                            Regenerate
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3" id="token-expiry-container"
                                style="{{ old('requires_token', $quiz->requires_token) ? '' : 'display: none;' }}">
                                <label for="token_expires_at" class="form-label">{{ __('Token Kadaluarsa') }}</label>
                                <input type="datetime-local"
                                    class="form-control @error('token_expires_at') is-invalid @enderror"
                                    id="token_expires_at" name="token_expires_at"
                                    value="{{ old('token_expires_at', $quiz->token_expires_at ? $quiz->token_expires_at->format('Y-m-d\TH:i') : '') }}">
                                @error('token_expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                            <a href="{{ route('admin.quizzes.index') }}"
                                class="btn btn-secondary">{{ __('general.cancel') }}</a>
                        </form>
                        <!-- Hidden form untuk regenerate token -->
                        <form id="regenerateTokenForm" action="{{ route('admin.quizzes.regenerate-token', $quiz) }}"
                            method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('requires_token').addEventListener('change', function() {
                document.getElementById('token-expiry-container').style.display =
                    this.checked ? 'block' : 'none';
            });

            @if ($quiz->requires_token)
                document.getElementById('regenerateTokenBtn').addEventListener('click', function() {
                    if (confirm(
                            'Apakah Anda yakin ingin regenerate token? Pastikan tidak ada peserta yang sedang mengerjakan quiz.'
                        )) {
                        document.getElementById('regenerateTokenForm').submit();
                    }
                });
            @endif
        </script>
    @endpush
@endsection
