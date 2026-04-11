<div class="nx-so-grid-3">
    <div class="nx-field" style="grid-column:1/-1">
        <label>CEP</label>
        <input type="text" wire:model="{{ $prefix }}.zip_code" placeholder="00000-000" maxlength="9">
    </div>
</div>
<div class="nx-so-grid-3">
    <div class="nx-field" style="grid-column:1/3">
        <label>Logradouro</label>
        <input type="text" wire:model="{{ $prefix }}.street" placeholder="Rua, Av, ...">
    </div>
    <div class="nx-field">
        <label>Número</label>
        <input type="text" wire:model="{{ $prefix }}.number" placeholder="123">
    </div>
</div>
<div class="nx-so-grid-3">
    <div class="nx-field">
        <label>Complemento</label>
        <input type="text" wire:model="{{ $prefix }}.complement" placeholder="Apto, Sala...">
    </div>
    <div class="nx-field">
        <label>Bairro</label>
        <input type="text" wire:model="{{ $prefix }}.district" placeholder="Bairro">
    </div>
    <div class="nx-field">
        <label>Cidade</label>
        <input type="text" wire:model="{{ $prefix }}.city" placeholder="Cidade">
    </div>
</div>
<div class="nx-so-grid-3">
    <div class="nx-field">
        <label>Estado</label>
        <select wire:model="{{ $prefix }}.state">
            <option value="">UF</option>
            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                <option value="{{ $uf }}">{{ $uf }}</option>
            @endforeach
        </select>
    </div>
</div>

