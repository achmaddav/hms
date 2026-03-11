<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Room;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Dashboard Reports
     */
    public function index()
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Today's stats
        $todayRevenue = $this->getTodayRevenue($hotelId);
        $todayCheckIns = $this->getTodayCheckIns($hotelId);
        $currentOccupancy = $this->getCurrentOccupancy($hotelId);
        $monthRevenue = $this->getMonthRevenue($hotelId);
        
        return view('manager.dashboard', compact(
            'todayRevenue',
            'todayCheckIns',
            'currentOccupancy',
            'monthRevenue'
        ));
    }
    
    /**
     * Daily Revenue Report
     */
    public function dailyRevenue(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Default: last 30 days
        $startDate = $request->input('start_date', now()->subDays(29)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get daily revenue
        $dailyData = CheckIn::forHotel($hotelId)
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->selectRaw('DATE(check_in_date) as date, SUM(total_amount) as revenue, COUNT(*) as bookings')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        
        // Calculate totals
        $totalRevenue = $dailyData->sum('revenue');
        $totalBookings = $dailyData->sum('bookings');
        $avgRevenue = $dailyData->count() > 0 ? $totalRevenue / $dailyData->count() : 0;
        
        return view('reports.daily-revenue', compact(
            'dailyData',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalBookings',
            'avgRevenue'
        ));
    }
    
    /**
     * Monthly Revenue Report
     */
    public function monthlyRevenue(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Default: last 12 months
        $year = $request->input('year', now()->year);
        
        // Get monthly revenue
        $monthlyData = CheckIn::forHotel($hotelId)
            ->whereYear('check_in_date', $year)
            ->selectRaw('MONTH(check_in_date) as month, SUM(total_amount) as revenue, COUNT(*) as bookings, SUM(total_nights) as total_nights')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Format data
        $formattedData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthData = $monthlyData->firstWhere('month', $m);
            $formattedData[] = [
                'month' => $m,
                'month_name' => Carbon::create()->month($m)->format('F'),
                'revenue' => $monthData ? $monthData->revenue : 0,
                'bookings' => $monthData ? $monthData->bookings : 0,
                'total_nights' => $monthData ? $monthData->total_nights : 0,
            ];
        }
        
        $totalRevenue = collect($formattedData)->sum('revenue');
        $totalBookings = collect($formattedData)->sum('bookings');
        $avgMonthlyRevenue = $totalRevenue / 12;
        
        // Get available years
        $availableYears = CheckIn::forHotel($hotelId)
            ->selectRaw('YEAR(check_in_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('reports.monthly-revenue', compact(
            'formattedData',
            'year',
            'totalRevenue',
            'totalBookings',
            'avgMonthlyRevenue',
            'availableYears'
        ));
    }
    
    /**
     * Occupancy Report
     */
    public function occupancy(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Default: current month
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        // Total rooms
        $totalRooms = Room::forHotel($hotelId)->count();
        
        // Get daily occupancy
        $dailyOccupancy = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $occupiedRooms = CheckIn::forHotel($hotelId)
                ->where('status', 'checked_in')
                ->whereDate('check_in_date', '<=', $currentDate)
                ->where(function($query) use ($currentDate) {
                    $query->whereNull('check_out_date')
                        ->orWhereDate('check_out_date', '>=', $currentDate);
                })
                ->count();
            
            $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
            
            $dailyOccupancy[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->format('d'),
                'occupied_rooms' => $occupiedRooms,
                'total_rooms' => $totalRooms,
                'occupancy_rate' => round($occupancyRate, 2),
            ];
            
            $currentDate->addDay();
        }
        
        // Calculate average occupancy for the month
        $avgOccupancyRate = collect($dailyOccupancy)->avg('occupancy_rate');
        $totalNights = collect($dailyOccupancy)->sum('occupied_rooms');
        
        // Get available months (last 12 months)
        $availableMonths = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $availableMonths[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y'),
            ];
        }
        
        return view('reports.occupancy', compact(
            'dailyOccupancy',
            'month',
            'totalRooms',
            'avgOccupancyRate',
            'totalNights',
            'availableMonths'
        ));
    }
    
    /**
     * Revenue by Room Type
     */
    public function revenueByRoomType(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        // Get revenue by room type
        $revenueByType = CheckIn::forHotel($hotelId)
            ->join('rooms', 'check_ins.room_id', '=', 'rooms.id')
            ->whereBetween('check_ins.check_in_date', [$startDate, $endDate])
            ->selectRaw('rooms.room_type, SUM(check_ins.total_amount) as revenue, COUNT(*) as bookings, SUM(check_ins.total_nights) as total_nights')
            ->groupBy('rooms.room_type')
            ->orderBy('revenue', 'desc')
            ->get();
        
        $totalRevenue = $revenueByType->sum('revenue');
        
        return view('manager.reports.revenue-by-type', compact(
            'revenueByType',
            'totalRevenue',
            'month'
        ));
    }

    public function performance(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        $period = $request->input('period', 'month'); // today, week, month, year
        
        // Determine date range based on period
        switch ($period) {
            case 'today':
                $startDate = today();
                $endDate = today();
                $previousStart = today()->subDay();
                $previousEnd = today()->subDay();
                break;
            case 'week':
                $startDate = now()->subDays(6)->startOfDay();
                $endDate = now()->endOfDay();
                $previousStart = now()->subDays(13)->startOfDay();
                $previousEnd = now()->subDays(7)->endOfDay();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfDay();
                $previousStart = now()->subYear()->startOfYear();
                $previousEnd = now()->subYear()->endOfDay();
                break;
            default: // month (30 days)
                $startDate = now()->subDays(29)->startOfDay();
                $endDate = now()->endOfDay();
                $previousStart = now()->subDays(59)->startOfDay();
                $previousEnd = now()->subDays(30)->endOfDay();
        }
        
        // Get current period data
        $currentData = CheckIn::forHotel($hotelId)
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->selectRaw('
                SUM(total_amount) as total_revenue,
                COUNT(*) as total_bookings,
                SUM(total_nights) as total_nights,
                AVG(total_nights) as avg_los
            ')
            ->first();
        
        // Get previous period data for comparison
        $previousData = CheckIn::forHotel($hotelId)
            ->whereBetween('check_in_date', [$previousStart, $previousEnd])
            ->selectRaw('
                SUM(total_amount) as total_revenue,
                COUNT(*) as total_bookings
            ')
            ->first();
        
        // Calculate metrics
        $totalRooms = Room::forHotel($hotelId)->count();
        $daysInPeriod = $startDate->diffInDays($endDate) + 1;
        
        $metrics = [
            'total_revenue' => $currentData->total_revenue ?? 0,
            'total_bookings' => $currentData->total_bookings ?? 0,
            'total_room_nights' => $currentData->total_nights ?? 0,
            'avg_los' => $currentData->avg_los ?? 0,
            'avg_daily_revenue' => $daysInPeriod > 0 ? ($currentData->total_revenue ?? 0) / $daysInPeriod : 0,
            'avg_booking_value' => $currentData->total_bookings > 0 ? ($currentData->total_revenue ?? 0) / $currentData->total_bookings : 0,
            'avg_occupancy' => $totalRooms > 0 && $daysInPeriod > 0 ? (($currentData->total_nights ?? 0) / ($totalRooms * $daysInPeriod)) * 100 : 0,
            'revpar' => $totalRooms > 0 && $daysInPeriod > 0 ? ($currentData->total_revenue ?? 0) / ($totalRooms * $daysInPeriod) : 0,
        ];
        
        // Calculate growth rates
        if ($previousData && $previousData->total_revenue > 0) {
            $metrics['revenue_growth'] = (($currentData->total_revenue - $previousData->total_revenue) / $previousData->total_revenue) * 100;
        }
        
        if ($previousData && $previousData->total_bookings > 0) {
            $previousOccupancy = ($previousData->total_bookings / ($totalRooms * $daysInPeriod)) * 100;
            $metrics['occupancy_growth'] = $metrics['avg_occupancy'] - $previousOccupancy;
        }
        
        // Revenue by Room Type - FIX: Explicit table name for hotel_id
        $revenueByTypeData = CheckIn::query()
            ->join('rooms', 'check_ins.room_id', '=', 'rooms.id')
            ->where('check_ins.hotel_id', $hotelId) // ← FIX: Specify check_ins.hotel_id
            ->whereBetween('check_ins.check_in_date', [$startDate, $endDate])
            ->selectRaw('rooms.room_type, SUM(check_ins.total_amount) as revenue, COUNT(*) as bookings')
            ->groupBy('rooms.room_type')
            ->orderBy('revenue', 'desc')
            ->get();
        
        $totalRevenue = $revenueByTypeData->sum('revenue');
        $revenueByType = $revenueByTypeData->map(function($item) use ($totalRevenue) {
            return [
                'type' => $item->room_type,
                'revenue' => $item->revenue,
                'bookings' => $item->bookings,
                'percentage' => $totalRevenue > 0 ? ($item->revenue / $totalRevenue) * 100 : 0,
            ];
        });
        
        // Top Performing Rooms - FIX: Explicit table name for hotel_id
        $topRooms = CheckIn::query()
            ->join('rooms', 'check_ins.room_id', '=', 'rooms.id')
            ->where('check_ins.hotel_id', $hotelId) // ← FIX: Specify check_ins.hotel_id
            ->whereBetween('check_ins.check_in_date', [$startDate, $endDate])
            ->selectRaw('
                rooms.id,
                rooms.room_number,
                rooms.room_type,
                SUM(check_ins.total_amount) as revenue,
                COUNT(*) as bookings,
                SUM(check_ins.total_nights) as nights
            ')
            ->groupBy('rooms.id', 'rooms.room_number', 'rooms.room_type')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) use ($daysInPeriod) {
                return [
                    'room_number' => $item->room_number,
                    'type' => $item->room_type,
                    'revenue' => $item->revenue,
                    'bookings' => $item->bookings,
                    'occupancy_rate' => $daysInPeriod > 0 ? ($item->nights / $daysInPeriod) * 100 : 0,
                ];
            });
        
        // Trend Data for Charts
        $trendData = $this->getTrendData($hotelId, $startDate, $endDate, $period);
        
        return view('reports.performance-summary', compact(
            'period',
            'metrics',
            'revenueByType',
            'topRooms',
            'trendData'
        ));
    }

    /**
     * Get trend data for charts
     */
    private function getTrendData($hotelId, $startDate, $endDate, $period)
    {
        $labels = [];
        $revenue = [];
        $occupancy = [];
        
        $totalRooms = Room::forHotel($hotelId)->count();
        
        if ($period == 'today') {
            // Hourly data for today (simplified to morning/afternoon/evening)
            $labels = ['Morning', 'Afternoon', 'Evening'];
            $revenue = [0, 0, 0];
            $occupancy = [0, 0, 0];
        } elseif ($period == 'week' || $period == 'month') {
            // Daily data
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $labels[] = $currentDate->format('d M');
                
                $dayData = CheckIn::forHotel($hotelId)
                    ->whereDate('check_in_date', $currentDate)
                    ->selectRaw('SUM(total_amount) as revenue, SUM(total_nights) as nights')
                    ->first();
                
                $revenue[] = $dayData->revenue ?? 0;
                $occupancy[] = $totalRooms > 0 ? (($dayData->nights ?? 0) / $totalRooms) * 100 : 0;
                
                $currentDate->addDay();
            }
        } else {
            // Monthly data for year
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = \Carbon\Carbon::create()->month($m)->format('M');
                
                $monthData = CheckIn::forHotel($hotelId)
                    ->whereYear('check_in_date', $startDate->year)
                    ->whereMonth('check_in_date', $m)
                    ->selectRaw('SUM(total_amount) as revenue, SUM(total_nights) as nights')
                    ->first();
                
                $daysInMonth = \Carbon\Carbon::create($startDate->year, $m)->daysInMonth;
                $revenue[] = $monthData->revenue ?? 0;
                $occupancy[] = $totalRooms > 0 && $daysInMonth > 0 ? (($monthData->nights ?? 0) / ($totalRooms * $daysInMonth)) * 100 : 0;
            }
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'occupancy' => $occupancy,
        ];
    }
    
    /**
     * Helper Methods
     */
    private function getTodayRevenue($hotelId)
    {
        return CheckIn::forHotel($hotelId)
            ->whereDate('check_in_date', today())
            ->sum('total_amount');
    }
    
    private function getTodayCheckIns($hotelId)
    {
        return CheckIn::forHotel($hotelId)
            ->whereDate('check_in_date', today())
            ->count();
    }
    
    private function getCurrentOccupancy($hotelId)
    {
        $totalRooms = Room::forHotel($hotelId)->count();
        $occupiedRooms = Room::forHotel($hotelId)->where('status', 'occupied')->count();
        
        return $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
    }
    
    private function getMonthRevenue($hotelId)
    {
        return CheckIn::forHotel($hotelId)
            ->whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->sum('total_amount');
    }
}
