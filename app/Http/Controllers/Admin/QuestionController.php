<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $rules = [
            'question' => 'required|string',
            'question_image' => 'nullable|image|max:2048', // Max 2MB
            'type' => 'required|in:multiple_choice,essay',
            'points' => 'required|integer|min:1',
            'requires_manual_grading' => 'boolean',
        ];

        if ($request->type === 'multiple_choice') {
            $rules['options'] = 'required|array|min:2|max:10';
            $rules['options.*.option'] = 'required|string|min:1';
            $rules['options.*.image'] = 'nullable|image|max:1024'; // Max 1MB
            $rules['correct_option'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $quiz, $request) {
            // Handle question image upload
            $questionImagePath = null;
            if ($request->hasFile('question_image')) {
                $questionImagePath = $request->file('question_image')->store('questions', 'public');
            }

            $question = $quiz->questions()->create([
                'question' => $validated['question'],
                'image_path' => $questionImagePath,
                'type' => $validated['type'],
                'points' => $validated['points'],
                'requires_manual_grading' => $validated['requires_manual_grading'] ?? false,
            ]);

            if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    // Handle option image upload
                    $optionImagePath = null;
                    if (isset($option['image']) && $request->hasFile("options.{$index}.image")) {
                        $optionImagePath = $request->file("options.{$index}.image")->store('options', 'public');
                    }

                    $question->options()->create([
                        'option' => $option['option'],
                        'image_path' => $optionImagePath,
                        'is_correct' => $index === (int)$validated['correct_option'],
                        'order' => $index
                    ]);
                }
            }
        });

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', __('quiz.question_created'));
    }

    public function edit(Quiz $quiz, Question $question)
    {
        return view('admin.questions.edit', compact('quiz', 'question'));
    }

    // app/Http/Controllers/Admin/QuestionController.php
    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $rules = [
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'points' => 'required|integer|min:1',
            'requires_manual_grading' => 'boolean',
        ];

        // Validasi dinamis berdasarkan type
        if ($request->type === 'multiple_choice') {
            $rules['options'] = 'required|array|min:2|max:10';
            $rules['options.*.option'] = 'required|string|min:1';
            $rules['correct_option'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        // Validasi tambahan untuk memastikan correct_option valid
        if ($request->type === 'multiple_choice') {
            $maxIndex = count($request->options) - 1;
            if ($request->correct_option > $maxIndex) {
                return back()->withInput()->withErrors(['correct_option' => 'Invalid correct option selected']);
            }
        }

        DB::transaction(function () use ($validated, $question, $request) {
            $question->update([
                'question' => $validated['question'],
                'type' => $validated['type'],
                'points' => $validated['points'],
                'requires_manual_grading' => $validated['requires_manual_grading'] ?? false,
            ]);

            if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
                $question->options()->delete();
                foreach ($validated['options'] as $index => $option) {
                    $question->options()->create([
                        'option' => $option['option'],
                        'is_correct' => $index === (int)$validated['correct_option']
                    ]);
                }
            }
        });

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', __('quiz.question_updated'));
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        $question->delete();
        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', __('quiz.question_deleted'));
    }
}
