<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
    public function generateToken(User $user)
    {
        $token = $user->generateToken();

        return redirect()->back()->with('success', "ID-Trainer berhasil digenerate: {$token}");
    }

    public function resetPin(User $user)
    {
        try {
            // Use DB transaction for safety
            DB::transaction(function () use ($user) {
                $user->update([
                    'pin' => null,
                    'pin_set' => false
                ]);
            });

            // Log the action for debugging
            Log::info("PIN reset for user {$user->id} ({$user->name}) by admin " . auth()->user()->id);

            return redirect()->back()->with('success', 'PIN berhasil direset untuk ' . $user->name);
        } catch (\Exception $e) {
            Log::error("Failed to reset PIN for user {$user->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mereset PIN: ' . $e->getMessage());
        }
    }

    public function downloadTokens(Request $request)
    {
        $users = User::query()
            ->when($request->department, function ($query, $department) {
                $query->where('department', $department);
            })
            ->when($request->position, function ($query, $position) {
                $query->where('position', $position);
            })
            ->when($request->perusahaan, function ($query, $perusahaan) { // Add this line
                $query->where('perusahaan', $perusahaan);
            })
            ->when($request->search, function ($query, $search) { // Add this line
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('perusahaan', 'like', "%{$search}%");
                });
            })
            ->whereNotNull('login_token')
            ->get();

        $pdf = PDF::loadView('admin.tokens.pdf', [
            'users' => $users,
            'filters' => [
                'department' => $request->department ?? null,
                'position' => $request->position ?? null,
                'perusahaan' => $request->perusahaan ?? null, // Add this line
                'search' => $request->search ?? null, // Add this line
            ]
        ]);

        return $pdf->download('daftar_id_trainer_' . date('Y-m-d') . '.pdf');
    }
}
