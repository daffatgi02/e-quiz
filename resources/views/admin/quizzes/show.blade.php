{{-- resources/views/admin/quizzes/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>{{ $quiz->title }}</h1>
                    <div>
                        <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="btn btn-primary">
                            {{ __('quiz.add_question') }}
                        </a>
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-warning">
                            {{ __('general.edit') }}
                        </a>
                        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
                            {{ __('general.back') }}
                        </a>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5>{{ __('general.details') }}</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        {{-- resources/views/admin/quizzes/show.blade.php (lanjutan) --}}
                                        <th>{{ __('general.description') }}:</th>
                                        <td>{{ $quiz->description }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.start_date') }}:</th>
                                        <td>{{ $quiz->start_date->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.duration') }}:</th>
                                        <td>{{ $quiz->duration }} {{ __('quiz.minutes') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.question_type') }}:</th>
                                        <td>{{ __('quiz.' . $quiz->question_type) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.single_attempt') }}:</th>
                                        <td>{{ $quiz->single_attempt ? __('general.yes') : __('general.no') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('general.status') }}:</th>
                                        <td>
                                            <span class="badge bg-{{ $quiz->is_active ? 'success' : 'danger' }}">
                                                {{ __('quiz.' . ($quiz->is_active ? 'active' : 'inactive')) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>{{ __('general.statistics') }}</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <th>{{ __('quiz.questions') }}:</th>
                                        <td>{{ $quiz->questions->count() }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.attempts') }}:</th>
                                        <td>{{ $quiz->attempts->count() }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.completed') }}:</th>
                                        <td>{{ $quiz->attempts->where('status', 'completed')->count() }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('quiz.graded') }}:</th>
                                        <td>{{ $quiz->attempts->where('status', 'graded')->count() }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('quiz.questions') }}</h5>
                    </div>
                    <div class="card-body">
                        @forelse($quiz->questions as $index => $question)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ __('quiz.question') }} {{ $index + 1 }}:
                                            {{ $question->question }}</h6>
                                        <div>
                                            <span class="badge bg-info me-2">{{ __('quiz.' . $question->type) }}</span>
                                            <span class="badge bg-secondary me-2">{{ $question->points }}
                                                {{ __('quiz.points') }}</span>
                                            <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}"
                                                class="btn btn-sm btn-warning">
                                                {{ __('general.edit') }}
                                            </a>
                                            <form
                                                action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('{{ __('general.confirm_delete') }}')">
                                                    {{ __('general.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($question->type === 'multiple_choice')
                                        <ul class="list-group">
                                            @foreach ($question->options as $option)
                                                <li
                                                    class="list-group-item {{ $option->is_correct ? 'list-group-item-success' : '' }}">
                                                    {{ $option->option }}
                                                    @if ($option->is_correct)
                                                        <span
                                                            class="badge bg-success float-end">{{ __('quiz.correct_answer') }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>{{ __('quiz.essay') }}</p>
                                        @if ($question->requires_manual_grading)
                                            <span class="badge bg-warning">{{ __('quiz.manual_grading') }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <h4>{{ __('quiz.no_questions') }}</h4>
                                <a href="{{ route('admin.quizzes.questions.create', $quiz) }}"
                                    class="btn btn-primary mt-3">
                                    {{ __('quiz.add_question') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
