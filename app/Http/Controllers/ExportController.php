<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExportStudentCoordinatorTema;
use App\Models\ExportStudentStatus;
use App\Models\ExportCoordinatorWithTeme;
use App\Models\ExportCoordinatorStudents;

class ExportController extends Controller
{
    //exportCoordinators
    public function ExportStudentCoordinatorTema(Request $request)
    {   
        return (new ExportStudentCoordinatorTema($request))->download('data.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
        
    }


    public function ExportCoordinatorStudents(Request $request)
    {   
        return (new ExportCoordinatorStudents($request))->download('data.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }



    public function ExportStudentStatus(Request $request)
    {
        return (new ExportStudentStatus($request))->download('data.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function ExportCoordinatorWithTeme(Request $request)
    {
        return (new ExportCoordinatorWithTeme($request))->download('data.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

  
}
