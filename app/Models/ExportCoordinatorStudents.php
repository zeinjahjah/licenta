<?php

namespace App\Models;
// use App\Models\Client\ExternalUser;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ExportCoordinatorStudents implements FromArray, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {               
        $aux =Workspace::where('status', 1)->orWhere('status', 2)->get()->toArray();
        return $aux;
        
    }

    public function headings(): array
    {
        return [

            'Workspace id',
            'Tema name',
            'Coordinator name',
            'Student name',
            
            
        ];
    }

    public function map($workspace): array
    {           

        $student_id         = $workspace['student_id'];
        $coordonator_id     = $workspace['coordonator_id'];
        $tema_id            = $workspace['tema_id'];

        
        $student = Student::where('id', $student_id)->with('user')->first(); 
        $coordonator = Coordonator::where('id', $coordonator_id)->with('user')->first(); 
        $tema = Teme::where('id', $workspace['tema_id'])->first();
        if ($student && $coordonator && $tema) {
            $studentName        = $student->user->name;
            $coordinatorName        = $coordonator->user->name;
            $temaTitle        = $tema->title;
    
            return [$workspace['student_id'],$temaTitle, $coordinatorName,$studentName];
        }else {
            return [];
        }
    }


}