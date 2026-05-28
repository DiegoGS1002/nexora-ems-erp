<?php

namespace App\Traits;

use App\Support\PerPage;
use Livewire\Attributes\Url;

/**
 * Trait HasPerPage
 *
 * Padroniza o controle de "itens por página" em todos os
 * componentes Livewire do Nexora EMS ERP.
 *
 * Uso:
 *   use App\Traits\HasPerPage;
 *   use HasPerPage;
 *
 * No render():
 *   ->paginate($this->perPage)
 */
trait HasPerPage
{
    /**
     * Itens por página — sincronizado com ?per_page= na URL.
     * O #[Url] permite que Livewire persista o valor no query string.
     */
    #[Url(as: 'per_page', history: true)]
    public int $perPage = PerPage::DEFAULT;

    /**
     * Garante que o tema de paginação seja 'tailwind' (usa vendor/livewire/tailwind.blade.php).
     * Pode ser sobrescrito no componente se necessário.
     */
    protected string $paginationTheme = 'tailwind';

    /**
     * Inicializa perPage a partir da URL ou localStorage-fallback via query string.
     * Chamado automaticamente pelo Livewire via mountHasPerPage().
     */
    public function mountHasPerPage(): void
    {
        $this->perPage = PerPage::sanitize($this->perPage);
    }

    /**
     * Intercepta mudança de perPage para sanitizar e resetar para página 1.
     */
    public function updatingPerPage(mixed $value): void
    {
        $this->perPage = PerPage::sanitize((int) $value);
        $this->resetPage();
    }

    /**
     * Retorna as opções disponíveis de itens por página.
     */
    public function getPerPageOptionsProperty(): array
    {
        return PerPage::OPTIONS;
    }
}
