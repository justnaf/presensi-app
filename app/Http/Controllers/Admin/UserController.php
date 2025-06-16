<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsWithFlash;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use RedirectsWithFlash;

    public function __construct()
    {
        $this->middleware('permission:view users', ['only' => ['index']]);
        $this->middleware('permission:edit users', ['only' => ['update']]); // Untuk edit role
        $this->middleware('permission:delete users', ['only' => ['destroy']]);
    }

    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only('search');

        $users = User::with(['roles:name', 'institutions:name']) // Eager load relasi
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'roles' => Role::all()->pluck('name'), // Kirim semua nama role untuk form edit
            'filters' => $filters,
        ]);
    }

    /**
     * Memperbarui role pengguna.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        try {
            $user->syncRoles($request->input('roles'));
            return $this->redirectSuccess('admin.users.index', 'Role pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Gagal memperbarui role pengguna.');
        }
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Pengaman agar tidak menghapus user sendiri atau admin utama
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        if ($user->hasRole('Administrator')) {
            return back()->with('error', 'Pengguna dengan role Administrator tidak dapat dihapus.');
        }

        try {
            $user->delete();
            return $this->redirectSuccess('admin.users.index', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Gagal menghapus pengguna.');
        }
    }
}
