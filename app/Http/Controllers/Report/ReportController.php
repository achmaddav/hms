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
            ->selectRaw('rooms.type, SUM(check_ins.total_amount) as revenue, COUNT(*) as bookings, SUM(check_ins.total_nights) as total_nights')
            ->groupBy('rooms.type')
            ->orderBy('revenue', 'desc')
            ->get();
        
        $totalRevenue = $revenueByType->sum('revenue');
        
        return view('manager.reports.revenue-by-type', compact(
            'revenueByType',
            'totalRevenue',
            'month'
        ));
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
