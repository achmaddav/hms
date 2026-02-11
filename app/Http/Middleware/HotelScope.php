<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HotelScope
{
    /**
     * Handle an incoming request.
     * 
     * Middleware ini otomatis set hotel_id pada session
     * dan memastikan user hanya bisa akses data hotel mereka
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bisa akses semua hotel
        if ($user->isSuperAdmin()) {
            // Cek apakah ada hotel yang dipilih di session
            $selectedHotelId = session('selected_hotel_id');
            
            if ($selectedHotelId) {
                $request->merge(['hotel_id' => $selectedHotelId]);
            }
            // Super admin bisa switch hotel, tidak enforce hotel_id
        } else {
            // User biasa hanya bisa akses hotel mereka
            if (!$user->hotel_id) {
                abort(403, 'User tidak memiliki akses ke hotel manapun.');
            }

            // Set hotel_id ke request
            $request->merge(['hotel_id' => $user->hotel_id]);
            
            // Set ke session juga
            session(['current_hotel_id' => $user->hotel_id]);
        }

        return $next($request);
    }
}
