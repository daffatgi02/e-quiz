{{-- resources/views/admin/questions/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('quiz.create_question') }} - {{ $quiz->title }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.quizzes.questions.store', $quiz) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="question" class="form-label">{{ __('quiz.question') }}</label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="3" required>{{ old('question') }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">{{ __('general.type') }}</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>{{ __('quiz.multiple_choice') }}</option>
                                <option value="essay" {{ old('type') == 'essay' ? 'selected' : '' }}>{{ __('quiz.essay') }}</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="points" class="form-label">{{ __('quiz.points') }}</label>
                            <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points', 1) }}" min="1" required>
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="options-container" style="display: none;">
                            <label class="form-label">{{ __('quiz.options') }}</label>
                            <div id="options-list">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="options[{{ $i }}][option]" placeholder="{{ __('quiz.option') }} {{ $i + 1 }}">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="{{ $i }}">
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div class="mb-3 essay-options" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requires_manual_grading" name="requires_manual_grading" value="1" {{ old('requires_manual_grading', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_manual_grading">
                                    {{ __('quiz.manual_grading') }}
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-secondary">{{ __('general.cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('type').addEventListener('change', function() {
    const optionsContainer = document.getElementById('options-container');
    const essayOptions = document.querySelector('.essay-options');

    if (this.value === 'multiple_choice') {
        optionsContainer.style.display = 'block';
        essayOptions.style.display = 'none';
    } else {
        optionsContainer.style.display = 'none';
        essayOptions.style.display = 'block';
    }
});

// Trigger change event on page load
document.getElementById('type').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
