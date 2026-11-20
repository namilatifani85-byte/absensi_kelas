<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller Manajemen Data Mahasiswa & Pencarian Terproteksi (SEC-08).
 */
class StudentController extends Controller
{
    /**
     * Menampilkan daftar mahasiswa dengan filter pencarian aman.
     * SEC-08 Safe: Menggunakan PDO parameter binding Eloquent.
     */
    public function index(Request $request): View
    {
        $query = Student::with('user');

        if ($request->filled('search')) {
            $searchTerm = trim($request->input('search'));

            // Parameterized Query: tahan terhadap payload SQLi
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nim', 'like', "%{$searchTerm}%")
                  ->orWhere('full_name', 'like', "%{$searchTerm}%")
                  ->orWhere('study_program', 'like', "%{$searchTerm}%");
            });
        }

        $students = $query->orderBy('nim')->paginate(10)->withQueryString();

        return view('students.index', compact('students'));
    }

    /**
     * Menampilkan profil detail mahasiswa.
     */
    public function show(Student $student): View
    {
        $student->load(['user', 'attendances.classModel']);

        return view('students.show', compact('student'));
    }
}
