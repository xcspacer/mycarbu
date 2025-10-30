<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <x-back-button />
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Detalhes do Lote #{{ $batch->id }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Informações do Lote</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cliente</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $batch->user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estação</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $batch->station_text }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade Total</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $batch->total_quantity }} m³</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade Utilizada</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $batch->total_used }} m³</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade Disponível</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $batch->total_remaining }} m³</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Período de Validade</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $batch->start_date->format('d/m/Y') }} - {{ $batch->end_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                    <p class="mt-1">
                                        @if($batch->isActive())
                                        @if($batch->hasAvailableQuantity())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Ativo
                                        </span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            Esgotado
                                        </span>
                                        @endif
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                            Expirado
                                        </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold">Detalhes por Combustível</h3>
                                @if($currentPrice && $allPrices->count() > 1)
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Preços:</label>
                                    <select id="price-selector" class="text-sm border border-gray-300 dark:border-gray-600 rounded px-3 py-1 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                        @foreach($allPrices as $price)
                                        <option value="{{ $price->id }}" 
                                            data-prices='{{ json_encode($price->getPricesForStation($batch->station)) }}'
                                            {{ $price->id === $currentPrice->id ? 'selected' : '' }}>
                                            {{ $price->date_start->format('d/m/Y') }} - {{ $price->date_end->format('d/m/Y') }}
                                            @if($price->is_active)
                                                (Ativo)
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="space-y-3">
                                @foreach($batch->getFuelTypesWithQuantities() as $fuel)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">{{ $fuel['label'] }}</h4>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Total:</span>
                                            <p class="font-medium">{{ $fuel['quantity'] }} m³</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Usado:</span>
                                            <p class="font-medium">{{ $fuel['used'] }} m³</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Disponível:</span>
                                            <p class="font-medium">{{ $fuel['remaining'] }} m³</p>
                                        </div>
                                    </div>
                                    @php
                                        $discountNormal = $batch->getDiscountPerLiter($fuel['type']);
                                        $discountPlus = $batch->getDiscountPerLiter($fuel['type'] . 'aditivo');

                                        // Obter preços base do Price model se disponível
                                        $stationPrices = $currentPrice ? $currentPrice->getPricesForStation($batch->station) : null;
                                        $basePriceNormal = $stationPrices[$fuel['type']] ?? null;
                                        $basePricePlus = $stationPrices[$fuel['type'] . 'aditivo'] ?? null;
                                    @endphp
                                    @if($currentPrice && ($discountNormal > 0 || $discountPlus > 0 || $basePriceNormal || $basePricePlus))
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <h5 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Preços por Litro:</h5>
                                        <div id="price-display-{{ $fuel['type'] }}" class="grid grid-cols-1 gap-3 text-xs">
                                            @if($basePriceNormal && $basePriceNormal > 0)
                                            <div class="bg-white dark:bg-gray-800 p-2 rounded border">
                                                <div class="font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ $fuel['label'] }}</div>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Preço Base:</span>
                                                        <p class="font-medium base-price-normal">{{ number_format($basePriceNormal, 5, ',', '.') }}€</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Desconto:</span>
                                                        <p class="font-medium text-green-600 dark:text-green-400">-{{ number_format($discountNormal, 5, ',', '.') }}€</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Preço Final:</span>
                                                        <p class="font-bold text-blue-600 dark:text-blue-400 final-price-normal">{{ number_format($basePriceNormal - $discountNormal, 5, ',', '.') }}€</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @if($basePricePlus && $basePricePlus > 0)
                                            <div class="bg-white dark:bg-gray-800 p-2 rounded border">
                                                <div class="font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ $fuel['label'] }}+</div>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Preço Base:</span>
                                                        <p class="font-medium base-price-plus">{{ number_format($basePricePlus, 5, ',', '.') }}€</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Desconto:</span>
                                                        <p class="font-medium text-green-600 dark:text-green-400">-{{ number_format($discountPlus, 5, ',', '.') }}€</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Preço Final:</span>
                                                        <p class="font-bold text-blue-600 dark:text-blue-400 final-price-plus">{{ number_format($basePricePlus - $discountPlus, 5, ',', '.') }}€</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @elseif($discountNormal > 0 || $discountPlus > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <h5 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Preços de Desconto por Litro:</h5>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            @if($discountNormal > 0)
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">{{ $fuel['label'] }}:</span>
                                                <p class="font-medium text-green-600 dark:text-green-400">{{ number_format($discountNormal, 5, ',', '.') }}€</p>
                                            </div>
                                            @endif
                                            @if($discountPlus > 0)
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">{{ $fuel['label'] }}+:</span>
                                                <p class="font-medium text-green-600 dark:text-green-400">{{ number_format($discountPlus, 5, ',', '.') }}€</p>
                                            </div>
                                            @endif
                                        </div>
                                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">⚠️ Preço base não disponível - apenas desconto exibido</p>
                                    </div>
                                    @endif
                                    <div class="mt-3">
                                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                            <span>{{ $fuel['used'] }} m³</span>
                                            <span>{{ $fuel['quantity'] }} m³</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-600">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $fuel['quantity'] > 0 ? ($fuel['used'] / $fuel['quantity']) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Utilização -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Histórico de Utilização</h3>

                    @if($batch->usages->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedido</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Combustível</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantidade</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($batch->usages as $usage)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $usage->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        #{{ $usage->fuelOrderItem->fuelOrder->id ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ strtoupper($usage->fuel_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $usage->quantity_used_m3 }} m³
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $usage->fuelOrderItem->customer->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('fuel-orders.show', $usage->fuelOrderItem->fuelOrder->id ?? '#') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            Ver Pedido
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        Este lote ainda não foi utilizado.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Histórico de Logs -->
            @if($canEdit)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Histórico de Alterações</h3>

                    @if($batch->logs->count() > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($batch->logs->sortByDesc('created_at') as $log)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $log->user->name }} - {{ ucfirst($log->action) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </p>
                                </div>
                            </div>
                            @if($log->action === 'updated' && isset($log->changes['fields_changed']))
                            <div class="mt-2 text-xs space-y-1">
                                @foreach($log->changes['fields_changed'] as $field => $change)
                                <p class="text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">{{ $change['label'] }}:</span>
                                    <span class="line-through text-red-600 dark:text-red-400">{{ $change['old'] ?? '-' }}</span>
                                    →
                                    <span class="text-green-600 dark:text-green-400">{{ $change['new'] ?? '-' }}</span>
                                </p>
                                @endforeach
                            </div>
                            @elseif($log->action === 'fuel_used' && isset($log->changes['fuel_type']))
                            <div class="mt-2 text-xs space-y-1">
                                <p class="text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Combustível utilizado:</span>
                                    <span class="text-blue-600 dark:text-blue-400">{{ strtoupper($log->changes['fuel_type']) }}</span>
                                </p>
                                <p class="text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Quantidade:</span>
                                    <span class="text-blue-600 dark:text-blue-400">{{ number_format($log->changes['quantity_used'], 3, ',', '.') }} m³</span>
                                </p>
                                @if(isset($log->changes['fuel_order_id']))
                                <p class="text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Pedido:</span>
                                    <span class="text-blue-600 dark:text-blue-400">#{{ $log->changes['fuel_order_id'] }}</span>
                                </p>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        Nenhum log encontrado.
                    </div>
                    @endif
                </div>
            </div>
            @endif
            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('batches.index') }}" class="mr-3 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Voltar
                </a>
                @if($canEdit)
                <a href="{{ route('batches.edit', $batch) }}" class="ml-4 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Editar
                </a>
                <x-danger-button
                    class="ml-4"
                    x-data="{}"
                    x-on:click="$dispatch('open-modal', 'confirm-batch-deletion')">
                    Eliminar Lote
                </x-danger-button>

                <x-modal name="confirm-batch-deletion" :show="false" focusable>
                    <form method="POST" action="{{ route('batches.destroy', $batch) }}" class="p-6">
                        @csrf
                        @method('DELETE')

                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Confirmar Eliminação
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Tens a certeza que pretendes eliminar este lote? Esta ação é irreversível.
                        </p>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                Cancelar
                            </x-secondary-button>

                            <x-danger-button class="ml-3">
                                Eliminar Lote
                            </x-danger-button>
                        </div>
                    </form>
                </x-modal>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceSelector = document.getElementById('price-selector');
            if (!priceSelector) return;

            priceSelector.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const prices = JSON.parse(selectedOption.dataset.prices);
                
                // Update all fuel type sections
                document.querySelectorAll('[id^="price-display-"]').forEach(function(display) {
                    const fuelType = display.id.replace('price-display-', '');
                    const fuelTypeAditivo = fuelType + 'aditivo';
                    
                    // Update normal fuel prices
                    const basePriceNormalEl = display.querySelector('.base-price-normal');
                    const finalPriceNormalEl = display.querySelector('.final-price-normal');
                    
                    if (basePriceNormalEl && prices[fuelType]) {
                        const basePrice = parseFloat(prices[fuelType]);
                        const discountEl = basePriceNormalEl.parentElement.nextElementSibling.querySelector('.text-green-600, .text-green-400');
                        const discount = discountEl ? parseFloat(discountEl.textContent.replace('-', '').replace('€', '').replace(',', '.')) : 0;
                        const finalPrice = basePrice - discount;
                        
                        basePriceNormalEl.textContent = basePrice.toFixed(5).replace('.', ',') + '€';
                        finalPriceNormalEl.textContent = finalPrice.toFixed(5).replace('.', ',') + '€';
                    }
                    
                    // Update plus fuel prices
                    const basePricePlusEl = display.querySelector('.base-price-plus');
                    const finalPricePlusEl = display.querySelector('.final-price-plus');
                    
                    if (basePricePlusEl && prices[fuelTypeAditivo]) {
                        const basePrice = parseFloat(prices[fuelTypeAditivo]);
                        const discountEl = basePricePlusEl.parentElement.nextElementSibling.querySelector('.text-green-600, .text-green-400');
                        const discount = discountEl ? parseFloat(discountEl.textContent.replace('-', '').replace('€', '').replace(',', '.')) : 0;
                        const finalPrice = basePrice - discount;
                        
                        basePricePlusEl.textContent = basePrice.toFixed(5).replace('.', ',') + '€';
                        finalPricePlusEl.textContent = finalPrice.toFixed(5).replace('.', ',') + '€';
                    }
                });
            });
        });
    </script>
</x-app-layout>