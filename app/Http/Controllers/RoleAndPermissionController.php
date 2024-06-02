<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionController extends Controller
{
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
            $role = Role::findByName($request->input('role'));
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

    public function give_permissions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'permissions' => 'required|array'
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ],403);
        }

        try {
            $role = Role::findByName($request->input('role'));
            $role->givePermissionTo($request->input('permissions'));

            return response()->json([
                'error' => false,
                'message' => 'Give permissions successfully'
            ], 201);
        }catch (Exception $exception){
            return response()->json([
                'error' => true,
                'message' => "failed to give permissions"
            ], 403);
        }
    }

    public function get_all_role(): JsonResponse
    {
        $roles = Role::all(['name']);
        return response()->json([
            'error' => false,
            'data' => $roles
        ]);
    }

    public function get_all_permissions(): JsonResponse
    {
        $permissions = Permission::all(['name']);
        return response()->json([
            'error' => false,
            'data' => $permissions
        ]);
    }

    public function get_permissions_by_role(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ],403);
        }

        try {
            $permissions = Role::findByName($request->input('role'))->permissions()->select(['name'])->get();
            return response()->json([
                'error' => false,
                'data' => $permissions
            ]);
        }catch (Exception $exception){
            return response()->json([
                'error' => true,
                'message' => "Failed to get data"
            ],403);
        }
    }
}
