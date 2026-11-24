<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Signatory;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Controller untuk manajemen master data penandatangan surat
 */
class SignatoryController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,operator');
        
        // Middleware khusus untuk method yang hanya admin yang bisa akses
        $this->middleware('role:admin')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ]);
    }

    /**
     * Menampilkan daftar penandatangan
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Signatory::query();

            // Filter status aktif/nonaktif
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            // Search berdasarkan nama, jabatan, atau NIP
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $perPage = in_array((int) $request->get('per_page', 10), [10, 25, 50, 100]) ? (int) $request->get('per_page', 10) : 10;
            $signatories = $query->orderBy('code')->paginate($perPage);
            
            return view('master.signatory.index', compact('signatories'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'SignatoryController.index', 'Gagal memuat daftar penandatangan.');
        }
    }

    /**
     * Menampilkan form tambah penandatangan
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $studioUsers = User::where('is_active', true)
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();

        return view('master.signatory.create', compact('studioUsers'));
    }

    /**
     * Menyimpan penandatangan baru
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'position' => 'required|string|max:150',
            'nip' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'restrict_signature_access' => 'nullable|boolean',
            'allowed_user_ids' => 'nullable|array',
            'allowed_user_ids.*' => 'exists:users,id',
            'signature_file' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:5120',
        ], [
            'signature_file.mimes' => 'Format spesimen TTD harus PNG (disarankan transparan), WEBP, JPG, atau JPEG.',
            'signature_file.max' => 'Ukuran berkas TTD maksimal 5 MB.',
        ]);

        try {
            // Set is_active explicitly using boolean helper
            $validated['is_active'] = $request->boolean('is_active');
            $validated['restrict_signature_access'] = $request->boolean('restrict_signature_access');
            $allowedUserIds = $validated['restrict_signature_access'] ? $request->input('allowed_user_ids', []) : [];
            unset($validated['signature_file'], $validated['allowed_user_ids']);

            if ($request->hasFile('signature_file')) {
                $validated['signature_path'] = $this->storeSignatureFile($request->file('signature_file'), $validated['code']);
            }

            // Simpan data menggunakan transaction
            $signatory = DB::transaction(function () use ($validated, $allowedUserIds) {
                $sig = Signatory::create($validated);
                $sig->allowedSignatureUsers()->sync($allowedUserIds);
                return $sig;
            });

            Log::info('Signatory created', [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            if ($signatory) {
                AuditLogService::log('create', 'Signatory', $signatory->id, [
                    'code' => $validated['code'],
                    'name' => $validated['name'],
                    'position' => $validated['position'],
                    'restrict_signature_access' => $validated['restrict_signature_access'],
                ]);
            }

            return redirect()
                ->route('master.signatories.index')
                ->with('success', 'Penandatangan berhasil ditambahkan.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'SignatoryController.store', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan form edit penandatangan
     * 
     * @param Signatory $signatory
     * @return \Illuminate\View\View
     */
    public function edit(Signatory $signatory)
    {
        $signatory->load('allowedSignatureUsers');
        $studioUsers = User::where('is_active', true)
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();
        $allowedUserIds = $signatory->allowedSignatureUsers->pluck('id')->all();

        return view('master.signatory.edit', compact('signatory', 'studioUsers', 'allowedUserIds'));
    }

    /**
     * Mengupdate data penandatangan
     * 
     * @param Request $request
     * @param Signatory $signatory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Signatory $signatory)
    {
        // Validasi input
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'position' => 'required|string|max:150',
            'nip' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'restrict_signature_access' => 'nullable|boolean',
            'allowed_user_ids' => 'nullable|array',
            'allowed_user_ids.*' => 'exists:users,id',
            'signature_file' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:5120',
            'remove_signature' => 'nullable|boolean',
        ], [
            'signature_file.mimes' => 'Format spesimen TTD harus PNG (disarankan transparan), WEBP, JPG, atau JPEG.',
            'signature_file.max' => 'Ukuran berkas TTD maksimal 5 MB.',
        ]);

        try {
            // Set is_active explicitly using boolean helper
            $validated['is_active'] = $request->boolean('is_active');
            $validated['restrict_signature_access'] = $request->boolean('restrict_signature_access');
            $allowedUserIds = $validated['restrict_signature_access'] ? $request->input('allowed_user_ids', []) : [];
            unset($validated['signature_file'], $validated['remove_signature'], $validated['allowed_user_ids']);

            if ($request->hasFile('signature_file')) {
                $oldPath = $signatory->getAbsoluteSignaturePath();
                $validated['signature_path'] = $this->storeSignatureFile($request->file('signature_file'), $validated['code']);
                if ($oldPath && File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            } elseif ($request->boolean('remove_signature') && $signatory->signature_path) {
                $oldPath = $signatory->getAbsoluteSignaturePath();
                if ($oldPath && File::exists($oldPath)) {
                    File::delete($oldPath);
                }
                $validated['signature_path'] = null;
            }

            // Update data menggunakan transaction
            DB::transaction(function () use ($signatory, $validated, $allowedUserIds) {
                $signatory->update($validated);
                $signatory->allowedSignatureUsers()->sync($allowedUserIds);
            });

            Log::info('Signatory updated', [
                'id' => $signatory->id,
                'code' => $validated['code'],
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            AuditLogService::log('update', 'Signatory', $signatory->id, [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'changes' => array_keys($validated),
            ]);

            return redirect()
                ->route('master.signatories.index')
                ->with('success', 'Penandatangan berhasil diperbarui.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'SignatoryController.update', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
        }
    }

    /**
     * Menonaktifkan penandatangan (soft delete)
     * 
     * @param Signatory $signatory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Signatory $signatory)
    {
        try {
            // Cek apakah penandatangan sudah nonaktif
            if (!$signatory->is_active) {
                return back()
                    ->withErrors(['error' => 'Penandatangan sudah dalam status nonaktif.']);
            }

            // Nonaktifkan data menggunakan transaction
            DB::transaction(function () use ($signatory) {
                $signatory->update(['is_active' => false]);
            });

            Log::info('Signatory deactivated', [
                'id' => $signatory->id,
                'code' => $signatory->code,
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            AuditLogService::log('delete', 'Signatory', $signatory->id, [
                'code' => $signatory->code,
                'name' => $signatory->name,
            ]);

            return redirect()
                ->route('master.signatories.index')
                ->with('success', 'Penandatangan berhasil dinonaktifkan.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'SignatoryController.destroy', 'Terjadi kesalahan saat menonaktifkan data. Silakan coba lagi.');
        }
    }

    /**
     * Simpan berkas TTD digital penandatangan ke private storage (terproteksi, tidak bisa diakses publik).
     */
    protected function storeSignatureFile($file, string $code): string
    {
        $dir = storage_path('app/private/signatures');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $slug = Str::slug($code) ?: 'signatory';
        $filename = "ttd_{$slug}_" . time() . '_' . Str::random(6) . ".{$ext}";
        $file->move($dir, $filename);

        return "private/signatures/{$filename}";
    }
}