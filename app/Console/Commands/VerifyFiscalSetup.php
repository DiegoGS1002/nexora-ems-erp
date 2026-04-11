<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class VerifyFiscalSetup extends Command
{
    protected $signature = 'fiscal:verify';
    protected $description = 'Verify fiscal module setup (tipo operacao and grupo tributario)';

    public function handle()
    {
        $this->info('🔍 Verificando configuração do módulo fiscal...');
        $this->newLine();

        $errors = [];
        $warnings = [];

        // Check routes
        $this->info('📍 Verificando rotas...');
        $routes = [
            'fiscal.tipo-operacao.index',
            'fiscal.tipo-operacao.create',
            'fiscal.tipo-operacao.edit',
            'fiscal.grupo-tributario.index',
            'fiscal.grupo-tributario.create',
            'fiscal.grupo-tributario.edit',
        ];

        foreach ($routes as $routeName) {
            if (Route::has($routeName)) {
                $this->line("  ✓ {$routeName}");
            } else {
                $errors[] = "Rota '{$routeName}' não encontrada";
                $this->error("  ✗ {$routeName} - NÃO ENCONTRADA");
            }
        }
        $this->newLine();

        // Check Livewire components
        $this->info('🔧 Verificando componentes Livewire...');
        $components = [
            'App\Livewire\Fiscal\TipoOperacao\Index',
            'App\Livewire\Fiscal\TipoOperacao\Form',
            'App\Livewire\Fiscal\GrupoTributario\Index',
            'App\Livewire\Fiscal\GrupoTributario\Form',
        ];

        foreach ($components as $component) {
            if (class_exists($component)) {
                $this->line("  ✓ {$component}");
            } else {
                $errors[] = "Componente '{$component}' não encontrado";
                $this->error("  ✗ {$component} - NÃO ENCONTRADO");
            }
        }
        $this->newLine();

        // Check views
        $this->info('👁️  Verificando views...');
        $views = [
            'livewire.fiscal.tipo-operacao.index',
            'livewire.fiscal.tipo-operacao.form',
            'livewire.fiscal.grupo-tributario.index',
            'livewire.fiscal.grupo-tributario.form',
        ];

        foreach ($views as $view) {
            $viewPath = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');
            if (File::exists($viewPath)) {
                $this->line("  ✓ {$view}");
            } else {
                $errors[] = "View '{$view}' não encontrada";
                $this->error("  ✗ {$view} - NÃO ENCONTRADA");
            }
        }
        $this->newLine();

        // Check models
        $this->info('📦 Verificando models...');
        $models = [
            'App\Models\TipoOperacaoFiscal',
            'App\Models\GrupoTributario',
        ];

        foreach ($models as $model) {
            if (class_exists($model)) {
                $this->line("  ✓ {$model}");
            } else {
                $errors[] = "Model '{$model}' não encontrado";
                $this->error("  ✗ {$model} - NÃO ENCONTRADO");
            }
        }
        $this->newLine();

        // Check Enums
        $this->info('🏷️  Verificando Enums...');
        $enums = [
            'App\Enums\TipoMovimentoFiscal',
            'App\Enums\RegimeTributario',
            'App\Enums\IpiModalidade',
            'App\Enums\IcmsModalidadeBC',
        ];

        foreach ($enums as $enum) {
            if (enum_exists($enum)) {
                $this->line("  ✓ {$enum}");
            } else {
                $errors[] = "Enum '{$enum}' não encontrado";
                $this->error("  ✗ {$enum} - NÃO ENCONTRADO");
            }
        }
        $this->newLine();

        // Check Form objects
        $this->info('📝 Verificando Form objects...');
        $forms = [
            'App\Livewire\Forms\TipoOperacaoFiscalForm',
            'App\Livewire\Forms\GrupoTributarioForm',
        ];

        foreach ($forms as $form) {
            if (class_exists($form)) {
                $this->line("  ✓ {$form}");
            } else {
                $errors[] = "Form '{$form}' não encontrado";
                $this->error("  ✗ {$form} - NÃO ENCONTRADO");
            }
        }
        $this->newLine();

        // Summary
        if (count($errors) === 0 && count($warnings) === 0) {
            $this->info('✅ Tudo configurado corretamente!');
            $this->newLine();
            $this->info('📌 URLs de acesso:');
            $this->line('  • Tipos de Operação: ' . url('/fiscal/tipos-operacao'));
            $this->line('  • Criar Tipo de Operação: ' . url('/fiscal/tipos-operacao/create'));
            $this->line('  • Grupos Tributários: ' . url('/fiscal/grupos-tributarios'));
            $this->line('  • Criar Grupo Tributário: ' . url('/fiscal/grupos-tributarios/create'));
            return 0;
        } else {
            $this->error('❌ Foram encontrados problemas:');
            foreach ($errors as $error) {
                $this->error("  • {$error}");
            }
            foreach ($warnings as $warning) {
                $this->warn("  ⚠ {$warning}");
            }
            return 1;
        }
    }
}

