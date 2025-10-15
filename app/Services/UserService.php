<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBranch;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * Crea un usuario (si no existe), le asigna un rol y lo asocia a una sucursal (branch).
     *
     * @param array $data {
     *     @type string  $name    Apellido y Nombre del usuario.
     *     @type string  $email        Correo electrónico del usuario.
     *     @type string  $password     Contraseña del usuario.
     *     @type string  $phone        Teléfono del usuario.
     *     @type string  $role         (Requerido si no se envia roleId)
     *     @type string  $roleId       (Requerido si no se envía el nombre del role)
     *     @type int     $branchId     (Opcional) ID de la sucursal (branch) asociada.
     * }
     *
     * @throws \Exception Si el rol especificado no existe.
     *
     * @return \App\Models\User Usuario creado o actualizado con sus relaciones cargadas.
     */
    public static function insertUserRole(array $data): User
    {
        $roleId = null;

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
                'phone' => $data['phone'] ?? null,
            ]
        );

        // Verificar rol
        if (isset($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            $roleId = $role->id;
        } else if (isset($data['roleId'])) {
            $roleId = $data['roleId'];
        }

        // 2️⃣ Asignar rol (elimina roles anteriores)
        $user->roles()->sync([$roleId]);

        // 3️⃣ Asociar branch si no está ya vinculada
        if (isset($data['branchId'])) {
            $user->userBranches()->firstOrCreate([
                'branch_id' => $data['branchId'],
            ]);
        }

        return $user->load(['roles', 'userBranches']);
    }

    /**
     * Actualiza los datos de un usuario, su rol y su sucursal (opcional).
     *
     * @param \App\Models\User $user Usuario a actualizar.
     * @param array $data {
     *     @type string|null  $name       (Opcional) Apellido y Nombre del usuario.
     *     @type string|null  $email      (Opcional) Correo electrónico del usuario.
     *     @type string|null  $password   (Opcional) Contraseña nueva (opcional).
     *     @type string|null  $phone      (Opcional) Teléfono del usuario.
     *     @type string|null  $role       Nombre del rol (opcional si se envía roleId)
     *     @type string|null  $roleId     ID del rol (opcional si se envía role)
     *     @type int|null     $branchId   ID de la sucursal (opcional)
     * }
     *
     * @throws \Exception Si el rol especificado no existe.
     *
     * @return \App\Models\User Usuario actualizado con relaciones cargadas.
     */
    public static function updateUser(User $user, array $data): User
    {
        // Actualizar datos básicos si vienen
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        if (isset($data['phone'])) {
            $user->phone = $data['phone'];
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Actualizar rol si se envía
        $roleId = null;

        if (isset($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if (!$role) {
                throw new \Exception("El rol '{$data['role']}' no existe.");
            }
            $roleId = $role->id;
        } elseif (isset($data['roleId'])) {
            $roleId = $data['roleId'];
        }

        if ($roleId) {
            // Reemplaza roles anteriores por este
            $user->roles()->sync([$roleId]);
        }

        // Actualizar branch si se envía
        if (isset($data['branchId'])) {
            $user->userBranches()->firstOrCreate([
                'branch_id' => $data['branchId'],
            ]);
        }

        return $user->load(['roles', 'userBranches']);
    }
}
