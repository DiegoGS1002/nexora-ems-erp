# Módulo de Holerite

## 1. Visão Geral

Geração e visualização de contracheques (holerites) baseados na Folha de Pagamento, com PDF para impressão e portal do funcionário.

### Funcionalidades
- Geração automática de holerites
- PDF com layout profissional
- Portal do funcionário
- Histórico de holerites
- Envio por email
- Assinatura digital

---

## 2. Estrutura

### Dados do Holerite
Busca informações da `Payroll` e gera PDF:

```php
public function generateHolerite(Payroll $payroll)
{
    $data = [
        'employee' => $payroll->employee,
        'reference' => $payroll->reference_month . '/' . $payroll->reference_year,
        'earnings' => $payroll->items()->where('item_type', 'earning')->get(),
        'deductions' => $payroll->items()->where('item_type', 'deduction')->get(),
        'gross_salary' => $payroll->gross_salary,
        'total_deductions' => $payroll->total_deductions,
        'net_salary' => $payroll->net_salary,
    ];
    
    $pdf = PDF::loadView('reports.holerite', $data);
    return $pdf->download("holerite_{$employee->id}_{$reference}.pdf");
}
```

---

## 3. Portal do Funcionário

```php
// Rota
Route::middleware('auth')->group(function () {
    Route::get('/meus-holerites', MinhasConsultasHolerite::class);
});

// Livewire
class MinhasConsultasHolerite extends Component
{
    public function render()
    {
        $payrolls = Payroll::where('employee_id', auth()->user()->employee_id)
            ->orderByDesc('reference_year')
            ->orderByDesc('reference_month')
            ->get();
        
        return view('livewire.rh.meus-holerites', compact('payrolls'));
    }
}
```

---

**Caminho**: `app/Http/Controllers/HoleriteController.php`, `app/Livewire/Rh/Holerites.php`

