<?php

namespace App\Http\Controllers\Api\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $permissions = Permission::all();
        $permissions=Permission::orderBy('id')->get();
        return response()->json($permissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $permiso = [
            'name' => $request->name,
            'guard_name' => 'api',
        ];
        $permission = Permission::create($permiso);
        return response()->json(['message' => 'Permiso creado con éxito', 'permission' => $permission], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permission = Permission::findById($id);
        if (!$permission) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }
        return response()->json($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::findById($id);
        if (!$permission) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:permissions,name,'. $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        $permiso = [
            'name' => $request->name,
            'guard_name' => 'api',
        ];
        $permission->update($permiso);
        // $permission->update($request->all());

        return response()->json(['message' => 'Permiso actualizado con éxito', 'permission' => $permission]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findById($id);
        if (!$permission) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }

        // Verificar si el permiso está asignado a algún rol o usuario antes de eliminar
        if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
            return response()->json(['message' => 'No se puede eliminar el permiso porque está asignado'], 409);
        }

        $permission->delete();
        return response()->json(['message' => 'Permiso eliminado con éxito']);
    }
}
