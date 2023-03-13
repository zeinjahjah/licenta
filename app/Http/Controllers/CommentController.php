<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Comment;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class CommentController extends Controller
{


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
            'data' => Comment::create($inputs)
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
        $event =  Event::find($id);

        if (!$event) {

            return response([
                'status' => 1,
                'data' => 'Nu mai exista acest event'
            ], 200);
        }
        $comments = Comment::where('event_id', $event->id)->get();
        foreach ($comments as $key => $comment) {
            $user_id= $comment->author_id;

            $user = User::where('id',$user_id)->first();
            $user_name= $user->name;

            $comments[$key]['author_name']= $user_name;
        }
       
        $comments = isset($comments) ? $comments : [];

        return response([
            'status' => 1,
            'data' => $comments
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
        $inputs           = $request->all();
        $bearerToken      = $request->bearerToken();
        $token            = PersonalAccessToken::findToken($bearerToken);
        $user             = $token->tokenable;

        $comment =  Comment::find($id);

        if ($comment->author_id != $user->id){
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }

        if(isset($inputs['event_id'])){
            unset($inputs['event_id']);
        }

        $comment->update($inputs);
        return response([
            'status' => 1,
            'data' => $comment
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
        $bearerToken      = $request->bearerToken();
        $token            = PersonalAccessToken::findToken($bearerToken);
        $user             = $token->tokenable;

        $comment =  Comment::find($id);

        if ($comment && $comment->author_id != $user->id){
            return response([
                'status' => 0,
                'data' => 'permission denied.'
            ], 401);
        }


        return response([
            'status' => 1,
            'user' => $user-> id,
            'data' => Comment::destroy($id)
        ], 200);
    }
}
