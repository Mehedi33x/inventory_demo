<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $type = $request->query('type', 'monthly');
        $totalProducts = Product::count();
        $totalSales    = Sale::sum('total_price');

        $groupFormat = match ($type) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u', // year-week
            'yearly' => '%Y',
            default => '%Y-%m' // monthly
        };
        $labelFormat = match ($type) {
            'daily' => 'M d',
            'weekly' => '\W\e\e\k W',
            'yearly' => 'Y',
            default => 'M Y'
        };
        $sales = Sale::selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as period, SUM(total_price) as total")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        $labels = [];
        $data   = [];
        $salesMap = $sales->pluck('total', 'period')->toArray();
        $range = match ($type) {
            'daily' => collect(range(0, 6))->map(fn($i) => now()->subDays($i)->format('Y-m-d'))->reverse()->values(),
            'weekly' => collect(range(0, 11))->map(fn($i) => now()->subWeeks($i)->format('Y-W'))->reverse()->values(),
            'yearly' => collect(range(0, 4))->map(fn($i) => now()->subYears($i)->format('Y'))->reverse()->values(),
            default => collect(range(0, 5))->map(fn($i) => now()->subMonths($i)->format('Y-m'))->reverse()->values()
        };
        foreach ($range as $key) {
            try {
                if ($type === 'weekly') {
                    $parts = explode('-W', $key);
                    if (count($parts) >= 2) {
                        [$year, $week] = $parts;
                        $date = Carbon::now()->setISODate($year, $week);
                    } else {
                        $date = Carbon::now();
                    }
                } else {
                    $date = Carbon::createFromFormat(match ($type) {
                        'daily'   => 'Y-m-d',
                        'monthly' => 'Y-m',
                        'yearly'  => 'Y',
                    }, $key);
                }

                $labels[] = $date->translatedFormat($labelFormat);
                $data[]   = (float) ($salesMap[$key] ?? 0);
            } catch (\Exception $e) {
                $labels[] = $key;
                $data[]   = (float) ($salesMap[$key] ?? 0);
            }
        }
        return response()->json([
            'total_sales'    => (float) $totalSales,
            'total_products' => (int) $totalProducts,
            'sales_chart'    => [
                'labels' => $labels,
                'data'   => $data,
            ],
        ]);
    }
}