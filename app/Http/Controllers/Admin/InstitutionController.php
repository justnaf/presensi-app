<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInstitutionRequest;
use App\Http\Requests\Admin\UpdateInstitutionRequest;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\RedirectsWithFlash;

class InstitutionController extends Controller
{
    use RedirectsWithFlash;
    /**
     * Terapkan middleware Spatie Permission ke semua metode di controller ini.
     */
    public function __construct()
    {
        $this->middleware('permission:view institutions', ['only' => ['index']]);
        $this->middleware('permission:create institutions', ['only' => ['store']]);
        $this->middleware('permission:edit institutions', ['only' => ['update']]);
        $this->middleware('permission:delete institutions', ['only' => ['destroy']]);
    }

    /**
     * Menampilkan daftar semua institusi.
     */
    public function index(Request $request): Response
    {
        // Ambil query pencarian dari request
        $filters = $request->only('search');

        $institutions = Institution::query()
            // Terapkan filter jika ada input pencarian
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            // Tambahkan query string ke link pagination
            ->withQueryString();

        return Inertia::render('Admin/Institutions', [
            'institutions' => $institutions,
            'filters' => $filters, // Kirim filter kembali ke frontend
        ]);
    }

    /**
     * Menyimpan institusi yang baru dibuat ke dalam database.
     */
    public function store(StoreInstitutionRequest $request): RedirectResponse
    {
        try {
            Institution::create($request->validated());
            return $this->redirectSuccess('admin.institutions.index', 'Institusi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Gagal menambahkan institusi.');
        }
    }

    /**
     * Memperbarui data institusi di database.
     */
    public function update(UpdateInstitutionRequest $request, Institution $institution): RedirectResponse
    {
        try {
            $institution->update($request->validated());
            return $this->redirectSuccess('admin.institutions.index', 'Institusi berhasil diperbarui.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Gagal memperbarui institusi.');
        }
    }

    /**
     * Menghapus institusi dari database.
     */
    public function destroy(Institution $institution): RedirectResponse
    {
        try {
            if ($institution->users()->exists()) {
                return back()->with('error', 'Gagal menghapus. Institusi masih memiliki pengguna terkait.');
            }
            $institution->delete();
            return $this->redirectSuccess('admin.institutions.index', 'Institusi berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Gagal menghapus institusi.');
        }
    }
}
