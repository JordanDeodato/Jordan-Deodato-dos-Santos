<?php

namespace App\Http\Controllers;

use App\Models\ImportedDataCsv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportedDataCsvController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        try {

            $query = DB::table('imported_data_csvs')
                ->select(DB::raw('DATE(data) as date'))
                ->groupBy(DB::raw('DATE(data)'))
                ->orderBy('date');

            if ($request->has('start_date')) {
                $query->whereDate('data', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('data', '<=', $request->end_date);
            }

            $dates = $query->pluck('date');

            if ($dates->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum dado encontrado para o período especificado'
                ], 404);
            }

            $analysis = [];

            foreach ($dates as $date) {
                try {
                    $dayData = ImportedDataCsv::whereDate('data', $date)
                        ->orderBy('temperatura')
                        ->get();

                    if ($dayData->isEmpty()) {
                        Log::warning("Dados vazios para a data: {$date}");
                        continue;
                    }

                    $temperatures = $dayData->pluck('temperatura')->toArray();

                    $analysis[] = [
                        'date' => $date,
                        'average' => $this->safeAverage($dayData),
                        'median' => $this->calculateMedian($temperatures),
                        'min' => $this->safeMin($dayData),
                        'max' => $this->safeMax($dayData),
                        'percentage_above_10' => $this->calculatePercentage($dayData, '>', 10),
                        'percentage_below_minus_10' => $this->calculatePercentage($dayData, '<', -10),
                        'percentage_between' => $this->calculatePercentage($dayData, 'between', -10, 10),
                    ];

                } catch (Exception $e) {
                    Log::error("Erro ao processar data {$date}: " . $e->getMessage());
                    continue;
                }
            }

            if (empty($analysis)) {
                throw new Exception("Nenhum dado pôde ser analisado devido a erros internos");
            }

            return response()->json([
                'data' => $analysis,
                'message' => 'Análise gerada com sucesso',
                'stats' => [
                    'total_days' => count($analysis),
                    'failed_days' => count($dates) - count($analysis),
                ]
            ]);

        } catch (Exception $e) {
            Log::error("Erro na análise de dados CSV: " . $e->getMessage());

            return response()->json([
                'message' => 'Erro ao processar a análise',
                'error' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getTrace() : null,
            ], 500);
        }
    }

    /**
     * Métodos auxiliares com tratamento de erros individuais
     */
    private function calculateMedian(array $values): float
    {
        try {
            sort($values);
            $count = count($values);

            if ($count === 0) {
                throw new Exception("Array vazio para cálculo de mediana");
            }

            $middle = floor(($count - 1) / 2);

            if ($count % 2) {
                return $values[$middle];
            } else {
                return ($values[$middle] + $values[$middle + 1]) / 2;
            }
        } catch (Exception $e) {
            Log::error("Erro no cálculo da mediana: " . $e->getMessage());
            return 0;
        }
    }

    private function safeAverage($collection)
    {
        try {
            return $collection->avg('temperatura') ?? 0;
        } catch (Exception $e) {
            Log::error("Erro no cálculo da média: " . $e->getMessage());
            return 0;
        }
    }

    private function safeMin($collection)
    {
        try {
            return $collection->min('temperatura') ?? 0;
        } catch (Exception $e) {
            Log::error("Erro no cálculo do mínimo: " . $e->getMessage());
            return 0;
        }
    }

    private function safeMax($collection)
    {
        try {
            return $collection->max('temperatura') ?? 0;
        } catch (Exception $e) {
            Log::error("Erro no cálculo do máximo: " . $e->getMessage());
            return 0;
        }
    }

    private function calculatePercentage($collection, $operator, $value1, $value2 = null)
    {
        try {
            $count = $collection->count();
            if ($count === 0)
                return 0;

            switch ($operator) {
                case '>':
                    $filtered = $collection->where('temperatura', '>', $value1)->count();
                    break;
                case '<':
                    $filtered = $collection->where('temperatura', '<', $value1)->count();
                    break;
                case 'between':
                    $filtered = $collection->whereBetween('temperatura', [$value1, $value2])->count();
                    break;
                default:
                    throw new Exception("Operador inválido: {$operator}");
            }

            return ($filtered / $count) * 100;
        } catch (Exception $e) {
            Log::error("Erro no cálculo de porcentagem: " . $e->getMessage());
            return 0;
        }
    }
}