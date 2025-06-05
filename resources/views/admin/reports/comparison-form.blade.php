{{-- resources/views/admin/reports/comparison-form.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('general.training_comparison') }}</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.reports.export.training.dynamic') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label">{{ __('general.training_title') }}</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="Contoh: Training APAR - PT WIG" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="pre_test_quiz_id" class="form-label">{{ __('general.pre_test_quiz') }}</label>
                                <select class="form-select @error('pre_test_quiz_id') is-invalid @enderror" 
                                        id="pre_test_quiz_id" name="pre_test_quiz_id" required>
                                    <option value="">{{ __('general.select_pre_test') }}</option>
                                    @foreach($quizzes as $quiz)
                                        <option value="{{ $quiz->id }}" {{ old('pre_test_quiz_id') == $quiz->id ? 'selected' : '' }}>
                                            {{ $quiz->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pre_test_quiz_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="post_test_quiz_id" class="form-label">{{ __('general.post_test_quiz') }}</label>
                                <select class="form-select @error('post_test_quiz_id') is-invalid @enderror" 
                                        id="post_test_quiz_id" name="post_test_quiz_id" required>
                                    <option value="">{{ __('general.select_post_test') }}</option>
                                    @foreach($quizzes as $quiz)
                                        <option value="{{ $quiz->id }}" {{ old('post_test_quiz_id') == $quiz->id ? 'selected' : '' }}>
                                            {{ $quiz->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('post_test_quiz_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- NEW: Filter berdasarkan status kelulusan -->
                            <div class="mb-3">
                                <label for="status_filter" class="form-label">{{ __('general.status_filter') }}</label>
                                <select class="form-select" id="status_filter" name="status_filter">
                                    <option value="all" {{ old('status_filter', 'all') == 'all' ? 'selected' : '' }}>
                                        {{ __('general.all_participants') }}
                                    </option>
                                    <option value="passed" {{ old('status_filter') == 'passed' ? 'selected' : '' }}>
                                        {{ __('general.passed_only') }}
                                    </option>
                                    <option value="failed" {{ old('status_filter') == 'failed' ? 'selected' : '' }}>
                                        {{ __('general.failed_only') }}
                                    </option>
                                    <option value="completed_post_test" {{ old('status_filter') == 'completed_post_test' ? 'selected' : '' }}>
                                        {{ __('general.completed_post_test_only') }}
                                    </option>
                                </select>
                                <small class="form-text text-muted">
                                    Filter peserta berdasarkan hasil Post Test
                                </small>
                            </div>

                            <!-- NEW: Sort option -->
                            <div class="mb-3">
                                <label for="sort_by" class="form-label">{{ __('general.sort_by') }}</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="name" {{ old('sort_by', 'name') == 'name' ? 'selected' : '' }}>
                                        {{ __('general.name') }}
                                    </option>
                                    <option value="post_test_score_desc" {{ old('sort_by') == 'post_test_score_desc' ? 'selected' : '' }}>
                                        {{ __('general.highest_post_test_score') }}
                                    </option>
                                    <option value="post_test_score_asc" {{ old('sort_by') == 'post_test_score_asc' ? 'selected' : '' }}>
                                        {{ __('general.lowest_post_test_score') }}
                                    </option>
                                    <option value="department" {{ old('sort_by') == 'department' ? 'selected' : '' }}>
                                        {{ __('general.department') }}
                                    </option>
                                    <option value="company" {{ old('sort_by') == 'company' ? 'selected' : '' }}>
                                        {{ __('general.company') }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="lang" class="form-label">{{ __('general.language') }}</label>
                                <select class="form-select" id="lang" name="lang">
                                    <option value="id" {{ old('lang', 'id') == 'id' ? 'selected' : '' }}>
                                        {{ __('general.indonesian') }}
                                    </option>
                                    <option value="en" {{ old('lang') == 'en' ? 'selected' : '' }}>
                                        {{ __('general.english') }}
                                    </option>
                                </select>
                            </div>

                            <div class="alert alert-info">
                                <strong>{{ __('general.info') }}:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Pilih quiz Pre Test dan Post Test yang ingin dibandingkan</li>
                                    <li>Masukkan judul training yang sesuai</li>
                                    <li><strong>Filter Status:</strong> Pilih untuk menampilkan hanya peserta yang lulus, tidak lulus, atau semua</li>
                                    <li><strong>Status kelulusan berdasarkan Post Test:</strong> Lead/Supervisor ≥80, Staff ≥70</li>
                                    <li>Laporan akan menampilkan perbandingan skor peserta sesuai filter yang dipilih</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
                                    {{ __('general.back') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-export"></i> {{ __('general.generate_comparison') }}
                                </button>
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
                const preTestSelect = document.getElementById('pre_test_quiz_id');
                const postTestSelect = document.getElementById('post_test_quiz_id');
                const titleInput = document.getElementById('title');

                // Auto generate title when both quizzes are selected
                function generateTitle() {
                    const preTestText = preTestSelect.options[preTestSelect.selectedIndex]?.text;
                    const postTestText = postTestSelect.options[postTestSelect.selectedIndex]?.text;

                    if (preTestText && postTestText && !titleInput.value.trim()) {
                        // Extract common parts from quiz titles
                        const preTestClean = preTestText.replace(/pre\s*test/gi, '').trim();
                        const postTestClean = postTestText.replace(/post\s*test/gi, '').trim();
                        
                        // Use the longer title as base
                        const baseTitle = preTestClean.length > postTestClean.length ? preTestClean : postTestClean;
                        titleInput.value = baseTitle || 'Training Comparison Report';
                    }
                }

                preTestSelect.addEventListener('change', generateTitle);
                postTestSelect.addEventListener('change', generateTitle);

                // Prevent selecting same quiz for both pre and post test
                preTestSelect.addEventListener('change', function() {
                    const selectedValue = this.value;
                    Array.from(postTestSelect.options).forEach(option => {
                        option.disabled = option.value === selectedValue && selectedValue !== '';
                    });
                });

                postTestSelect.addEventListener('change', function() {
                    const selectedValue = this.value;
                    Array.from(preTestSelect.options).forEach(option => {
                        option.disabled = option.value === selectedValue && selectedValue !== '';
                    });
                });
            });
        </script>
    @endpush
@endsection