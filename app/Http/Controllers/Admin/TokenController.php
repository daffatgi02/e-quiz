<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TokenController extends Controller
{
    public function generateToken(User $user)
    {
        $token = $user->generateToken();

        return redirect()->back()->with('success', "ID-Trainer berhasil digenerate: {$token}");
    }

    public function resetPin(User $user)
    {
        $user->update([
            'pin' => null,
            'pin_set' => false
        ]);

        return redirect()->back()->with('success', 'PIN berhasil direset');
    }

    public function downloadTokens(Request $request)
    {
        $users = User::query()
            ->when($request->department, function($query, $department) {
                $query->where('department', $department);
            })
            ->when($request->position, function($query, $position) {
                $query->where('position', $position);
            })
            ->whereNotNull('login_token')
            ->get();

        $pdf = PDF::loadView('admin.tokens.pdf', [
            'users' => $users,
            'filters' => [
                'department' => $request->department ?? null,
                'position' => $request->position ?? null
            ]
        ]);

        return $pdf->download('daftar_id_trainer_' . date('Y-m-d') . '.pdf');
    }
}
