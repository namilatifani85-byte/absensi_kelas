<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ClassSearchTest extends TestCase
{
    public function test_class_controller_uses_parameterized_search_without_raw_concatenation(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/ClassController.php'));

        // Pastikan tidak ada raw string SQL interpolation yang rentan SQLi
        $this->assertStringNotContainsString('DB::raw', $controllerFile, 'ClassController dilarang menggunakan DB::raw pada pencarian');
        $this->assertStringNotContainsString('whereRaw', $controllerFile, 'ClassController dilarang menggunakan whereRaw dengan interpolasi variabel');

        // Pastikan menggunakan parameter binding Eloquent query builder
        $this->assertStringContainsString("where('name', 'like', \"%{\$searchTerm}%\")", $controllerFile, 'Pencarian nama kelas wajib menggunakan parameterized binding');
        $this->assertStringContainsString("orWhere('code', 'like', \"%{\$searchTerm}%\")", $controllerFile, 'Pencarian kode kelas wajib menggunakan parameterized binding');
    }

    public function test_student_controller_uses_parameterized_search(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/StudentController.php'));

        $this->assertStringNotContainsString('DB::raw', $controllerFile, 'StudentController dilarang menggunakan DB::raw pada pencarian');
        $this->assertStringNotContainsString('whereRaw', $controllerFile, 'StudentController dilarang menggunakan whereRaw dengan interpolasi variabel');

        $this->assertStringContainsString("where('nim', 'like', \"%{\$searchTerm}%\")", $controllerFile);
        $this->assertStringContainsString("orWhere('full_name', 'like', \"%{\$searchTerm}%\")", $controllerFile);
    }

    public function test_role_middleware_enforces_lecturer_authorization(): void
    {
        $middlewareFile = file_get_contents(base_path('app/Http/Middleware/EnsureUserHasRole.php'));

        $this->assertStringContainsString("abort(403", $middlewareFile, 'Middleware role wajib melempar 403 jika otorisasi gagal');
        $this->assertStringContainsString("\$request->user()->role", $middlewareFile, 'Middleware role wajib mencocokkan atribut role pengguna');
    }

    public function test_store_class_request_contains_required_validations(): void
    {
        $requestFile = file_get_contents(base_path('app/Http/Requests/StoreClassRequest.php'));

        $this->assertStringContainsString("'code' => ['required'", $requestFile);
        $this->assertStringContainsString("'unique:classes,code'", $requestFile);
        $this->assertStringContainsString("'day' => ['required'", $requestFile);
        $this->assertStringContainsString("'time_end' => ['required', 'date_format:H:i', 'after:time_start']", $requestFile);
    }
}
