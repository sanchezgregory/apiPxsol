<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Models\User;
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

            return response()->json($user, '200');

        } catch(\Exception $e) {
            return response()->json('Something wrong has happened, ' . $e->getMessage(), 500);
        }

    }
}
