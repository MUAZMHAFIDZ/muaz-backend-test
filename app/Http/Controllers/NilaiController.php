<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    public function nilaiRT()
    {
        $dataHasil = DB::table('nilai')
            ->select(
                'id_siswa',
                'nama',
                'nisn',
                DB::raw("SUM(CASE WHEN nama_pelajaran = 'Realistic' THEN skor ELSE 0 END) as realistic"),
                DB::raw("SUM(CASE WHEN nama_pelajaran = 'Investigative' THEN skor ELSE 0 END) as investigative"),
                DB::raw("SUM(CASE WHEN nama_pelajaran = 'Artistic' THEN skor ELSE 0 END) as artistic"),
                DB::raw("SUM(CASE WHEN nama_pelajaran = 'Social' THEN skor ELSE 0 END) as social"),
                DB::raw("SUM(CASE WHEN nama_pelajaran = 'Enterprising' THEN skor ELSE 0 END) as enterprising"),
                DB::raw("SUM(CASE WHEN nama_pelajaran = 'Conventional' THEN skor ELSE 0 END) as conventional")
            )
            ->where('materi_uji_id', 7)
            ->groupBy('id_siswa', 'nama', 'nisn')
            ->get();

        $dataSiswa = $dataHasil->map(function ($item) {
            return [
                'nama' => $item->nama,
                'nilaiRt' => [
                    'realistic' => (int) $item->realistic,
                    'investigative' => (int) $item->investigative,
                    'artistic' => (int) $item->artistic,
                    'social' => (int) $item->social,
                    'enterprising' => (int) $item->enterprising,
                    'conventional' => (int) $item->conventional,
                ],
                'nisn' => $item->nisn,
            ];
        });

        return response()->json($dataSiswa);
    }

    public function nilaiST()
    {
        $dataHasil = DB::table('nilai')
            ->select(
                'id_siswa',
                'nama',
                'nisn',
                DB::raw("SUM(CASE WHEN pelajaran_id = 44 THEN skor * 41.67 ELSE 0 END) as verbal"),
                DB::raw("SUM(CASE WHEN pelajaran_id = 45 THEN skor * 29.67 ELSE 0 END) as kuantitatif"),
                DB::raw("SUM(CASE WHEN pelajaran_id = 46 THEN skor * 100.00 ELSE 0 END) as penalaran"),
                DB::raw("SUM(CASE WHEN pelajaran_id = 47 THEN skor * 23.81 ELSE 0 END) as figural"),
                DB::raw("SUM(
                    CASE 
                        WHEN pelajaran_id = 44 THEN skor * 41.67
                        WHEN pelajaran_id = 45 THEN skor * 29.67
                        WHEN pelajaran_id = 46 THEN skor * 100.00
                        WHEN pelajaran_id = 47 THEN skor * 23.81
                        ELSE 0
                    END
                ) as total")
            )
            ->where('materi_uji_id', 4)
            ->groupBy('id_siswa', 'nama', 'nisn')
            ->orderByDesc('total')
            ->get();

        $dataSiswa = $dataHasil->map(function ($item) {
            return [
                'nama' => $item->nama,
                'nisn' => $item->nisn,
                'listNilai' => [
                    'verbal' => (float) $item->verbal,
                    'kuantitatif' => (float) $item->kuantitatif,
                    'penalaran' => (float) $item->penalaran,
                    'figural' => (float) $item->figural,
                ],
                'total' => (float) $item->total,
            ];
        });

        return response()->json($dataSiswa);
    }

}
