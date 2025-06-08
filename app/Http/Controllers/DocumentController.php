<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\View; 
use ZipArchive;
use Barryvdh\DomPDF\Facade\Pdf; 

class DocumentController extends Controller
{
    /**
     * Menampilkan daftar dokumen yang bisa diunduh.
     */
    public function index()
    {
        
        // $documents = Document::all();
        $dummy_data = [
            'INV-001' => ['tipe' => 'Invoice', 'nomor' => 'INV-001', 'pelanggan' => 'PT. Angin Ribut'],
            'INV-002' => ['tipe' => 'Invoice', 'nomor' => 'INV-002', 'pelanggan' => 'CV. Maju Mundur'],
            'LAP-01' => ['tipe' => 'Laporan', 'judul' => 'Laporan Penjualan Januari'],
            'LAP-02' => ['tipe' => 'Laporan', 'judul' => 'Laporan Stok Gudang'],
            'SURAT-01' => ['tipe' => 'Surat', 'perihal' => 'Surat Penawaran Kerjasama'],
        ];

        return view('documents.index', ['documents' => $dummy_data]);
    }

    /**
     * Membuat PDF dari item yang dipilih, menggabungkannya ke ZIP, dan mengirimkannya.
     */
    public function downloadSelected(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'string', // Pastikan setiap item adalah string
        ]);

        $selected_ids = $request->input('item_ids');
        
        // Data dummy, idealnya diambil dari database berdasarkan $selected_ids
        $database_data = [
            'INV-001' => ['tipe' => 'Invoice', 'nomor' => 'INV-001', 'pelanggan' => 'PT. Angin Ribut', 'total' => 'Rp 1.500.000'],
            'INV-002' => ['tipe' => 'Invoice', 'nomor' => 'INV-002', 'pelanggan' => 'CV. Maju Mundur', 'total' => 'Rp 3.250.000'],
            'LAP-01' => ['tipe' => 'Laporan', 'judul' => 'Laporan Penjualan Januari', 'pembuat' => 'Budi'],
            'LAP-02' => ['tipe' => 'Laporan', 'judul' => 'Laporan Stok Gudang', 'pembuat' => 'Citra'],
            'SURAT-01' => ['tipe' => 'Surat', 'perihal' => 'Surat Penawaran Kerjasama', 'tujuan' => 'PT. Sejahtera Abadi'],
        ];

        // 2. Buat direktori sementara di storage Laravel
        $temp_dir = storage_path('app/temp_pdfs_' . uniqid());
        if (!File::makeDirectory($temp_dir, 0777, true, true)) {
            return back()->with('error', 'Gagal membuat direktori sementara.');
        }

        $pdf_files = [];

        // 3. Loop dan generate setiap PDF
        foreach ($selected_ids as $id) {
            if (isset($database_data[$id])) {
                $data = $database_data[$id];
                
                // Membuat view terpisah untuk setiap tipe PDF adalah praktik yang baik
                // Contoh: view('pdfs.invoice', $data)
                // Untuk kesederhanaan, kita buat HTML langsung di sini
                $html_content = View::make('pdfs.template', ['data' => $data])->render();

                $pdf_filename = $data['tipe'] . '-' . preg_replace('/[^A-Za-z0-9\-]/', '', $id) . '.pdf';
                $file_path = $temp_dir . '/' . $pdf_filename;

                // Gunakan facade PDF untuk menyimpan file
                Pdf::loadHTML($html_content)->setPaper('a4', 'portrait')->save($file_path);

                if (File::exists($file_path)) {
                    $pdf_files[] = $file_path;
                }
            }
        }

        // 4. Proses pembuatan ZIP jika ada file PDF yang berhasil dibuat
        if (!empty($pdf_files)) {
            $zip = new ZipArchive();
            $zip_filename = 'dokumen_terpilih_' . date('Y-m-d_H-i-s') . '.zip';
            $zip_path = $temp_dir . '/' . $zip_filename;

            if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {
                foreach ($pdf_files as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();
                
                // 5. Kirim file ZIP untuk diunduh lalu hapus direktori sementara
                return response()->download($zip_path)->deleteFileAfterSend(true);
            }
        }

        // Jika tidak ada file yang dibuat atau proses gagal, kembali dengan pesan error
        File::deleteDirectory($temp_dir); // Pastikan direktori sementara tetap dihapus
        return back()->with('error', 'Gagal memproses unduhan. Pastikan file yang dipilih valid.');
    }
}