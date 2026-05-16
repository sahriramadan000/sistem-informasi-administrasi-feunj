<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Controller untuk user mengedit profile dan password mereka sendiri
 */
class ProfileController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan form edit profile
     */
    public function edit()
    {
        try {
            $user = Auth::user();
            return view('profile.edit', compact('user'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'ProfileController.edit', 'Gagal memuat halaman profile.');
        }
    }

    /**
     * Update profile user (nama, email, username)
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
                'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            ], [
                'name.required' => 'Nama wajib diisi.',
                'name.max' => 'Nama maksimal 100 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan oleh user lain.',
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username sudah digunakan oleh user lain.',
            ]);

            // Update user data
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
            ]);

            // Log to audit trail
            AuditLogService::log('update', 'Profile', $user->id, [
                'action' => 'profile_update',
                'changes' => array_keys($validated),
            ]);

            Log::info('User profile updated', [
                'user_id' => $user->id,
                'username' => $user->username,
            ]);

            return redirect()->route('profile.edit')
                ->with('success', 'Profile berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return $this->handleError($e, 'ProfileController.update', 'Gagal memperbarui profile.');
        }
    }

    /**
     * Update password user
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
                'new_password_confirmation' => 'required|string|min:6',
            ], [
                'current_password.required' => 'Password saat ini wajib diisi.',
                'new_password.required' => 'Password baru wajib diisi.',
                'new_password.min' => 'Password baru minimal 6 karakter.',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
                'new_password_confirmation.required' => 'Konfirmasi password wajib diisi.',
                'new_password_confirmation.min' => 'Konfirmasi password minimal 6 karakter.',
            ]);

            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Password saat ini tidak benar.',
                ]);
            }

            // Check if new password is same as current
            if (Hash::check($validated['new_password'], $user->password)) {
                return back()->withErrors([
                    'new_password' => 'Password baru tidak boleh sama dengan password saat ini.',
                ]);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            // Log to audit trail
            AuditLogService::log('update', 'Profile', $user->id, [
                'action' => 'password_change',
            ]);

            Log::info('User password changed', [
                'user_id' => $user->id,
                'username' => $user->username,
            ]);

            return redirect()->route('profile.edit')
                ->with('success', 'Password berhasil diubah!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return $this->handleError($e, 'ProfileController.updatePassword', 'Gagal mengubah password.');
        }
    }
}
