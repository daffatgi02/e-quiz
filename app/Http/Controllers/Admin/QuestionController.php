<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'points' => 'required|integer|min:1',
            'requires_manual_grading' => 'boolean',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.option' => 'required_with:options|string',
            'correct_option' => 'required_if:type,multiple_choice|integer'
        ]);

        DB::transaction(function () use ($validated, $quiz) {
            $question = $quiz->questions()->create([
                'question' => $validated['question'],
                'type' => $validated['type'],
                'points' => $validated['points'],
                'requires_manual_grading' => $validated['requires_manual_grading'] ?? false,
            ]);

            if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    $question->options()->create([
                        'option' => $option['option'],
                        'is_correct' => $index === (int)$validated['correct_option']
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

    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'points' => 'required|integer|min:1',
            'requires_manual_grading' => 'boolean',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.option' => 'required_with:options|string',
            'correct_option' => 'required_if:type,multiple_choice|integer'
        ]);

        DB::transaction(function () use ($validated, $question) {
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
