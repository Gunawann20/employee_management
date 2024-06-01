<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 403);
        }

        $field = "email";
        if (is_numeric($request->input('username'))){
            var_dump("numeric");
            $field = "phone";
        }

        var_dump($field);

        if (! $token = auth()->attempt([
            $field => $request->input('username'),
            'password' => $request->input('password')
        ])){
            return response()->json([
                'error' => true,
                'message' => 'Email or password is wrong!'
            ], 401);
        }

        return response()->json([
            'error' => false,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users|min:11',
            'password' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 403);
        }

        try {
            User::query()->create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => bcrypt($request->input('password'))
            ]);
            return response()->json([
                'error' => false,
                'message' => 'User create successfully'
            ], 201);
        }catch (\Exception $exception){
            return response()->json([
                'error' => true,
                'message' => "Failed to create new user"
            ], 501);
        }
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
