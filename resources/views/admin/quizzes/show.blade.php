{{-- resources/views/admin/quizzes/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>{{ $quiz->title }}</h1>
                    <div>
                        @if ($quiz->requires_token && (!$quiz->token_expires_at || $quiz->token_expires_at->isPast()))
                            <form action="{{ route('admin.quizzes.regenerate-token', $quiz) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    Regenerate Token
                                </button>
                            </form>
                        @endif

                        <!-- Add these buttons -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-file-export"></i> {{ __('Questions') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="{{ route('admin.quizzes.questions.export', $quiz) }}" class="dropdown-item">
                                        <i class="fas fa-download"></i> {{ __('Export Questions') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.quizzes.questions.import', $quiz) }}" class="dropdown-item">
                                        <i class="fas fa-upload"></i> {{ __('Import Questions') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

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
                                        <th>Token Required:</th>
                                        <td>{{ $quiz->requires_token ? __('general.yes') : __('general.no') }}</td>
                                    </tr>
                                    @if ($quiz->requires_token)
                                        <tr>
                                            <th>Token:</th>
                                            <td>
                                                <code class="fs-5">{{ $quiz->quiz_token }}</code>
                                                @if ($quiz->token_expires_at)
                                                    <br>
                                                    <small class="text-muted">
                                                        Expires: {{ $quiz->token_expires_at->format('d F Y H:i') }}
                                                    </small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
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
                                    {{-- Display question image if exists --}}
                                    @if ($question->image_path)
                                        <div class="question-image mb-3">
                                            <img src="{{ Storage::url($question->image_path) }}" class="img-fluid" 
                                                 alt="Question Image" style="max-height: 300px; object-fit: contain;">
                                        </div>
                                    @endif

                                    @if ($question->type === 'multiple_choice')
                                        <div class="options-list">
                                            @foreach ($question->options->sortBy('order') as $optionIndex => $option)
                                                <div class="option-item mb-2 {{ $option->is_correct ? 'correct-option' : '' }}">
                                                    <div class="d-flex align-items-start">
                                                        <div class="option-letter me-3">
                                                            <span class="badge bg-{{ $option->is_correct ? 'success' : 'light text-dark' }} fs-6">
                                                                {{ chr(65 + $optionIndex) }}.
                                                            </span>
                                                        </div>
                                                        <div class="option-content flex-grow-1">
                                                            <div class="option-text">
                                                                {{ $option->option }}
                                                            </div>
                                                            {{-- Display option image if exists --}}
                                                            @if ($option->image_path)
                                                                <div class="option-image mt-2">
                                                                    <img src="{{ Storage::url($option->image_path) }}" 
                                                                         class="img-thumbnail" alt="Option Image" 
                                                                         style="max-height: 150px; max-width: 200px; object-fit: contain;">
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="option-status ms-2">
                                                            @if ($option->is_correct)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check"></i> {{ __('quiz.correct_answer') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="essay-question">
                                            <p class="text-muted">{{ __('quiz.essay') }}</p>
                                            @if ($question->requires_manual_grading)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-user-edit"></i> {{ __('quiz.manual_grading') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-question-circle fa-3x text-muted"></i>
                                </div>
                                <h4 class="text-muted">{{ __('quiz.no_questions') }}</h4>
                                <p class="text-muted">Belum ada pertanyaan untuk quiz ini</p>
                                <a href="{{ route('admin.quizzes.questions.create', $quiz) }}"
                                    class="btn btn-primary mt-3">
                                    <i class="fas fa-plus"></i> {{ __('quiz.add_question') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .option-item {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        
        .option-item:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .option-item.correct-option {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .option-letter {
            min-width: 35px;
        }
        
        .option-text {
            font-size: 14px;
            line-height: 1.5;
        }
        
        .question-image img,
        .option-image img {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .essay-question {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        
        .card-header {
            background-color: #f8f9fa;
        }
        
        .options-list {
            background-color: #fdfdfd;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #f0f0f0;
        }
    </style>
    @endpush
@endsection