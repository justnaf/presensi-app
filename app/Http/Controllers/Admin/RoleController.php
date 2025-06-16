<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Traits\RedirectsWithFlash;

class RoleController extends Controller
{
    use RedirectsWithFlash;

    public function __construct()
    {
        $this->middleware('permission:view roles', ['only' => ['index']]);
        $this->middleware('permission:create roles', ['only' => ['store']]);
        $this->middleware('permission:edit roles', ['only' => ['update']]);
        $this->middleware('permission:delete roles', ['only' => ['destroy']]);
    }

    public function index(Request $request): Response
    {
        $filters = $request->only('search');

        // Eager load permissions untuk efisiensi query
        $roles = Role::with('permissions:name')->when($request->input('search'), function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Roles', [
            'roles' => $roles,
            // Ambil semua nama permission untuk form checkbox
            'permissions' => Permission::orderBy('name')->get()->pluck('name'),
            'filters' => $filters,
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        try {
            $role = Role::create($request->only('name'));
            $role->syncPermissions($request->input('permissions', []));

            // 3. Ganti redirect dengan metode dari Trait
            return $this->redirectSuccess('admin.roles.index', 'Role berhasil ditambahkan.');
        } catch (\Exception $e) {
            // 3. Ganti redirect dengan metode dari Trait
            return $this->redirectError($e, 'Gagal menambahkan role.');
        }
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        try {
            $role->update($request->only('name'));
            $role->syncPermissions($request->input('permissions', []));

            // 3. Ganti redirect dengan metode dari Trait
            return $this->redirectSuccess('admin.roles.index', 'Role berhasil diperbarui.');
        } catch (\Exception $e) {
            // 3. Ganti redirect dengan metode dari Trait
            return $this->redirectError($e, 'Gagal memperbarui role.');
        }
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['Administrator', 'Super Admin'])) {
            return back()->with('error', 'Role Administrator tidak dapat dihapus.');
        }

        try {
            $role->delete();
            // 3. Ganti redirect dengan metode dari Trait
            return $this->redirectSuccess('admin.roles.index', 'Role berhasil dihapus.');
        } catch (\Exception $e) {
            // 3. Ganti redirect dengan metode dari Trait
            return $this->redirectError($e, 'Gagal menghapus role.');
        }
    }
}
