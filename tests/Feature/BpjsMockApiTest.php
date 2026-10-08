<?php

namespace Tests\Feature;

use Database\Seeders\HospitalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BpjsMockApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(HospitalSeeder::class);
    }
    /**
     * Test check_bpjs with input number.
     */
    public function test_check_bpjs_with_number(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/check-bpjs', [
            'number' => '1234567890123456',
        ]);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'nik_atau_kartu' => '1234567890123456',
                    'nama' => 'Budi Santoso',
                    'status_kepesertaan' => 'AKTIF',
                    'kelas_rawat' => 'Kelas 1',
                    'faskes_tingkat_1' => 'Puskesmas Kecamatan Gambir',
                    'eligibility' => 'Berhak mendapatkan pelayanan rawat jalan & inap',
                ],
            ]);
    }

    /**
     * Test check_bpjs without number (fallback to default mock).
     */
    public function test_check_bpjs_without_number(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/check-bpjs', []);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'nik_atau_kartu' => '1234567890123456',
                ],
            ]);
    }

    /**
     * Test get_referral_status with valid referral_no.
     */
    public function test_get_referral_status_success(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/referral-status', [
            'referral_no' => 'RUJ-2026-0001',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'nomor_rujukan' => 'RUJ-2026-0001',
                    'status' => 'AKTIF',
                    'faskes_asal' => 'Puskesmas Kecamatan Gambir',
                    'faskes_tujuan' => 'RSUP Nasional Cipto Mangunkusumo',
                    'poli' => 'Poli Penyakit Dalam',
                    'berlaku_sampai' => '2026-11-01',
                ],
            ]);
    }

    /**
     * Test get_referral_status without referral_no (validation error).
     */
    public function test_get_referral_status_missing_parameter(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/referral-status', []);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'referral_no is required',
            ]);
    }

    /**
     * Test hospital_finder / search_hospital.
     */
    public function test_hospitals(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/hospitals', [
            'location' => 'Jakarta',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    [
                        'nama_rs' => 'RSUPN Dr. Cipto Mangunkusumo (RSCM)',
                        'tipe' => 'RSUP / Kelas A',
                        'mitra_bpjs' => true,
                        'lokasi' => 'Jakarta Pusat',
                        'fasilitas' => [
                            'IGD 24 Jam',
                            'Poli Spesialis Lengkap',
                            'ICU',
                        ],
                        'jarak_estimasi' => '2.5 km',
                    ],
                    [
                        'nama_rs' => 'RSUD Tarakan',
                        'tipe' => 'RSUD / Kelas B',
                        'mitra_bpjs' => true,
                        'lokasi' => 'Jakarta Pusat',
                        'fasilitas' => [
                            'IGD 24 Jam',
                            'Poli Jantung',
                            'Poli Anak',
                        ],
                        'jarak_estimasi' => '4.1 km',
                    ],
                ],
            ]);
    }

    /**
     * Test GET /api/hospitals endpoint.
     */
    public function test_get_hospitals_endpoint(): void
    {
        $response = $this->getJson('/api/hospitals');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'nama_rs',
                        'tipe',
                        'mitra_bpjs',
                        'lokasi',
                        'fasilitas',
                        'jarak_estimasi',
                    ],
                ],
            ]);
    }

    /**
     * Test find_specialist with specialty.
     */
    public function test_find_specialist(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/specialists', [
            'specialty' => 'Penyakit Dalam',
            'location' => 'Jakarta',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    [
                        'nama_dokter' => 'dr. Andi Pratama, Sp.PD',
                        'spesialisasi' => 'Penyakit Dalam',
                        'rumah_sakit' => 'RSUD Tarakan',
                        'jadwal_praktek' => 'Senin - Kamis (08.00 - 12.00)',
                    ],
                    [
                        'nama_dokter' => 'dr. Siti Rahma, Sp.PD',
                        'spesialisasi' => 'Penyakit Dalam',
                        'rumah_sakit' => 'RSCM',
                        'jadwal_praktek' => 'Rabu - Sabtu (10.00 - 14.00)',
                    ],
                ],
            ]);
    }

    /**
     * Test search_web with query.
     */
    public function test_search_web_success(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/search-web', [
            'query' => 'aturan rujukan BPJS',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'query' => 'aturan rujukan BPJS',
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
            ]);
    }

    /**
     * Test search_web missing query.
     */
    public function test_search_web_missing_query(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/search-web', []);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'query is required',
            ]);
    }

    /**
     * Test endpoint not found returns JSON 404.
     */
    public function test_endpoint_not_found(): void
    {
        $response = $this->postJson('/api/v1/mock-bpjs/unknown-endpoint');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Endpoint not found',
            ]);
    }

    /**
     * Test check_bpjs via GET method.
     */
    public function test_check_bpjs_via_get(): void
    {
        $response = $this->getJson('/api/v1/mock-bpjs/check-bpjs?number=9998887776665554');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'nik_atau_kartu' => '9998887776665554',
                    'nama' => 'Budi Santoso',
                ],
            ]);
    }

    /**
     * Test invalid HTTP method (e.g. DELETE) returns JSON 405.
     */
    public function test_invalid_http_method(): void
    {
        $response = $this->deleteJson('/api/v1/mock-bpjs/check-bpjs');

        $response->assertStatus(405)
            ->assertJson([
                'status' => 'error',
                'message' => 'Method not allowed',
            ]);
    }

    /**
     * Test invalid JSON payload returns JSON 400.
     */
    public function test_invalid_json_payload(): void
    {
        $response = $this->call(
            'POST',
            '/api/v1/mock-bpjs/check-bpjs',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            '{invalid_json'
        );

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid JSON payload',
            ]);
    }
}
