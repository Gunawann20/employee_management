<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function Laravel\Prompts\password;

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

    public function forget_password(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 403);
        }

        $token = Str::random(5);

        try {
            $exists = DB::table('password_resets')->where('email', '=', $request->input('email'))->exists();
            if (! $exists){
                DB::table('password_resets')->insert([
                    'email' => $request->input('email'),
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
            }else{
                return response()->json([
                    'error' => false,
                    'message' => 'We have e-mailed your token reset password'
                ], 201);
            }

            Mail::send('email.forgotPassword', ['token' => $token], function ($message) use ($request){
                $message->to($request->input('email'));
                $message->subject("Reset password");
            });

            return response()->json([
                'error' => false,
                'message' => 'We have e-mailed your token reset password'
            ], 201);
        }catch (\Exception $exception){
            Log::error("Send email: ". $exception->getMessage());
            return response()->json([
                'error' => true,
                'message' => "Failed"
            ], 403);
        }
    }

    public function submit_reset_password(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 403);
        }

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->input('email'),
                'token' => $request->input('token')
            ])->first();

        if (! $updatePassword){
            return response()->json([
                'error' => true,
                'message' => "invalid token"
            ], 403);
        }

        User::query()->where('email', '=', $request->input('email'))
            ->update([
                'password' => bcrypt($request->input('password'))
            ]);

        DB::table('password_resets')->where(['email' => $request->input('email')])->delete();

        return response()->json([
            'error' => false,
            'message' => 'password has been changed'
        ],201);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
