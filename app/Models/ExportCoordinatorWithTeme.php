<?php

namespace App\Models;
// use App\Models\Client\ExternalUser;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ExportCoordinatorWithTeme implements FromArray, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        return Teme::all()->toArray();
    }

    public function headings(): array
    {
        return [
            'Coordinator ID',
            'Coordinator',
            'Coordinator Email',
            'Tema title',
            'Tema type',
            'Detail',
            'Specializare'
        ];
    }

    public function map($tema): array
    {  
        $coordinator = Coordonator::find($tema['coordonator_id'])->with('user')->first();
        $coordinatorId    = $coordinator['id'];
        $coordinatorName    = isset($coordinator['user']) ? $coordinator['user']['name'] : "";
        $coordinatorEmail    = isset($coordinator['email']) ? $coordinator['user']['email'] : "";
        $temaTitle = $tema['title'];
        $temaType = $tema['tema_type'];
        $temaDetalii = $tema['detalii'];
        $temaSpecializare = $tema['specializare'];

        return [$coordinatorId, $coordinatorName, $coordinatorEmail,
        $temaTitle, $temaType, $temaDetalii, $temaSpecializare];

    }
}