<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route untuk menampilkan halaman dengan daftar dokumen
Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');

// Route untuk menangani proses download file ZIP
Route::post('documents/download', [DocumentController::class, 'downloadSelected'])->name('documents.download');

Route::redirect('/', '/documents');
