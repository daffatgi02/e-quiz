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

    /**
     * Export quiz questions to JSON file
     */
    public function exportQuestions(Quiz $quiz)
    {
        $questions = $quiz->questions()->with('options')->get();

        $data = [
            'quiz_id' => $quiz->id,
            'quiz_title' => $quiz->title,
            'questions' => $questions->map(function ($question) {
                return [
                    'question' => $question->question,
                    'type' => $question->type,
                    'points' => $question->points,
                    'requires_manual_grading' => $question->requires_manual_grading,
                    'image_path' => $question->image_path, // Include image path if present
                    'options' => $question->type === 'multiple_choice' ? $question->options->map(function ($option) {
                        return [
                            'option' => $option->option,
                            'is_correct' => $option->is_correct,
                            'image_path' => $option->image_path, // Include option image path if present
                            'order' => $option->order
                        ];
                    }) : [],
                ];
            }),
        ];

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $filename = 'quiz_' . $quiz->id . '_questions_' . date('Ymd_His') . '.json';

        return response()->streamDownload(function () use ($jsonData) {
            echo $jsonData;
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Show form for importing questions
     */
    public function showImportForm(Quiz $quiz)
    {
        return view('admin.questions.import', compact('quiz'));
    }

    /**
     * Import questions from JSON file
     */
    public function importQuestions(Request $request, Quiz $quiz)
    {
        $request->validate([
            'questions_file' => 'required|file|mimes:json',
        ]);

        try {
            $jsonData = file_get_contents($request->file('questions_file')->path());
            $data = json_decode($jsonData, true);

            if (!isset($data['questions']) || !is_array($data['questions'])) {
                return back()->with('error', 'Format file JSON tidak valid');
            }

            DB::transaction(function () use ($quiz, $data) {
                foreach ($data['questions'] as $questionData) {
                    $question = $quiz->questions()->create([
                        'question' => $questionData['question'],
                        'type' => $questionData['type'],
                        'points' => $questionData['points'],
                        'requires_manual_grading' => $questionData['requires_manual_grading'] ?? false,
                        'image_path' => $questionData['image_path'] ?? null,
                    ]);

                    if ($questionData['type'] === 'multiple_choice' && isset($questionData['options'])) {
                        foreach ($questionData['options'] as $index => $optionData) {
                            $question->options()->create([
                                'option' => $optionData['option'],
                                'is_correct' => $optionData['is_correct'],
                                'image_path' => $optionData['image_path'] ?? null,
                                'order' => $optionData['order'] ?? $index
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('admin.quizzes.show', $quiz)
                ->with('success', count($data['questions']) . ' pertanyaan berhasil diimpor');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor pertanyaan: ' . $e->getMessage());
        }
    }
}
