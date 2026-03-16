<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Room;
use App\Models\UtilityPayment;
use App\Models\SalaryPayment;
use App\Exports\RoomRevenueExport;
use App\Exports\RoomExpenseExport;
use App\Exports\FinancialSummaryExport;
use App\Exports\SalaryReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    /**
     * Financial reports dashboard
     */
    public function index()
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Quick stats for current month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $monthRevenue = CheckIn::forHotel($hotelId)
            ->whereBetween('check_in_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');
        
        $monthUtilityExpense = UtilityPayment::forHotel($hotelId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');
        
        $monthSalaryExpense = SalaryPayment::forHotel($hotelId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('net_salary');
        
        $monthExpense = $monthUtilityExpense + $monthSalaryExpense;
        $monthProfit = $monthRevenue - $monthExpense;
        
        return view('reports.financial.financial-index', compact(
            'monthRevenue',
            'monthExpense',
            'monthProfit',
            'monthUtilityExpense',
            'monthSalaryExpense'
        ));
    }
    
    /**
     * Room revenue report
     */
    public function roomRevenue(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Get filter parameters
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $roomId = $request->input('room_id');
        $status = $request->input('status');
        
        // Query check-ins with payments and charges
        $query = CheckIn::with(['room', 'payments', 'additionalCharges'])
            ->forHotel($hotelId)
            ->whereBetween('check_in_date', [$startDate, $endDate]);
        
        if ($roomId) {
            $query->where('room_id', $roomId);
        }
        
        if ($status) {
            $query->where('payment_status', $status);
        }
        
        $checkIns = $query->orderBy('check_in_date', 'desc')->paginate(20);
        
        // Calculate totals
        $totalRevenue = $query->sum('total_amount');
        $totalPaid = $query->sum('paid_amount');
        $totalOutstanding = $totalRevenue - $totalPaid;
        
        // Get rooms for filter
        $rooms = Room::forHotel($hotelId)->orderBy('room_number')->get();
        
        return view('reports.financial.room-revenue', compact(
            'checkIns',
            'rooms',
            'totalRevenue',
            'totalPaid',
            'totalOutstanding',
            'startDate',
            'endDate',
            'roomId',
            'status'
        ));
    }
    
    /**
     * Room expense report
     */
    public function roomExpense(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Get filter parameters
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $roomId = $request->input('room_id');
        $utilityType = $request->input('utility_type');
        
        // Query utility payments
        $query = UtilityPayment::with(['room'])
            ->forHotel($hotelId)
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($roomId) {
            $query->where('room_id', $roomId);
        }
        
        if ($utilityType) {
            $query->where('utility_type', $utilityType);
        }
        
        $utilities = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate totals
        $totalExpense = $query->sum('total_amount');
        $totalPaid = $query->where('status', 'paid')->sum('total_amount');
        $totalPending = $query->where('status', 'pending')->sum('total_amount');
        
        // Get rooms for filter
        $rooms = Room::forHotel($hotelId)->orderBy('room_number')->get();
        
        return view('reports.financial.room-expense', compact(
            'utilities',
            'rooms',
            'totalExpense',
            'totalPaid',
            'totalPending',
            'startDate',
            'endDate',
            'roomId',
            'utilityType'
        ));
    }
    
    /**
     * Financial summary report
     */
    public function financialSummary(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Get filter parameters
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Revenue breakdown
        $roomRevenue = CheckIn::forHotel($hotelId)
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->sum('total_amount');
        
        $additionalServicesRevenue = CheckIn::forHotel($hotelId)
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->withSum('additionalCharges', 'amount')
            ->get()
            ->sum('additional_charges_sum_amount');
        
        // Expense breakdown
        $electricityExpense = UtilityPayment::forHotel($hotelId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('utility_type', 'electricity')
            ->sum('total_amount');
        
        $waterExpense = UtilityPayment::forHotel($hotelId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('utility_type', 'water')
            ->sum('total_amount');
        
        $otherUtilitiesExpense = UtilityPayment::forHotel($hotelId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('utility_type', ['electricity', 'water'])
            ->sum('total_amount');
        
        $salaryExpense = SalaryPayment::forHotel($hotelId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('net_salary');
        
        // Totals
        $totalRevenue = $roomRevenue + $additionalServicesRevenue;
        $totalExpense = $electricityExpense + $waterExpense + $otherUtilitiesExpense + $salaryExpense;
        $netProfit = $totalRevenue - $totalExpense;
        
        return view('reports.financial.financial-summary', compact(
            'roomRevenue',
            'additionalServicesRevenue',
            'electricityExpense',
            'waterExpense',
            'otherUtilitiesExpense',
            'salaryExpense',
            'totalRevenue',
            'totalExpense',
            'netProfit',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Salary report
     */
    public function salaryReport(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Get filter parameters
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $status = $request->input('status');
        
        // Query salary payments
        $query = SalaryPayment::with(['employee'])
            ->forHotel($hotelId)
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $salaries = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate totals
        $totalGrossSalary = $query->sum('gross_salary');
        $totalDeductions = $query->sum('total_deductions');
        $totalNetSalary = $query->sum('net_salary');
        
        return view('reports.financial.salary-report', compact(
            'salaries',
            'totalGrossSalary',
            'totalDeductions',
            'totalNetSalary',
            'startDate',
            'endDate',
            'status'
        ));
    }
    
    /**
     * Export room revenue to Excel
     */
    public function exportRoomRevenue(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $roomId = $request->input('room_id');
        $status = $request->input('status');
        
        $filename = 'room-revenue-' . $startDate . '-to-' . $endDate . '.xlsx';
        
        return Excel::download(
            new RoomRevenueExport($hotelId, $startDate, $endDate, $roomId, $status),
            $filename
        );
    }
    
    /**
     * Export room expense to Excel
     */
    public function exportRoomExpense(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $roomId = $request->input('room_id');
        $utilityType = $request->input('utility_type');
        
        $filename = 'room-expense-' . $startDate . '-to-' . $endDate . '.xlsx';
        
        return Excel::download(
            new RoomExpenseExport($hotelId, $startDate, $endDate, $roomId, $utilityType),
            $filename
        );
    }
    
    /**
     * Export financial summary to Excel
     */
    public function exportFinancialSummary(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $filename = 'financial-summary-' . $startDate . '-to-' . $endDate . '.xlsx';
        
        return Excel::download(
            new FinancialSummaryExport($hotelId, $startDate, $endDate),
            $filename
        );
    }
    
    /**
     * Export salary report to Excel
     */
    public function exportSalaryReport(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $status = $request->input('status');
        
        $filename = 'salary-report-' . $startDate . '-to-' . $endDate . '.xlsx';
        
        return Excel::download(
            new SalaryReportExport($hotelId, $startDate, $endDate, $status),
            $filename
        );
    }
}
