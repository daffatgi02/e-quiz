<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nik',
        'position',
        'department',
        'perusahaan',
        'is_admin',
        'is_active',
        'language',
        'login_token',
        'pin',
        'pin_set',
        'token_issued_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'pin_set' => 'boolean',
        'token_issued_at' => 'datetime',
    ];

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Tambahkan method untuk generate token
    public function generateToken()
    {
        $this->login_token = strtoupper(Str::random(8));
        $this->token_issued_at = now();
        $this->save();
        return $this->login_token;
    }

    // Method untuk set PIN
    public function setPin($pin)
    {
        $this->pin = Hash::make($pin);
        $this->pin_set = true;
        $this->save();
    }

    // Method untuk verifikasi PIN
    public function verifyPin($pin)
    {
        return Hash::check($pin, $this->pin);
    }
}
