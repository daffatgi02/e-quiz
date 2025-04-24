{{-- resources/views/admin/questions/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('quiz.edit_question') }} - {{ $quiz->title }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.quizzes.questions.update', [$quiz, $question]) }}" id="questionForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="question" class="form-label">{{ __('quiz.question') }}</label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="3" required>{{ old('question', $question->question) }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">{{ __('general.type') }}</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="multiple_choice" {{ old('type', $question->type) == 'multiple_choice' ? 'selected' : '' }}>{{ __('quiz.multiple_choice') }}</option>
                                <option value="essay" {{ old('type', $question->type) == 'essay' ? 'selected' : '' }}>{{ __('quiz.essay') }}</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="points" class="form-label">{{ __('quiz.points') }}</label>
                            <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points', $question->points) }}" min="1" required>
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="options-container" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">{{ __('quiz.options') }}</label>
                                <button type="button" class="btn btn-sm btn-primary" id="add-option">
                                    <i class="fas fa-plus"></i> {{ __('quiz.add_option') }}
                                </button>
                            </div>
                            <div id="options-list">
                                @if($question->type === 'multiple_choice' && $question->options->count())
                                    @foreach($question->options as $index => $option)
                                        <div class="input-group mb-2 option-item">
                                            <input type="text" class="form-control" name="options[{{ $index }}][option]" value="{{ old('options.'.$index.'.option', $option->option) }}" placeholder="{{ __('quiz.option') }} {{ $index + 1 }}">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" type="radio" name="correct_option" value="{{ $index }}" {{ old('correct_option', $option->is_correct ? $index : null) == $index ? 'checked' : '' }}>
                                            </div>
                                            @if($index >= 2)
                                                <button class="btn btn-danger" type="button" onclick="removeOption(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="input-group mb-2 option-item">
                                            <input type="text" class="form-control" name="options[{{ $i }}][option]" value="{{ old('options.'.$i.'.option') }}" placeholder="{{ __('quiz.option') }} {{ $i + 1 }}">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" type="radio" name="correct_option" value="{{ $i }}" {{ old('correct_option') == $i ? 'checked' : '' }}>
                                            </div>
                                            @if($i >= 2)
                                                <button class="btn btn-danger" type="button" onclick="removeOption(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endfor
                                @endif
                            </div>
                            <small class="text-muted">{{ __('quiz.select_correct_answer') }}</small>
                        </div>

                        <div class="mb-3 essay-options" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requires_manual_grading" name="requires_manual_grading" value="1" {{ old('requires_manual_grading', $question->requires_manual_grading) ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_manual_grading">
                                    {{ __('quiz.manual_grading') }}
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-secondary">{{ __('general.cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const optionsContainer = document.getElementById('options-container');
    const essayOptions = document.querySelector('.essay-options');
    const optionsList = document.getElementById('options-list');
    const addOptionButton = document.getElementById('add-option');
    let optionCount = document.querySelectorAll('.option-item').length;

    // Form submission validation
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        if (typeSelect.value === 'multiple_choice') {
            const radioButtons = document.querySelectorAll('input[name="correct_option"]:checked');
            if (radioButtons.length === 0) {
                e.preventDefault();
                alert('{{ __("quiz.please_select_correct_answer") }}');
                return false;
            }
        }
    });

    // Type select change handler
    typeSelect.addEventListener('change', function() {
        if (this.value === 'multiple_choice') {
            optionsContainer.style.display = 'block';
            essayOptions.style.display = 'none';
        } else {
            optionsContainer.style.display = 'none';
            essayOptions.style.display = 'block';
        }
    });

    // Add option button handler
    addOptionButton.addEventListener('click', function() {
        if (optionCount >= 10) {
            alert('{{ __("quiz.max_options_reached") }}');
            return;
        }

        const optionHtml = `
            <div class="input-group mb-2 option-item">
                <input type="text" class="form-control" name="options[${optionCount}][option]" placeholder="{{ __('quiz.option') }} ${optionCount + 1}">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="radio" name="correct_option" value="${optionCount}">
                </div>
                <button class="btn btn-danger" type="button" onclick="removeOption(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        optionsList.insertAdjacentHTML('beforeend', optionHtml);
        optionCount++;
        updateOptionPlaceholders();
    });

    // Trigger change event on page load
    typeSelect.dispatchEvent(new Event('change'));
});

function removeOption(button) {
    const optionItems = document.querySelectorAll('.option-item');
    if (optionItems.length <= 2) {
        alert('{{ __("quiz.min_options_required") }}');
        return;
    }

    const optionItem = button.closest('.option-item');
    optionItem.remove();
    updateOptionPlaceholders();
}

function updateOptionPlaceholders() {
    const optionItems = document.querySelectorAll('.option-item');
    optionItems.forEach((item, index) => {
        const input = item.querySelector('input[type="text"]');
        const radio = item.querySelector('input[type="radio"]');
        input.name = `options[${index}][option]`;
        input.placeholder = `{{ __('quiz.option') }} ${index + 1}`;
        radio.value = index;
    });
}
</script>
@endpush
@endsection
