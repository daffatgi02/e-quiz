<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ __('quiz.report') }} - {{ $quiz->title }}</title>
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

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .question {
            margin-bottom: 15px;
            border: 1px solid #eee;
            padding: 10px;
            page-break-inside: avoid;
        }

        .header-info {
            margin-bottom: 5px;
        }

        .answer-box {
            margin: 10px 0;
            padding: 5px 10px;
        }

        .correct-answer {
            background-color: #d4edda;
            color: #155724;
        }

        .wrong-answer {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ __('quiz.report') }}: {{ $quiz->title }}</h1>
        @if ($quiz->requires_token)
            <p><strong>Quiz Token:</strong> {{ $quiz->quiz_token }}</p>
            <p><strong>Token Expires:</strong>
                {{ $quiz->token_expires_at ? $quiz->token_expires_at->format('d F Y H:i:s') : 'No expiry' }}</p>
        @endif
        <p class="header-info">{{ __('general.date') }}: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    @foreach ($attempts as $attempt)
        <div class="section">
            <h2>{{ __('general.user') }}: {{ $attempt->user->name }} ({{ $attempt->user->nik }})</h2>
            <table>
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
                    <td>{{ $attempt->completed_at ? $attempt->completed_at->format('d F Y H:i:s') : __('quiz.not_completed') }}
                    </td>
                </tr>
                <tr>
                    <th>{{ __('quiz.status') }}</th>
                    <td>{{ __('quiz.' . $attempt->status) }}</td>
                    <th>{{ __('quiz.score') }}</th>
                    <td>{{ $attempt->score ?? __('quiz.not_graded') }}</td>
                </tr>
            </table>

            <h3>{{ __('quiz.questions_answers') }}</h3>
            @foreach ($attempt->answers as $answer)
                <div class="question">
                    <p><strong>{{ __('quiz.question') }}: {{ $answer->question->question }}</strong></p>

                    @if ($answer->question->type === 'multiple_choice')
                        @php
                            $userAnswerOption = $answer->questionOption;
                            $correctOption = $answer->question->options->where('is_correct', true)->first();
                            $isCorrect = $userAnswerOption && $userAnswerOption->is_correct;
                        @endphp

                        <div class="answer-box {{ $isCorrect ? 'correct-answer' : 'wrong-answer' }}">
                            <strong>{{ __('quiz.user_answer') }}:</strong>
                            {{ $userAnswerOption ? $userAnswerOption->option : __('quiz.no_answer') }}
                            {{ $isCorrect ? '✓' : '✗' }}
                        </div>

                        @if (!$isCorrect)
                            <div class="answer-box correct-answer">
                                <strong>{{ __('quiz.correct_answer') }}:</strong> {{ $correctOption->option }}
                            </div>
                        @endif
                    @else
                        <div style="margin: 10px 0;">
                            <strong>{{ __('quiz.user_answer') }}:</strong><br>
                            <div style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;">
                                {{ $answer->essay_answer ?? __('quiz.no_answer') }}
                            </div>
                        </div>
                    @endif

                    <div style="margin-top: 10px; text-align: right;">
                        <strong>{{ __('quiz.points') }}: {{ $answer->points_earned ?? 0 }} /
                            {{ $answer->question->points }}</strong>
                    </div>
                </div>
            @endforeach
        </div>
        @if (!$loop->last)
            <hr style="margin: 30px 0;">
        @endif
    @endforeach
</body>

</html>
