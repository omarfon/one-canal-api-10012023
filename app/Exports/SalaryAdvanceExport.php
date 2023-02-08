<?php

namespace App\Exports;

use App\Models\SalaryAdvance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SalaryAdvanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct($request) {
        $this->request = $request;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $query = SalaryAdvance::with('account.bank', 'user.business', 'reason');

        // if ($this->request['start_date'] && $this->request['end_date']) {
        //     $query->whereBetween('created_at', [$this->request['start_date'] . " 00:00", $this->request['end_date'] . " 23:59"]);
        // }

        $query->filterByColumns($this->request->all());

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tipo de documento',
            'Número de documento',
            'Nombre de cliente',
            'Correo electrónico',
            'RUC',
            'Nombre de empresa',
            'Periodo',
            'Fecha de solicitud',
            'Banco de cuenta',
            'Número de cuenta',
            'Código de Cuenta Interbancario',
            'Monto total (S/)',
            'Comisión (S/)',
            'A transferir (S/)',
            'Motivo',
            'Estado'
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->user->document_type,
            $row->user->document_number,
            $row->user->names . " " . $row->user->surnames,
            $row->user->email,
            $row->user->business->ruc,
            $row->user->business->name,
            $row->period_name,
            date("d-m-Y H:i:s", strtotime($row->created_at)),
            $row->account->bank->short_name ?? $row->account->bank->name,
            $row->account->number . " ",
            $row->account->cci . " ",
            $row->amount,
            $row->fees_amount,
            $row->transfer_amount,
            $row->reason->name,
            ($row->status == 'REGISTERED') ? 'Solicitado' : (($row->status == 'CONFIRMED') ? 'Aprobado' : (($row->status == 'DEPOSITED') ? 'Abonado' : (($row->status == 'COMPLETED') ? 'Finalizado' : (($row->status == 'CANCELED') ? 'Cancelado' : ''))))
        ];
    }
}
