<?php

namespace App\Http\Controllers;

use App\Models\Coordonator;
use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\File;
use App\Models\Event;
use App\Models\Student;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use PHPUnit\Framework\MockObject\Builder\Stub;

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
            $student =  Student::where('user_id', $user->id)->first();

            $workspace =  Workspace::where('student_id', $student->id)->first();

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
        $event = Event::where('id',$id)->with('attachment', 'comments')->first();
        
        $user_id= $event->author_id;
        $user_type= $event->author_type;
        
        $user = User::where('id',$user_id)->first();
        $user_name =$user->name;

        $event['author_name']= $user_name;
   

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
