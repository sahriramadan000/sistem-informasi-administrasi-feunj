<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Controller untuk manajemen master data pengguna
 */
class UserController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Menampilkan daftar pengguna
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate(10);
        return view('master.users.index', compact('users'));
    }

    /**
     * Menampilkan form tambah pengguna
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('master.users.create');
    }

    /**
     * Menyimpan pengguna baru
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:150|unique:users,email',
                'username' => 'required|string|max:50|unique:users,username',
                'password' => 'required|string|min:6|confirmed',
                'role' => ['required', Rule::in(['admin', 'operator', 'viewer'])],
                'is_active' => 'boolean',
            ]);

            // Handle checkbox values
            $validated['is_active'] = $request->has('is_active');
            $validated['password'] = Hash::make($validated['password']);

            // Simpan data menggunakan transaction
            DB::transaction(function () use ($validated) {
                User::create($validated);
            });

            Log::info('User created', [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'created_by' => auth()->id()
            ]);

            return redirect()
                ->route('master.users.index')
                ->with('success', 'Pengguna berhasil ditambahkan.');

        } catch (\Throwable $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
        }
    }

    /**
     * Menampilkan detail pengguna
     * 
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('master.users.show', compact('user'));
    }

    /**
     * Menampilkan form edit pengguna
     * 
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('master.users.edit', compact('user'));
    }

    /**
     * Mengupdate data pengguna
     * 
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
                'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
                'password' => 'nullable|string|min:6|confirmed',
                'role' => ['required', Rule::in(['admin', 'operator', 'viewer'])],
                'is_active' => 'boolean',
            ]);

            // Handle checkbox values
            $validated['is_active'] = $request->has('is_active');
            
            // Hanya update password jika diisi
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Update data menggunakan transaction
            DB::transaction(function () use ($user, $validated) {
                $user->update($validated);
            });

            Log::info('User updated', [
                'id' => $user->id,
                'username' => $validated['username'],
                'updated_by' => auth()->id()
            ]);

            return redirect()
                ->route('master.users.index')
                ->with('success', 'Pengguna berhasil diperbarui.');

        } catch (\Throwable $e) {
            Log::error('Error updating user', [
                'id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.']);
        }
    }

    /**
     * Menghapus pengguna
     * 
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        try {
            // Cek apakah user sedang login
            if ($user->id === auth()->id()) {
                return back()
                    ->withErrors(['error' => 'Anda tidak dapat menghapus akun yang sedang digunakan.']);
            }

            // Cek apakah user memiliki surat
            if ($user->letters()->exists()) {
                return back()
                    ->withErrors(['error' => 'Pengguna tidak dapat dihapus karena memiliki riwayat pembuatan surat.']);
            }

            // Hapus data menggunakan transaction
            DB::transaction(function () use ($user) {
                $user->delete();
            });

            Log::info('User deleted', [
                'id' => $user->id,
                'username' => $user->username,
                'deleted_by' => auth()->id()
            ]);

            return redirect()
                ->route('master.users.index')
                ->with('success', 'Pengguna berhasil dihapus.');

        } catch (\Throwable $e) {
            Log::error('Error deleting user', [
                'id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.']);
        }
    }
}
