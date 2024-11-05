<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('ver usuarios')) {
            return response()->json(['message' => 'No tiene permiso para ver usuarios'], 403);
        }
        $usuarios = User::orderByRaw("CASE WHEN name = 'Admin' THEN 0 ELSE 1 END, name ASC")->get();
        return response()->json(['usuarios' => $usuarios], 200);
    }
    public function show($id)
    {
        if (!auth()->user()->hasPermissionTo('ver usuarios')) {
            return response()->json(['message' => 'No tiene permiso para ver usuarios'], 403);
        }
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json(['usuario' => $usuario], 200);
        // return response()->json(['message' => 'Usuario eliminado con éxito'], 200);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('crear usuarios')) {
            return response()->json(['message' => 'No tiene permiso para crear usuarios'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $usuario = User::create($request->all());
        $usuario->assignRole('usuario');
        $this->sendVerificationEmail($usuario);
        return response()->json(['message' => 'Usuario creado con éxito. Verifica tu correo electrónico para activar tu cuenta.', 'usuario' => $usuario], 201);
    }
    private function generateVerificationLink($user)
    {
        $verificationUrl = URL::signedRoute('auth.verify-email', ['id' => $user->id]);
        return $verificationUrl;
    }

    private function sendVerificationEmail($user)
    {
        $verificationUrl = $this->generateVerificationLink($user);
        $mailData = [
            'nombre' => $user->name,
            'title' => 'Verifica tu correo electrónico',
            'url' => $verificationUrl,
        ];
        Mail::to($user->email)->send(new \App\Mail\VerifyEmail($mailData));
    }
    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('editar usuarios')) {
            return response()->json(['message' => 'No tiene permiso para editar usuarios'], 403);
        }
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:users,email,'. $id,
            'password' => 'sometimes|required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $usuario->update($request->all());
        return response()->json(['message' => 'Usuario actualizado con éxito', 'usuario' => $usuario], 200);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasPermissionTo('eliminar usuarios')) {
            return response()->json(['message' => 'No tiene permiso para eliminar usuarios'], 403);
        }
        if ($id == 1) {
            return response()->json(['message' => 'No se puede eliminar el usuario admin'], 403);
        }
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado con éxito'], 200);
    }
    ////////Roles/////////
    public function assignRoles(Request $request)
    {
        $userIds = $request->input('user_ids');
        $roleIds = $request->input('role_ids');
        if (!$userIds ||!is_array($userIds) ||!$roleIds ||!is_array($roleIds)) {
            return response()->json(['message' => 'Se requieren IDs de usuarios y roles para asignar'], 422);
        }
        $users = User::whereIn('id', $userIds)->get();
        if ($users->count()!== count($userIds)) {
            return response()->json(['message' => 'Algunos usuarios no fueron encontrados'], 404);
        }
        $roles = Role::whereIn('id', $roleIds)->get();
        if ($roles->count()!== count($roleIds)) {
            return response()->json(['message' => 'Algunos roles no fueron encontrados'], 404);
        }
        foreach ($users as $user) {
            $user->assignRole($roles);
        }
        return response()->json(['message' => 'Roles asignados con éxito a usuarios']);
    }
    public function removeRoles(Request $request)
    {
        $userIds = $request->input('user_ids');
        $roleIdsToRemove = $request->input('role_ids');
        if (!$userIds ||!is_array($userIds) ||!$roleIdsToRemove ||!is_array($roleIdsToRemove)) {
            return response()->json(['message' => 'Se requieren IDs de usuarios y roles para remover'], 422);
        }
        $users = User::whereIn('id', $userIds)->get();
        if ($users->count()!== count($userIds)) {
            return response()->json(['message' => 'Algunos usuarios no fueron encontrados'], 404);
        }
        $protectedUserId = 1;
        $protectedRoleId = 3;
        foreach ($users as $user) {
            $rolesToRemoveForUser = $roleIdsToRemove;
            if ($user->id === $protectedUserId) {
                $rolesToRemoveForUser = array_diff($rolesToRemoveForUser, [$protectedRoleId]);
            }
            $userRoles = $user->roles()->pluck('id');
            $rolesToKeep = $userRoles->diff($rolesToRemoveForUser);
            $user->syncRoles($rolesToKeep);
        }
        return response()->json(['message' => 'Roles removidos con éxito de usuarios']);
    }
}
