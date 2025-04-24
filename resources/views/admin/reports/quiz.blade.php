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
                        <i class="fas fa-file-excel"></i> {{ __('general.export') }} Selected
                    </button>
                    <a href="{{ route('admin.reports.export.quiz', $quiz) }}" class="btn btn-primary">
                        <i class="fas fa-file-excel"></i> {{ __('general.export') }} All
                    </a>
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
                            <h6>{{ __('general.total') }} {{ __('quiz.attempts') }}</h6>
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
                        <h5 class="mb-0">{{ __('quiz.attempts') }}</h5>
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
                                            <a href="{{ route('admin.reports.export.attempt', $attempt) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-file-excel"></i> {{ __('general.export') }}
                                            </a>
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

    // Bulk export
    bulkExportButton.addEventListener('click', function() {
        bulkExportForm.submit();
    });
});
</script>
@endpush
@endsection
