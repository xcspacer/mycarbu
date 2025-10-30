<?php

namespace App\Http\Controllers;

use App\Models\FuelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FuelRequestController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $fuelRequests = FuelRequest::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $fuelRequests = FuelRequest::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('fuel-requests.index', compact('fuelRequests'));
    }

    public function show(FuelRequest $fuelRequest): View
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $fuelRequest->user_id !== $user->id) {
            abort(403, 'Não tem permissão para ver este pedido.');
        }

        $fuelRequest->load('user');

        return view('fuel-requests.show', compact('fuelRequest'));
    }

    public function create(): View
    {
        $energyApiUrl = config('services.energy_api.url');
        $energyApiToken = config('services.energy_api.token');

        $user = Auth::user();

        $addresses = [];
        $prices = null;
        $customerDiscounts = null;
        
        try {
            // Buscar moradas do cliente
            $fleetCustomer = Http::withHeaders([
                'X-API-Token' => $energyApiToken,
            ])->get("{$energyApiUrl}/api/fleet-customers/{$user->email}")->json();

            if (isset($fleetCustomer['success']) && $fleetCustomer['success'] && isset($fleetCustomer['data'])) {
                $addresses = $fleetCustomer['data']['household'] ?? [];
                
                // Buscar descontos do cliente
                if (isset($fleetCustomer['data'])) {
                    $customerDiscounts = [
                        'goa' => $fleetCustomer['data']['frota_goa_15d'] ?? 0,
                        'goah' => $fleetCustomer['data']['frota_goah_15d'] ?? 0,
                        'c95s' => $fleetCustomer['data']['frota_c95s_15d'] ?? 0,
                        'c95a' => $fleetCustomer['data']['frota_c95a_15d'] ?? 0,
                        'c98' => $fleetCustomer['data']['frota_c98_15d'] ?? 0,
                        'agricola' => $fleetCustomer['data']['frota_agricola_15d'] ?? 0,
                    ];
                }
            }
            
            // Buscar preços ativos
            $pricesResponse = Http::withHeaders([
                'X-API-Token' => $energyApiToken,
            ])->get("{$energyApiUrl}/api/prices/active")->json();
            
            if (isset($pricesResponse['success']) && $pricesResponse['success'] && isset($pricesResponse['data'])) {
                $prices = [
                    'goa' => $pricesResponse['data']['goa'] ?? 0,
                    'goah' => $pricesResponse['data']['goah'] ?? 0,
                    'c95s' => $pricesResponse['data']['c95s'] ?? 0,
                    'c95a' => $pricesResponse['data']['c95a'] ?? 0,
                    'c98' => $pricesResponse['data']['c98'] ?? 0,
                    'agricola' => $pricesResponse['data']['agricola'] ?? 0,
                ];
            }
        } catch (\Exception $e) {
            // Silently handle error
        }

        return view('fuel-requests.create', compact('addresses', 'prices', 'customerDiscounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'requests' => 'required|array|min:1',
            'requests.*.address_title' => 'required|string|max:255',
            'requests.*.fuel_type' => 'required|string|in:goa,goah,c95s,c95a,c98,agricola',
            'requests.*.quantity' => 'required|integer|min:1',
            'total_quantity' => 'required|integer|min:1|max:32000',
            'delivery_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000'
        ], [
            'requests.required' => 'Deve adicionar pelo menos um pedido',
            'requests.min' => 'Deve adicionar pelo menos um pedido',
            'total_quantity.max' => 'O volume total não pode exceder 32.000 litros por pedido',
            'delivery_date.required' => 'A data de entrega é obrigatória',
            'delivery_date.after_or_equal' => 'A data de entrega deve ser hoje ou no futuro'
        ]);

        $organizedData = [];
        $totalValue = 0;

        foreach ($request->requests as $requestItem) {
            $addressTitle = $requestItem['address_title'];
            $unitPrice = floatval($requestItem['unit_price'] ?? 0);
            $quantity = intval($requestItem['quantity'] ?? 0);
            $totalPrice = floatval($requestItem['total_price'] ?? 0);
            
            $totalValue += $totalPrice;
            
            if (!isset($organizedData[$addressTitle])) {
                $organizedData[$addressTitle] = [
                    'address' => $addressTitle,
                    'fuels' => []
                ];
            }

            $organizedData[$addressTitle]['fuels'][] = [
                'type' => $requestItem['fuel_type'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice
            ];
        }

        $organizedData = array_values($organizedData);

        $fuelRequest = FuelRequest::create([
            'user_id' => Auth::id(),
            'total_quantity' => $request->total_quantity,
            'delivery_date' => $request->delivery_date,
            'notes' => $request->notes,
            'request_data' => [
                'organized_data' => $organizedData,
                'raw_requests' => $request->requests,
                'total_value' => $totalValue
            ]
        ]);

        return redirect()->route('fuel-requests.show', $fuelRequest)
            ->with('success', 'Pedido de combustível criado e enviado com sucesso!');
    }

    public function destroy(FuelRequest $fuelRequest): RedirectResponse
    {
        $user = Auth::user();

        // Users can only delete their own requests, except admins who can delete any
        if (!$user->isAdmin() && $fuelRequest->user_id !== $user->id) {
            abort(403, 'Não tem permissão para apagar este pedido.');
        }

        $fuelRequest->delete();

        return redirect()->route('fuel-requests.index')
            ->with('success', 'Pedido de combustível apagado com sucesso!');
    }
}

