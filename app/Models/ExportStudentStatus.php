<?php

namespace App\Models;
// use App\Models\Client\ExternalUser;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ExportStudentStatus implements FromArray, WithMapping, WithHeadings
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
            'Specializare',
            'Status'
        ];
    }

    public function map($user): array
    {            
        $studentName    = isset($user['user']) ? $user['user']['name'] : "";
        $studentEmail    = isset($user['user']) ? $user['user']['email'] : "";
        $specializare = $user['specializare'];

        $status = 'rejected';


        if ($user && !$user['workspace']) {
            $status = 'fara tema';
            return [$user['id'], $studentName, $studentEmail, $specializare, $status];
        }
   
        if ($user && $user['workspace'] && $user['workspace']['status'] == 3) {
            $status = 'rejected';
            return [$user['id'], $studentName, $studentEmail, $specializare, $status];
        }

        if ($user && $user['workspace'] && $user['workspace']['status'] == 0) {
            $status = 'in asteptare';
            return [$user['id'], $studentName, $studentEmail, $specializare, $status];
        }
        return [];

    }
}