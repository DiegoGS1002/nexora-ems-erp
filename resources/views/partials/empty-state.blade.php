{{--
    Empty State Partial — Nexora ERP
    Parâmetros:
      $title       : Título principal  (ex: "Nenhum cliente cadastrado")
      $description : Texto descritivo  (ex: "Adicione o primeiro cliente...")
      $actionLabel : Rótulo do botão   (ex: "Adicionar Cliente")
      $actionRoute : Route do botão    (ex: route('clients.create'))
--}}
<div class="nx-empty-state">

    {{-- Ilustração SVG --}}
    <svg class="nx-empty-illustration" viewBox="0 0 240 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

        {{-- Fundo decorativo --}}
        <ellipse cx="120" cy="175" rx="90" ry="14" fill="#E2E8F0" opacity="0.6"/>

        {{-- Folha / documento base --}}
        <rect x="50" y="28" width="140" height="130" rx="12" fill="#F8FAFC" stroke="#E2E8F0" stroke-width="1.5"/>

        {{-- Detalhe topo do documento --}}
        <rect x="50" y="28" width="140" height="36" rx="12" fill="#EFF6FF"/>
        <rect x="50" y="52" width="140" height="12" fill="#EFF6FF"/>

        {{-- Ícone de tabela no topo --}}
        <rect x="68" y="40" width="18" height="3" rx="1.5" fill="#93C5FD"/>
        <rect x="92" y="40" width="30" height="3" rx="1.5" fill="#BFDBFE"/>
        <rect x="128" y="40" width="22" height="3" rx="1.5" fill="#BFDBFE"/>

        {{-- Linhas de tabela (linhas vazias) --}}
        {{-- Header --}}
        <line x1="65" y1="78" x2="175" y2="78" stroke="#E2E8F0" stroke-width="1"/>

        {{-- Row 1 --}}
        <rect x="65" y="86" width="22" height="7" rx="3.5" fill="#E2E8F0"/>
        <rect x="95" y="86" width="38" height="7" rx="3.5" fill="#E2E8F0"/>
        <rect x="141" y="86" width="28" height="7" rx="3.5" fill="#E2E8F0"/>
        <line x1="65" y1="100" x2="175" y2="100" stroke="#F1F5F9" stroke-width="1"/>

        {{-- Row 2 --}}
        <rect x="65" y="108" width="28" height="7" rx="3.5" fill="#EEF2FF"/>
        <rect x="101" y="108" width="44" height="7" rx="3.5" fill="#EEF2FF"/>
        <rect x="153" y="108" width="18" height="7" rx="3.5" fill="#EEF2FF"/>
        <line x1="65" y1="122" x2="175" y2="122" stroke="#F1F5F9" stroke-width="1"/>

        {{-- Row 3 --}}
        <rect x="65" y="130" width="20" height="7" rx="3.5" fill="#E2E8F0"/>
        <rect x="93" y="130" width="32" height="7" rx="3.5" fill="#E2E8F0"/>
        <rect x="133" y="130" width="24" height="7" rx="3.5" fill="#E2E8F0"/>
        <line x1="65" y1="144" x2="175" y2="144" stroke="#F1F5F9" stroke-width="1"/>

        {{-- Ícone de lupa no centro (indicando "nada encontrado") --}}
        <circle cx="120" cy="116" r="26" fill="white" stroke="#E8EEF5" stroke-width="1.5"/>
        {{-- Lupa outline --}}
        <circle cx="116" cy="112" r="9" stroke="#94A3B8" stroke-width="2" fill="none"/>
        <line x1="123" y1="119" x2="130" y2="126" stroke="#94A3B8" stroke-width="2.5" stroke-linecap="round"/>
        {{-- X dentro da lupa --}}
        <line x1="112.5" y1="108.5" x2="119.5" y2="115.5" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round"/>
        <line x1="119.5" y1="108.5" x2="112.5" y2="115.5" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round"/>

        {{-- Estrelinhas / pontos decorativos --}}
        <circle cx="42" cy="60" r="3" fill="#BFDBFE"/>
        <circle cx="198" cy="80" r="3" fill="#C7D2FE"/>
        <circle cx="38" cy="120" r="2" fill="#DDD6FE"/>
        <circle cx="202" cy="50" r="2" fill="#BFDBFE"/>
        <circle cx="210" cy="140" r="3" fill="#C7D2FE" opacity="0.7"/>
        <circle cx="30" cy="90" r="2.5" fill="#DDD6FE" opacity="0.7"/>
    </svg>

    <h3 class="nx-empty-title">{{ $title ?? 'Nenhum registro encontrado' }}</h3>
    <p class="nx-empty-desc">{{ $description ?? 'Não há dados para exibir no momento.' }}</p>

    @if(!empty($actionRoute))
        <a href="{{ $actionRoute }}" class="nx-btn nx-btn-primary nx-btn-sm nx-empty-action">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            {{ $actionLabel ?? 'Adicionar' }}
        </a>
    @endif
</div>

