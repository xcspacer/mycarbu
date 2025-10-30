<?php

namespace App\Http\Controllers;

use App\Exports\BatchesExport;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BatchController extends Controller
{
    public function index()
    {
        $query = Batch::with(['user']);

        if (Auth::user()->canManageSystem()) {
            $batches = $query->latest()->paginate(10);
        } else {
            $batches = $query->where('user_id', Auth::id())->latest()->paginate(10);
        }

        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        if (!Auth::user()->canEditData()) {
            abort(403, 'Apenas administradores podem criar lotes.');
        }

        $users = User::all();
        $stations = [
            'vigo' => 'Vigo',
            'huelva' => 'Huelva',
            'merida' => 'Mérida',
            'salamanca' => 'Salamanca',
        ];

        return view('batches.create', compact('users', 'stations'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canEditData()) {
            abort(403, 'Apenas administradores podem criar lotes.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'station' => ['required', 'in:vigo,huelva,merida,salamanca'],
            'goa_quantity' => ['nullable', 'numeric', 'min:0'],
            'goa_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'goa_plus_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'sp95_quantity' => ['nullable', 'numeric', 'min:0'],
            'sp95_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'sp95_plus_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'sp98_quantity' => ['nullable', 'numeric', 'min:0'],
            'sp98_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'user_id.required' => 'O utilizador é obrigatório.',
            'station.required' => 'A estação é obrigatória.',
            'goa_quantity.min' => 'A quantidade de GOA deve ser maior ou igual a zero.',
            'goa_discount_per_liter.min' => 'O desconto de GOA deve ser maior ou igual a zero.',
            'goa_plus_discount_per_liter.min' => 'O desconto de GOA+ deve ser maior ou igual a zero.',
            'sp95_quantity.min' => 'A quantidade de SP95 deve ser maior ou igual a zero.',
            'sp95_discount_per_liter.min' => 'O desconto de SP95 deve ser maior ou igual a zero.',
            'sp95_plus_discount_per_liter.min' => 'O desconto de SP95+ deve ser maior ou igual a zero.',
            'sp98_quantity.min' => 'A quantidade de SP98 deve ser maior ou igual a zero.',
            'sp98_discount_per_liter.min' => 'O desconto de SP98 deve ser maior ou igual a zero.',
            'start_date.required' => 'A data de início é obrigatória.',
            'end_date.required' => 'A data de fim é obrigatória.',
            'end_date.after_or_equal' => 'A data de fim deve ser posterior ou igual à data de início.',
        ]);

        $totalQuantity = ($validated['goa_quantity'] ?? 0) + ($validated['sp95_quantity'] ?? 0) + ($validated['sp98_quantity'] ?? 0);
        if ($totalQuantity <= 0) {
            return back()
                ->withInput()
                ->withErrors(['quantities' => 'Deve especificar pelo menos um combustível com quantidade maior que zero.']);
        }

        $validated['goa_quantity'] = $validated['goa_quantity'] ?? 0;
        $validated['goa_discount_per_liter'] = $validated['goa_discount_per_liter'] ?? 0;
        $validated['goa_plus_discount_per_liter'] = $validated['goa_plus_discount_per_liter'] ?? 0;
        $validated['sp95_quantity'] = $validated['sp95_quantity'] ?? 0;
        $validated['sp95_discount_per_liter'] = $validated['sp95_discount_per_liter'] ?? 0;
        $validated['sp95_plus_discount_per_liter'] = $validated['sp95_plus_discount_per_liter'] ?? 0;
        $validated['sp98_quantity'] = $validated['sp98_quantity'] ?? 0;
        $validated['sp98_discount_per_liter'] = $validated['sp98_discount_per_liter'] ?? 0;

        try {
            DB::beginTransaction();

            $batch = Batch::create($validated);

            DB::commit();

            return redirect()->route('batches.index')
                ->with('success', 'Lote criado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar lote. ' . $e->getMessage()]);
        }
    }

    public function show(Batch $batch)
    {
        if (!Auth::user()->canManageSystem() && $batch->user_id !== Auth::id()) {
            abort(403);
        }

        $batch->load(['user']);
        $canEdit = Auth::user()->canEditData();

        return view('batches.show', compact('batch', 'canEdit'));
    }

    public function edit(Batch $batch)
    {
        if (!Auth::user()->canEditData()) {
            abort(403, 'Apenas administradores podem editar lotes.');
        }

        $users = User::all();
        $stations = [
            'vigo' => 'Vigo',
            'huelva' => 'Huelva',
            'merida' => 'Mérida',
            'salamanca' => 'Salamanca',
        ];

        return view('batches.edit', compact('batch', 'users', 'stations'));
    }

    public function update(Request $request, Batch $batch)
    {
        if (!Auth::user()->canEditData()) {
            abort(403, 'Apenas administradores podem editar lotes.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'station' => ['required', 'in:vigo,huelva,merida,salamanca'],
            'goa_quantity' => ['nullable', 'numeric', 'min:0'],
            'goa_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'goa_plus_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'sp95_quantity' => ['nullable', 'numeric', 'min:0'],
            'sp95_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'sp95_plus_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'sp98_quantity' => ['nullable', 'numeric', 'min:0'],
            'sp98_discount_per_liter' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'user_id.required' => 'O utilizador é obrigatório.',
            'station.required' => 'A estação é obrigatória.',
            'goa_quantity.min' => 'A quantidade de GOA deve ser maior ou igual a zero.',
            'goa_discount_per_liter.min' => 'O desconto de GOA deve ser maior ou igual a zero.',
            'goa_plus_discount_per_liter.min' => 'O desconto de GOA+ deve ser maior ou igual a zero.',
            'sp95_quantity.min' => 'A quantidade de SP95 deve ser maior ou igual a zero.',
            'sp95_discount_per_liter.min' => 'O desconto de SP95 deve ser maior ou igual a zero.',
            'sp95_plus_discount_per_liter.min' => 'O desconto de SP95+ deve ser maior ou igual a zero.',
            'sp98_quantity.min' => 'A quantidade de SP98 deve ser maior ou igual a zero.',
            'sp98_discount_per_liter.min' => 'O desconto de SP98 deve ser maior ou igual a zero.',
            'start_date.required' => 'A data de início é obrigatória.',
            'end_date.required' => 'A data de fim é obrigatória.',
            'end_date.after_or_equal' => 'A data de fim deve ser posterior ou igual à data de início.',
        ]);

        $totalQuantity = ($validated['goa_quantity'] ?? 0) + ($validated['sp95_quantity'] ?? 0) + ($validated['sp98_quantity'] ?? 0);
        if ($totalQuantity <= 0) {
            return back()
                ->withInput()
                ->withErrors(['quantities' => 'Deve especificar pelo menos um combustível com quantidade maior que zero.']);
        }

        $validated['goa_quantity'] = $validated['goa_quantity'] ?? 0;
        $validated['goa_discount_per_liter'] = $validated['goa_discount_per_liter'] ?? 0;
        $validated['goa_plus_discount_per_liter'] = $validated['goa_plus_discount_per_liter'] ?? 0;
        $validated['sp95_quantity'] = $validated['sp95_quantity'] ?? 0;
        $validated['sp95_discount_per_liter'] = $validated['sp95_discount_per_liter'] ?? 0;
        $validated['sp95_plus_discount_per_liter'] = $validated['sp95_plus_discount_per_liter'] ?? 0;
        $validated['sp98_quantity'] = $validated['sp98_quantity'] ?? 0;
        $validated['sp98_discount_per_liter'] = $validated['sp98_discount_per_liter'] ?? 0;

        try {
            DB::beginTransaction();

            $originalAttributes = $batch->getOriginal();
            $batch->update($validated);

            $changes = [];
            $fieldsToCompare = [
                'user_id' => 'Utilizador',
                'station' => 'Estação',
                'goa_quantity' => 'Quantidade GOA (m³)',
                'sp95_quantity' => 'Quantidade SP95 (m³)',
                'sp98_quantity' => 'Quantidade SP98 (m³)',
                'goa_discount_per_liter' => 'Desconto GOA (€/L)',
                'goa_plus_discount_per_liter' => 'Desconto GOA+ (€/L)',
                'sp95_discount_per_liter' => 'Desconto SP95 (€/L)',
                'sp95_plus_discount_per_liter' => 'Desconto SP95+ (€/L)',
                'sp98_discount_per_liter' => 'Desconto SP98 (€/L)',
                'start_date' => 'Data de Início',
                'end_date' => 'Data de Fim',
            ];

            foreach ($fieldsToCompare as $field => $label) {
                $oldValue = $originalAttributes[$field] ?? null;
                $newValue = $batch->$field;

                if ($oldValue != $newValue) {
                    if (str_contains($field, 'discount_per_liter')) {
                        $oldValue = $oldValue ? number_format($oldValue, 5, ',', '.') . '€' : '0,00000€';
                        $newValue = number_format($newValue, 5, ',', '.') . '€';
                    }
                    
                    if ($field === 'user_id') {
                        $oldUser = User::find($oldValue);
                        $newUser = User::find($newValue);
                        $oldValue = $oldUser ? $oldUser->name : 'Utilizador #' . $oldValue;
                        $newValue = $newUser ? $newUser->name : 'Utilizador #' . $newValue;
                    }

                    $changes[$field] = [
                        'label' => $label,
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }

            DB::commit();

            return redirect()->route('batches.index')
                ->with('success', 'Lote atualizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar lote. ' . $e->getMessage()]);
        }
    }

    public function destroy(Batch $batch)
    {
        if (!Auth::user()->canEditData()) {
            abort(403, 'Apenas administradores podem eliminar lotes.');
        }

        try {
            DB::beginTransaction();

            $batch->delete();

            DB::commit();

            return redirect()->route('batches.index')
                ->with('success', 'Lote eliminado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Erro ao eliminar lote. ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $query = Batch::query();

        if (!Auth::user()->canManageSystem()) {
            $query->where('user_id', Auth::id());
        }

        $filename = 'lotes_combustivel_'.now()->format('Y_m_d').'.xlsx';

        return Excel::download(new BatchesExport($query), $filename);
    }
}
