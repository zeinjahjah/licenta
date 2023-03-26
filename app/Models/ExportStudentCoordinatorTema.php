<?php

namespace App\Models;
// use App\Models\Client\ExternalUser;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ExportStudentCoordinatorTema implements FromArray, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        return Student::with('user', 'workspace')->get()->toArray();
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Student',
            'Student Email',
            'Coordonator',
            'Coordonator Email',
            'Titlul Licenei'
        ];
    }

    public function map($user): array
    {            

        if ($user && $user['workspace'] && $user['workspace']['status'] == 1) {
            $studentName    = isset($user['user']) ? $user['user']['name'] : "";
            $studentEmail    = isset($user['user']) ? $user['user']['email'] : "";

            $coordinatorId = $user['workspace']['coordonator_id'];
            $coordinator = Coordonator::find($coordinatorId)->with('user')->first();
            $coordinatorName    = isset($coordinator['user']) ? $coordinator['user']['name'] : "";
            $coordinatorEmail    = isset($coordinator['email']) ? $coordinator['user']['email'] : "";

            $coordinatorId = $user['workspace']['coordonator_id'];

            $temaId = $user['workspace']['tema_id'];
            $tema = Teme::find($temaId)->first();
            $temaTitle = $tema['title'];

            return [$user['id'], $studentName, $studentEmail, $coordinatorName,  $coordinatorEmail, $temaTitle];
        }
        return [];
    }
}