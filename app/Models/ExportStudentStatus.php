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
    private $counter = 0;
    private $rejectedCount;
    private $faraTemaCount;
    private $inAsteptareCount;


    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->rejectedCount = Student::whereHas('workspace', function ($query) {
            $query->where('status', 3);
        })->count();
        $this->rejectedCount = 0;
        $this->faraTemaCount = Student::whereDoesntHave('workspace')->count();
        $this->inAsteptareCount = Student::whereHas('workspace', function ($query) {
            $query->where('status', 0);
        })->count();
        
        $this->faraTemaCount = strval($this->faraTemaCount);
        $this->inAsteptareCount = strval($this->inAsteptareCount);
        $this->rejectedCount = strval($this->rejectedCount);
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
            'Status', 
            '', 
            'Nr. studenti respins', 
            'Nr. studneti faraTema', 
            'Nr. studneti inAsteptare'
        ];
    }

    public function map($user): array
    {   
        $studentName    = isset($user['user']) ? $user['user']['name'] : "";
        $studentEmail    = isset($user['user']) ? $user['user']['email'] : "";
        $specializare = $user['specializare'];

        $status = 'rejected';

        if ($user && !$user['workspace']) {
            $this->counter +=1;   
            $status = 'fara tema';
            if ( $this->counter == 1) {
                return [$user['id'], $studentName, $studentEmail, $specializare, $status, "", $this->rejectedCount, $this->faraTemaCount, $this->inAsteptareCount];
            } else {
                return [$user['id'], $studentName, $studentEmail, $specializare, $status];
            }
        }
   
        if ($user && $user['workspace'] && $user['workspace']['status'] == 3) {
            $this->counter +=1;   
            $status = 'rejected';
            if ( $this->counter == 1) {
                return [$user['id'], $studentName, $studentEmail, $specializare, $status, "", $this->rejectedCount, $this->faraTemaCount, $this->inAsteptareCount];
            } else {
                return [$user['id'], $studentName, $studentEmail, $specializare, $status];
            }
        }

        if ($user && $user['workspace'] && $user['workspace']['status'] == 0) {
            $this->counter +=1;   
            $status = 'in asteptare';
            if ( $this->counter == 1) {
                return [$user['id'], $studentName, $studentEmail, $specializare, $status, "", $this->rejectedCount, $this->faraTemaCount, $this->inAsteptareCount];
            } else {
                return [$user['id'], $studentName, $studentEmail, $specializare, $status];
            }
        }

        return [];
    }
}