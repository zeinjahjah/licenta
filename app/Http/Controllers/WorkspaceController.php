<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Coordonator;
use App\Models\Student;
use App\Models\Teme;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;

use Laravel\Sanctum\PersonalAccessToken;

use function PHPSTORM_META\type;

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

        $tema_id =Teme::whereId($inputs['tema_id'])->update(['is_taken' => 1]);
          
        $inputs['student_id'] = $student->id;
        $inputs['status'] = 0;
 
        return response([
            'status' => 1,
            'data' => Workspace::create($inputs)
        ], 200);
    }

    public function studentWorkspaceStatus(Request $request)
    {
        $inputs = $request->all();
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;
        
    
        // // get user_id from Student table
        $student =  Student::where('user_id', $user->id)->first();
        $aux = $student->id;

        // // get student_id from Workspace table
        $workspace =  Workspace::where('student_id', $aux)->first();
        if($workspace){
            $result = $workspace->status;

            
        }
        else{

            $result = null;

        }
        
        return response([
            'status' => 1,
            'data' =>  ['workspace_status'=> $result],
           
        ], 200);
        
    }


    public function getWorkspaceByStatus(Request $request, $status_id)
    {
        $result      = [];
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;
        $coordonator =  Coordonator::where('user_id', $user->id)->first();


        // get conrdinator workspaces based on status
        $workspaces =  Workspace::where('coordonator_id', $coordonator->id)->where('status', $status_id)->get();

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

    public function getAcceptedStudents (Request $request)
    {
        $result      = [];
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;
        $coordonator =  Coordonator::where('user_id', $user->id)->first();

        if (!$coordonator || !$coordonator->is_admin) {
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }

        $coordonators =  Coordonator::where("is_admin", 0)->with('user')->get();

        foreach ($coordonators as $key =>  $coordonator) {
            if (isset($coordonator['user'])){
                $coordonator['email'] = $coordonator['user']['email'];
                $coordonator['name'] = $coordonator['user']['name'];
            }else{
                $coordonator['email'] = '';
            } 
            unset($coordonator['user']);

            $workspaces =  Workspace::where('coordonator_id', $coordonator->id)->where('status', 1)->get();
            $students = [];
            if (count($workspaces) > 0) {
                foreach ($workspaces as $key2 => $workspace) {
                    // get student data
                    $student = Student::where('id', $workspace['student_id'])->with('user')->first();
                    if (isset($student['user'])){
                        $students[$key2]['email']        = $student['user']['email'];
                        $students[$key2]['name']         = $student['user']['name'];
                        $students[$key2]['specializare'] = $student['specializare'];
                        $students[$key2]['workspace_id'] = $workspace['id'];
                    }else {
                        continue;
                    }
    
                    // get tema data
                    $tema = Teme::where('id', $workspace['tema_id'])->first();
                    $students[$key2]['tema'] = $tema['title'];
                }
                if (isset($student['user'])){
                    $students[$key2]['email']        = $student['user']['email'];
                    $students[$key2]['name']         = $student['user']['name'];
                    $students[$key2]['specializare'] = $student['specializare'];
                }
                $coordonators[$key]['students'] = $students;
            }else {
                $coordonators[$key]['students'] = [];

            }
        }
        
        return response([
            'status' => 1,
            'data' => $coordonators
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        
        $result      = [];
        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        $user        = $token->tokenable;
        $workspace = Workspace::find($id);

        if ($user->type == 'coordonator') {
            $coordonator =  Coordonator::where('user_id', $user->id)->with('user')->first();
            $student =  Student::where('id', $workspace->student_id)->with('user')->first();

            if ($coordonator->id != $workspace->coordonator_id){
                return response([
                    'status' => 0,
                    'data' => 'permission denied.'
                ], 401);
            }
            
        }else  if ($user->type == 'student') {
            $student =  Student::where('user_id', $user->id)->with('user')->first();
            $coordonator =  Coordonator::where('id', $workspace->coordonator_id)->with('user')->first();

            if ($student->id != $workspace->student_id){
                return response([
                    'status' => 0,
                    'data' => 'permission denied.'
                ], 401);
            }
  
        }
        $tema = Teme::where('id', $workspace->tema_id)->first();

        $workspace['tema title']=$tema['title'];
        $workspace['coordonator']=$coordonator['user']['name'];
        $workspace['student']=$student['user']['name'];


        return response([
            'status' => 1,
            'data' => $workspace
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
        $workspace =  Workspace::where('coordonator_id', $coordonator->id)->first();
        // echo json_encode($workspace->coordonator_id);die;

    
        if(!$workspace || $workspace->coordonator_id != $coordonator->id){
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }

        $statusStudent= Student::where('id',$workspace->student_id)->first();
        
        if ($inputs['status'] == 2 ) {
            
            Teme::whereId( $workspace['tema_id'])->update(['is_taken' => 0]);

        return response([
            'status' => 1,
            'data' => 'lucru este terminată in workspace'   
        ], 200);
        }
        else if ($inputs['status'] == 3 ) {
            Workspace::where('id', $workspace->id)->delete(); 
            Teme::whereId( $workspace['tema_id'])->update(['is_taken' => 0]);
              
        return response([
            'status' => 1,
            'data' => 'Student este respinsa.'   
        ], 200);
 
        }else{
            $workspace =  Workspace::find($id);
            $workspace->update($request->all());
            return response([
                'status' => 1,
                'data' => $workspace
            ], 200);
        }
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
