<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Painel de Controlo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:px-6 lg:px-8">
            @if($prices)
            <div class="mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                            Preços Ativos
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Período: {{ \Carbon\Carbon::parse($prices['date_start'])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($prices['date_end'])->format('d/m/Y') }}
                        </p>

                        @if($customerFound)
                            @php
                                $hasAnyDiscount = false;
                                if ($fleetCustomer) {
                                    $hasAnyDiscount = floatval($fleetCustomer['frota_goa_15d'] ?? 0) > 0 ||
                                                      floatval($fleetCustomer['frota_goah_15d'] ?? 0) > 0 ||
                                                      floatval($fleetCustomer['frota_c95s_15d'] ?? 0) > 0 ||
                                                      floatval($fleetCustomer['frota_c95a_15d'] ?? 0) > 0 ||
                                                      floatval($fleetCustomer['frota_c98_15d'] ?? 0) > 0 ||
                                                      floatval($fleetCustomer['frota_agricola_15d'] ?? 0) > 0;
                                }
                            @endphp
                            
                            @if($fleetCustomer && $hasAnyDiscount)
                                <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <p class="text-sm text-green-700 dark:text-green-300">
                                        <strong>Cliente:</strong> {{ $fleetCustomer['name'] }} - Descontos aplicados
                                    </p>
                                </div>
                            @else
                                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-900/20 rounded-lg">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <strong>Cliente:</strong> {{ $fleetCustomer['name'] ?? 'N/A' }} - Os preços exibidos são PVP (sem descontos configurados)
                                    </p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @php
                                    $fuels = [
                                        'GOA' => ['key' => 'goa', 'fleet_key' => 'frota_goa_15d'],
                                        'GOAH' => ['key' => 'goah', 'fleet_key' => 'frota_goah_15d'],
                                        'C95S' => ['key' => 'c95s', 'fleet_key' => 'frota_c95s_15d'],
                                        'C95A' => ['key' => 'c95a', 'fleet_key' => 'frota_c95a_15d'],
                                        'C98' => ['key' => 'c98', 'fleet_key' => 'frota_c98_15d'],
                                        'Agricola' => ['key' => 'agricola', 'fleet_key' => 'frota_agricola_15d'],
                                    ];
                                @endphp
                                
                                @foreach($fuels as $label => $fuel)
                                    @php
                                        $priceKey = $fuel['key'];
                                        $fleetKey = $fuel['fleet_key'];
                                        $fleetPrice = $fleetCustomer ? floatval($fleetCustomer[$fleetKey] ?? 0) : 0;
                                        $originalPrice = floatval($prices[$priceKey] ?? 0);
                                        $finalPrice = $originalPrice - $fleetPrice;
                                    @endphp
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ $label }}</p>
                                        @if($fleetPrice > 0)
                                            <p class="text-lg font-bold text-green-600 dark:text-green-400">€{{ number_format($finalPrice, 5, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">€{{ number_format($originalPrice, 5, ',', '.') }} (PVP)</p>
                                    @else
                                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">€{{ number_format($originalPrice, 5, ',', '.') }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg mb-4">
                                <p class="text-sm text-amber-700 dark:text-amber-300">
                                    <strong>Cliente não encontrado.</strong> Os preços exibidos são PVP.
                                </p>
                            </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">GOA</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">€{{ number_format(floatval($prices['goa']), 5, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">GOAH</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">€{{ number_format(floatval($prices['goah']), 5, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">C95S</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">€{{ number_format(floatval($prices['c95s']), 5, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">C95A</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">€{{ number_format(floatval($prices['c95a']), 5, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">C98</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">€{{ number_format(floatval($prices['c98']), 5, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Agricola</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">€{{ number_format(floatval($prices['agricola']), 5, ',', '.') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('fuel-requests.index') }}" class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">Pedidos de Combustível</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ver todos os pedidos</p>
                            </div>
                        </a>

                        <a href="{{ route('fuel-requests.create') }}" class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">Novo Pedido</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Criar novo pedido</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
