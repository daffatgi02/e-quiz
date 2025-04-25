{{-- resources/views/admin/quizzes/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ __('quiz.title') }}</h1>
                <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                    {{ __('quiz.create') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('quiz.title') }}</th>
                                <th>{{ __('quiz.question_type') }}</th>
                                <th>{{ __('quiz.questions') }}</th>
                                <th>{{ __('quiz.duration') }}</th>
                                <th>{{ __('quiz.start_date') }}</th>
                                <th>{{ __('quiz.status') }}</th>
                                <th>{{ __('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td>{{ $quiz->title }}</td>
                                    <td>{{ __('quiz.' . $quiz->question_type) }}</td>
                                    <td>{{ $quiz->questions->count() }}</td>
                                    <td>{{ $quiz->duration }} {{ __('quiz.minutes') }}</td>
                                    <td>{{ $quiz->start_date->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $quiz->is_active ? 'success' : 'danger' }}">
                                            {{ __('quiz.' . ($quiz->is_active ? 'active' : 'inactive')) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-sm btn-info">
                                                {{ __('general.view') }}
                                            </a>
                                            <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-sm btn-warning">
                                                {{ __('general.edit') }}
                                            </a>
                                            <a href="{{ route('admin.quizzes.track', $quiz) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-chart-bar"></i> {{ __('quiz.track') }}
                                            </a>
                                            <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('general.confirm_delete') }}')">
                                                    {{ __('general.delete') }}
                                                </button>
                                            </form>
                                        </div>
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
