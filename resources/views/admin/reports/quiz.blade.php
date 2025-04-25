{{-- resources/views/admin/reports/quiz.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ __('quiz.report') }}: {{ $quiz->title }}</h1>
                <div>
                    <button id="bulk-export" class="btn btn-success" disabled>
                        <i class="fas fa-file-pdf"></i> {{ __('quiz.export_selected') }}
                    </button>

                    <div class="dropdown d-inline-block">
                        <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-pdf"></i> {{ __('general.export') }} All
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.reports.export.quiz', ['quiz' => $quiz, 'lang' => 'id']) }}">
                                    🇮🇩 Bahasa Indonesia
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.reports.export.quiz', ['quiz' => $quiz, 'lang' => 'en']) }}">
                                    🇺🇸 English
                                </a>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        {{ __('general.back') }}
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Total Peserta</h6>
                            <h3>{{ $statistics['total_attempts'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>{{ __('general.average') }} {{ __('quiz.score') }}</h6>
                            <h3>{{ $statistics['average_score'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>{{ __('quiz.passed') }}</h6>
                            <h3>{{ $statistics['passed'] }} ({{ $statistics['passing_rate'] }}%)</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>{{ __('quiz.failed') }}</h6>
                            <h3>{{ $statistics['failed'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attempts Table -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Hasil Laporan</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all">
                            <label class="form-check-label" for="select-all">
                                {{ __('general.select_all') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="bulk-export-form" action="{{ route('admin.reports.export.bulk', $quiz) }}" method="POST">
                        @csrf
                        <input type="hidden" name="lang" id="bulk-export-lang" value="id">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" class="d-none">
                                    </th>
                                    <th>{{ __('general.user') }}</th>
                                    <th>{{ __('general.nik') }}</th>
                                    <th>{{ __('general.department') }}</th>
                                    <th>{{ __('quiz.started_at') }}</th>
                                    <th>{{ __('quiz.status') }}</th>
                                    <th>{{ __('quiz.score') }}</th>
                                    <th>{{ __('general.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $attempt)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="attempt_ids[]" value="{{ $attempt->id }}" class="attempt-checkbox">
                                        </td>
                                        <td>{{ $attempt->user->name }}</td>
                                        <td>{{ $attempt->user->nik }}</td>
                                        <td>{{ $attempt->user->department }}</td>
                                        <td>{{ $attempt->started_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'completed' ? 'warning' : 'info') }}">
                                                {{ __('quiz.' . $attempt->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($attempt->score !== null)
                                                {{ $attempt->score }}
                                                @if($attempt->score >= $passingScore)
                                                    <span class="badge bg-success">{{ __('quiz.passed') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('quiz.failed') }}</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.reports.attempt.detail', $attempt) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> {{ __('general.view') }}
                                            </a>

                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-file-pdf"></i> Export
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.reports.export.attempt', ['attempt' => $attempt, 'lang' => 'id']) }}">
                                                            🇮🇩 Bahasa Indonesia
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.reports.export.attempt', ['attempt' => $attempt, 'lang' => 'en']) }}">
                                                            🇺🇸 English
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const attemptCheckboxes = document.querySelectorAll('.attempt-checkbox');
    const bulkExportButton = document.getElementById('bulk-export');
    const bulkExportForm = document.getElementById('bulk-export-form');

    // Select/Deselect all
    selectAllCheckbox.addEventListener('change', function() {
        attemptCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkExportButton();
    });

    // Update bulk export button state
    attemptCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkExportButton);
    });

    function updateBulkExportButton() {
        const checkedCount = document.querySelectorAll('.attempt-checkbox:checked').length;
        bulkExportButton.disabled = checkedCount === 0;
    }

    // Bulk export with language selection
    bulkExportButton.addEventListener('click', function() {
        const dropdown = document.createElement('div');
        dropdown.className = 'dropdown d-inline-block position-absolute';
        dropdown.style.top = this.offsetTop + this.offsetHeight + 'px';
        dropdown.style.left = this.offsetLeft + 'px';
        dropdown.innerHTML = `
            <ul class="dropdown-menu show">
                <li><a class="dropdown-item" href="#" data-lang="id">🇮🇩 Bahasa Indonesia</a></li>
                <li><a class="dropdown-item" href="#" data-lang="en">🇺🇸 English</a></li>
            </ul>
        `;

        document.body.appendChild(dropdown);

        dropdown.addEventListener('click', function(e) {
            if (e.target.matches('.dropdown-item')) {
                e.preventDefault();
                document.getElementById('bulk-export-lang').value = e.target.dataset.lang;
                bulkExportForm.submit();
                document.body.removeChild(dropdown);
            }
        });

        setTimeout(() => {
            document.addEventListener('click', function removeDropdown(e) {
                if (!dropdown.contains(e.target)) {
                    document.body.removeChild(dropdown);
                    document.removeEventListener('click', removeDropdown);
                }
            });
        }, 0);
    });
});
</script>
@endpush
@endsection
