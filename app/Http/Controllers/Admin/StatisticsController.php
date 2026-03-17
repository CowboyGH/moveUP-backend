<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Payment;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Получить общую статистику
     */
    public function overview(): JsonResponse
    {
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalSubscriptions = UserSubscription::count();
        $activeSubscriptions = UserSubscription::where('is_active', true)
            ->where('end_date', '>', now())
            ->count();

        $currentMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $previousMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $revenueGrowth = $previousMonthRevenue > 0
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 2)
            : 0;

        return ApiResponse::success('success', [
            'total_revenue' => round($totalRevenue / 1000, 2), // в тысячах рублей
            'total_subscriptions' => $totalSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'current_month_revenue' => round($currentMonthRevenue / 1000, 2),
            'revenue_growth' => $revenueGrowth
        ]);
    }

    /**
     * Получить статистику выручки по месяцам
     */
    public function revenue(Request $request): JsonResponse
    {
        $year = $request->get('year', Carbon::now()->year);

        $revenue = Payment::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => (int) $item->month,
                    'total' => round($item->total / 1000, 2) // Конвертируем в тысячи рублей
                ];
            });

        // Формируем данные для всех 12 месяцев
        $months = ['янв', 'фев', 'мар', 'апр', 'май', 'июнь', 'июль', 'авг', 'сент', 'окт', 'нояб', 'дек'];
        $data = [];

        foreach (range(1, 12) as $month) {
            $data[] = [
                'month' => $month,
                'month_name' => $months[$month - 1],
                'value' => $revenue->has($month) ? $revenue[$month]['total'] : 0
            ];
        }

        return ApiResponse::success('success', [
            'year' => (int) $year,
            'data' => $data
        ]);
    }

    /**
     * Получить статистику количества подписок по месяцам
     */
    public function subscriptionsCount(Request $request): JsonResponse
    {
        $year = $request->get('year', Carbon::now()->year);

        $subscriptions = UserSubscription::whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => (int) $item->month,
                    'total' => $item->total
                ];
            });

        // Формируем данные для всех 12 месяцев
        $months = ['янв', 'фев', 'мар', 'апр', 'май', 'июнь', 'июль', 'авг', 'сент', 'окт', 'нояб', 'дек'];
        $data = [];

        foreach (range(1, 12) as $month) {
            $data[] = [
                'month' => $month,
                'month_name' => $months[$month - 1],
                'value' => $subscriptions->has($month) ? $subscriptions[$month]['total'] : 0
            ];
        }

        return ApiResponse::success('success', [
            'year' => (int) $year,
            'data' => $data
        ]);
    }

    /**
     * Получить статистику подписок с фильтром по периоду
     */
    public function subscriptionsByPeriod(Request $request): JsonResponse
    {
        $period = (int) $request->get('period', 12);
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths($period - 1)->startOfMonth();

        // Получаем данные по месяцам за указанный период
        $subscriptions = UserSubscription::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(function ($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        // Формируем данные для графика
        $months = ['янв', 'фев', 'мар', 'апр', 'май', 'июнь', 'июль', 'авг', 'сент', 'окт', 'нояб', 'дек'];
        $data = [];

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $year = $currentDate->year;
            $month = $currentDate->month;
            $key = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

            $found = $subscriptions->get($key);

            $data[] = [
                'month' => $month,
                'month_name' => $months[$month - 1],
                'year' => $year,
                'label' => $months[$month - 1] . ' ' . $year,
                'value' => $found ? (int) $found->total : 0
            ];

            $currentDate->addMonth();
        }

        return ApiResponse::success('success', [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'data' => $data
        ]);
    }
}
