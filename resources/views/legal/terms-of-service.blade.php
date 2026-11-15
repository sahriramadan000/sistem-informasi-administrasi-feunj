<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ketentuan Layanan (Terms of Service) - Sistem Informasi Administrasi FEB UNJ</title>

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
                <a href="{{ route('privacy-policy') }}"
                   class="px-3.5 py-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
                    Kebijakan Privasi
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
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>Terms of Service</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Ketentuan Layanan
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
                        Penerimaan Ketentuan
                    </h2>
                    <p>
                        Dengan mengakses atau menggunakan <strong>Sistem Informasi Administrasi FEB UNJ</strong>, Anda setuju untuk mematuhi seluruh Ketentuan Layanan ini serta peraturan tata kelola administrasi yang berlaku di Fakultas Ekonomi dan Bisnis Universitas Negeri Jakarta.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">2</span>
                        Penggunaan yang Diizinkan
                    </h2>
                    <ul class="list-disc pl-6 space-y-1.5">
                        <li>Aplikasi ini ditujukan bagi staf, operator, pimpinan, dan administrator yang telah diberikan akun resmi untuk keperluan pengelolaan persuratan, arsip digital, legalisir, dan peminjaman ruang.</li>
                        <li>Pengguna wajib menjaga kerahasiaan kredensial akun (username dan kata sandi) serta bertanggung jawab penuh atas setiap aktivitas yang dilakukan menggunakan akun tersebut.</li>
                        <li>Dilarang menyalahgunakan akses, mencoba menembus pembatasan otorisasi, atau mengunggah berkas berbahaya (*malware*/skrip ilegal) ke dalam sistem.</li>
                    </ul>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">3</span>
                        Pengarsipan &amp; Integrasi Google Drive
                    </h2>
                    <p>
                        Dokumen yang diunggah melalui fitur pengarsipan akan disimpan secara terstruktur ke akun Google Drive yang telah ditetapkan oleh administrator pengelola sistem. Pengguna bertanggung jawab memastikan keabsahan, kelengkapan, dan kesesuaian dokumen yang diarsipkan.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">4</span>
                        Ketersediaan &amp; Pembaruan Layanan
                    </h2>
                    <p>
                        Pengelola berhak melakukan pemeliharaan sistem, pembaruan fitur, serta penyesuaian ketentuan layanan sewaktu-waktu guna menjaga keamanan, keandalan, dan kelancaran operasional administrasi.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FF5500] inline-flex items-center justify-center text-xs font-bold">5</span>
                        Kontak Pengelola
                    </h2>
                    <p>
                        Untuk bantuan teknis, laporan kendala, atau pertanyaan terkait Ketentuan Layanan ini, silakan hubungi administrator melalui email:
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
            <a href="{{ route('privacy-policy') }}" class="hover:text-gray-700 transition-colors">Privacy Policy</a>
            <span>&bull;</span>
            <a href="{{ route('terms-of-service') }}" class="text-[#FF5500] font-semibold">Terms of Service</a>
        </div>
    </footer>

    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>

</html>
