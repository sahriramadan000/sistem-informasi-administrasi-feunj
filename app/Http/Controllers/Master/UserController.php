<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
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
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Search berdasarkan nama, username, atau email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->orderBy('name')->paginate($request->get('per_page', 10));
            
            return view('master.users.index', compact('users'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'UserController.index', 'Gagal memuat daftar pengguna.');
        }
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
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'operator', 'viewer'])],
            'is_active' => 'boolean',
            'can_access_letters' => 'boolean',
            'can_access_legalizations' => 'boolean',
        ]);

        try {
            // Handle checkbox values
            $validated['is_active'] = $request->has('is_active');
            $validated['can_access_letters'] = $request->has('can_access_letters');
            $validated['can_access_legalizations'] = $request->has('can_access_legalizations');
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

            // Log to audit trail
            $user = User::where('username', $validated['username'])->first();
            if ($user) {
                AuditLogService::log('create', 'User', $user->id, [
                    'username' => $validated['username'],
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'role' => $validated['role'] ?? 'viewer',
                ]);
            }

            return redirect()
                ->route('master.users.index')
                ->with('success', 'Pengguna berhasil ditambahkan.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'UserController.store', 'Terjadi kesalahan saat menyimpan data pengguna. Silakan coba lagi.');
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
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'operator', 'viewer'])],
            'is_active' => 'boolean',
            'can_access_letters' => 'boolean',
            'can_access_legalizations' => 'boolean',
        ]);

        try {
            // Handle checkbox values
            $validated['is_active'] = $request->has('is_active');
            $validated['can_access_letters'] = $request->has('can_access_letters');
            $validated['can_access_legalizations'] = $request->has('can_access_legalizations');
            
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

            // Log to audit trail
            AuditLogService::log('update', 'User', $user->id, [
                'username' => $validated['username'],
                'name' => $validated['name'],
                'changes' => array_keys($validated),
            ]);

            return redirect()
                ->route('master.users.index')
                ->with('success', 'Pengguna berhasil diperbarui.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'UserController.update', 'Terjadi kesalahan saat memperbarui data pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Menonaktifkan pengguna (soft delete)
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
                    ->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.']);
            }

            // Cek apakah user sudah nonaktif
            if (!$user->is_active) {
                return back()
                    ->withErrors(['error' => 'Pengguna sudah dalam status nonaktif.']);
            }

            // Nonaktifkan data menggunakan transaction
            DB::transaction(function () use ($user) {
                $user->update(['is_active' => false]);
            });

            Log::info('User deactivated', [
                'id' => $user->id,
                'username' => $user->username,
                'deactivated_by' => auth()->id()
            ]);

            // Log to audit trail
            AuditLogService::log('delete', 'User', $user->id, [
                'username' => $user->username,
                'name' => $user->name,
            ]);

            return redirect()
                ->route('master.users.index')
                ->with('success', 'Pengguna berhasil dinonaktifkan.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'UserController.destroy', 'Terjadi kesalahan saat menonaktifkan data pengguna. Silakan coba lagi.');
        }
    }
}
