<?php

namespace App\Http\Controllers;

use App\Models\Coordonator;
use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\File;
use App\Models\Teme;
use App\Models\Event;
use App\Models\Student;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use PHPUnit\Framework\MockObject\Builder\Stub;
use Carbon\Carbon;

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

            $events = Event::where('workspace_id', $workspace->id)->with('attachment')->get();
            $events = isset($events) ? $events : [];
            return response([
                'status' => 1,
                'data' => $events
            ], 200);

        } else if ($user->type = 'coordonator') {
            $workspace =  Workspace::where('student_id', $studentId)->first();
            $events = Event::where('workspace_id', $workspace->id)->with('attachment')->get();
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
        // echo json_encode($inputs);die;
        if ( !isset($inputs['descriere'])) {
            $inputs['descriere'] = '';
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


    public function coordinatorHomepage(Request $request,)
    {
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;
        $coordonator =  Coordonator::where('user_id', $user->id)->first();
        $results = [];
        $events = Event::where('author_id',$coordonator->id)
                        ->where('author_type', 'coordonator')
                        ->where('due_date', '>=', Carbon::today()->toDateString())->get();
        
        foreach ($events as  $event) {
            $workspace =  Workspace::where('id', $event->workspace_id)->first();
            $student =  Student::where('id', $workspace->student_id)->with('user')->first();
            $tema = Teme::where('id', $workspace->tema_id)->first();
            $results [] = [
                'student_name' => $student['user'] ? $student['user']['name'] : '',
                'student_email' => $student['user'] ? $student['user']['email'] : '',
                'tema_title' => $tema['title'],
                'event_title' => $event['title'],
                'event_type' => $event['type'],
                'event_deadline' => $event['due_date'],
            ];

        }

        return response([
            'status' => 1,
            'data' => $results
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
