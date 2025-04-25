<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('quiz.attempt_detail') }} - {{ $attempt->quiz->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .question {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            page-break-inside: avoid;
        }
        .question-header {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .option {
            margin: 8px 0;
            padding-left: 20px;
        }
        .correct-option {
            background-color: #d4edda;
            color: #155724;
            padding: 5px;
            margin: 5px 0;
        }
        .user-selected-correct {
            background-color: #d4edda;
            color: #155724;
            padding: 5px;
            margin: 5px 0;
            border-left: 4px solid #28a745;
        }
        .user-selected-wrong {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px;
            margin: 5px 0;
            border-left: 4px solid #dc3545;
        }
        .legend {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .points-box {
            float: right;
            padding: 5px 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('quiz.attempt_detail') }}</h1>
        <h2>{{ $attempt->quiz->title }}</h2>
        <p>{{ __('general.date') }}: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <table>
        <tr>
            <th>{{ __('general.user') }}</th>
            <td>{{ $attempt->user->name }}</td>
            <th>{{ __('general.nik') }}</th>
            <td>{{ $attempt->user->nik }}</td>
        </tr>
        <tr>
            <th>{{ __('general.department') }}</th>
            <td>{{ $attempt->user->department }}</td>
            <th>{{ __('general.position') }}</th>
            <td>{{ $attempt->user->position }}</td>
        </tr>
        <tr>
            <th>{{ __('quiz.started_at') }}</th>
            <td>{{ $attempt->started_at->format('d F Y H:i:s') }}</td>
            <th>{{ __('quiz.completed_at') }}</th>
            <td>{{ $attempt->completed_at ? $attempt->completed_at->format('d F Y H:i:s') : __('quiz.not_completed') }}</td>
        </tr>
        <tr>
            <th>{{ __('quiz.status') }}</th>
            <td>{{ __('quiz.' . $attempt->status) }}</td>
            <th>{{ __('quiz.score') }}</th>
            <td>{{ $attempt->score ?? __('quiz.not_graded') }}</td>
        </tr>
    </table>

    <div class="legend">
        <strong>Keterangan:</strong><br>
        <span style="display: inline-block; width: 20px; height: 10px; background-color: #d4edda; border-left: 4px solid #28a745;"></span> Jawaban peserta (Benar)<br>
        <span style="display: inline-block; width: 20px; height: 10px; background-color: #f8d7da; border-left: 4px solid #dc3545;"></span> Jawaban peserta (Salah)<br>
        <span style="display: inline-block; width: 20px; height: 10px; background-color: #d4edda;"></span> Jawaban yang benar
    </div>

    <h3>{{ __('quiz.questions_answers') }}</h3>
    @foreach($attempt->quiz->questions as $index => $question)
        <div class="question">
            <div class="question-header">
                {{ __('quiz.question') }} {{ $index + 1 }}: {{ $question->question }}
                <span class="points-box">{{ __('quiz.points') }}: {{ $question->points }}</span>
            </div>

            @php
                $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
            @endphp

            @if($question->type === 'multiple_choice')
                @foreach($question->options as $option)
                    @if($userAnswer && $userAnswer->question_option_id == $option->id)
                        @if($option->is_correct)
                            <div class="user-selected-correct">
                                ✅ {{ $option->option }} ({{ __('quiz.user_answer') }} - {{ __('quiz.correct') }})
                            </div>
                        @else
                            <div class="user-selected-wrong">
                                ❌ {{ $option->option }} ({{ __('quiz.user_answer') }} - {{ __('quiz.incorrect') }})
                            </div>
                        @endif
                    @elseif($option->is_correct)
                        <div class="correct-option">
                            ◎ {{ $option->option }} ({{ __('quiz.correct_answer') }})
                        </div>
                    @else
                        <div class="option">
                            ○ {{ $option->option }}
                        </div>
                    @endif
                @endforeach
            @else
                <div style="margin: 10px 0;">
                    <strong>{{ __('quiz.user_answer') }}:</strong><br>
                    <div style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;">
                        {{ $userAnswer->essay_answer ?? __('quiz.no_answer') }}
                    </div>
                </div>
            @endif

            <div style="margin-top: 10px; text-align: right;">
                <strong>Nilai: {{ $userAnswer ? $userAnswer->points_earned : 0 }} / {{ $question->points }}</strong>
            </div>
        </div>
    @endforeach
</body>
</html>
