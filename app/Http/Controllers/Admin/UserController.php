<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $hotelId = $this->getCurrentHotelId($request);
        
        $query = User::query();
        $query->where('id', '!=', $user->id); // Exclude self
        
        // Super admin bisa lihat semua user atau filter by hotel
        if ($user->isSuperAdmin()) {
            if ($hotelId) {
                $query->where('hotel_id', $hotelId);
            }
            // Jika tidak ada hotelId, show semua user
        } else {
            // Admin hotel hanya bisa lihat user hotel mereka
            $query->where('hotel_id', $hotelId);
        }
        
        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->with('hotel')->orderBy('name')->paginate(15);
        
        // Get hotels untuk filter (super admin only)
        $hotels = $user->isSuperAdmin() ? Hotel::all() : collect();
        
        return view('admin.users.index', compact('users', 'hotels'));
    }
    
    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get available hotels
        if ($user->isSuperAdmin()) {
            $hotels = Hotel::active()->get();
        } else {
            $hotels = collect([$user->hotel]);
        }
        
        // Available roles based on user permission
        $roles = $this->getAvailableRoles();
        
        return view('admin.users.form', compact('hotels', 'roles'));
    }
    
    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $hotelId = $this->getCurrentHotelId($request);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:' . implode(',', array_keys($this->getAvailableRoles())),
        ];
        
        // Super admin bisa pilih hotel, admin tidak bisa
        if ($user->isSuperAdmin()) {
            $rules['hotel_id'] = 'required|exists:hotels,id';
        }
        
        $validated = $request->validate($rules);
        
        // Set hotel_id
        if ($user->isSuperAdmin()) {
            $validated['hotel_id'] = $request->hotel_id;
        } else {
            // Admin hotel assign ke hotel mereka
            $validated['hotel_id'] = $hotelId;
        }
        
        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }
    
    /**
     * Display the specified user
     */
    public function show(Request $request, User $user)
    {
        $this->authorizeUserAccess($request, $user);
        
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Show the form for editing the specified user
     */
    public function edit(Request $request, User $user)
    {
        $this->authorizeUserAccess($request, $user);
        
        $currentUser = auth()->user();
        
        // Get available hotels
        if ($currentUser->isSuperAdmin()) {
            $hotels = Hotel::active()->get();
        } else {
            $hotels = collect([$currentUser->hotel]);
        }
        
        // Available roles
        $roles = $this->getAvailableRoles();
        
        return view('admin.users.form', compact('user', 'hotels', 'roles'));
    }
    
    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeUserAccess($request, $user);
        
        $currentUser = auth()->user();
        $hotelId = $this->getCurrentHotelId($request);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => 'required|in:' . implode(',', array_keys($this->getAvailableRoles())),
        ];
        
        // Super admin bisa ubah hotel
        if ($currentUser->isSuperAdmin()) {
            $rules['hotel_id'] = 'required|exists:hotels,id';
        }
        
        $validated = $request->validate($rules);
        
        // Set hotel_id
        if ($currentUser->isSuperAdmin()) {
            $validated['hotel_id'] = $request->hotel_id;
        }
        
        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $user->update($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate!');
    }
    
    /**
     * Remove the specified user
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorizeUserAccess($request, $user);
        
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }
        
        // Prevent deleting super admin (optional)
        if ($user->isSuperAdmin()) {
            return redirect()->back()
                ->with('error', 'Super admin tidak bisa dihapus!');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
    
    /**
     * Get current hotel ID
     */
    private function getCurrentHotelId(Request $request)
    {
        return $request->input('hotel_id') ?? 
               session('current_hotel_id') ?? 
               auth()->user()->hotel_id;
    }
    
    /**
     * Authorize user access
     */
    private function authorizeUserAccess(Request $request, User $user)
    {
        $currentUser = auth()->user();
        
        // Super admin bisa akses semua
        if ($currentUser->isSuperAdmin()) {
            return;
        }
        
        // Admin hotel hanya bisa akses user dari hotel mereka
        if ($user->hotel_id !== $currentUser->hotel_id) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }
    }
    
    /**
     * Get available roles based on current user permission
     */
    private function getAvailableRoles()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // Super admin bisa create semua role kecuali super_admin
            return [
                'admin' => 'Hotel Admin',
                'receptionist' => 'Receptionist',
                'customer' => 'Customer',
            ];
        } else {
            // Admin hotel hanya bisa create receptionist dan customer
            return [
                'receptionist' => 'Receptionist',
                'customer' => 'Customer',
            ];
        }
    }
}
