<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ViewRenderTest extends TestCase
{
    public function test_all_blade_template_files_exist(): void
    {
        $views = [
            'resources/views/layouts/app.blade.php',
            'resources/views/layouts/guest.blade.php',
            'resources/views/components/status-badge.blade.php',
            'resources/views/dashboard.blade.php',
            'resources/views/welcome.blade.php',
        ];

        foreach ($views as $view) {
            $path = base_path($view);
            $this->assertFileExists($path, "File view {$view} harus ada.");
        }
    }

    public function test_app_layout_contains_csrf_meta_and_navbar(): void
    {
        $content = file_get_contents(base_path('resources/views/layouts/app.blade.php'));
        $this->assertStringContainsString('csrf-token', $content);
        $this->assertStringContainsString('Absensi', $content);
        $this->assertStringContainsString('x-heroicon-o-', $content);
    }

    public function test_status_badge_contains_four_wcag_aaa_statuses(): void
    {
        $content = file_get_contents(base_path('resources/views/components/status-badge.blade.php'));
        $this->assertStringContainsString('badge-present', $content);
        $this->assertStringContainsString('badge-permit', $content);
        $this->assertStringContainsString('badge-sick', $content);
        $this->assertStringContainsString('badge-absent', $content);
    }
}
