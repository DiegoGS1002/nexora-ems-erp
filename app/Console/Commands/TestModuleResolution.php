<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ModulePageController;
use Illuminate\Support\Facades\Route;

class TestModuleResolution extends Command
{
    protected $signature = 'test:module-resolution';
    protected $description = 'Test how module items are resolved';

    public function handle()
    {
        $this->info('🔍 Testing Module Resolution for Fiscal Module');
        $this->newLine();

        // Get all modules
        $modules = ModulePageController::allModules();
        $fiscal = $modules['fiscal'] ?? null;

        if (!$fiscal) {
            $this->error('Fiscal module not found!');
            return 1;
        }

        $this->info('📦 Fiscal Module Items:');
        $this->newLine();

        // Use reflection to access private method
        $reflection = new \ReflectionClass(ModulePageController::class);
        $method = $reflection->getMethod('resolveItems');
        $method->setAccessible(true);

        $resolvedItems = $method->invoke(null, $fiscal);

        foreach ($resolvedItems as $item) {
            $routeName = $item['route'] ?? 'N/A';
            $available = $item['available'] ? '✅ DISPONÍVEL' : '❌ INDISPONÍVEL';
            $href = $item['href'] ?? 'N/A';

            $this->line("📄 {$item['title']}");
            $this->line("   Route: {$routeName}");
            $this->line("   Status: {$available}");
            $this->line("   Link: {$href}");

            // Check if route exists
            if (is_string($routeName) && $routeName !== '') {
                $exists = Route::has($routeName);
                $this->line("   Route::has(): " . ($exists ? '✅ TRUE' : '❌ FALSE'));
            }

            $this->newLine();
        }

        return 0;
    }
}

