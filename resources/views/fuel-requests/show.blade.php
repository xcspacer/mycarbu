<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pedido de Combustível
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

                    <div class="hidden print:block mb-4">
                        <h1 class="text-xl font-bold text-center mb-2">PEDIDO DE COMBUSTÍVEL #{{ $fuelRequest->id }}</h1>
                        <div class="text-sm text-center border-b pb-2 mb-4">
                            <p><strong>Solicitante:</strong> {{ $fuelRequest->user->name }} | 
                               <strong>Total:</strong> {{ $fuelRequest->total_formatted }} | 
                               <strong>Entrega:</strong> {{ $fuelRequest->delivery_date->format('d/m/Y') }} | 
                               <strong>Pedido:</strong> {{ $fuelRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg mb-6 print:!hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Solicitante</label>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $fuelRequest->user->name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade Total</label>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $fuelRequest->total_formatted }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor Total</label>
                            <div class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                €{{ isset($fuelRequest->request_data['total_value']) ? number_format($fuelRequest->request_data['total_value'], 4, ',', '.') : '0,0000' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de Entrega</label>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $fuelRequest->delivery_date->format('d/m/Y') }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data do Pedido</label>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $fuelRequest->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">
                                Total do Pedido
                            </h3>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-900 dark:text-blue-100">
                                    {{ $fuelRequest->total_formatted }}
                                </div>
                                <div class="text-sm font-semibold text-blue-800 dark:text-blue-200">
                                    €{{ isset($fuelRequest->request_data['total_value']) ? number_format($fuelRequest->request_data['total_value'], 4, ',', '.') : '0,0000' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($fuelRequest->notes)
                    <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg print:!bg-white print:!border-gray-400 print:!p-2 print:!mb-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2 print:!text-black print:!mb-1">Observações:</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 print:!text-black print:!text-xs">{{ $fuelRequest->notes }}</p>
                    </div>
                    @endif

                    <div class="space-y-6 print:!space-y-2">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 print:!text-sm print:!mb-2 print:!font-bold print:!hidden">Detalhes do Pedido</h3>

                        @if($fuelRequest->request_data && isset($fuelRequest->request_data['organized_data']))
                        @foreach($fuelRequest->request_data['organized_data'] as $groupData)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden print:!border-gray-400 print:page-break-inside-avoid">
                            <div class="bg-gray-100 dark:bg-gray-700 px-4 py-3 print:!bg-gray-200 print:!py-1 print:!px-2">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 print:!text-xs print:!font-bold">
                                    {{ $groupData['address'] }}
                                </h4>
                            </div>

                            <div class="divide-y divide-gray-200 dark:divide-gray-600 print:!divide-gray-300">
                                <div class="p-4 print:!p-2">
                                    <div class="grid grid-cols-4 gap-4 py-2 px-3 mb-2 print:!hidden">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</span>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantidade</span>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Preço Unit.</span>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</span>
                                    </div>
                                    <div class="space-y-2 print:!space-y-0">
                                        @foreach($groupData['fuels'] as $fuel)
                                        <div class="grid grid-cols-4 gap-4 py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded print:!bg-white print:!py-0.5 print:!px-0">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 print:!text-xs">
                                                {{ strtoupper($fuel['type']) }}
                                            </span>
                                            <span class="text-sm text-gray-700 dark:text-gray-300 print:!text-xs">
                                                {{ number_format($fuel['quantity'], 0, ',', '.') }} LT
                                            </span>
                                            <span class="text-sm text-gray-700 dark:text-gray-300 print:!text-xs">
                                                €{{ number_format($fuel['unit_price'] ?? 0, 4, ',', '.') }}/L
                                            </span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100 print:!text-xs print:!font-bold">
                                                €{{ number_format($fuel['total_price'] ?? 0, 4, ',', '.') }}
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-8 no-print">
                        <a href="{{ route('fuel-requests.index') }}" class="mr-3 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Voltar
                        </a>
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body > header,
            body > nav,
            nav,
            header,
            .no-print {
                display: none !important;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
            }

            main,
            .py-12 {
                padding: 0 !important;
            }

            .sm\:px-6,
            .lg\:px-8 {
                padding: 0 !important;
            }

            .shadow-sm,
            .rounded-lg,
            .rounded {
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .p-6 {
                padding: 15mm 10mm !important;
            }

            .print\:block {
                display: block !important;
            }

            .print\:\!hidden {
                display: none !important;
            }

            .print\:\!bg-gray-200 {
                background-color: #e5e7eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page-break-inside-avoid {
                page-break-inside: avoid;
            }

            .dark\:bg-gray-800,
            .dark\:bg-gray-700,
            .dark\:border-gray-600 {
                background-color: white !important;
                border-color: #d1d5db !important;
            }

            .dark\:text-gray-100,
            .dark\:text-gray-200,
            .dark\:text-gray-300 {
                color: black !important;
            }
        }

        @media screen {
            .print\:block {
                display: none;
            }
        }
    </style>
</x-app-layout>





