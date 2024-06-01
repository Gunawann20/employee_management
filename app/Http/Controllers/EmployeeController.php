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

    public function update_employee(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
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
            $employee = Employee::query()->where('id', '=', $data['id'])->firstOrFail();
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
}
