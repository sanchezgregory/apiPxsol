<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Models\User;
use App\Models\UserFile;
use http\Env\Response;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        foreach (User::all() as $user)
        {
            $users[] = $user->getImagesForUserByRaw();
        }

        if (count($users)) return response()->json($users, 200);

        return response()->json('No users found', '200');
    }

    public function uploadFile(UploadRequest $request)
    {
        try {
            $user = User::where('id', '=', $request->user_id)->first();

            if ($request->hasFile('image')) {

                $ext = $request->image->getClientOriginalExtension();
                $imageName = $user->id . "_" . time();
                $imageName = $imageName . "." . $ext;

                $request->file('image')->storeAs('images', $imageName, 'public');

                $userFile = new UserFile();
                $userFile->user_id = $user->id;
                $userFile->file_name = 'images/'.$imageName;
                $userFile->url = "http://localhost:8000/storage/".$userFile->file_name;
                $user->user_files()->save($userFile);

                return response()->json($user->getImagesForUserByRaw($userFile), '201');
            }

        } catch(\Exception $e) {
            return response()->json('Something wrong has happened, ' . $e->getMessage(), 500);
        }

    }

    public function getUser(User $user)
    {
        return response()->json($user->getImagesForUserByRaw(), 200);
    }
}
