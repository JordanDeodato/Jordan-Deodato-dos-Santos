<?php

namespace Tests\Feature\Controllers;

use App\Models\ImportedDataCsv;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImportedDataCsvControllerTest extends TestCase
{
    use RefreshDatabase;

    private $sampleData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sampleData = [
            ['data' => '2023-01-01 00:00:00', 'temperatura' => 15.5],
            ['data' => '2023-01-01 01:00:00', 'temperatura' => 16.0],
            ['data' => '2023-01-01 02:00:00', 'temperatura' => 14.5],
            ['data' => '2023-01-02 00:00:00', 'temperatura' => -12.0],
            ['data' => '2023-01-02 01:00:00', 'temperatura' => -11.5],
            ['data' => '2023-01-03 00:00:00', 'temperatura' => 5.0],
            ['data' => '2023-01-03 01:00:00', 'temperatura' => 7.0],
        ];

        DB::table('imported_data_csvs')->insert($this->sampleData);
    }

    /** @test */
    public function it_returns_analysis_for_all_dates()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $response = $this->getJson('/api/csv/analyze');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'date',
                        'average',
                        'median',
                        'min',
                        'max',
                        'percentage_above_10',
                        'percentage_below_minus_10',
                        'percentage_between',
                    ]
                ],
                'message',
                'stats' => ['total_days', 'failed_days']
            ]);

        $this->assertCount(3, $response->json()['data']);
    }

    /** @test */
    public function it_filters_by_date_range()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/csv/analyze?start_date=2023-01-02&end_date=2023-01-03');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json()['data']);
    }

    /** @test */
    public function it_validates_date_parameters()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/csv/analyze?start_date=invalid-date');
        $response->assertStatus(422);

        $response = $this->getJson('/api/csv/analyze?start_date=2023-01-02&end_date=2023-01-01');
        $response->assertStatus(422);
    }

    /** @test */
    public function it_returns_404_when_no_data_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/csv/analyze?start_date=2024-01-01');
        $response->assertStatus(404);
    }

    /** @test */
    public function it_calculates_correct_statistics()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/csv/analyze?start_date=2023-01-01&end_date=2023-01-01');

        $data = $response->json()['data'][0];

        $this->assertEquals('2023-01-01', $data['date']);
        $this->assertEquals(15.33, round($data['average'], 2));
        $this->assertEquals(15.5, $data['median']);
        $this->assertEquals(14.5, $data['min']);
        $this->assertEquals(16.0, $data['max']);
        $this->assertEquals(100, $data['percentage_above_10']);
        $this->assertEquals(0, $data['percentage_below_minus_10']);
        $this->assertEquals(0, $data['percentage_between']);
    }

    /** @test */
    public function it_handles_empty_days_gracefully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        ImportedDataCsv::whereDate('data', '2023-01-01')->delete();

        $response = $this->getJson('/api/csv/analyze');
        $response->assertStatus(200);

        $this->assertCount(2, $response->json()['data']);
    }

    /** @test */
    public function it_handles_errors_gracefully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        DB::shouldReceive('table')->andThrow(new \Exception('Database error'));

        $response = $this->getJson('/api/csv/analyze');
        $response->assertStatus(500)
            ->assertJsonStructure([
                'message',
                'error',
                'details'
            ]);
    }
}