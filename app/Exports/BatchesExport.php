<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BatchesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->with(['user'])->get();
    }

    public function headings(): array
    {
        return [
            'Nº Lote',
            'Cliente',
            'Estação',
            'GOA Total (m³)',
            'GOA Usado (m³)',
            'GOA Disponível (m³)',
            'GOA Desconto (€/L)',
            'GOA+ Desconto (€/L)',
            'SP95 Total (m³)',
            'SP95 Usado (m³)',
            'SP95 Disponível (m³)',
            'SP95 Desconto (€/L)',
            'SP95+ Desconto (€/L)',
            'SP98 Total (m³)',
            'SP98 Usado (m³)',
            'SP98 Disponível (m³)',
            'SP98 Desconto (€/L)',
            'Total (m³)',
            'Total Usado (m³)',
            'Total Disponível (m³)',
            'Utilização (%)',
            'Data Início',
            'Data Fim',
            'Estado',
        ];
    }

    public function map($batch): array
    {
        return [
            $batch->id,
            $batch->user->name,
            ucfirst($batch->station),
            $batch->goa_quantity ?: '',
            $batch->goa_used ?: '',
            $batch->goa_remaining ?: '',
            $batch->goa_discount_per_liter > 0 ? number_format($batch->goa_discount_per_liter, 5, ',', '.') : '',
            $batch->goa_plus_discount_per_liter > 0 ? number_format($batch->goa_plus_discount_per_liter, 5, ',', '.') : '',
            $batch->sp95_quantity ?: '',
            $batch->sp95_used ?: '',
            $batch->sp95_remaining ?: '',
            $batch->sp95_discount_per_liter > 0 ? number_format($batch->sp95_discount_per_liter, 5, ',', '.') : '',
            $batch->sp95_plus_discount_per_liter > 0 ? number_format($batch->sp95_plus_discount_per_liter, 5, ',', '.') : '',
            $batch->sp98_quantity ?: '',
            $batch->sp98_used ?: '',
            $batch->sp98_remaining ?: '',
            $batch->sp98_discount_per_liter > 0 ? number_format($batch->sp98_discount_per_liter, 5, ',', '.') : '',
            $batch->total_quantity,
            $batch->total_used,
            $batch->total_remaining,
            number_format($batch->usage_percentage, 2, ',', '.'),
            $batch->start_date->format('d/m/Y'),
            $batch->end_date->format('d/m/Y'),
            $this->getStatus($batch),
        ];
    }

    protected function getStatus($batch): string
    {
        if ($batch->isActive()) {
            return $batch->hasAvailableQuantity() ? 'Ativo' : 'Esgotado';
        }
        return 'Expirado';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
