<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuizAttempt;

class CompleteTimedOutQuizzes extends Command
{
    protected $signature = 'quiz:complete-timeout';
    protected $description = 'Automatically complete quiz attempts that have timed out';

    public function handle()
    {
        $attempts = QuizAttempt::where('status', 'in_progress')
            ->with('quiz')
            ->get();

        foreach ($attempts as $attempt) {
            $endTime = $attempt->started_at->addMinutes($attempt->quiz->duration);

            if (now()->gt($endTime)) {
                $attempt->update([
                    'status' => 'completed',
                    'completed_at' => $endTime
                ]);

                $this->info("Completed timed out attempt: {$attempt->id}");
            }
        }
    }
}
