{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">{{ __('general.reports') }}</h1>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quiz.title') }} {{ __('general.reports') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('quiz.title') }}</th>
                                <th>{{ __('quiz.start_date') }}</th>
                                <th>{{ __('quiz.attempts') }}</th>
                                <th>{{ __('general.average') }} {{ __('quiz.score') }}</th>
                                <th>{{ __('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td>{{ $quiz->title }}</td>
                                    <td>{{ $quiz->start_date->format('Y-m-d H:i') }}</td>
                                    <td>{{ $quiz->attempts_count }}</td>
                                    <td>
                                        @if($quiz->attempts->isNotEmpty())
                                            {{ number_format($quiz->attempts->first()->average_score, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.reports.quiz', $quiz) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('general.view') }}
                                        </a>
                                        <a href="{{ route('admin.reports.export.quiz', $quiz) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-file-excel"></i> {{ __('general.export') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $quizzes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
