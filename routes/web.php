<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\SuperAdmin\HotelController;
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
        return view('super-admin.super-admin-dashboard');
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
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin', 'hotel.scope'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin Hotel
    Route::get('/dashboard', function () {
        $hotel = auth()->user()->hotel;
        return view('admin.dashboard', compact('hotel'));
    })->name('dashboard');
    
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
| RECEPTIONIST ROUTES
| - Operational tasks
| - Data ter-scope ke hotel mereka
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:receptionist', 'hotel.scope'])->prefix('receptionist')->name('receptionist.')->group(function () {
    Route::get('/dashboard', function () {
        $hotel = auth()->user()->hotel;
        return view('receptionist.dashboard', compact('hotel'));
    })->name('dashboard');
    
    // Check-in / Check-out
    // Route::get('/check-in', [CheckInController::class, 'index'])->name('check-in.index');
    // Route::post('/check-in', [CheckInController::class, 'store'])->name('check-in.store');
    
    // View Bookings (read-only atau limited)
    // Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
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
