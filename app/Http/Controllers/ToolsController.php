<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller untuk Modul Tools Administrasi (Client-Side Utilitas Dokumen, QR/Barcode & Helper Kampus)
 */
class ToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman utama Hub Tools Administrasi
     */
    public function index()
    {
        return view('tools.index');
    }

    /**
     * QR Code & Barcode 1D Studio (Custom Logo + Template Desain Siap Pakai)
     */
    public function qrBarcode(Request $request)
    {
        $initialUrl = (string) $request->query('url', '');
        $requestedTab = (string) $request->query('tab', 'qr');
        $initialTab = in_array($requestedTab, ['qr', 'barcode'], true) ? $requestedTab : 'qr';

        return view('tools.qr-barcode', compact('initialUrl', 'initialTab'));
    }

    /**
     * Konverter PDF <-> Gambar & Kompresi Ukuran Berkas
     */
    public function pdfImage(Request $request)
    {
        $requestedTab = (string) $request->query('tab', 'pdf-to-img');
        $initialTab = in_array($requestedTab, ['pdf-to-img', 'img-to-pdf', 'compress'], true)
            ? $requestedTab
            : 'pdf-to-img';

        return view('tools.pdf-image', compact('initialTab'));
    }

    /**
     * Kelola Halaman PDF (Gabung, Pisah/Putar/Hapus, & Watermark PDF/Gambar)
     */
    public function pdfManager(Request $request)
    {
        $requestedTab = (string) $request->query('tab', 'merge');
        $initialTab = in_array($requestedTab, ['merge', 'split', 'watermark'], true)
            ? $requestedTab
            : 'merge';

        return view('tools.pdf-manager', compact('initialTab'));
    }

    /**
     * Helper Administrasi Kampus (Terbilang, Nama/Gelar, Romawi/Durasi, WA & GDrive Link)
     */
    public function adminHelper()
    {
        return view('tools.admin-helper');
    }
}
