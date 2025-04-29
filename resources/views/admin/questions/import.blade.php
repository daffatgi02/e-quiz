{{-- resources/views/admin/questions/import.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Import Questions') }} - {{ $quiz->title }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-info mb-4">
                        <p>{{ __('Please make sure your JSON file follows the correct format.') }}</p>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#formatModal">{{ __('View JSON format example') }}</a>
                    </div>

                    <form method="POST" action="{{ route('admin.quizzes.questions.import.store', $quiz) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="questions_file" class="form-label">{{ __('Questions File (JSON)') }}</label>
                            <input type="file" class="form-control @error('questions_file') is-invalid @enderror" id="questions_file" name="questions_file" accept=".json" required>
                            @error('questions_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('Select a JSON file with questions to import.') }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirm_import" name="confirm_import" required>
                                <label class="form-check-label" for="confirm_import">
                                    {{ __('I confirm that I want to import these questions') }}
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JSON Format Modal -->
<div class="modal fade" id="formatModal" tabindex="-1" aria-labelledby="formatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formatModalLabel">{{ __('Example JSON Format') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <pre><code>{
  "quiz_id": 1,
  "quiz_title": "Sample Quiz",
  "questions": [
    {
      "question": "What is the capital of France?",
      "type": "multiple_choice",
      "points": 1,
      "requires_manual_grading": false,
      "options": [
        {
          "option": "London",
          "is_correct": false,
          "order": 0
        },
        {
          "option": "Paris",
          "is_correct": true,
          "order": 1
        },
        {
          "option": "Berlin",
          "is_correct": false,
          "order": 2
        },
        {
          "option": "Madrid",
          "is_correct": false,
          "order": 3
        }
      ]
    },
    {
      "question": "Explain the difference between HTTP and HTTPS.",
      "type": "essay",
      "points": 5,
      "requires_manual_grading": true,
      "options": []
    }
  ]
}</code></pre>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      </div>
    </div>
  </div>
</div>
@endsection
