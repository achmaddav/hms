<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services for current hotel
     */
    public function index(Request $request)
    {
        $hotelId = $this->getCurrentHotelId($request);
        
        $query = Service::forHotel($hotelId);

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $services = $query->orderBy('name')->paginate(10);

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service
     */
    public function create()
    {
        return view('admin.services.form');
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $hotelId = $this->getCurrentHotelId($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:room_service,spa,laundry,restaurant,transportation,other',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['hotel_id'] = $hotelId;
        $validated['is_active'] = $request->has('is_active');

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service berhasil ditambahkan!');
    }

    /**
     * Display the specified service
     */
    public function show(Request $request, Service $service)
    {
        $this->authorizeHotelAccess($request, $service);
        
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit(Request $request, Service $service)
    {
        $this->authorizeHotelAccess($request, $service);
        
        return view('admin.services.form', compact('service'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, Service $service)
    {
        $this->authorizeHotelAccess($request, $service);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:room_service,spa,laundry,restaurant,transportation,other',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service berhasil diupdate!');
    }

    /**
     * Remove the specified service
     */
    public function destroy(Request $request, Service $service)
    {
        $this->authorizeHotelAccess($request, $service);
        
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service berhasil dihapus!');
    }

    /**
     * Toggle service active status
     */
    public function toggleStatus(Request $request, Service $service)
    {
        $this->authorizeHotelAccess($request, $service);
        
        $service->update([
            'is_active' => !$service->is_active
        ]);

        $status = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Service berhasil {$status}!");
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
     * Authorize hotel access
     */
    private function authorizeHotelAccess(Request $request, Service $service)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return;
        }

        if ($service->hotel_id !== $user->hotel_id) {
            abort(403, 'Anda tidak memiliki akses ke layanan ini.');
        }
    }
}
