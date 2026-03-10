<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminLogsPageTest extends TestCase
{
    use RefreshDatabase;

    private array $createdLogFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->createdLogFiles as $filePath) {
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        parent::tearDown();
    }

    public function test_admin_can_list_log_files(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => 'admin',
        ]);

        $fileName = 'test-log-' . Str::uuid() . '.log';
        $this->createLogFile($fileName, "Linha de teste\n");

        $response = $this->actingAs($admin)->get(route('admin.logs.index'));

        $response->assertOk();
        $response->assertSee('Logs do Sistema');
        $response->assertSee($fileName);
        $response->assertSee(route('admin.logs.view', ['logFile' => $fileName]), false);
        $response->assertSee(route('admin.logs.download', ['logFile' => $fileName]), false);
    }

    public function test_admin_view_shows_only_last_500kb_when_file_is_bigger_than_limit(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => 'admin',
        ]);

        $fileName = 'test-tail-' . Str::uuid() . '.log';
        $content = "INICIO-NAO-DEVE-APARECER\n"
            . str_repeat('A', 600 * 1024)
            . "\nFIM-DEVE-APARECER";
        $this->createLogFile($fileName, $content);

        $response = $this->actingAs($admin)->get(route('admin.logs.view', ['logFile' => $fileName]));

        $response->assertOk();
        $response->assertSee('Conteúdo parcial');
        $response->assertSee('FIM-DEVE-APARECER');
        $response->assertDontSee('INICIO-NAO-DEVE-APARECER');
    }

    public function test_admin_can_download_log_file(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => 'admin',
        ]);

        $fileName = 'test-download-' . Str::uuid() . '.log';
        $this->createLogFile($fileName, "download-content\n");

        $response = $this->actingAs($admin)->get(route('admin.logs.download', ['logFile' => $fileName]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString($fileName, $response->headers->get('content-disposition', ''));
    }

    private function createLogFile(string $fileName, string $content): void
    {
        $directory = storage_path('logs');
        File::ensureDirectoryExists($directory);

        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
        File::put($filePath, $content);

        $this->createdLogFiles[] = $filePath;
    }
}
