<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\File;
use Laravel\Sanctum\PersonalAccessToken;
use Storage;

class AttachmentController extends Controller
{

  public function getEventFile ($eventId){
    
    $event = Event::find($eventId);

    if (!$event) {

      return response([
          'status' => 1,
          'data' => 'Nu mai exista acest event'
      ], 200);
    }

    $fileModel = File::where('event_id', $eventId)->first();

    $file = Storage::disk('public')->get('uploads/' . $fileModel->file_name);

    return response($file, 200);
  }

  public function RemoveFile(Request $req, $fileId){

    $bearerToken      = $req->bearerToken();
    $token            = PersonalAccessToken::findToken($bearerToken);
    $user             = $token->tokenable;

    $file = File::find($fileId);

    if ($file && $file->author_id != $user->id){
        return response([
            'status' => 0,
            'data' => 'permission denied.'
        ], 401);
    }


    if($file){
      Storage::disk('public')->delete('uploads/' . $file->file_name);

      return response([
          'status' => 1,
          'data' => File::destroy($fileId)
      ], 200);
    }else{
      return response([
        'status' => 1,
        'data' => 'Nu mai exista acest fisier'
    ], 200);
    }

  }

  public function fileUpload(Request $req){

        $req->validate([
        'file' => 'required|mimes:csv,txt,xlx,xls,pdf,zip|max:2048'
        ]);

        $event_id = $req->event_id;
        $event = Event::find($event_id);
        
        if (!$event) {

          return response([
              'status' => 1,
              'data' => 'Nu mai exista acest event'
          ], 200);
        }

        $bearerToken      = $req->bearerToken();
        $token            = PersonalAccessToken::findToken($bearerToken);
        $user             = $token->tokenable;
        $author_id = $user->id;
        
        if ($user->type == 'student') {
            $author_type = 'student';
        } else if ($user->type = 'coordonator') {
            $author_type = 'coordonator';
        }

        $fileModel = new File;
        if($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->file_name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/' . $filePath;
            $fileModel->author_id = $author_id;
            $fileModel->author_type = $author_type;
            $fileModel->event_id = $event_id;
            $fileModel->save();
            return response([
              'status' => 1,
              'data' => $fileModel
          ], 200);
        }
   }
}