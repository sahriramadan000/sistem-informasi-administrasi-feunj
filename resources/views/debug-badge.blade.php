<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Badge NEW</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-2xl font-bold mb-4">Debug Badge NEW Feature</h1>
            <p class="mb-2"><strong>Current User:</strong> {{ $currentUser->name }} (ID: {{ $currentUser->id }})</p>
            <p class="mb-4"><strong>Role:</strong> {{ $currentUser->role }}</p>
            
            <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                <p class="text-sm"><strong>Info:</strong> Badge "NEW" akan muncul jika:</p>
                <ul class="list-disc ml-6 text-sm mt-2">
                    <li>Surat dibuat dalam 24 jam terakhir</li>
                    <li>Surat belum pernah dilihat detailnya oleh user yang login</li>
                    <li>Surat bukan dibuat oleh user yang login</li>
                </ul>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold">Testing Badge pada {{ $letters->count() }} Surat Terbaru</h2>
            </div>
            
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Surat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badge Test</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($letters as $letter)
                    @php
                        $authId = auth()->id();
                        $isNew = $letter->isNewFor($authId);
                        $isRecent = $letter->created_at >= now()->subHours(24);
                        $isViewed = $letter->isViewedBy($authId);
                        $notCreator = $letter->created_by != $authId;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">{{ $letter->letter_number }}</span>
                                @if($isNew)
                                    <span class="inline-flex items-center rounded-full bg-red-500 px-2.5 py-0.5 text-xs font-bold text-white shadow-sm animate-pulse">
                                        NEW
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            {{ $letter->created_at->format('Y-m-d H:i:s') }}
                            <br>
                            <span class="text-xs text-gray-500">{{ $letter->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            User #{{ $letter->created_by }}
                            <br>
                            <span class="text-xs text-gray-500">{{ $letter->creator->name }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    @if($isRecent)
                                        <span class="text-green-600">✓</span>
                                    @else
                                        <span class="text-red-600">✗</span>
                                    @endif
                                    <span class="text-xs">Is Recent (< 24h)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($notCreator)
                                        <span class="text-green-600">✓</span>
                                    @else
                                        <span class="text-red-600">✗</span>
                                    @endif
                                    <span class="text-xs">Not Created By You</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(!$isViewed)
                                        <span class="text-green-600">✓</span>
                                    @else
                                        <span class="text-red-600">✗</span>
                                    @endif
                                    <span class="text-xs">Not Viewed</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($isNew)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Badge WILL Show
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    ✗ No Badge
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded p-4">
            <p class="text-sm font-semibold mb-2">Cara Test:</p>
            <ol class="list-decimal ml-6 text-sm space-y-1">
                <li>Lihat tabel di atas - surat dengan status "✓ Badge WILL Show" seharusnya menampilkan badge merah "NEW"</li>
                <li>Jika badge muncul di atas tapi tidak muncul di /letters, ada masalah di view letters/index.blade.php</li>
                <li>Klik tombol di bawah untuk membuka halaman /letters dan bandingkan</li>
            </ol>
            <div class="mt-4">
                <a href="{{ route('letters.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Buka Halaman Letters →
                </a>
            </div>
        </div>
    </div>
</body>
</html>
