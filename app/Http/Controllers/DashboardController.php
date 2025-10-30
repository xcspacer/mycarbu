<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(): View
    {
        $energyApiUrl = config('services.energy_api.url');
        $energyApiToken = config('services.energy_api.token');
        
        $prices = null;
        $user = auth()->user();
        
        try {
            $response = Http::withHeaders([
                'X-API-Token' => $energyApiToken,
            ])->get("{$energyApiUrl}/api/prices/active");
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['success']) && $data['success']) {
                    $prices = $data['data'];
                }
            }
        } catch (\Exception $e) {
            // Silently handle error
        }

        $fleetCustomer = null;
        $customerFound = false;
        
        try {
            $response = Http::withHeaders([
                'X-API-Token' => $energyApiToken,
                'Accept' => 'application/json',
            ])->get("{$energyApiUrl}/api/fleet-customers/{$user->email}");
            
            $data = $response->json();
            
            // Check if response has data (even if status is 403)
            // Some APIs return 403 but still send the data in body
            if (isset($data['error'])) {
                $fleetCustomer = null;
                $customerFound = false;
            } elseif (isset($data['success']) && $data['success'] && isset($data['data'])) {
                $fleetCustomer = $data['data'];
                $customerFound = true;
            } else {
                $customerFound = false;
            }
        } catch (\Exception $e) {
            // Silently handle error
            $customerFound = false;
        }

        return view('dashboard', compact('prices', 'fleetCustomer', 'customerFound'));
    }
}
