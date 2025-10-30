<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <x-back-button />
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Editar Lote #{{ $batch->id }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('batches.update', $batch) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="user_id" :value="__('Cliente')" />
                                <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Selecione um cliente</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (old('user_id') ?? $batch->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="station" :value="__('Estação')" />
                                <select id="station" name="station" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Selecione a estação</option>
                                    @foreach($stations as $key => $label)
                                        <option value="{{ $key }}" {{ (old('station') ?? $batch->station) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('station')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="start_date" :value="__('Data de Início')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $batch->start_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="end_date" :value="__('Data de Fim')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date', $batch->end_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Quantidades de Combustível (m³)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <x-input-label for="goa_quantity" :value="__('GOA (m³)')" />
                                    <x-text-input id="goa_quantity" class="block mt-1 w-full" type="number" step="0.001" min="0" name="goa_quantity" :value="old('goa_quantity', $batch->goa_quantity)" />
                                    <x-input-error :messages="$errors->get('goa_quantity')" class="mt-2" />
                                    @if($batch->goa_used > 0)
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-1">
                                            Já utilizado: {{ $batch->goa_used }} m³
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <x-input-label for="sp95_quantity" :value="__('SP95 (m³)')" />
                                    <x-text-input id="sp95_quantity" class="block mt-1 w-full" type="number" step="0.001" min="0" name="sp95_quantity" :value="old('sp95_quantity', $batch->sp95_quantity)" />
                                    <x-input-error :messages="$errors->get('sp95_quantity')" class="mt-2" />
                                    @if($batch->sp95_used > 0)
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-1">
                                            Já utilizado: {{ $batch->sp95_used }} m³
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <x-input-label for="sp98_quantity" :value="__('SP98 (m³)')" />
                                    <x-text-input id="sp98_quantity" class="block mt-1 w-full" type="number" step="0.001" min="0" name="sp98_quantity" :value="old('sp98_quantity', $batch->sp98_quantity)" />
                                    <x-input-error :messages="$errors->get('sp98_quantity')" class="mt-2" />
                                    @if($batch->sp98_used > 0)
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-1">
                                            Já utilizado: {{ $batch->sp98_used }} m³
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Pelo menos um combustível deve ter quantidade maior que zero.
                            </p>
                            <x-input-error :messages="$errors->get('quantities')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Descontos por Litro (€)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div>
                                    <x-input-label for="goa_discount_per_liter" :value="__('GOA')" />
                                    <x-text-input id="goa_discount_per_liter" class="block mt-1 w-full" type="number" step="0.00001" min="0" name="goa_discount_per_liter" :value="old('goa_discount_per_liter', $batch->goa_discount_per_liter)" />
                                    <x-input-error :messages="$errors->get('goa_discount_per_liter')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="goa_plus_discount_per_liter" :value="__('GOA+')" />
                                    <x-text-input id="goa_plus_discount_per_liter" class="block mt-1 w-full" type="number" step="0.00001" min="0" name="goa_plus_discount_per_liter" :value="old('goa_plus_discount_per_liter', $batch->goa_plus_discount_per_liter)" />
                                    <x-input-error :messages="$errors->get('goa_plus_discount_per_liter')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="sp95_discount_per_liter" :value="__('SP95')" />
                                    <x-text-input id="sp95_discount_per_liter" class="block mt-1 w-full" type="number" step="0.00001" min="0" name="sp95_discount_per_liter" :value="old('sp95_discount_per_liter', $batch->sp95_discount_per_liter)" />
                                    <x-input-error :messages="$errors->get('sp95_discount_per_liter')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="sp95_plus_discount_per_liter" :value="__('SP95+')" />
                                    <x-text-input id="sp95_plus_discount_per_liter" class="block mt-1 w-full" type="number" step="0.00001" min="0" name="sp95_plus_discount_per_liter" :value="old('sp95_plus_discount_per_liter', $batch->sp95_plus_discount_per_liter)" />
                                    <x-input-error :messages="$errors->get('sp95_plus_discount_per_liter')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="sp98_discount_per_liter" :value="__('SP98')" />
                                    <x-text-input id="sp98_discount_per_liter" class="block mt-1 w-full" type="number" step="0.00001" min="0" name="sp98_discount_per_liter" :value="old('sp98_discount_per_liter', $batch->sp98_discount_per_liter)" />
                                    <x-input-error :messages="$errors->get('sp98_discount_per_liter')" class="mt-2" />
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Os descontos são aplicados primeiro, antes dos descontos do cliente.
                            </p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('batches.index') }}" class="mr-3 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Cancelar
                            </a>
                            <x-primary-button class="ml-4">
                                Salvar
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>