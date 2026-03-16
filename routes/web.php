<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Payment\SalaryPaymentController;
use App\Http\Controllers\Payment\UtilityPaymentController;
use App\Http\Controllers\SuperAdmin\HotelController;
use App\Http\Controllers\Receptionist\CheckInController;
use App\Http\Controllers\Receptionist\ReceptionistRoomController;
use App\Http\Controllers\Report\FinancialReportController;
use App\Http\Controllers\Report\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Multi-Tenant Hotel Management System
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Guest Routes (untuk yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| SUPER ADMIN ROUTES
| - Manage semua hotel
| - Akses ke semua data
| - Switch antar hotel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    // Dashboard Super Admin
    Route::get('/dashboard', function () {
        return view('super-admin.dashboard');
    })->name('dashboard');
    
    // Hotel Management
    Route::resource('hotels', HotelController::class);
    Route::post('/hotels/switch', [HotelController::class, 'switchHotel'])->name('hotels.switch');
    Route::post('/hotels/clear-selection', [HotelController::class, 'clearHotelSelection'])->name('hotels.clear-selection');
    
    // User Management (manage users dari semua hotel)
    // Route::resource('users', UserController::class);
    
    // Reports & Analytics (data dari semua hotel)
    // Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

/*
|--------------------------------------------------------------------------
| HOTEL ADMIN ROUTES
| - Manage hotel mereka sendiri
| - Data ter-scope otomatis ke hotel mereka
| - SUPER ADMIN juga bisa akses route ini saat switch hotel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin,admin', 'hotel.scope'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin Hotel
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Super admin: ambil hotel dari session
        if ($user->isSuperAdmin()) {
            $hotelId = session('selected_hotel_id');
            $hotel = \App\Models\Hotel::findOrFail($hotelId);
        } else {
            // Admin biasa: ambil dari user
            $hotel = $user->hotel;
        }
        
        return view('admin.dashboard', compact('hotel'));
    })->name('dashboard');
    
    // User Management 
    Route::resource('users', UserController::class);

    // Master Kamar (otomatis ter-filter by hotel_id)
    Route::resource('rooms', RoomController::class);
    Route::patch('/rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.update-status');
    
    // Master Layanan (otomatis ter-filter by hotel_id)
    Route::resource('services', ServiceController::class);
    Route::patch('/services/{service}/toggle', [ServiceController::class, 'toggleStatus'])->name('services.toggle');
    
    // Bookings
    // Route::resource('bookings', BookingController::class);
    
    // Staff Management (staff untuk hotel ini saja)
    // Route::resource('staff', StaffController::class);
});

/*
|--------------------------------------------------------------------------
| MANAGER ROUTES
| - View reports only
| - Revenue & occupancy analytics
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:manager', 'hotel.scope'])->prefix('manager')->name('manager.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily-revenue', [ReportController::class, 'dailyRevenue'])->name('daily-revenue');
        Route::get('/monthly-revenue', [ReportController::class, 'monthlyRevenue'])->name('monthly-revenue');
        Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
        Route::get('/revenue-by-type', [ReportController::class, 'revenueByRoomType'])->name('revenue-by-type');
        Route::get('/performance', [ReportController::class, 'performance'])->name('performance');

        // Financial Reports
        Route::get('/financial', [FinancialReportController::class, 'index'])->name('financial.index');
        Route::get('/room-revenue', [FinancialReportController::class, 'roomRevenue'])->name('room-revenue');
        Route::get('/room-expense', [FinancialReportController::class, 'roomExpense'])->name('room-expense');
        Route::get('/financial-summary', [FinancialReportController::class, 'financialSummary'])->name('financial-summary');
        Route::get('/salary-report', [FinancialReportController::class, 'salaryReport'])->name('salary-report');
        
        // Excel Exports
        Route::get('/export/room-revenue', [FinancialReportController::class, 'exportRoomRevenue'])->name('export.room-revenue');
        Route::get('/export/room-expense', [FinancialReportController::class, 'exportRoomExpense'])->name('export.room-expense');
        Route::get('/export/financial-summary', [FinancialReportController::class, 'exportFinancialSummary'])->name('export.financial-summary');
        Route::get('/export/salary-report', [FinancialReportController::class, 'exportSalaryReport'])->name('export.salary-report');
    });

    Route::resource('salary-payments', SalaryPaymentController::class)->except(['edit', 'update']);
    Route::post('salary-payments/{salaryPayment}/approve', [SalaryPaymentController::class, 'approve'])->name('salary-payments.approve');
    Route::post('salary-payments/{salaryPayment}/cancel', [SalaryPaymentController::class, 'cancel'])->name('salary-payments.cancel');
});

/*
|--------------------------------------------------------------------------
| RECEPTIONIST ROUTES
| - Operational tasks
| - Data ter-scope ke hotel mereka
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:receptionist', 'hotel.scope'])->prefix('receptionist')->name('receptionist.')->group(function () {
    // Dashboard Receptionist
    Route::get('/dashboard', function () {
        $hotel = auth()->user()->hotel;
        return view('receptionist.dashboard', compact('hotel'));
    })->name('dashboard');
    
    // Check-in Management 
    Route::resource('checkins', CheckInController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/checkins/{checkin}/payment', [CheckInController::class, 'addPayment'])->name('checkins.add-payment');
    Route::post('/checkins/{checkin}/charge', [CheckInController::class, 'addCharge'])->name('checkins.add-charge');
    Route::post('/checkins/{checkin}/checkout', [CheckInController::class, 'checkout'])->name('checkins.checkout');

    // Room Management 
    Route::get('/rooms', [ReceptionistRoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [ReceptionistRoomController::class, 'show'])->name('rooms.show');
    Route::patch('/rooms/{room}/status', [ReceptionistRoomController::class, 'updateStatus'])->name('rooms.update-status');
    Route::patch('/rooms/{room}/quick-status', [ReceptionistRoomController::class, 'quickStatusUpdate'])->name('rooms.quick-status');

    Route::resource('utility-payments', UtilityPaymentController::class)->except(['edit', 'update']);
    Route::post('utility-payments/{utilityPayment}/mark-paid', [UtilityPaymentController::class, 'markAsPaid'])->name('utility-payments.mark-paid');
});

/*
|--------------------------------------------------------------------------
| CUSTOMER ROUTES
| - View & Book rooms
| - Manage their bookings
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');
    
    // Browse Hotels & Rooms
    // Route::get('/hotels', [PublicHotelController::class, 'index'])->name('hotels.index');
    // Route::get('/hotels/{hotel}', [PublicHotelController::class, 'show'])->name('hotels.show');
    
    // Bookings
    // Route::resource('my-bookings', CustomerBookingController::class);
});
