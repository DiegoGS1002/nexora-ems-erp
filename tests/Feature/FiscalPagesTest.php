<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Fiscal\TipoOperacao\Index as TipoOperacaoIndex;
use App\Livewire\Fiscal\TipoOperacao\Form as TipoOperacaoForm;
use App\Livewire\Fiscal\GrupoTributario\Index as GrupoTributarioIndex;
use App\Livewire\Fiscal\GrupoTributario\Form as GrupoTributarioForm;

class FiscalPagesTest extends TestCase
{
    /**
     * Test that tipo operacao index page loads
     */
    public function test_tipo_operacao_index_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('fiscal.tipo-operacao.index'));

        $response->assertStatus(200);
    }

    /**
     * Test that tipo operacao create page loads
     */
    public function test_tipo_operacao_create_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('fiscal.tipo-operacao.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(TipoOperacaoForm::class);
    }

    /**
     * Test that grupo tributario index page loads
     */
    public function test_grupo_tributario_index_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('fiscal.grupo-tributario.index'));

        $response->assertStatus(200);
    }

    /**
     * Test that grupo tributario create page loads
     */
    public function test_grupo_tributario_create_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('fiscal.grupo-tributario.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(GrupoTributarioForm::class);
    }
}

