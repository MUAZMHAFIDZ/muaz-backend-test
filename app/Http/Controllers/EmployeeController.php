<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('division:id,name');

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        $employees = $query->paginate(10);

        $formatted = collect($employees->items())->map(function ($employee) {
            return [
                'id' => $employee->id,
                'image' => $employee->image,
                'name' => $employee->name,
                'phone' => $employee->phone,
                'position' => $employee->position,
                'division' => [
                    'id' => $employee->division->id ?? null,
                    'name' => $employee->division->name ?? null,
                ],
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data karyawan berhasil diambil.',
            'data' => [
                'employees' => $formatted,
            ],
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
        ]);
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            $filename = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('employee', $filename);
            }

            Employee::create([
                'image' => $filename,
                'name' => $request->name,
                'phone' => $request->phone,
                'division_id' => $request->division,
                'position' => $request->position,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data karyawan berhasil ditambahkan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan data karyawan.',
            ], 500);
        }
    }
    
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            if ($request->hasFile('image')) {
                if ($employee->image && Storage::exists('employee/' . $employee->image)) {
                    Storage::delete('employee/' . $employee->image);
                }

                $imageName = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->storeAs('employee', $imageName);
                $employee->image = $imageName;
            }

            $employee->name = $request->name;
            $employee->phone = $request->phone;
            $employee->division_id = $request->division;
            $employee->position = $request->position;

            $employee->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data karyawan berhasil diupdate.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal update data karyawan.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            if ($employee->image && Storage::disk('public')->exists('employee/' . $employee->image)) {
                Storage::disk('public')->delete('employee/' . $employee->image);
            }

            $employee->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data karyawan berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data karyawan.',
            ], 500);
        }
    }
}
