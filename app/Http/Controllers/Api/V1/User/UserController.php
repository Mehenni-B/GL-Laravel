<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return response(['message' => 'user data','user' =>  $request->user()], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:3',
        ]);
        if ($validator->fails())
            return response(['errors' => $validator->errors()->all()], 422);

        $user = $request->user();
        $user->update(['name' => $request->name]);
        return response(['message' => 'user updated successfully', 'user' =>  $request->user()], 200);
    }
}
