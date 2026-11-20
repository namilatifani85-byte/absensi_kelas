<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use App\Models\ClassModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller Manajemen Kelas & Pencarian Terproteksi (SEC-08).
 * Menggunakan 100% Prepared Statements / Eloquent Parameter Binding.
 */
class ClassController extends Controller
{
    /**
     * Menampilkan daftar kelas dengan fitur pencarian aman dari SQL Injection.
     * SEC-08 Safe: Parameter $searchTerm dilewatkan via parameter binding PDO bawaan Eloquent.
     */
    public function index(Request $request): View
    {
        $query = ClassModel::with('lecturer')->where('is_active', true);

        if ($request->filled('search')) {
            $searchTerm = trim($request->input('search'));

            // Parameterized Query: payload seperti "' OR '1'='1 --" diperlakukan sebagai teks string biasa
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('room', 'like', "%{$searchTerm}%");
            });
        }

        $classes = $query->orderBy('code')->paginate(10)->withQueryString();

        return view('classes.index', compact('classes'));
    }

    /**
     * Form pembuatan kelas baru (khusus Dosen).
     */
    public function create(): View
    {
        $this->authorizeLecturer();

        return view('classes.create');
    }

    /**
     * Menyimpan data kelas baru ke database.
     */
    public function store(StoreClassRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['lecturer_id'] = auth()->id();
        $data['is_active'] = true;

        ClassModel::create($data);

        return redirect()->route('classes.index')
            ->with('success', 'Kelas perkuliahan baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail informasi kelas dan jadwal.
     */
    public function show(ClassModel $class): View
    {
        $class->load(['lecturer', 'attendances.student']);

        return view('classes.show', compact('class'));
    }

    /**
     * Form penyuntingan data kelas (khusus Dosen).
     */
    public function edit(ClassModel $class): View
    {
        $this->authorizeLecturer();

        return view('classes.edit', compact('class'));
    }

    /**
     * Memperbarui data kelas perkuliahan.
     */
    public function update(UpdateClassRequest $request, ClassModel $class): RedirectResponse
    {
        $this->authorizeLecturer();

        $class->update($request->validated());

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas perkuliahan berhasil diperbarui.');
    }

    /**
     * Menghapus kelas perkuliahan.
     */
    public function destroy(ClassModel $class): RedirectResponse
    {
        $this->authorizeLecturer();

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Kelas perkuliahan berhasil dihapus.');
    }

    /**
     * Proteksi otorisasi internal untuk peran dosen.
     */
    private function authorizeLecturer(): void
    {
        if (! auth()->user() || ! auth()->user()->isLecturer()) {
            abort(403, 'Akses ditolak. Tindakan ini hanya diizinkan untuk akun Dosen.');
        }
    }
}
