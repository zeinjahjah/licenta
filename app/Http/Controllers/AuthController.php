<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Coordonator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;


class AuthController extends Controller
{
    public function register(Request $request)
    {

        $fields = $request->validate([
            'name' => 'required | string | min:3',
            'email' => 'required | string | unique:users,email',
            'password' => 'required | string | confirmed',
            // 'phone'      => 'string',
            // 'address'      => 'string',
            // 'facultatea'   => 'string',
            'specializare'   => 'required_if:type,==,0',
            'is_admin'  => 'integer',
            'type'      => 'required | integer'
        ]);

        $fields['phone']='';
        $fields['address']='';
        $fields['facultatea']='';
        
        if ($fields['type'] == 0) {
            $type = 'student';
            $fields['specializare'] = $request->get('specializare') ? $request->get('specializare') : '';
        } else if ($fields['type'] == 1) {
            $type = 'coordonator';
            $fields['specializare']='';
        }else if ($fields['type'] == 2) {
            $type = 'admin';
            $fields['specializare']='';
        }

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'type' => $type
        ]); 

        if($fields['type'] == '0'){
            $student = Student::create([
                'user_id' => $user->id,
                'address' => $fields['address'],
                'phone' => $fields['phone'],
                'facultatea' => $fields['facultatea'],
                'specializare' => $fields['specializare']
                ]);
                $user['student_id']= $student['id'];
                
        }else if ($fields['type'] == '1' || $fields['type'] == '2') {
            $corrdonator = Coordonator::create([
                'user_id' => $user->id,
                'address' => $fields['address'],
                'phone' => $fields['phone'],
                'facultatea' => $fields['facultatea'],
                'specializare' => $fields['specializare'],
                'is_admin' => $fields['is_admin']     
            ]);
            $user['corrdonator_id']= $corrdonator['id'];
        }

        $response = $user;
        $token = $user->createToken('mapptoken')->plainTextToken;
        $response['token'] = $token;
        $response['status'] = 1;

        return response($response, 201);
    }

    public function logout(Request $request)
    {

        $bearerToken = $request->bearerToken();
        $token       = PersonalAccessToken::findToken($bearerToken);
        PersonalAccessToken::destroy($token->id);
        return [
            'status' => 1,
            'message' => 'Logged out'
        ];
    }


    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required | string ',
            'password' => 'required | string '
        ]);

        // check email
        $user = User::where('email', $fields['email'])->with('student', 'coordonator')->first();

        // check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'status' => 0,
                'message' => 'Bad Creds'

            ], 401);
        }

        if( $user->type == 'student'){
            
        }
        $token = $user->createToken('mapptoken')->plainTextToken;

        $response = $user;
        $response['token'] = $token;
        $response['status'] = 1;

        return response($response, 201);
    }
}
