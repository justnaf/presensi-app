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

class InstitutionController extends Controller
{

    /**
     * Terapkan middleware Spatie Permission ke semua metode di controller ini.
     */
    public function __construct()
    {
        $this->middleware('permission:view institutions', ['only' => ['index', 'show']]);
        $this->middleware('permission:create institutions', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit institutions', ['only' => ['edit', 'update']]);
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
     * Menampilkan form untuk membuat institusi baru.
     */
    public function create() {}

    /**
     * Menyimpan institusi yang baru dibuat ke dalam database.
     */
    public function store(StoreInstitutionRequest $request): RedirectResponse
    {
        try {
            Institution::create($request->validated());
            return redirect()->route('admin.institutions.index')
                ->with('success', 'Institusi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.institutions.index')
                ->with('error', 'Gagal menambahkan institusi.');
        }
    }

    /**
     * Menampilkan detail satu institusi.
     */
    public function show(Institution $institution) {}

    /**
     * Menampilkan form untuk mengedit institusi.
     */
    public function edit(Institution $institution) {}

    /**
     * Memperbarui data institusi di database.
     */
    public function update(UpdateInstitutionRequest $request, Institution $institution): RedirectResponse
    {
        try {
            $institution->update($request->validated());
            return redirect()->route('admin.institutions.index')
                ->with('success', 'Institusi berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.institutions.index')
                ->with('error', 'Gagal memperbarui institusi.');
        }
    }

    /**
     * Menghapus institusi dari database.
     */
    public function destroy(Institution $institution): RedirectResponse
    {
        try {
            // Pastikan tidak ada relasi yang menghalangi penghapusan jika perlu
            if ($institution->users()->exists()) { // Contoh pengecekan relasi
                return back()->with('error', 'Gagal menghapus. Institusi masih memiliki pengguna terkait.');
            }
            $institution->delete();
            return redirect()->route('admin.institutions.index')
                ->with('success', 'Institusi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.institutions.index')
                ->with('error', 'Gagal menghapus institusi.');
        }
    }
}
