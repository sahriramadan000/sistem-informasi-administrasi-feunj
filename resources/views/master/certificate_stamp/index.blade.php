@extends('layouts.app')

@section('title', 'Master Stempel Sertifikat - FEB UNJ')

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .checkerboard-bg {
            background-color: #f8fafc;
            background-image:
                linear-gradient(45deg, #e2e8f0 25%, transparent 25%),
                linear-gradient(-45deg, #e2e8f0 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #e2e8f0 75%),
                linear-gradient(-45deg, transparent 75%, #e2e8f0 75%);
            background-size: 16px 16px;
            background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="stamp" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Master Stempel Sertifikat</h1>
                <p class="text-xs sm:text-sm text-gray-500">Kelola aset cap/stempel digital PNG transparan untuk Studio Sertifikat</p>
            </div>
        </div>

        <button type="button" onclick="openStampModal()"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Stempel Baru</span>
        </button>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200/80 px-5 py-3.5 flex items-center gap-3 text-emerald-800 text-sm font-medium">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl bg-rose-50 border border-rose-200/80 px-5 py-3.5 text-rose-800 text-sm space-y-1">
            @foreach ($errors->all() as $err)
                <p class="font-medium">{{ $err }}</p>
            @endforeach
        </div>
    @endif

    {{-- KPI & Filter Bar --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="mac-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Stempel</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalStamps }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="mac-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Stempel Aktif</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $activeStamps }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="mac-card p-5 flex items-center">
            <form method="GET" action="{{ route('master.certificate-stamps.index') }}" class="flex items-center gap-2 w-full">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/kode stempel..."
                        class="w-full pl-9 pr-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                </div>
                <button type="submit" class="px-3.5 py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 cursor-pointer">
                    Cari
                </button>
            </form>
        </div>
    </div>

    {{-- Grid Stempel --}}
    @if ($stamps->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach ($stamps as $stamp)
                <div class="mac-card overflow-hidden flex flex-col justify-between group hover:border-indigo-200 transition-all">
                    <div>
                        <div class="checkerboard-bg h-44 flex items-center justify-center p-5 border-b border-gray-100 relative">
                            <img src="{{ $stamp->file_url }}" alt="{{ $stamp->name }}"
                                class="max-h-32 max-w-full object-contain drop-shadow-xs transition-transform group-hover:scale-105"
                                style="opacity: {{ $stamp->default_opacity / 100 }};">

                            <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $stamp->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-gray-200 text-gray-600' }}">
                                    {{ $stamp->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <span class="absolute bottom-2.5 left-3 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-white/90 text-gray-700 border border-gray-200 shadow-2xs">
                                Opacity {{ $stamp->default_opacity }}%
                            </span>
                        </div>

                        <div class="p-4 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-sm font-bold text-gray-900 truncate">{{ $stamp->name }}</h3>
                                @if ($stamp->code)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/70">
                                        {{ $stamp->code }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-gray-400">Tersedia di Studio Sertifikat</p>
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-2">
                        <button type="button"
                            onclick="openStampModal({{ $stamp->id }}, '{{ addslashes($stamp->name) }}', '{{ addslashes($stamp->code ?? '') }}', {{ $stamp->default_opacity }}, {{ $stamp->is_active ? 'true' : 'false' }}, '{{ $stamp->file_url }}')"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold text-gray-700 bg-white hover:bg-gray-100 border border-gray-200 rounded-xl transition-colors cursor-pointer">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                            <span>Edit</span>
                        </button>

                        <form method="POST" action="{{ route('master.certificate-stamps.destroy', $stamp) }}"
                              onsubmit="return confirm('Hapus stempel {{ addslashes($stamp->name) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center justify-center p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/70 rounded-xl transition-colors cursor-pointer"
                                title="Hapus Stempel">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="mac-card p-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-3xl bg-indigo-50 border border-indigo-200/70 text-indigo-600 flex items-center justify-center mx-auto">
                <i data-lucide="stamp" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900">Belum Ada Stempel Sertifikat</h3>
            <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto">
                Tambahkan gambar cap/stempel fakultas (format PNG transparan) agar staf tinggal klik menempelkannya di Studio Sertifikat.
            </p>
            <button type="button" onclick="openStampModal()"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full shadow-xs cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Upload Stempel Pertama</span>
            </button>
        </div>
    @endif
</div>

{{-- Modal Tambah / Edit Stempel --}}
<div id="stampModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/45 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden">
        <form id="stampForm" method="POST" action="{{ route('master.certificate-stamps.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="stampFormMethod" name="_method" value="POST">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 id="stampModalTitle" class="text-base font-bold text-gray-900">Tambah Stempel Baru</h3>
                    <button type="button" onclick="closeStampModal()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label for="stamp_name" class="block text-xs font-bold text-gray-700 mb-1">Nama Stempel <span class="text-rose-500">*</span></label>
                        <input type="text" id="stamp_name" name="name" required placeholder="Contoh: Stempel Dekan FEB UNJ"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="stamp_code" class="block text-xs font-bold text-gray-700 mb-1">Kode (Opsional)</label>
                            <input type="text" id="stamp_code" name="code" placeholder="FEB-UNJ"
                                class="w-full px-3.5 py-2 text-sm font-mono uppercase border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                        </div>
                        <div>
                            <label for="stamp_opacity" class="block text-xs font-bold text-gray-700 mb-1">Opacity Default (%)</label>
                            <input type="number" id="stamp_opacity" name="default_opacity" min="10" max="100" value="88"
                                class="w-full px-3.5 py-2 text-sm font-mono border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                        </div>
                    </div>

                    <div>
                        <label for="stamp_file" class="block text-xs font-bold text-gray-700 mb-1">Gambar Stempel (PNG Transparan) <span id="stampFileReq" class="text-rose-500">*</span></label>
                        <div class="flex items-center gap-3 p-3 rounded-2xl checkerboard-bg border border-dashed border-gray-300">
                            <img id="stampModalPreview" src="" alt="" class="hidden w-16 h-16 object-contain bg-white/80 rounded-xl p-1">
                            <input type="file" id="stamp_file" name="stamp_file" accept=".png,.webp,.jpg,.jpeg" required
                                onchange="previewStampFile(this)"
                                class="block w-full text-xs text-gray-700 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white cursor-pointer">
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Tips: Jika gambar berlatar putih polos, Studio Sertifikat juga punya fitur hapus background putih otomatis.</p>
                    </div>

                    <label class="inline-flex items-center gap-2 cursor-pointer pt-1">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="stamp_is_active" name="is_active" value="1" checked class="rounded border-gray-300 text-emerald-600">
                        <span class="text-xs font-semibold text-gray-700">Aktif &amp; Tampilkan di Studio Sertifikat</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeStampModal()" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-xl shadow-xs cursor-pointer">Simpan Stempel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openStampModal(id = null, name = '', code = '', opacity = 88, isActive = true, previewUrl = '') {
        const modal = document.getElementById('stampModal');
        const form = document.getElementById('stampForm');
        const methodInput = document.getElementById('stampFormMethod');
        const title = document.getElementById('stampModalTitle');
        const fileInput = document.getElementById('stamp_file');
        const fileReq = document.getElementById('stampFileReq');
        const previewImg = document.getElementById('stampModalPreview');

        if (id) {
            form.action = `/master/certificate-stamps/${id}`;
            methodInput.value = 'PUT';
            title.textContent = 'Edit Stempel Sertifikat';
            fileInput.required = false;
            fileReq.classList.add('hidden');
        } else {
            form.action = '{{ route('master.certificate-stamps.store') }}';
            methodInput.value = 'POST';
            title.textContent = 'Tambah Stempel Baru';
            fileInput.required = true;
            fileReq.classList.remove('hidden');
        }

        document.getElementById('stamp_name').value = name;
        document.getElementById('stamp_code').value = code;
        document.getElementById('stamp_opacity').value = opacity;
        document.getElementById('stamp_is_active').checked = isActive;
        fileInput.value = '';

        if (previewUrl) {
            previewImg.src = previewUrl;
            previewImg.classList.remove('hidden');
        } else {
            previewImg.classList.add('hidden');
            previewImg.src = '';
        }

        modal.classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeStampModal() {
        document.getElementById('stampModal').classList.add('hidden');
    }

    function previewStampFile(input) {
        const previewImg = document.getElementById('stampModalPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endpush
