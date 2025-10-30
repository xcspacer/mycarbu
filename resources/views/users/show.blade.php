<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <x-back-button />
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Utilizador | {{ $user->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Nome</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Email</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Tipo de Utilizador</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    @if($user->role === 'admin')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        Administrador
                                    </span>
                                    @elseif($user->role === 'operator')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        Operador
                                    </span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                        Utilizador
                                    </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Data de Registo</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Última Atualização</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Email Verificado</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->email_verified_at?->format('d/m/Y H:i') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($user->energy_fleet_customer_id)
                    <div class="mb-6 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações do Energy</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Energy Fleet Customer ID</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->energy_fleet_customer_id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Status no Energy</p>
                                <p class="mt-1">
                                    @if($user->is_active_from_energy)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Ativo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            Inativo
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('users.index') }}" class="mr-3 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Voltar
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="ml-4 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Editar
                        </a>
                        <x-danger-button
                            class="ml-4"
                            x-data="{}"
                            x-on:click="$dispatch('open-modal', 'confirm-user-deletion')">
                            Eliminar Utilizador
                        </x-danger-button>

                        <x-modal name="confirm-user-deletion" :show="false" focusable>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="p-6">
                                @csrf
                                @method('DELETE')

                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Tens a certeza que pretendes eliminar este utilizador?
                                </h2>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Esta ação é irreversível. Todos os dados associados a este utilizador serão permanentemente removidos.
                                </p>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        Cancelar
                                    </x-secondary-button>

                                    <x-danger-button class="ml-3">
                                        Eliminar Utilizador
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
