<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionController extends Controller
{
    public function create_role(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 403);
        }

        try {
            Role::create(['name' => $request->input('role')]);
            return response()->json([
                'error' => false,
                'message' => 'Create new role successfully'
            ], 201);
        }catch (\Exception $exception){
            Log::error("Create role: ".$exception->getMessage());
            return response()->json([
                'error' => true,
                'message' => "Failed create new role"
            ], 403);
        }
    }

    public function create_permission(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ], 403);
        }

        try {
            Permission::create(['name' => $request->input('permission')]);
            return response()->json([
                'error' => false,
                'message' => 'Create permission successfully'
            ], 201);
        }catch (\Exception $exception){
            return response()->json([
                'error' => true,
                'message' => 'Failed to create new permission'
            ], 403);
        }

    }

    public function give_role(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'role' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ], 403);
        }

        try {
            $user = User::query()->where('id', '=', $request->input('user_id'))->firstOrFail();
            $role = Role::query()->where('name', '=', $request->input('role'))->firstOrFail();
            $user->assignRole($role);

            return response()->json([
                'error' => false,
                'message' => "Assign role successfully"
            ], 201);
        }catch (\Exception $exception){
            return response()->json([
                'error' => true,
                'message' => "Assign role failed"
            ], 403);
        }
    }
}
