<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\File;
use App\Models\Event;
use Laravel\Sanctum\PersonalAccessToken;

class EventController extends Controller
{

  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $studentId = null)
    {

        $inputs = $request->all();
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;
        if ($user->type == 'student') {
            $workspace =  Workspace::where('student_id', $user->id)->first();
            $events = Event::where('workspace_id', $workspace->id)->get();
            $events = isset($events) ? $events : [];
            return response([
                'status' => 1,
                'data' => $events
            ], 200);

        } else if ($user->type = 'coordonator') {
            $workspace =  Workspace::where('student_id', $studentId)->first();
            $events = Event::where('workspace_id', $workspace->id)->get();
            $events = isset($events) ? $events : [];
            return response([
                'status' => 1,
                'data' => $events
            ], 200);

        }

        return response([
            'status' => 0,
            'data' => 'permission denied.'
        ], 401);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs           = $request->all();
        $bearerToken      = $request->bearerToken();
        $token            = PersonalAccessToken::findToken($bearerToken);
        $user             = $token->tokenable;
        $inputs['author_id'] = $user->id;
        
        if ($user->type == 'student') {
            $inputs['author_type'] = 'student';
        } else if ($user->type = 'coordonator') {
            $inputs['author_type'] = 'coordonator';
        }
        return response([
            'status' => 1,
            'data' => Event::create($inputs)
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
        // $file = File::where("event_id", $id)->first();
        $event = Event::find($id);
        if($event){
            $event = $event->with('attachment', 'comments')->get();
        }
        // echo json_encode($event);die;
        return response([
            'status' => 1,
            'data' => $event
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
        $event =  Event::find($id);
        $event->update($request->all());
        return response([
            'status' => 1,
            'data' => $event
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
        return response([
            'status' => 1,
            'data' => Event::destroy($id)
        ], 200);
    }
}
