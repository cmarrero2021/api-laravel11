<?php

namespace App\Http\Controllers\Api\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $roles = Role::with('permissions')->get();
        $roles=Role::orderBy('name')->get();

        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'sometimes|exists:permissions,name',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $rol = [
            'name' => $request->name,
            'guard_name' => 'api',
        ];
        $role = Role::create($rol);
        if ($request->input('permissions')) {
            $role->givePermissionTo($request->input('permissions'));
        }
        return response()->json(['message' => 'Rol creado con éxito', 'role' => $role->load('permissions')], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $role = Role::findById($id)->load('permissions');
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findById($id);
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:roles,name,'. $id,
            'permissions' => 'sometimes|array',
            'permissions.*' => 'sometimes|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $rol = [
            'name' => $request->name,
            'guard_name' => 'api',
        ];
        $role->update($rol);
        if ($request->input('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        } elseif (!$request->input('permissions') && $role->permissions()->count() > 0) {
            $role->revokePermissionTo($role->permissions->pluck('name'));
        }
        return response()->json(['message' => 'Rol actualizado con éxito', 'role' => $role->load('permissions')]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findById($id);
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        // Verificar si el rol está asignado a algún usuario antes de eliminar
        if ($role->users()->count() > 0) {
            return response()->json(['message' => 'No se puede eliminar el rol porque está asignado'], 409);
        }

        $role->delete();
        return response()->json(['message' => 'Rol eliminado con éxito']);
    }
    public function assignPermissions(Request $request, string $roleId)
    {
        $role = Role::findById($roleId);
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $permissionIds = $request->input('permission_ids');
        if (!$permissionIds ||!is_array($permissionIds)) {
            return response()->json(['message' => 'Se requieren IDs de permisos para asignar'], 422);
        }

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        if ($permissions->count()!== count($permissionIds)) {
            return response()->json(['message' => 'Algunos permisos no fueron encontrados'], 404);
        }

        $role->givePermissionTo($permissions);
        return response()->json(['message' => 'Permisos asignados con éxito al rol', 'role' => $role->load('permissions')]);
    }
    public function removePermissions(Request $request, string $roleId)
    {
        $role = Role::findById($roleId);
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $permissionIds = $request->input('permission_ids');
        if (!$permissionIds ||!is_array($permissionIds)) {
            return response()->json(['message' => 'Se requieren IDs de permisos para eliminar'], 422);
        }

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        if ($permissions->count()!== count($permissionIds)) {
            return response()->json(['message' => 'Algunos permisos no fueron encontrados'], 404);
        }

        $role->revokePermissionTo($permissions);
        return response()->json(['message' => 'Permisos eliminados con éxito del rol', 'role' => $role->load('permissions')]);
    }

}
