<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Get unique departments dan positions untuk filter
        $departments = User::distinct()->pluck('department');
        $positions = User::distinct()->pluck('position');

        // Build query dengan filter
        $users = User::query()
            ->when($request->department, function($query, $department) {
                $query->where('department', $department);
            })
            ->when($request->position, function($query, $position) {
                $query->where('position', $position);
            })
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users', 'departments', 'positions'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|unique:users',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'position' => $validated['position'],
            'department' => $validated['department'],
            'email' => $validated['nik'] . '@dummy.com', // Email dummy
            'password' => bcrypt(Str::random(16)), // Password dummy
            'is_active' => true,
        ]);

        // Generate token otomatis
        $user->generateToken();

        return redirect()->route('admin.users.index')
            ->with('success', __('general.user_created') . ' - Token: ' . $user->login_token);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|unique:users,nik,' . $user->id,
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', __('general.user_updated'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', __('general.cannot_delete_self'));
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', __('general.user_deleted'));
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return redirect()->back()
            ->with('success', __('general.status_updated'));
    }

    public function history(User $user)
    {
        $attempts = $user->quizAttempts()
            ->with('quiz')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $statistics = [
            'total_attempts' => $user->quizAttempts()->count(),
            'completed' => $user->quizAttempts()->where('status', 'completed')->count(),
            'graded' => $user->quizAttempts()->where('status', 'graded')->count(),
            'average_score' => $user->quizAttempts()->where('status', 'graded')->avg('score') ?? 0,
        ];

        return view('admin.users.history', compact('user', 'attempts', 'statistics'));
    }
}
