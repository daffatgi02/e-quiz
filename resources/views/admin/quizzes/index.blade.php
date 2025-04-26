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
                                <th>Token</th>
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
                                    <td>
                                        @if($quiz->requires_token)
                                            <span class="badge bg-info">{{ $quiz->quiz_token }}</span>
                                            @if($quiz->token_expires_at)
                                                <br>
                                                <small class="text-muted">
                                                    Exp: {{ $quiz->token_expires_at->format('d/m/Y H:i') }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">No Token</span>
                                        @endif
                                    </td>
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
                                            @if($quiz->requires_token && (!$quiz->token_expires_at || $quiz->token_expires_at->isPast()))
                                                <form action="{{ route('admin.quizzes.regenerate-token', $quiz) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        Regenerate Token
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.quizzes.track', $quiz) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-chart-bar"></i> {{ __('quiz.track') }}
                                            </a>
                                            <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('{{ __('general.confirm_delete') }}')">
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
