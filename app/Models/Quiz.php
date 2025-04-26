<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'duration',
        'question_type',
        'is_active',
        'single_attempt',
        'requires_token',
        'quiz_token',
        'token_expires_at'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'is_active' => 'boolean',
        'single_attempt' => 'boolean',
        'requires_token' => 'boolean',
        'token_expires_at' => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function tokenUsers()
    {
        return $this->belongsToMany(User::class, 'quiz_token_users')
            ->withPivot('token_used_at')
            ->withTimestamps();
    }
    public function generateToken()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $part1 = '';
        $part2 = '';

        // Generate 4 random letters for each part
        for ($i = 0; $i < 4; $i++) {
            $part1 .= $characters[rand(0, strlen($characters) - 1)];
            $part2 .= $characters[rand(0, strlen($characters) - 1)];
        }

        $this->quiz_token = $part1 . '-' . $part2;
        $this->save();

        return $this->quiz_token;
    }

    public function isTokenValid()
    {
        if (!$this->requires_token) return true;

        if (!$this->quiz_token) return false;

        if ($this->token_expires_at && $this->token_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function canUserAccessWithToken($user)
    {
        if (!$this->requires_token) return true;

        // Cek apakah user sudah menggunakan token
        return $this->tokenUsers()->where('user_id', $user->id)->exists();
    }
}
