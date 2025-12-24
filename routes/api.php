<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SertifikatController;

// Jalur bawaan Laravel (biarkan atau hapus tidak masalah)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- JALUR APLIKASI CERT-VAULT ---
Route::get('/certs', [SertifikatController::class, 'index']);      // Ambil Data
Route::post('/certs', [SertifikatController::class, 'store']);     // Simpan Data
Route::put('/certs/{id}', [SertifikatController::class, 'update']); // Edit Data
Route::delete('/certs/{id}', [SertifikatController::class, 'destroy']); // Hapus Data
Route::post('/certs/import', [SertifikatController::class, 'import']);
