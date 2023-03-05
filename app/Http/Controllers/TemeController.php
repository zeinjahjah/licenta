<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teme;
use App\Models\Coordonator;
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
            $coordonator = Coordonator::where('id',$coordonator_id)->first();
            $teme = Teme::where('coordonator_id',$coordonator_id)->get();
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
        if($user->type != 'coordonator'){
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
        if($user->type != 'coordonator' || $tema->coordonator_id != $coordonator->id){
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
        if($user->type != 'coordonator' || $tema->coordonator_id != $coordonator->id){
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
}
