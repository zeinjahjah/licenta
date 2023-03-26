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

        $acceptedStudentsStatus = [1, 2];
        $rejectedStudentsStatus = [0, 3];

        $status = 'rejected';

        if ($user && $user['workspace'] && in_array($user['workspace']['status'], $acceptedStudentsStatus)) {
            $status = 'accepted';
        }

        if ($user && $user['workspace'] && in_array($user['workspace']['status'], $rejectedStudentsStatus)) {
            $status = 'rejected';
        }
        
        return [$user['id'], $studentName, $studentEmail, $specializare, $status];
       
    }
}