<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CleanDuplicateQuizAttempts extends Command
{
    protected $signature = 'quiz:clean-duplicates';
    protected $description = 'Clean duplicate quiz attempts with in_progress status';

    public function handle()
    {
        $this->info('Cleaning duplicate quiz attempts...');

        // Find users with multiple in_progress attempts
        $users = User::whereHas('quizAttempts', function($query) {
            $query->where('status', 'in_progress');
        })->get();

        foreach ($users as $user) {
            $inProgressAttempts = $user->quizAttempts()
                ->where('status', 'in_progress')
                ->orderBy('started_at', 'asc')
                ->get();

            // Jika ada lebih dari satu attempt in_progress
            if ($inProgressAttempts->count() > 1) {
                $this->info("User {$user->name} has {$inProgressAttempts->count()} in_progress attempts");

                // Keep only the latest attempt
                $latestAttempt = $inProgressAttempts->last();

                foreach ($inProgressAttempts as $attempt) {
                    if ($attempt->id !== $latestAttempt->id) {
                        // Set older attempts to completed
                        $attempt->update([
                            'status' => 'completed',
                            'completed_at' => now()
                        ]);
                        $this->info("Marked attempt {$attempt->id} as completed");
                    }
                }
            }
        }

        $this->info('Cleanup completed!');
    }
}
