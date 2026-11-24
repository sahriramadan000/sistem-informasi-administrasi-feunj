<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Layanan pemrosesan TTD digital secara aman di sisi server (Zero-Exposure).
 * File TTD asli tidak pernah dikirim langsung ke browser saat diaktifkan.
 */
class SecureSignatureService
{
    /**
     * Muat gambar dari disk (PNG, JPEG, atau WEBP) dengan dukungan kanal alpha penuh.
     */
    public function loadGdImage(string $absolutePath)
    {
        if (!File::exists($absolutePath)) {
            throw new RuntimeException("Berkas TTD tidak ditemukan: {$absolutePath}");
        }

        $binary = File::get($absolutePath);
        $img = @imagecreatefromstring($binary);
        if (!$img) {
            throw new RuntimeException('Gagal membaca format berkas gambar TTD.');
        }

        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        return $img;
    }

    /**
     * Buat gambar pratinjau ber-watermark resolusi rendah agar operator bisa memosisikan
     * TTD di kanvas tanpa pernah memegang file PNG asli TTD pejabat.
     */
    public function renderWatermarkedPreview(string $absolutePath): string
    {
        $src = $this->loadGdImage($absolutePath);
        $srcW = imagesx($src);
        $srcH = imagesy($src);

        $maxW = 420;
        $targetW = min($srcW, $maxW);
        $targetH = max(1, (int) round($srcH * ($targetW / max(1, $srcW))));

        $preview = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($preview, false);
        imagesavealpha($preview, true);
        $transparent = imagecolorallocatealpha($preview, 0, 0, 0, 127);
        imagefill($preview, 0, 0, $transparent);

        imagealphablending($preview, true);
        imagecopyresampled($preview, $src, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);

        // Gambar garis diagonal & teks watermark "PREVIEW ONLY"
        $lineColor = imagecolorallocatealpha($preview, 220, 38, 38, 72);
        $textColor = imagecolorallocatealpha($preview, 185, 28, 28, 42);

        imagesetthickness($preview, 2);
        imageline($preview, 0, $targetH, $targetW, 0, $lineColor);
        imageline($preview, 0, 0, $targetW, $targetH, $lineColor);

        for ($y = 12; $y < $targetH; $y += 42) {
            for ($x = 12; $x < $targetW; $x += 130) {
                imagestring($preview, 3, $x, $y, 'PREVIEW ONLY', $textColor);
            }
        }

        ob_start();
        imagepng($preview);
        $out = ob_get_clean();

        imagedestroy($src);
        imagedestroy($preview);

        return $out ?: '';
    }

    /**
     * Tempelkan TTD asli dari private storage langsung ke gambar halaman hasil render browser
     * secara flatten sehingga file TTD terpisah tidak pernah keluar ke klien.
     *
     * @param array<int, array<string, mixed>> $instructions
     */
    public function stampSignaturesOnPage(string $pageBinary, array $instructions, string $outputFormat = 'png'): string
    {
        $pageImg = @imagecreatefromstring($pageBinary);
        if (!$pageImg) {
            throw new RuntimeException('Gagal memproses gambar halaman dokumen.');
        }

        imagepalettetotruecolor($pageImg);
        imagealphablending($pageImg, true);
        imagesavealpha($pageImg, true);

        $pageW = imagesx($pageImg);
        $pageH = imagesy($pageImg);

        foreach ($instructions as $item) {
            $path = $item['absolute_path'] ?? null;
            if (!$path || !File::exists($path)) {
                continue;
            }

            $sig = $this->loadGdImage($path);
            $srcW = imagesx($sig);
            $srcH = imagesy($sig);

            if (!empty($item['removeBg'])) {
                $this->stripWhiteBackground($sig, (int) ($item['bgThreshold'] ?? 225));
            }

            $widthPct = max(0.02, min(0.95, (float) ($item['widthPct'] ?? 0.18)));
            $targetW = max(16, (int) round($pageW * $widthPct));
            $targetH = max(16, (int) round($targetW * ($srcH / max(1, $srcW))));

            $scaled = imagecreatetruecolor($targetW, $targetH);
            imagealphablending($scaled, false);
            imagesavealpha($scaled, true);
            $clear = imagecolorallocatealpha($scaled, 0, 0, 0, 127);
            imagefill($scaled, 0, 0, $clear);
            imagecopyresampled($scaled, $sig, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
            imagedestroy($sig);

            $opacity = max(10, min(100, (int) ($item['opacity'] ?? 100)));
            if ($opacity < 100) {
                $this->applyOpacity($scaled, $opacity);
            }

            $rotation = (float) ($item['rotation'] ?? 0);
            if (abs($rotation) >= 0.5) {
                $rotClear = imagecolorallocatealpha($scaled, 0, 0, 0, 127);
                // HTML5 canvas positif searah jarum jam, GD imagerotate positif berlawanan arah jarum jam
                $rotated = imagerotate($scaled, -$rotation, $rotClear);
                imagedestroy($scaled);
                $scaled = $rotated;
                imagealphablending($scaled, false);
                imagesavealpha($scaled, true);
            }

            $finalW = imagesx($scaled);
            $finalH = imagesy($scaled);
            $centerX = (int) round($pageW * (float) ($item['xPct'] ?? 0.5));
            $centerY = (int) round($pageH * (float) ($item['yPct'] ?? 0.75));
            $destX = (int) round($centerX - ($finalW / 2));
            $destY = (int) round($centerY - ($finalH / 2));

            imagealphablending($pageImg, true);
            imagecopy($pageImg, $scaled, $destX, $destY, 0, 0, $finalW, $finalH);
            imagedestroy($scaled);
        }

        ob_start();
        if (strtolower($outputFormat) === 'jpg' || strtolower($outputFormat) === 'jpeg') {
            imagejpeg($pageImg, null, 95);
        } else {
            imagepng($pageImg);
        }
        $result = ob_get_clean();

        imagedestroy($pageImg);

        return $result ?: '';
    }

    protected function stripWhiteBackground($img, int $threshold): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $a = ($rgba >> 24) & 0x7F;
                if ($a >= 125) {
                    continue;
                }
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                if ($r >= $threshold && $g >= $threshold && $b >= $threshold) {
                    $transparent = imagecolorallocatealpha($img, $r, $g, $b, 127);
                    imagesetpixel($img, $x, $y, $transparent);
                }
            }
        }
    }

    protected function applyOpacity($img, int $opacityPercent): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $factor = $opacityPercent / 100.0;
        imagealphablending($img, false);
        imagesavealpha($img, true);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $a = ($rgba >> 24) & 0x7F;
                if ($a >= 127) {
                    continue;
                }
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                $alphaOpaqueFrac = (127 - $a) / 127.0;
                $newAlpha = (int) round(127 - ($alphaOpaqueFrac * $factor * 127));
                $newAlpha = max(0, min(127, $newAlpha));

                $col = imagecolorallocatealpha($img, $r, $g, $b, $newAlpha);
                imagesetpixel($img, $x, $y, $col);
            }
        }
    }
}
