<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teme;
use App\Models\Coordonator;
use App\Models\Student;
use Laravel\Sanctum\PersonalAccessToken;

class TemeController extends Controller
{
    /**
     * Display a listing of the resource based on userid.
     *
     * @return \Illuminate\Http\Response
     */
    public function temeByCoordonator($coordonator_id)
    {
        $coordonator = Coordonator::where('id', $coordonator_id)->first();
        $teme = Teme::where('coordonator_id', $coordonator_id)->get();
        $result = [
            'coordonator' => $coordonator,
            'teme' => $teme
        ];

        return response([
            'status' => 1,
            'data' => $result
        ], 200);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'status' => 1,
            'data' => Teme::all()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;
                

        if ($user->type != 'coordonator') {
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }
        // get coordonator id
        $coordonator =  Coordonator::where('user_id', $user->id)->first();
        $inputs['coordonator_id'] = $coordonator->id;

        return response([
            'status' => 1,
            'data' => Teme::create($inputs)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response([
            'status' => 1,
            'data' => Teme::find($id)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tema =  Teme::find($id);
        $inputs = $request->all();
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;

        // get coordonator id
        $coordonator =  Coordonator::where('user_id', $user->id)->first();
        if ($user->type != 'coordonator' || $tema->coordonator_id != $coordonator->id) {
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }

        $tema->update($request->all());
        return response([
            'status' => 1,
            'data' => $tema
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $tema =  Teme::find($id);
        $inputs = $request->all();
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;

        // get coordonator id
        $coordonator =  Coordonator::where('user_id', $user->id)->first();
        if ($user->type != 'coordonator' || $tema->coordonator_id != $coordonator->id) {
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }

        return response([
            'status' => 1,
            'data' => Teme::destroy($id)
        ], 200);
    }
    // ======================== 
    public function selectSubject(Request $request)
    {

        $inputs = $request->all();
        // echo json_encode($inputs);die;
        $student_id = $inputs['student_id'];
        // echo json_encode($inputs);die;
        $teme_id = $inputs['teme_id'];
        $tema = Teme::where('id', $teme_id)->first();
        $coordonator_id = $tema['coordonator_id'];

        $result = [
            'student_id' => $student_id,
            'tema_id' => $teme_id,
            'coordonator_id' => $coordonator_id,

        ];

        return response([
            'status' => 1,
            'data' => $result
        ], 200);
    }


    public function allCoordinatorsWithSubjects()
    {
        $coordonators = Coordonator::with('teme', 'user')->get();
        foreach ($coordonators as $key => $coordonator) {
            // get email from user table
            if (isset($coordonator['user'])) {
                $coordonators[$key]['email'] = $coordonator['user']['email'];
                $coordonators[$key]['name'] = $coordonator['user']['name'];
            } else {
                $coordonators[$key]['email'] = '';
                $coordonators[$key]['name'] = '';
            }
            unset($coordonators[$key]['user']);
        }

        return response([
            'status' => 1,
            'data' => $coordonators
        ], 200);
    }

    public function allstudentswithSubject()
    {
        $students = Student::with('workspace', 'user')->get();

        $students = $students->map(function ($student) {
            if (isset($student->user)) {
                $student->email = $student->user->email;
                $student->student_name = $student->user->name;
            } else {
                $student->email = '';
                $student->student_name = '';
            }
            unset($student->user);
        
            if (isset($student->workspace)) {
                $tema = Teme::where('id', $student->workspace->tema_id)->first();
                $student->tema = $tema;
        
                $coordonator = Coordonator::where('id', $student->workspace->coordonator_id)->with('user')->first();
        
                if (isset($coordonator->user)) {
                    $coordonator->email = $coordonator->user->email;
                    $coordonator->coordinator_name = $coordonator->user->name;
                } else {
                    $coordonator->email = '';
                    $coordonator->coordinator_name = '';
                }
                unset($coordonator->user);
        
                $student->coordonator = $coordonator;
            }
        
            return $student;
        });
        
        // Sort the collection by the 'student_name' attribute
        $students = $students->sortBy('student_name');

        
        foreach ($students as $student) {
           $result[] = $student;
        }

        return response([
            'status' => 1,
            'data' => $result
        ], 200);
    }
}
