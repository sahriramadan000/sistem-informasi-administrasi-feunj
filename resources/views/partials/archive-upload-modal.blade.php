@php
    $__driveService = app(\App\Services\GoogleDriveService::class);
    $__gdriveReady = $__driveService->isConnected() && !empty($__driveService->getRootFolderId());
    $__gdriveLastError = $__driveService->getLastError();
@endphp

{{-- Shared Modal: Upload Satu Pintu ke Google Drive & Fallback Link Manual --}}
<div id="archiveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/45 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full border border-slate-200/90 overflow-hidden transform transition-all scale-95 opacity-0" id="archiveModalContent">
        <form id="archiveForm" method="POST" action="" enctype="multipart/form-data" onsubmit="submitArchiveLink(event)">
            @csrf
            <input type="hidden" id="archive_mode" value="{{ $__gdriveReady ? 'upload' : 'link' }}">
            <div class="p-6 space-y-4">
                {{-- Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-200/70 flex items-center justify-center text-blue-600">
                            <i data-lucide="cloud-upload" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Arsipkan Dokumen Surat</h3>
                            <p class="text-[11px] text-slate-400 font-mono" id="archiveModalLetterNumber">-</p>
                        </div>
                    </div>
                    <button type="button" onclick="hideArchiveModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Inline Error Banner --}}
                <div id="archiveModalError" class="hidden rounded-2xl bg-rose-50 border border-rose-200/80 px-4 py-3 text-xs text-rose-800 space-y-1">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>
                        <span id="archiveModalErrorText" class="font-medium leading-relaxed"></span>
                    </div>
                </div>

                {{-- Mode Switcher Tabs --}}
                <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 rounded-2xl">
                    <button type="button" id="tabBtnUpload" onclick="switchArchiveMode('upload')"
                        class="px-3 py-2 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i>
                        <span>Upload Berkas</span>
                    </button>
                    <button type="button" id="tabBtnLink" onclick="switchArchiveMode('link')"
                        class="px-3 py-2 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <i data-lucide="link" class="w-3.5 h-3.5"></i>
                        <span>Link Manual GDrive</span>
                    </button>
                </div>

                {{-- Panel 1: Upload File Satu Pintu --}}
                <div id="panelUpload" class="space-y-3">
                    @if (!$__gdriveReady)
                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200/80 text-xs text-amber-900 space-y-2">
                            <div class="flex items-start gap-2">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="font-bold">Google Drive Satu Pintu Belum Siap</p>
                                    <p class="text-[11px] text-amber-800 mt-0.5">
                                        {{ $__gdriveLastError ?: 'Akun Google Drive atau Root Folder belum dihubungkan. Anda dapat menggunakan tab Link Manual GDrive sementara.' }}
                                    </p>
                                </div>
                            </div>
                            @if (auth()->user()?->isAdmin())
                                <a href="{{ route('admin.gdrive.index') }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-600 hover:bg-amber-700 text-white text-[11px] font-bold transition-colors">
                                    <i data-lucide="settings" class="w-3 h-3"></i>
                                    <span>Atur Google Drive Sekarang</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-50/70 border border-blue-200/60 text-[11px] text-blue-800">
                            <i data-lucide="folder-tree" class="w-3.5 h-3.5 text-blue-600 flex-shrink-0"></i>
                            <span>Berkas otomatis tersimpan ke folder <strong>Tahun &amp; Tipe Surat</strong> di Google Drive.</span>
                        </div>
                    @endif

                    <label for="modal_archive_file" id="dropZoneArchive"
                        class="flex flex-col items-center justify-center gap-2 p-5 border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl bg-slate-50/60 hover:bg-blue-50/30 transition-all cursor-pointer text-center">
                        <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200/80 flex items-center justify-center text-blue-600 shadow-2xs">
                            <i data-lucide="file-up" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800" id="selectedFileName">Pilih berkas atau seret ke sini</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">PDF, JPG, PNG, DOC, DOCX (Maks. {{ config('services.google_drive.max_file_size_mb', 20) }} MB)</p>
                        </div>
                        <input type="file" id="modal_archive_file" name="archive_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden" onchange="handleArchiveFileSelect(this)">
                    </label>
                </div>

                {{-- Panel 2: Manual Drive URL --}}
                <div id="panelLink" class="space-y-1.5 hidden">
                    <label for="modal_drive_url" class="text-xs font-semibold text-slate-700">
                        URL / Link Google Drive
                    </label>
                    <input type="url" id="modal_drive_url" name="drive_url" value=""
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium"
                        placeholder="https://drive.google.com/file/d/.../view?usp=sharing">
                    <p class="text-[11px] text-slate-400">Gunakan tab ini jika berkas sudah diunggah manual ke Google Drive.</p>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                    <button type="button" id="btnRemoveArchive" onclick="removeCurrentArchive()"
                        class="hidden inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Hapus Arsip</span>
                    </button>

                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" onclick="hideArchiveModal()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitArchive"
                            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-xs transition-all cursor-pointer">
                            Simpan Arsip
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function switchArchiveMode(mode) {
        const modeInput = document.getElementById('archive_mode');
        const panelUpload = document.getElementById('panelUpload');
        const panelLink = document.getElementById('panelLink');
        const tabUpload = document.getElementById('tabBtnUpload');
        const tabLink = document.getElementById('tabBtnLink');
        const btnSubmit = document.getElementById('btnSubmitArchive');

        if (modeInput) modeInput.value = mode;

        if (mode === 'upload') {
            panelUpload?.classList.remove('hidden');
            panelLink?.classList.add('hidden');
            tabUpload?.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
            tabUpload?.classList.remove('text-slate-500');
            tabLink?.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
            tabLink?.classList.add('text-slate-500');
            if (btnSubmit) btnSubmit.textContent = 'Upload & Arsipkan';
        } else {
            panelUpload?.classList.add('hidden');
            panelLink?.classList.remove('hidden');
            tabLink?.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
            tabLink?.classList.remove('text-slate-500');
            tabUpload?.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
            tabUpload?.classList.add('text-slate-500');
            if (btnSubmit) btnSubmit.textContent = 'Simpan Tautan';
        }
    }

    function handleArchiveFileSelect(input) {
        const label = document.getElementById('selectedFileName');
        if (!label) return;
        if (input.files && input.files[0]) {
            const f = input.files[0];
            const mb = (f.size / (1024 * 1024)).toFixed(2);
            label.textContent = `${f.name} (${mb} MB)`;
        } else {
            label.textContent = 'Pilih berkas atau seret ke sini';
        }
    }

    function openArchiveModal(letterId, letterNumber, currentUrl) {
        const modal = document.getElementById('archiveModal');
        const modalContent = document.getElementById('archiveModalContent');
        const form = document.getElementById('archiveForm');
        const input = document.getElementById('modal_drive_url');
        const fileInput = document.getElementById('modal_archive_file');
        const fileLabel = document.getElementById('selectedFileName');
        const titleEl = document.getElementById('archiveModalLetterNumber');
        const errBox = document.getElementById('archiveModalError');
        const removeBtn = document.getElementById('btnRemoveArchive');

        if (!modal || !modalContent || !form) return;

        form.action = `/letters/${letterId}/archive-link`;
        if (titleEl) titleEl.textContent = letterNumber || '-';
        if (input) input.value = currentUrl || '';
        if (fileInput) fileInput.value = '';
        if (fileLabel) fileLabel.textContent = 'Pilih berkas atau seret ke sini';
        if (errBox) errBox.classList.add('hidden');

        if (removeBtn) {
            if (currentUrl) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        }

        switchArchiveMode('{{ $__gdriveReady ? 'upload' : 'link' }}');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function hideArchiveModal() {
        const modal = document.getElementById('archiveModal');
        const modalContent = document.getElementById('archiveModalContent');
        if (!modal || !modalContent) return;

        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    async function removeCurrentArchive() {
        if (!confirm('Hapus arsip dokumen dari surat ini?')) return;
        const form = document.getElementById('archiveForm');
        const formData = new FormData();
        formData.append('remove_archive', '1');
        await sendArchiveRequest(form.action, formData, 'Menghapus...');
    }

    async function submitArchiveLink(e) {
        e.preventDefault();
        const form = document.getElementById('archiveForm');
        const mode = document.getElementById('archive_mode')?.value || 'upload';
        const fileInput = document.getElementById('modal_archive_file');
        const driveUrl = document.getElementById('modal_drive_url')?.value || '';
        const errBox = document.getElementById('archiveModalError');
        const errText = document.getElementById('archiveModalErrorText');

        if (errBox) errBox.classList.add('hidden');

        const formData = new FormData();
        if (mode === 'upload') {
            if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                if (errBox && errText) {
                    errText.textContent = 'Silakan pilih berkas terlebih dahulu atau pindah ke tab Link Manual GDrive.';
                    errBox.classList.remove('hidden');
                }
                return;
            }
            formData.append('archive_file', fileInput.files[0]);
            await sendArchiveRequest(form.action, formData, 'Mengunggah ke Drive...');
        } else {
            formData.append('drive_url', driveUrl);
            await sendArchiveRequest(form.action, formData, 'Menyimpan...');
        }
    }

    async function sendArchiveRequest(url, formData, loadingText) {
        const btn = document.getElementById('btnSubmitArchive');
        const errBox = document.getElementById('archiveModalError');
        const errText = document.getElementById('archiveModalErrorText');
        const prevText = btn ? btn.textContent : 'Simpan Arsip';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="inline-block animate-spin mr-1">⏳</span> ${loadingText}`;
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                hideArchiveModal();
                if (typeof showToast === 'function') showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 700);
            } else {
                if (errBox && errText) {
                    errText.textContent = data.message || 'Gagal memproses arsip surat.';
                    errBox.classList.remove('hidden');
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
                if (typeof showToast === 'function') showToast(data.message || 'Gagal menyimpan arsip.', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = prevText;
                }
            }
        } catch (err) {
            if (errBox && errText) {
                errText.textContent = 'Terjadi kesalahan jaringan saat menghubungi server.';
                errBox.classList.remove('hidden');
            }
            if (typeof showToast === 'function') showToast('Terjadi kesalahan jaringan.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.textContent = prevText;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('dropZoneArchive');
        const fileInput = document.getElementById('modal_archive_file');
        if (!dropZone || !fileInput) return;

        ['dragenter', 'dragover'].forEach(evt => {
            dropZone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropZone.classList.add('border-blue-500', 'bg-blue-50/50');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropZone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500', 'bg-blue-50/50');
            });
        });

        dropZone.addEventListener('drop', (e) => {
            if (e.dataTransfer?.files?.length) {
                fileInput.files = e.dataTransfer.files;
                handleArchiveFileSelect(fileInput);
            }
        });
    });
</script>
