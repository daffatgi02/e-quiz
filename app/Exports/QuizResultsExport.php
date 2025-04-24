<?php
// app/Exports/QuizResultsExport.php
namespace App\Exports;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuizResultsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $quiz;
    protected $attempts;

    public function __construct(Quiz $quiz, $attempts = null)
    {
        $this->quiz = $quiz;
        $this->attempts = $attempts ?? $quiz->attempts()->with(['user', 'answers.question', 'answers.questionOption'])->get();
    }

    public function collection()
    {
        return $this->attempts;
    }

    public function headings(): array
    {
        return [
            'User Name',
            'NIK',
            'Department',
            'Position',
            'Started At',
            'Completed At',
            'Status',
            'Score',
            'Questions & Answers'
        ];
    }

    public function map($attempt): array
    {
        $questionsAnswers = $attempt->answers->map(function($answer) {
            $question = $answer->question;

            if ($question->type === 'multiple_choice') {
                $userAnswer = $answer->questionOption ? $answer->questionOption->option : 'No Answer';
                $correctAnswer = $question->options->where('is_correct', true)->first()->option;
                return sprintf(
                    "Q: %s\nA: %s\nCorrect: %s\nPoints: %s/%s",
                    $question->question,
                    $userAnswer,
                    $correctAnswer,
                    $answer->points_earned ?? 0,
                    $question->points
                );
            } else {
                return sprintf(
                    "Q: %s\nA: %s\nPoints: %s/%s",
                    $question->question,
                    $answer->essay_answer ?? 'No Answer',
                    $answer->points_earned ?? 0,
                    $question->points
                );
            }
        })->implode("\n\n");

        return [
            $attempt->user->name,
            $attempt->user->nik,
            $attempt->user->department,
            $attempt->user->position,
            $attempt->started_at->format('Y-m-d H:i:s'),
            $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i:s') : 'Not Completed',
            $attempt->status,
            $attempt->score ?? 'Not Graded',
            $questionsAnswers
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
