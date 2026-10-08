<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BpjsMockController extends Controller
{
    /**
     * Check BPJS membership status.
     * Supports both GET and POST.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkBpjs(Request $request): JsonResponse
    {
        $number = $request->filled('number') ? (string) $request->input('number') : '1234567890123456';

        return response()->json([
            'status' => 'success',
            'data' => [
                'nik_atau_kartu' => $number,
                'nama' => 'Budi Santoso',
                'status_kepesertaan' => 'AKTIF',
                'kelas_rawat' => 'Kelas 1',
                'faskes_tingkat_1' => 'Puskesmas Kecamatan Gambir',
                'eligibility' => 'Berhak mendapatkan pelayanan rawat jalan & inap',
            ],
        ], 200);
    }

    /**
     * Get BPJS referral status.
     * Supports both GET and POST.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function referralStatus(Request $request): JsonResponse
    {
        if ($request->isMethod('get')) {
            $referralNo = $request->filled('referral_no') ? (string) $request->input('referral_no') : 'RUJ-2026-0001';
        } else {
            $validator = Validator::make($request->all(), [
                'referral_no' => 'required|string',
            ], [
                'referral_no.required' => 'referral_no is required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $referralNo = (string) $request->input('referral_no');
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'nomor_rujukan' => $referralNo,
                'status' => 'AKTIF',
                'faskes_asal' => 'Puskesmas Kecamatan Gambir',
                'faskes_tujuan' => 'RSUP Nasional Cipto Mangunkusumo',
                'poli' => 'Poli Penyakit Dalam',
                'berlaku_sampai' => '2026-11-01',
            ],
        ], 200);
    }

    /**
     * Search hospitals / hospital finder.
     * Mengambil daftar rumah sakit dari database MySQL.
     * Supports both GET and POST.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function hospitals(Request $request): JsonResponse
    {
        $hospitals = Hospital::query()
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $hospitals,
        ], 200);
    }

    /**
     * Get single hospital detail by ID.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function showHospital(int|string $id): JsonResponse
    {
        $hospital = Hospital::query()->find($id);

        if (!$hospital) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hospital not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $hospital,
        ], 200);
    }

    /**
     * Find specialists.
     * Supports both GET and POST.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function specialists(Request $request): JsonResponse
    {
        $specialty = $request->filled('specialty') ? (string) $request->input('specialty') : 'Penyakit Dalam';

        return response()->json([
            'status' => 'success',
            'data' => [
                [
                    'nama_dokter' => 'dr. Andi Pratama, Sp.PD',
                    'spesialisasi' => $specialty,
                    'rumah_sakit' => 'RSUD Tarakan',
                    'jadwal_praktek' => 'Senin - Kamis (08.00 - 12.00)',
                ],
                [
                    'nama_dokter' => 'dr. Siti Rahma, Sp.PD',
                    'spesialisasi' => $specialty,
                    'rumah_sakit' => 'RSCM',
                    'jadwal_praktek' => 'Rabu - Sabtu (10.00 - 14.00)',
                ],
            ],
        ], 200);
    }

    /**
     * Mock search web.
     * Supports both GET and POST.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchWeb(Request $request): JsonResponse
    {
        if ($request->isMethod('get')) {
            $query = $request->filled('query') ? (string) $request->input('query') : 'aturan rujukan BPJS';
        } else {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string',
            ], [
                'query.required' => 'query is required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $query = (string) $request->input('query');
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'query' => $query,
                'results' => [
                    [
                        'title' => 'Aturan Rujukan Faskes BPJS Kesehatan',
                        'snippet' => 'Peserta BPJS wajib membawa surat rujukan dari Faskes Tingkat 1 yang masih berlaku untuk pelayanan rujukan.',
                    ],
                    [
                        'title' => 'Prosedur Berobat dalam Kondisi Gawat Darurat',
                        'snippet' => 'Dalam kondisi darurat medis, peserta BPJS dapat langsung mendatangi IGD rumah sakit terdekat tanpa surat rujukan.',
                    ],
                ],
            ],
        ], 200);
    }
}
