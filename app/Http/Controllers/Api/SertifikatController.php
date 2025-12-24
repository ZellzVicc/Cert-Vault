<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use Illuminate\Http\Request;

class SertifikatController extends Controller
{
    // 1. AMBIL SEMUA DATA (READ)
    public function index()
    {
        $data = Sertifikat::latest()->get();
        return response()->json($data);
    }

    // 2. SIMPAN DATA BARU (CREATE)
    public function store(Request $request)
    {
        // Validasi simpel biar No Sertifikat gak tabrakan
        $request->validate([
            'noSertif' => 'unique:sertifikats,no_sertif'
        ]);

        $sertifikat = Sertifikat::create([
            'nama'        => $request->nama,
            'ktp'         => $request->ktp,
            'no_sertif'   => $request->noSertif,
            'no_reg'      => $request->noReg,
            'kualifikasi' => $request->kualifikasi,
            'wilayah'     => $request->wilayah,
            'tgl_terbit'  => $request->tahunTerbit,
            'tgl_expired' => $request->tglExpired,
        ]);

        return response()->json([
            'message' => 'Sukses disimpan',
            'data'    => $sertifikat
        ]);
    }

    // 3. UPDATE DATA (UPDATE)
    public function update(Request $request, $id)
    {
        $sertifikat = Sertifikat::find($id);

        if ($sertifikat) {
            $sertifikat->update([
                'nama'        => $request->nama,
                'ktp'         => $request->ktp,
                'no_sertif'   => $request->noSertif,
                'no_reg'      => $request->noReg,
                'kualifikasi' => $request->kualifikasi,
                'wilayah'     => $request->wilayah,
                'tgl_terbit'  => $request->tahunTerbit,
                'tgl_expired' => $request->tglExpired,
            ]);
            return response()->json(['message' => 'Sukses diupdate']);
        }

        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    // 4. HAPUS DATA (DELETE)
    public function destroy($id)
    {
        Sertifikat::destroy($id);
        return response()->json(['message' => 'Sukses dihapus']);
    }

    // 5. IMPORT BANYAK DATA (LOGIKA BARU)
    public function import(Request $request)
    {
        $data = $request->all();

        foreach ($data as $row) {
            // updateOrCreate akan mencari data berdasarkan 'no_sertif' (array pertama).
            // Kalau ketemu -> Data diupdate.
            // Kalau TIDAK ketemu -> Data baru dibuat (walaupun NIK-nya sama dengan orang lain).

            Sertifikat::updateOrCreate(
                ['no_sertif' => $row['no_sertif']], // KUNCI UNIK
                [
                    'nama'        => $row['nama'],
                    'ktp'         => $row['ktp'],      // NIK boleh sama
                    'no_reg'      => $row['no_reg'],
                    'kualifikasi' => $row['kualifikasi'],
                    'wilayah'     => $row['wilayah'],
                    'tgl_terbit'  => $row['tgl_terbit'],
                    'tgl_expired' => $row['tgl_expired'],
                ]
            );
        }

        return response()->json(['message' => 'Import Berhasil']);
    }
}
