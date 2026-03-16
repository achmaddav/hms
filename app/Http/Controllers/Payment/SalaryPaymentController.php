<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\SalaryPayment;
use App\Models\User;
use Illuminate\Http\Request;

class SalaryPaymentController extends Controller
{
    /**
     * Display a listing of salary payments
     */
    public function index(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        $query = SalaryPayment::with(['employee', 'processedBy'])
            ->forHotel($hotelId);
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by month
        if ($request->has('month') && $request->month != '') {
            $query->where('month_year', $request->month);
        }
        
        // Filter by employee
        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('employee_id', $request->employee_id);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('payment_number', 'like', '%' . $request->search . '%');
        }
        
        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get employees for filter
        $employees = User::where('hotel_id', $hotelId)
            ->whereIn('role', ['admin', 'manager', 'receptionist'])
            ->orderBy('name')
            ->get();
        
        return view('payment.salary-index', compact('payments', 'employees'));
    }
    
    /**
     * Show the form for creating a new salary payment
     */
    public function create()
    {
        $hotelId = auth()->user()->hotel_id;
        
        $employees = User::where('hotel_id', $hotelId)
            ->whereIn('role', ['admin', 'manager', 'receptionist'])
            ->orderBy('name')
            ->get();
        
        return view('payment.salary-create', compact('employees'));
    }
    
    /**
     * Store a newly created salary payment
     */
    public function store(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'month_year' => 'required|date_format:Y-m',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'overtime' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'insurance' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'working_days' => 'nullable|integer|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'bank_account' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        // Validate employee belongs to hotel
        $employee = User::findOrFail($validated['employee_id']);
        if ($employee->hotel_id !== $hotelId) {
            abort(403, 'Karyawan ini tidak tersedia.');
        }
        
        // Check if already exists for this month
        $exists = SalaryPayment::forHotel($hotelId)
            ->where('employee_id', $validated['employee_id'])
            ->where('month_year', $validated['month_year'])
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gaji untuk karyawan ini di bulan tersebut sudah ada.');
        }
        
        // Create payment
        $payment = new SalaryPayment($validated);
        $payment->hotel_id = $hotelId;
        $payment->payment_number = SalaryPayment::generatePaymentNumber();
        $payment->processed_by = auth()->id();
        
        // Set defaults for nullable fields
        $payment->allowances = $payment->allowances ?? 0;
        $payment->overtime = $payment->overtime ?? 0;
        $payment->bonus = $payment->bonus ?? 0;
        $payment->tax = $payment->tax ?? 0;
        $payment->insurance = $payment->insurance ?? 0;
        $payment->other_deductions = $payment->other_deductions ?? 0;
        $payment->working_days = $payment->working_days ?? 0;
        $payment->overtime_hours = $payment->overtime_hours ?? 0;
        
        // Calculate totals
        $payment->calculateTotals();
        
        $payment->save();
        
        return redirect()->route('manager.salary-payments.show', $payment)
            ->with('success', 'Gaji berhasil dicatat! Nomor: ' . $payment->payment_number);
    }
    
    /**
     * Display the specified salary payment
     */
    public function show(SalaryPayment $salaryPayment)
    {
        $this->authorizeAccess($salaryPayment);
        
        $salaryPayment->load(['employee', 'processedBy', 'approvedBy']);
        
        return view('payment.salary-show', compact('salaryPayment'));
    }
    
    /**
     * Mark payment as paid (approve)
     */
    public function approve(Request $request, SalaryPayment $salaryPayment)
    {
        $this->authorizeAccess($salaryPayment);
        
        if ($salaryPayment->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Pembayaran ini sudah disetujui.');
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        $salaryPayment->markAsPaid(
            $validated['payment_method'],
            auth()->id(),
            $validated['reference_number'] ?? null
        );
        
        if ($request->notes) {
            $salaryPayment->notes = ($salaryPayment->notes ? $salaryPayment->notes . "\n" : '') . $validated['notes'];
            $salaryPayment->save();
        }
        
        return redirect()->route('manager.salary-payments.show', $salaryPayment)
            ->with('success', 'Pembayaran gaji berhasil disetujui dan ditandai sebagai lunas!');
    }
    
    /**
     * Cancel salary payment
     */
    public function cancel(SalaryPayment $salaryPayment)
    {
        $this->authorizeAccess($salaryPayment);
        
        if ($salaryPayment->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Tidak dapat membatalkan pembayaran yang sudah lunas.');
        }
        
        $salaryPayment->status = 'cancelled';
        $salaryPayment->save();
        
        return redirect()->route('manager.salary-payments.show', $salaryPayment)
            ->with('success', 'Pembayaran gaji dibatalkan.');
    }
    
    /**
     * Authorize access
     */
    private function authorizeAccess(SalaryPayment $salaryPayment)
    {
        if ($salaryPayment->hotel_id !== auth()->user()->hotel_id) {
            abort(403, 'Anda tidak memiliki akses ke pembayaran ini.');
        }
    }
}
