<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi (Privacy Policy) - Sistem Informasi Administrasi FEB UNJ</title>

    {{-- Fonts: Plus Jakarta Sans & Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ICON --}}
    <link rel="icon" type="image/png" href="{{ asset('logo-unj.png') }}">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #FAF7F2;
            background-image:
                radial-gradient(circle at 50% 48%, rgba(255, 255, 255, 0.96) 0%, rgba(250, 247, 242, 0.7) 50%),
                radial-gradient(circle at 12% 18%, rgba(254, 215, 170, 0.22) 0%, transparent 35%),
                radial-gradient(circle at 88% 82%, rgba(254, 215, 170, 0.22) 0%, transparent 35%),
                radial-gradient(rgba(210, 198, 180, 0.4) 1.3px, transparent 1.3px);
            background-size: auto, auto, auto, 28px 28px;
            background-attachment: fixed;
        }

        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(230, 222, 210, 0.75);
            box-shadow: 0 20px 60px -15px rgba(60, 45, 25, 0.08),
                        0 0 0 1px rgba(230, 222, 210, 0.45);
        }
    </style>
</head>

<body class="min-h-full text-gray-800 antialiased flex flex-col">
    {{-- Top Navigation --}}
    <header class="w-full border-b border-[#EAE5DC] bg-white/80 backdrop-blur-md sticky top-0 z-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4">
            <a href="{{ route('login') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('logo-unj.png') }}" alt="Logo UNJ" class="w-9 h-9 object-contain">
                <div>
                    <p class="text-sm font-bold text-gray-900 group-hover:text-[#FF5500] transition-colors leading-tight">
                        SIA FEB UNJ
                    </p>
                    <p class="text-[11px] text-gray-500">Fakultas Ekonomi dan Bisnis UNJ</p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('terms-of-service') }}"
                   class="px-3.5 py-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
                    Ketentuan Layanan
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold text-white bg-[#FF5500] hover:bg-[#E64D00] rounded-full shadow-xs transition-all">
                    <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                    <span>Login</span>
                </a>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <div class="mac-card p-6 sm:p-10 space-y-8">
            {{-- Header --}}
            <div class="pb-6 border-b border-gray-100 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 border border-orange-200/70 text-[#FF5500] text-xs font-bold">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    <span>Privacy Policy</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Kebijakan Privasi
                </h1>
                <p class="text-sm text-gray-500">
                    Sistem Informasi Administrasi Terpadu &mdash; Fakultas Ekonomi dan Bisnis Universitas Negeri Jakarta (FEB UNJ)
                </p>
                <p class="text-xs text-gray-400">
                    Terakhir diperbarui: {{ date('d F Y') }}
                </p>
            </div>

            {{-- Sections --}}
            <div class="space-y-6 text-sm text-gray-600 leading-relaxed">
                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">1</span>
                        Ruang Lingkup &amp; Identitas Layanan
                    </h2>
                    <p>
                        <strong>Sistem Informasi Administrasi FEB UNJ</strong> adalah aplikasi internal yang dikelola untuk mendukung layanan administrasi persuratan, pengarsipan digital, legalisir dokumen, dan peminjaman ruang di lingkungan Fakultas Ekonomi dan Bisnis Universitas Negeri Jakarta.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">2</span>
                        Informasi yang Kami Kumpulkan
                    </h2>
                    <ul class="list-disc pl-6 space-y-1.5">
                        <li><strong>Data Akun Pengguna:</strong> Nama lengkap, alamat email/username, jabatan atau peran (*role*), serta hak akses modul yang diberikan oleh administrator.</li>
                        <li><strong>Data Administrasi &amp; Dokumen:</strong> Nomor surat, tanggal, perihal, metadata pengarsipan, data legalisir, jadwal peminjaman ruang, serta berkas dokumen digital yang diunggah ke sistem.</li>
                        <li><strong>Log Aktivitas &amp; Keamanan:</strong> Alamat IP, jenis peramban (*user agent*), catatan waktu akses, dan log error sistem untuk keperluan audit keamanan serta pemeliharaan aplikasi.</li>
                    </ul>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">3</span>
                        Penggunaan Layanan Google Drive API
                    </h2>
                    <p>
                        Aplikasi ini menggunakan integrasi <strong>Google Drive API</strong> (dengan izin akses terbatas <code>drive.file</code>) secara khusus untuk memfasilitasi pengarsipan dokumen resmi satu pintu ke penyimpanan Google Drive resmi pengelola arsip.
                    </p>
                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200/70 text-xs sm:text-sm text-blue-900 space-y-1.5">
                        <p class="font-bold">Komitmen Kepatuhan Google API Services User Data Policy:</p>
                        <p>
                            Penggunaan dan transfer informasi yang diterima dari Google API oleh aplikasi ini mematuhi
                            <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener noreferrer" class="underline font-semibold hover:text-blue-700">Google API Services User Data Policy</a>,
                            termasuk persyaratan <em>Limited Use</em>. Aplikasi hanya membuat struktur folder arsip (berdasarkan tahun dan jenis surat) serta mengunggah, membaca tautan, atau menghapus dokumen arsip yang dikelola oleh sistem ini, dan <strong>tidak pernah</strong> menggunakan data tersebut untuk periklanan, penjualan data, maupun pelatihan model AI eksternal.
                        </p>
                    </div>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">4</span>
                        Penyimpanan &amp; Keamanan Data
                    </h2>
                    <p>
                        Seluruh kredensial otorisasi (termasuk token akses Google OAuth) disimpan secara terenkripsi. Akses terhadap fitur pengelolaan data dibatasi berdasarkan peran pengguna (*role-based access control*) yang diautentikasi oleh sistem.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">5</span>
                        Pembagian Data kepada Pihak Ketiga
                    </h2>
                    <p>
                        Kami tidak menjual, menyewakan, atau membagikan data pengguna maupun dokumen administrasi kepada pihak ketiga di luar kebutuhan operasional resmi institusi dan integrasi penyimpanan Google Drive yang telah diotorisasi oleh administrator.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">6</span>
                        Kontak Pengelola
                    </h2>
                    <p>
                        Apabila Anda memiliki pertanyaan mengenai Kebijakan Privasi ini atau ingin mengajukan permintaan terkait data akun dan arsip, silakan hubungi tim pengelola melalui email:
                        <a href="mailto:feb@unj.ac.id" class="text-[#FF5500] font-semibold hover:underline">feb@unj.ac.id</a>.
                    </p>
                </section>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="w-full py-6 px-6 text-center text-xs text-gray-500">
        <p>&copy; {{ date('Y') }} Fakultas Ekonomi dan Bisnis Universitas Negeri Jakarta</p>
        <div class="flex items-center justify-center gap-3 mt-1.5 text-[11px] text-gray-400">
            <a href="{{ route('privacy-policy') }}" class="text-[#FF5500] font-semibold">Privacy Policy</a>
            <span>&bull;</span>
            <a href="{{ route('terms-of-service') }}" class="hover:text-gray-700 transition-colors">Terms of Service</a>
        </div>
    </footer>

    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>

</html>
