<div class="nx-list-page nx-form-page--compact nx-catalog-page">

    <div class="nx-form-header" style="margin-bottom:24px;">
        <a href="{{ route('product-categories.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Categorias
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Categoria' : 'Nova Categoria' }}</h1>
                <p class="nx-form-subtitle">{{ $isEditing ? 'Atualize os dados da categoria' : 'Crie uma nova categoria para organizar seus produtos' }}</p>
            </div>
            @if($isEditing)
                <span class="nx-status-badge nx-status-badge--edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editando
                </span>
            @endif
        </div>
    </div>

    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    @if($errors->any())
        <div class="alert-error" style="margin-bottom:16px;">
            <strong>Corrija os erros abaixo:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form wire:submit="save">
        <div class="nx-form-card">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Dados da Categoria</h3>
                    <span class="nx-section-hint">Campos com <span style="color:#EF4444">*</span> são obrigatórios</span>
                </div>

                <div class="nx-form-stack">
                    <div class="nx-field">
                        <label>Nome <span class="nx-required">*</span></label>
                        <input type="text" wire:model.blur="form.name" placeholder="Ex: Eletrônicos, Informática, Alimentos..." maxlength="100">
                        @error('form.name') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-field">
                        <label>Descrição</label>
                        <textarea wire:model.blur="form.description" rows="3" placeholder="Descreva o tipo de produtos desta categoria..."></textarea>
                        @error('form.description') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-form-grid nx-form-grid--2">
                        <div class="nx-field">
                            <label>Cor de Identificação <span class="nx-required">*</span></label>
                            <div class="nx-control-row nx-control-row--color">
                                <input type="color" wire:model.live="form.color" class="nx-color-picker">
                                <input type="text" wire:model.blur="form.color" maxlength="7" placeholder="#6366F1" class="nx-monospace-input">
                            </div>
                            @error('form.color') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="nx-field">
                            <label>Status</label>
                            <label class="nx-toggle-row nx-toggle-row--control">
                                <div class="nx-toggle-info">
                                    <span class="nx-toggle-label">{{ $form->is_active ? 'Ativa' : 'Inativa' }}</span>
                                </div>
                                <span class="nx-switch">
                                    <input type="checkbox" wire:model.live="form.is_active">
                                    <span class="nx-switch-track"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Preview --}}
                @if($form->name)
                    <div class="nx-catalog-preview">
                        <span style="width:14px;height:14px;border-radius:4px;background:{{ $form->color }};display:inline-block;flex-shrink:0;"></span>
                        <span style="font-size:13px;font-weight:600;color:#374151;">{{ $form->name }}</span>
                        @if(!$form->is_active)
                            <span style="font-size:11px;color:#94A3B8;margin-left:auto;">Inativa</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="nx-form-footer nx-form-footer--separated">
            <a href="{{ route('product-categories.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            <button type="submit" wire:loading.attr="disabled" wire:loading.class="nx-btn--loading" class="nx-btn nx-btn-primary">
                <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                {{ $isEditing ? 'Salvar Alterações' : 'Criar Categoria' }}
            </button>
        </div>
    </form>

</div>
