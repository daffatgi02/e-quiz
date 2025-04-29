{{-- resources/views/admin/questions/json-format.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('JSON Format for Question Import') }}</div>

                <div class="card-body">
                    <p>{{ __('To import questions, please use the following JSON format:') }}</p>

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
          "is_correct": false
        },
        {
          "option": "Paris",
          "is_correct": true
        },
        {
          "option": "Berlin",
          "is_correct": false
        },
        {
          "option": "Madrid",
          "is_correct": false
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

                    <div class="alert alert-info mt-3">
                        <h5>{{ __('Notes:') }}</h5>
                        <ul>
                            <li>{{ __('The "quiz_id" and "quiz_title" fields are optional and will be ignored during import.') }}</li>
                            <li>{{ __('For multiple-choice questions, make sure one option has "is_correct" set to true.') }}</li>
                            <li>{{ __('For essay questions, the "options" array should be empty.') }}</li>
                        </ul>
                    </div>

                    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-primary mt-3">{{ __('Back to Quizzes') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
