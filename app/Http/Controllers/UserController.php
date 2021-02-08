<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Models\User;
use App\Models\UserFile;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all(), 200);
    }

    public function uploadFile(UploadRequest $request)
    {
        try {
            $user = User::where('id', '=', $request->user_id)->first();
            if (!$user) return response()->json('User not found', 404);

            if ($request->hasFile('image')) {

                $ext = $request->image->getClientOriginalExtension();
                $imageName = $user->id . "_" . time();
                $imageName = $imageName . "." . $ext;

                $request->file('image')->storeAs('images', $imageName, 'public');

                $userFile = new UserFile();
                $userFile->user_id = $user->id;
                $userFile->file_name = 'images/'.$imageName;
                $userFile->url = "url";
                $user->user_files()->save($userFile);

                $data = [
                    'user_id' => $user->id,
                    'uploaded_file' => [
                        'id' => $userFile->id,
                        'file_name' => $userFile->file_name,
                        'url' => $userFile->url,
                        'created_at' => $userFile->created_at,
                    ],
                    'files' => $user->getImagesForUserByRaw()
                ];

                return response()->json($data, '201');
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
