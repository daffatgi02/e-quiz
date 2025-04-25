<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nik' => 'required|string|unique:users',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'is_admin' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', __('general.user_created'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nik' => 'required|string|unique:users,nik,' . $user->id,
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

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
        // Ambil semua attempt dari user
        $attempts = $user->quizAttempts()
            ->with('quiz')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Hitung statistik
        $statistics = [
            'total_attempts' => $user->quizAttempts()->count(),
            'completed' => $user->quizAttempts()->where('status', 'completed')->count(),
            'graded' => $user->quizAttempts()->where('status', 'graded')->count(),
            'average_score' => $user->quizAttempts()->where('status', 'graded')->avg('score') ?? 0,
        ];

        return view('admin.users.history', compact('user', 'attempts', 'statistics'));
    }
}
