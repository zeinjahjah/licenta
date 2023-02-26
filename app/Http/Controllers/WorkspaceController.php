<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Coordonator;
use App\Models\Student;
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
        if($user->type != 'coordonator' || !$coordonator->is_admin){
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }

        return response([
            'status' => 1,
            'data' => Workspace::all()
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
        // get coordonator id
        $coordonator =  Student::where('user_id', $user->id)->first();
        $inputs['student_id'] = $coordonator->id;
        $inputs['status'] = 0;

        return response([
            'status' => 1,
            'data' => Workspace::create($inputs)
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
        if(!$workspace || $user->type != 'coordonator' || $workspace->coordonator_id != $coordonator->id){
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
