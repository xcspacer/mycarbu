<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pedir Combustível
        </h2>
    </x-slot>

    <div class="py-12" x-data="fuelRequestForm()">
        <div class="sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

                    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data Prevista de Entrega
                                </label>
                                <input type="date" x-model="deliveryDate" :min="minDate"
                                    class="block w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300">
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">
                                Total do Pedido
                            </h3>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-900 dark:text-blue-100">
                                    <span x-text="totalQuantity.toLocaleString('pt-PT')"></span> LT
                                </div>
                                <div class="text-sm font-semibold text-blue-800 dark:text-blue-200">
                                    <span x-text="totalValue.toFixed(4)"></span> €
                                </div>
                            </div>
                        </div>
                        <div x-show="totalQuantity > 32000" class="mt-2 text-red-600 dark:text-red-400 text-sm font-medium">
                            ⚠️ Atenção: O volume total excede 32.000 litros por pedido
                        </div>
                    </div>

                    <div class="mb-6" x-show="requests.length > 0">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Pedidos Adicionados
                        </h3>
                        <div class="space-y-2">
                            <template x-for="(request, index) in requests" :key="index">
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex-1">
                                            <span class="font-medium" x-text="request.address_title"></span> - 
                                            <span x-text="request.fuel_type.toUpperCase()"></span>
                                        </div>
                                        <button type="button" @click="removeRequest(index)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Quantidade:</span>
                                            <span class="font-medium ml-1" x-text="request.quantity.toLocaleString('pt-PT') + ' LT'"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Preço Unit.:</span>
                                            <span class="font-medium ml-1" x-text="formatPrice(request.unit_price) + ' €/L'"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Total:</span>
                                            <span class="font-bold ml-1" x-text="formatPrice(request.total_price) + ' €'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Adicionar Pedido
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Morada
                                </label>
                                <select x-model="selectedAddress" @change="onAddressChange()"
                                    class="block w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300">
                                    <option value="">Selecione uma morada</option>
                                    <template x-for="address in availableAddresses" :key="address.title">
                                        <option :value="address.title" x-text="address.title"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Combustível
                                </label>
                                <select x-model="selectedFuel" x-bind:disabled="!selectedAddress"
                                    class="block w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300 disabled:opacity-50">
                                    <option value="">Selecione um combustível</option>
                                    <option value="goa">GOA - Gasóleo Rodoviário</option>
                                    <option value="goah">GOAH - Gasóleo Aditivado</option>
                                    <option value="c95s">C95S - Gasolina 95 Simples</option>
                                    <option value="c95a">C95A - Gasolina 95 Aditivada</option>
                                    <option value="c98">C98 - Gasolina 98</option>
                                    <option value="agricola">Agrícola</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Quantidade (LT)
                                </label>
                                <div class="flex space-x-2">
                                    <input type="number" x-model.number="quantity" x-bind:disabled="!selectedFuel" min="1"
                                        class="block w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300 disabled:opacity-50"
                                        placeholder="Quantidade">
                                    <button type="button" @click="addRequest()"
                                        x-bind:disabled="!canAddRequest"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-green-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('fuel-requests.store') }}" @submit="prepareSubmit($event)">
                        @csrf
                        
                        <div id="hidden-requests"></div>
                        <input type="hidden" name="total_quantity" x-bind:value="totalQuantity">
                        <input type="hidden" name="delivery_date" x-bind:value="deliveryDate">

                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('Observações (opcional)')" class="dark:text-gray-300" />
                            <textarea
                                id="notes"
                                name="notes"
                                rows="4"
                                class="block mt-1 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Informações adicionais sobre o pedido...">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('fuel-requests.index') }}" class="mr-3 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Cancelar
                            </a>
                            <x-primary-button type="submit" x-bind:disabled="requests.length === 0 || !deliveryDate || totalQuantity > 32000">
                                Enviar Pedido
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fuelRequestForm() {
            return {
                availableAddresses: @json($addresses),
                prices: @json($prices ?? []),
                discounts: @json($customerDiscounts ?? []),
                requests: [],
                selectedAddress: '',
                selectedFuel: '',
                quantity: 0,
                deliveryDate: '',
                minDate: new Date().toISOString().split('T')[0],

                get totalQuantity() {
                    return this.requests.reduce((sum, request) => sum + request.quantity, 0);
                },

                get totalValue() {
                    return this.requests.reduce((sum, request) => sum + (request.total_price || 0), 0);
                },

                get canAddRequest() {
                    return this.selectedAddress && this.selectedFuel && this.quantity > 0 && this.deliveryDate;
                },

                onAddressChange() {
                    this.selectedFuel = '';
                    this.quantity = 0;
                },

                getFuelPrice(fuelType) {
                    return this.prices[fuelType] || 0;
                },

                getFuelDiscount(fuelType) {
                    return this.discounts[fuelType] || 0;
                },

                calculateUnitPrice(fuelType) {
                    const price = this.getFuelPrice(fuelType);
                    const discount = this.getFuelDiscount(fuelType);
                    return Math.max(0, price - discount);
                },

                formatPrice(value) {
                    return Number(value).toFixed(4);
                },

                addRequest() {
                    if (!this.canAddRequest) return;
                    
                    const unitPrice = this.calculateUnitPrice(this.selectedFuel);
                    const totalPrice = unitPrice * this.quantity;
                    
                    this.requests.push({
                        address_title: this.selectedAddress,
                        fuel_type: this.selectedFuel,
                        quantity: this.quantity,
                        unit_price: unitPrice,
                        total_price: totalPrice
                    });
                    
                    this.selectedFuel = '';
                    this.quantity = 0;
                },

                removeRequest(index) {
                    this.requests.splice(index, 1);
                },

                prepareSubmit(event) {
                    if (this.requests.length === 0) {
                        event.preventDefault();
                        alert('Adicione pelo menos um pedido');
                        return;
                    }
                    
                    if (!this.deliveryDate) {
                        event.preventDefault();
                        alert('Selecione a data de entrega');
                        return;
                    }
                    
                    if (this.totalQuantity > 32000) {
                        event.preventDefault();
                        alert('O volume total não pode exceder 32.000 litros por pedido');
                        return;
                    }
                    
                    const hiddenContainer = document.getElementById('hidden-requests');
                    hiddenContainer.innerHTML = '';
                    
                    this.requests.forEach((request, index) => {
                        ['address_title', 'fuel_type', 'quantity', 'unit_price', 'total_price'].forEach(field => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = `requests[${index}][${field}]`;
                            input.value = request[field];
                            hiddenContainer.appendChild(input);
                        });
                    });
                }
            }
        }
    </script>
</x-app-layout>




