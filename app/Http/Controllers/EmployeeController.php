<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function create_employee(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'image' => 'required|image|mimes:jpeg, png, jpg, gif|max:1024',
            'name' => 'required',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date',
            'salary' => 'required',
            'work_place' => 'required',
            'position' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 403);
        }

        $data = $validator->getData();

        $image = $request->file('image');
        $filename = Str::random(10) . $image->getClientOriginalName();
        $storePubliclyAs = $image->storePubliclyAs('images', $filename);

        try {
            User::query()->where("id", '=', $data["user_id"])->firstOrFail();
            Employee::query()->create([
                'user_id' => $data['user_id'],
                'image' => $filename,
                'name' => $data['name'],
                'gender' => $data['gender'],
                'birthdate' => Date::createFromFormat('Y-m-d', $data['birthdate']),
                'salary' => $data['salary'],
                'work_place' => $data['work_place'],
                'position' => $data['position']
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Create employee successfully'
            ], 201);
        }catch (\Exception $exception){
            if (isset($storePubliclyAs)){
                Storage::delete($storePubliclyAs);
            }
            return response()->json([
                'error' => true,
                'message' => 'Failed create employee'
            ], 403);
        }
    }

    public function update_employee_by_id(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg, png, jpg, gif|max:1024',
            'name' => 'required',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date',
            'salary' => 'required',
            'work_place' => 'required',
            'position' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 403);
        }

        $data = $validator->getData();


        if (isset($data['image'])){
            $image = $request->file('image');
            $filename = Str::random(10) . $image->getClientOriginalName();
            $storePubliclyAs = $image->storePubliclyAs('images', $filename);
        }

        try {
            $employee = Employee::query()->where('id', '=', $id)->firstOrFail();
            if (isset($filename)){
                $employee->update([
                    'image' => $filename,
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'birthdate' => Date::createFromFormat('Y-m-d', $data['birthdate']),
                    'salary' => $data['salary'],
                    'work_place' => $data['work_place'],
                    'position' => $data['position']
                ]);
            }else{
                $employee->update([
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'birthdate' => Date::createFromFormat('Y-m-d', $data['birthdate']),
                    'salary' => $data['salary'],
                    'work_place' => $data['work_place'],
                    'position' => $data['position']
                ]);
            }

            return response()->json([
                'error' => false,
                'message' => 'Update employee successfully'
            ], 201);
        }catch (\Exception $exception){
            if (isset($storePubliclyAs)){
                Storage::delete($storePubliclyAs);
            }
            return response()->json([
                'error' => true,
                'message' => 'Failed update employee'
            ], 403);
        }
    }

    public function list_employee(): JsonResponse
    {
        try {
            return response()->json([
                'error' => false,
                'data' => Employee::query()->select()->get()
            ]);
        }catch (\Exception $exception){
            Log::error('List employee: '. $exception->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'failed to get data'
            ], 403);
        }
    }

    public function get_employee(): JsonResponse
    {
        try {
            $sessionUserId = auth()->id();
            $employee = Employee::query()->where('user_id', '=', $sessionUserId)->firstOrFail();
            return response()->json([
                'error' => false,
                'data' => $employee
            ]);
        }catch (\Exception $exception){
            return response()->json([
                'error' => 'true',
                'message' => 'Failed to get data'
            ], 403);
        }
    }

    public function get_by_id(string $id): JsonResponse
    {
        try {
            $employee = Employee::query()->where('id', '=', $id)->firstOrFail();
            return response()->json([
                'error' => false,
                'data' => $employee
            ]);
        }catch (\Exception $exception){
            return response()->json([
                'error' => 'true',
                'message' => 'Failed to get data'
            ], 403);
        }
    }

    public function update_employee(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg, png, jpg, gif|max:1024',
            'name' => 'required',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date'
        ]);

        if ($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 403);
        }

        $data = $request->only('image', 'name', 'gender', 'birthdate');


        if (isset($data['image'])){
            $image = $request->file('image');
            $filename = Str::random(10) . $image->getClientOriginalName();
            $storePubliclyAs = $image->storePubliclyAs('images', $filename);
        }

        try {
            $sessionUserId = auth()->id();
            $employee = Employee::query()->where('user_id', '=', $sessionUserId)->firstOrFail();
            if (isset($filename)){
                $employee->update([
                    'image' => $filename,
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'birthdate' => Date::createFromFormat('Y-m-d', $data['birthdate'])
                ]);
            }else{
                $employee->update([
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'birthdate' => Date::createFromFormat('Y-m-d', $data['birthdate'])
                ]);
            }

            return response()->json([
                'error' => false,
                'message' => 'Update employee successfully'
            ], 201);
        }catch (\Exception $exception){
            Log::error('update employee :'. $exception->getMessage());
            if (isset($storePubliclyAs)){
                Storage::delete($storePubliclyAs);
            }
            return response()->json([
                'error' => true,
                'message' => 'Failed update employee'
            ], 403);
        }
    }

    public function delete_employee($id): JsonResponse
    {
        try {
            $user = Employee::query()->where('id', '=', $id)->firstOrFail();
            Storage::delete("images/". $user['image']);
            $user->delete();

            return response()->json([
                "error" => true,
                "message" => "Delete employee successfully"
            ], 201);
        }catch (\Exception $exception){
            return response()->json([
                "error" => true,
                "message" => "Delete employee failed"
            ], 403);
        }
    }
}
