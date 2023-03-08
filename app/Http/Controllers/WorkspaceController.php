<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Coordonator;
use App\Models\Student;
use App\Models\Teme;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class WorkspaceController extends Controller
{

  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $inputs = $request->all();
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;

        // get coordonator id
        $coordonator =  Coordonator::where('user_id', $user->id)->first();

        // check if it is admin
        if($user->type != 'coordonator' || !$coordonator->is_admin){
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }

        // get all workspaces
        $workspaces =  Workspace::all();
        foreach ($workspaces as $key => $workspace) {
                // get student data
                $tema = Teme::where('id', $workspace['tema_id'])->first();
                $workspaces[$key]['tema'] = $tema;

                // get coordonator data
                $coordonator = Coordonator::where('id', $workspace['coordonator_id'])->with('user')->first();
                // add email from user table
                if (isset($coordonator['user'])) {
                    $coordonator['email'] = $coordonator['user']['email'];
                } else {
                    $coordonator['email'] = '';
                }
                unset($coordonator['user']);
                $workspaces[$key]['coordonator'] = $coordonator;

                // get student data
                $student = Student::where('id', $workspace['student_id'])->with('user')->first();
                // add email from user table
                if (isset($student['user'])) {
                    $student['email'] = $student['user']['email'];
                } else {
                    $student['email'] = '';
                }
                unset($student['user']);
                $workspaces[$key]['student'] = $student;
        }



        return response([
            'status' => 1,
            'data' => $workspaces
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
        
        
        if($user->type != 'student'){
            return response([
                'status' => 0,
                'data' => 'numai studentii poate sa faca workspace'
            ], 401);
        } 
        
        // get Student id
        $student =  Student::where('user_id', $user->id)->first();
        

        $inputs['student_id'] = $student->id;
        
        $inputs['status'] = 0;

        return response([
            'status' => 1,
            'data' => Workspace::create($inputs)
        ], 200);
    }


    public function getWorkspaceByStatus(Request $request, $status_id)
    {
        $result      = [];
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;

        // get conrdinator workspaces based on status
        $workspaces =  Workspace::where('coordonator_id', $user->id)->where('status', $status_id)->get();

        foreach ($workspaces as  $workspace) {
            // get student data
            $student = Student::where('id', $workspace['student_id'])->with('user')->first();
            if (isset($student['user'])){
                $student['email'] = $student['user']['email'];
            }else{
                $student['email'] = '';
            }
            unset($student['user']);

            // get tema data
            $tema = Teme::where('id', $workspace['tema_id'])->first();
            $result [] = [
            'worspace_id' => $workspace['id'],
            'student' => $student,
            'tema' =>$tema
            ];
        }
        
        return response([
            'status' => 1,
            'data' => $result
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
            'data' => Workspace::find($id)
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
        $workspace =  Workspace::find($id);

        $inputs = $request->all();
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;

        // get coordonator id
        $coordonator =  Coordonator::where('user_id', $user->id)->first();
        

        if(!$workspace || $workspace->coordonator_id != $coordonator->id){
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }

   

        $tema =  Workspace::find($id);
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
        $workspace =  Workspace::find($id);

        $inputs = $request->all();
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;

        // get coordonator id
        $coordonator =  Coordonator::where('user_id', $user->id)->first();
        if(!$workspace || $user->type != 'coordonator' || $workspace->coordonator_id != $coordonator->id){
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }
        
        
        return response([
            'status' => 1,
            'data' => Workspace::destroy($id)
        ], 200);
    }
}
