{{-- resources/views/admin/questions/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('quiz.create_question') }} - {{ $quiz->title }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.quizzes.questions.store', $quiz) }}" id="questionForm" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="question" class="form-label">{{ __('quiz.question') }}</label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="3" required>{{ old('question') }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="question_image" class="form-label">{{ __('quiz.question_image') }}</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('question_image') is-invalid @enderror" id="question_image" name="question_image" accept="image/*">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearImage('question_image')">{{ __('general.clear') }}</button>
                            </div>
                            <div id="question_image_preview" class="mt-2"></div>
                            @error('question_image')
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
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">{{ __('quiz.options') }}</label>
                                <button type="button" class="btn btn-sm btn-primary" id="add-option">
                                    <i class="fas fa-plus"></i> {{ __('quiz.add_option') }}
                                </button>
                            </div>
                            <div id="options-list" class="sortable">
                                @if(old('options'))
                                    @foreach(old('options') as $index => $option)
                                        <div class="option-item mb-3" data-index="{{ $index }}">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="drag-handle me-2" style="cursor: move;">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <input type="text" class="form-control" name="options[{{ $index }}][option]" value="{{ $option['option'] }}" placeholder="{{ __('quiz.option') }} {{ $index + 1 }}">
                                                        </div>
                                                        <div class="ms-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="correct_option" value="{{ $index }}" {{ old('correct_option') == $index ? 'checked' : '' }}>
                                                                <label class="form-check-label">{{ __('quiz.correct') }}</label>
                                                            </div>
                                                        </div>
                                                        @if($index >= 2)
                                                            <button class="btn btn-danger btn-sm ms-2" type="button" onclick="removeOption(this)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2">
                                                        <input type="file" class="form-control form-control-sm" name="options[{{ $index }}][image]" accept="image/*" onchange="previewOptionImage(this, {{ $index }})">
                                                        <div id="option_{{ $index }}_preview" class="mt-2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="option-item mb-3" data-index="{{ $i }}">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="drag-handle me-2" style="cursor: move;">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <input type="text" class="form-control" name="options[{{ $i }}][option]" placeholder="{{ __('quiz.option') }} {{ $i + 1 }}">
                                                        </div>
                                                        <div class="ms-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="correct_option" value="{{ $i }}">
                                                                <label class="form-check-label">{{ __('quiz.correct') }}</label>
                                                            </div>
                                                        </div>
                                                        @if($i >= 2)
                                                            <button class="btn btn-danger btn-sm ms-2" type="button" onclick="removeOption(this)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2">
                                                        <input type="file" class="form-control form-control-sm" name="options[{{ $i }}][image]" accept="image/*" onchange="previewOptionImage(this, {{ $i }})">
                                                        <div id="option_{{ $i }}_preview" class="mt-2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            </div>
                            <small class="text-muted">{{ __('quiz.drag_to_reorder') }}</small>
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

@push('styles')
<style>
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f8f9fa;
    }
    .option-item {
        transition: transform 0.2s;
    }
    .option-item.dragging {
        transform: scale(1.02);
    }
    .preview-image {
        max-width: 200px;
        max-height: 200px;
        object-fit: contain;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js" integrity="sha256-bQqDH8GbS66FF5etM5MVfoYa+3hiRZwRImNZsn4sQzc=" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('SortableJS available:', typeof Sortable !== 'undefined');
    const typeSelect = document.getElementById('type');
    const optionsContainer = document.getElementById('options-container');
    const essayOptions = document.querySelector('.essay-options');
    const optionsList = document.getElementById('options-list');
    const addOptionButton = document.getElementById('add-option');
    let optionCount = document.querySelectorAll('.option-item').length;

    // Initialize Sortable
    new Sortable(optionsList, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function(evt) {
            updateOptionIndexes();
        }
    });

    // Image preview for question
    document.getElementById('question_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('question_image_preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });

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
            <div class="option-item mb-3" data-index="${optionCount}">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="drag-handle me-2" style="cursor: move;">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="flex-grow-1">
                                <input type="text" class="form-control" name="options[${optionCount}][option]" placeholder="{{ __('quiz.option') }} ${optionCount + 1}">
                            </div>
                            <div class="ms-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="correct_option" value="${optionCount}">
                                    <label class="form-check-label">{{ __('quiz.correct') }}</label>
                                </div>
                            </div>
                            <button class="btn btn-danger btn-sm ms-2" type="button" onclick="removeOption(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <input type="file" class="form-control form-control-sm" name="options[${optionCount}][image]" accept="image/*" onchange="previewOptionImage(this, ${optionCount})">
                            <div id="option_${optionCount}_preview" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        optionsList.insertAdjacentHTML('beforeend', optionHtml);
        optionCount++;
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
    updateOptionIndexes();
}

function updateOptionIndexes() {
    const optionItems = document.querySelectorAll('.option-item');
    optionItems.forEach((item, index) => {
        item.setAttribute('data-index', index);

        // Update input names
        const textInput = item.querySelector('input[type="text"]');
        const radio = item.querySelector('input[type="radio"]');
        const fileInput = item.querySelector('input[type="file"]');
        const preview = item.querySelector('[id^="option_"]');

        textInput.name = `options[${index}][option]`;
        textInput.placeholder = `{{ __('quiz.option') }} ${index + 1}`;
        radio.value = index;

        if (fileInput) {
            fileInput.name = `options[${index}][image]`;
            fileInput.setAttribute('onchange', `previewOptionImage(this, ${index})`);
        }

        if (preview) {
            preview.id = `option_${index}_preview`;
        }
    });
}

function previewOptionImage(input, index) {
    const file = input.files[0];
    const preview = document.getElementById(`option_${index}_preview`);

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
        }
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}

function clearImage(inputId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(inputId + '_preview');
    input.value = '';
    preview.innerHTML = '';
}
</script>
@endpush
@endsection
