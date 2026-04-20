<?php

namespace App\Livewire\Fiscal;

use App\Enums\PayableStatus;
use App\Models\AccountPayable;
use App\Models\FiscalInvoiceIn;
use App\Models\FiscalInvoiceItemIn;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Notas Fiscais de Entrada')]
class NotaFiscalEntrada extends Component
{
    use WithPagination, WithFileUploads;

    /* ─── Filters ─── */
    public string $search       = '';
    public string $filterStatus = '';

    /* ─── Modal State ─── */
    public bool   $showModal    = false;
    public bool   $showDetail   = false;
    public bool   $showXmlModal = false;
    public string $activeTab    = 'cabecalho';
    public ?int   $editingId    = null;
    public ?int   $viewingId    = null;

    /* ─── XML Upload ─── */
    public $xmlFile = null;

    /* ══════════════════════════════════════════
       FORM — Cabeçalho
    ══════════════════════════════════════════ */
    public string $supplier_id        = '';
    public string $supplier_name      = '';
    public string $supplier_cnpj      = '';
    public string $supplier_ie        = '';
    public string $purchase_order_id  = '';
    public string $invoice_number     = '';
    public string $series             = '1';
    public string $access_key         = '';
    public string $doc_type           = 'nfe';
    public string $issue_date         = '';
    public string $entry_date         = '';
    public string $cfop               = '';
    public string $operation_nature   = '';
    public string $status             = 'digitada';
    public string $notes              = '';

    /* ══════════════════════════════════════════
       FORM — Itens
    ══════════════════════════════════════════ */
    public array  $invoiceItems  = [];
    public string $searchProduct = '';
    public array  $searchResults = [];

    /* ══════════════════════════════════════════
       FORM — Totais
    ══════════════════════════════════════════ */
    public string $products_total  = '0';
    public string $shipping_total  = '0';
    public string $insurance_total = '0';
    public string $other_expenses  = '0';
    public string $discount_total  = '0';
    public string $tax_total       = '0';
    public string $total_value     = '0';

    /* ─── Computed ─── */

    #[Computed]
    public function invoices()
    {
        return FiscalInvoiceIn::with(['supplier'])
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('invoice_number', 'like', '%' . $this->search . '%')
                   ->orWhere('supplier_name', 'like', '%' . $this->search . '%')
                   ->orWhere('access_key', 'like', '%' . $this->search . '%');
            }))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'       => FiscalInvoiceIn::count(),
            'digitada'    => FiscalInvoiceIn::where('status', 'digitada')->count(),
            'aguardando'  => FiscalInvoiceIn::where('status', 'aguardando_conferencia')->count(),
            'escriturada' => FiscalInvoiceIn::where('status', 'escriturada')->count(),
            'cancelada'   => FiscalInvoiceIn::where('status', 'cancelada')->count(),
            'valor_total' => FiscalInvoiceIn::whereNotIn('status', ['cancelada'])->sum('total_value'),
        ];
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::select('id', 'name', 'social_name', 'taxNumber')->orderBy('social_name')->get();
    }

    #[Computed]
    public function purchaseOrders()
    {
        return PurchaseOrder::with('supplier')
            ->whereIn('status', ['aprovado', 'recebido_parcial'])
            ->latest()
            ->get();
    }

    /* ─── Lifecycle ─── */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    /* ─── Modal ─── */

    public function openModal(): void
    {
        $this->resetForm();
        $this->entry_date = now()->format('Y-m-d');
        $this->showModal  = true;
    }

    public function openEdit(int $id): void
    {
        $invoice = FiscalInvoiceIn::with('items')->findOrFail($id);

        $this->editingId        = $id;
        $this->supplier_id      = (string) ($invoice->supplier_id ?? '');
        $this->supplier_name    = $invoice->supplier_name ?? '';
        $this->supplier_cnpj    = $invoice->supplier_cnpj ?? '';
        $this->supplier_ie      = $invoice->supplier_ie ?? '';
        $this->purchase_order_id= (string) ($invoice->purchase_order_id ?? '');
        $this->invoice_number   = $invoice->invoice_number;
        $this->series           = $invoice->series;
        $this->access_key       = $invoice->access_key ?? '';
        $this->doc_type         = $invoice->doc_type;
        $this->issue_date       = $invoice->issue_date?->format('Y-m-d') ?? '';
        $this->entry_date       = $invoice->entry_date?->format('Y-m-d') ?? '';
        $this->cfop             = $invoice->cfop ?? '';
        $this->operation_nature = $invoice->operation_nature ?? '';
        $this->status           = $invoice->status;
        $this->notes            = $invoice->notes ?? '';
        $this->products_total   = (string) $invoice->products_total;
        $this->shipping_total   = (string) $invoice->shipping_total;
        $this->insurance_total  = (string) $invoice->insurance_total;
        $this->other_expenses   = (string) $invoice->other_expenses;
        $this->discount_total   = (string) $invoice->discount_total;
        $this->tax_total        = (string) $invoice->tax_total;
        $this->total_value      = (string) $invoice->total_value;

        $this->invoiceItems = $invoice->items->map(fn($item) => [
            'id'           => $item->id,
            'product_id'   => $item->product_id ?? '',
            'product_code' => $item->product_code ?? '',
            'product_name' => $item->product_name,
            'ncm'          => $item->ncm ?? '',
            'cfop'         => $item->cfop ?? '',
            'unit'         => $item->unit,
            'quantity'     => (string) $item->quantity,
            'unit_price'   => (string) $item->unit_price,
            'total_price'  => (string) $item->total_price,
            'icms_value'   => (string) $item->icms_value,
            'ipi_value'    => (string) $item->ipi_value,
            'pis_value'    => (string) $item->pis_value,
            'cofins_value' => (string) $item->cofins_value,
        ])->toArray();

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function openDetail(int $id): void
    {
        $this->viewingId  = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->viewingId  = null;
    }

    public function openXmlModal(): void
    {
        $this->xmlFile      = null;
        $this->showXmlModal = true;
    }

    public function closeXmlModal(): void
    {
        $this->showXmlModal = false;
        $this->xmlFile      = null;
    }

    /* ─── XML Import ─── */

    public function importXml(): void
    {
        $this->validate(['xmlFile' => 'required|file|mimes:xml|max:5120']);

        try {
            $content = file_get_contents($this->xmlFile->getRealPath());

            // Remove namespaces for easier parsing
            $content = preg_replace('/\s+xmlns[^=]*="[^"]*"/', '', $content);
            $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOWARNING);

            if (!$xml) {
                session()->flash('error', 'XML inválido ou malformado.');
                return;
            }

            // Navigate to NFe/infNFe node
            $infNFe = $xml->NFe->infNFe ?? $xml->infNFe ?? null;
            if (!$infNFe) {
                // Try nfeProc wrapper
                $infNFe = $xml->NFe->infNFe ?? $xml->nfeProc->NFe->infNFe ?? null;
            }

            if (!$infNFe) {
                session()->flash('error', 'Estrutura do XML NF-e não reconhecida.');
                return;
            }

            $ide   = $infNFe->ide ?? null;
            $emit  = $infNFe->emit ?? null;
            $total = $infNFe->total->ICMSTot ?? null;

            // Populate header
            $this->invoice_number   = (string) ($ide->nNF ?? '');
            $this->series           = (string) ($ide->serie ?? '1');
            $this->doc_type         = 'nfe';
            $this->operation_nature = (string) ($ide->natOp ?? '');
            $this->cfop             = '';

            // Access key from Id attribute or chNFe
            $idAttr = (string) ($infNFe['Id'] ?? '');
            if (strlen($idAttr) === 47) {
                $this->access_key = substr($idAttr, 3);
            }

            // Dates
            $dhEmi = (string) ($ide->dhEmi ?? $ide->dEmi ?? '');
            if ($dhEmi) {
                $this->issue_date = substr($dhEmi, 0, 10);
            }
            $this->entry_date = now()->format('Y-m-d');

            // Supplier
            if ($emit) {
                $this->supplier_cnpj = preg_replace('/\D/', '', (string) ($emit->CNPJ ?? ''));
                $this->supplier_name = (string) ($emit->xNome ?? $emit->xFant ?? '');
                $this->supplier_ie   = (string) ($emit->IE ?? '');

                // Try to find supplier by CNPJ
                if ($this->supplier_cnpj) {
                    $sup = Supplier::where('taxNumber', 'like', '%' . $this->supplier_cnpj . '%')->first();
                    if ($sup) {
                        $this->supplier_id = (string) $sup->id;
                    }
                }
            }

            // Totals
            if ($total) {
                $this->products_total  = (string) ((float) ($total->vProd ?? 0));
                $this->shipping_total  = (string) ((float) ($total->vFrete ?? 0));
                $this->insurance_total = (string) ((float) ($total->vSeg ?? 0));
                $this->other_expenses  = (string) ((float) ($total->vOutro ?? 0));
                $this->discount_total  = (string) ((float) ($total->vDesc ?? 0));
                $this->tax_total       = (string) round(
                    (float)($total->vICMS ?? 0) +
                    (float)($total->vIPI ?? 0) +
                    (float)($total->vPIS ?? 0) +
                    (float)($total->vCOFINS ?? 0),
                    2
                );
                $this->total_value = (string) ((float) ($total->vNF ?? 0));
            }

            // Items
            $this->invoiceItems = [];
            foreach ($infNFe->det as $det) {
                $prod    = $det->prod ?? null;
                $imposto = $det->imposto ?? null;

                $icmsVal = 0;
                if ($imposto) {
                    foreach (['ICMS00', 'ICMS10', 'ICMS20', 'ICMS30', 'ICMS40', 'ICMS51', 'ICMS60', 'ICMS70', 'ICMS90'] as $tag) {
                        if (isset($imposto->ICMS->$tag)) {
                            $icmsVal = (float) ($imposto->ICMS->$tag->vICMS ?? 0);
                            break;
                        }
                    }
                }

                $ipiVal    = (float) ($imposto->IPI->IPITrib->vIPI ?? 0);
                $pisVal    = (float) ($imposto->PIS->PISAliq->vPIS ?? $imposto->PIS->PISOutr->vPIS ?? 0);
                $cofinsVal = (float) ($imposto->COFINS->COFINSAliq->vCOFINS ?? $imposto->COFINS->COFINSOutr->vCOFINS ?? 0);

                $productCode = (string) ($prod->cProd ?? '');
                $productName = (string) ($prod->xProd ?? '');
                $qty         = (float) ($prod->qCom ?? 0);
                $unitPrice   = (float) ($prod->vUnCom ?? 0);
                $totalPrice  = (float) ($prod->vProd ?? 0);

                // Try to find product
                $productId = '';
                if ($productCode) {
                    $p = Product::where('sku', $productCode)->orWhere('ean', $productCode)->first();
                    if ($p) {
                        $productId = (string) $p->id;
                    }
                }

                $this->invoiceItems[] = [
                    'id'           => null,
                    'product_id'   => $productId,
                    'product_code' => $productCode,
                    'product_name' => $productName,
                    'ncm'          => (string) ($prod->NCM ?? ''),
                    'cfop'         => (string) ($prod->CFOP ?? ''),
                    'unit'         => (string) ($prod->uCom ?? 'UN'),
                    'quantity'     => (string) $qty,
                    'unit_price'   => (string) $unitPrice,
                    'total_price'  => (string) $totalPrice,
                    'icms_value'   => (string) $icmsVal,
                    'ipi_value'    => (string) $ipiVal,
                    'pis_value'    => (string) $pisVal,
                    'cofins_value' => (string) $cofinsVal,
                ];
            }

            $this->status = 'importada';
            $this->closeXmlModal();
            $this->activeTab = 'cabecalho';
            $this->showModal = true;

            session()->flash('message', 'XML importado com sucesso! Revise os dados e salve.');

        } catch (\Throwable $e) {
            session()->flash('error', 'Erro ao processar XML: ' . $e->getMessage());
        }
    }

    /* ─── Item Management ─── */

    public function addItem(): void
    {
        $this->invoiceItems[] = [
            'id'           => null,
            'product_id'   => '',
            'product_code' => '',
            'product_name' => '',
            'ncm'          => '',
            'cfop'         => '',
            'unit'         => 'UN',
            'quantity'     => '1',
            'unit_price'   => '0',
            'total_price'  => '0',
            'icms_value'   => '0',
            'ipi_value'    => '0',
            'pis_value'    => '0',
            'cofins_value' => '0',
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->invoiceItems[$index]);
        $this->invoiceItems = array_values($this->invoiceItems);
        $this->recalcTotals();
    }

    public function updatedInvoiceItems(): void
    {
        $this->recalcTotals();
    }

    public function recalcTotals(): void
    {
        $prodTotal = 0;
        foreach ($this->invoiceItems as &$item) {
            $qty   = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $total = round($qty * $price, 2);
            $item['total_price'] = (string) $total;
            $prodTotal += $total;
        }
        $this->products_total = (string) round($prodTotal, 2);
        $this->total_value    = (string) round(
            $prodTotal
            + (float)$this->shipping_total
            + (float)$this->insurance_total
            + (float)$this->other_expenses
            - (float)$this->discount_total,
            2
        );
    }

    public function searchProducts(string $query): void
    {
        if (strlen($query) < 2) {
            $this->searchResults = [];
            return;
        }
        $this->searchResults = Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->limit(8)
            ->get(['id', 'name', 'sku', 'unit_measure'])
            ->toArray();
    }

    public function selectProduct(int $itemIndex, string $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        $this->invoiceItems[$itemIndex]['product_id']   = (string) $product->id;
        $this->invoiceItems[$itemIndex]['product_name'] = $product->name;
        $this->invoiceItems[$itemIndex]['unit']         = $product->unit_measure ?? 'UN';
        $this->searchResults = [];
    }

    /* ─── Save ─── */

    public function save(): void
    {
        $this->validate([
            'supplier_name'  => 'required|string|max:255',
            'invoice_number' => 'required|string|max:9',
            'series'         => 'required|string|max:3',
            'entry_date'     => 'required|date',
            'invoiceItems'   => 'array|min:1',
        ]);

        DB::transaction(function () {
            $data = [
                'supplier_id'       => $this->supplier_id ?: null,
                'supplier_name'     => $this->supplier_name,
                'supplier_cnpj'     => $this->supplier_cnpj,
                'supplier_ie'       => $this->supplier_ie,
                'purchase_order_id' => $this->purchase_order_id ?: null,
                'invoice_number'    => $this->invoice_number,
                'series'            => $this->series,
                'access_key'        => $this->access_key ?: null,
                'doc_type'          => $this->doc_type,
                'issue_date'        => $this->issue_date ?: null,
                'entry_date'        => $this->entry_date,
                'cfop'              => $this->cfop,
                'operation_nature'  => $this->operation_nature,
                'status'            => $this->status,
                'products_total'    => (float) $this->products_total,
                'shipping_total'    => (float) $this->shipping_total,
                'insurance_total'   => (float) $this->insurance_total,
                'other_expenses'    => (float) $this->other_expenses,
                'discount_total'    => (float) $this->discount_total,
                'tax_total'         => (float) $this->tax_total,
                'total_value'       => (float) $this->total_value,
                'notes'             => $this->notes,
                'created_by'        => Auth::id(),
            ];

            if ($this->editingId) {
                $invoice = FiscalInvoiceIn::findOrFail($this->editingId);
                $invoice->update($data);
                $invoice->items()->delete();
            } else {
                $invoice = FiscalInvoiceIn::create($data);
            }

            foreach ($this->invoiceItems as $item) {
                FiscalInvoiceItemIn::create([
                    'fiscal_invoice_in_id' => $invoice->id,
                    'product_id'           => $item['product_id'] ?: null,
                    'product_code'         => $item['product_code'] ?? '',
                    'product_name'         => $item['product_name'],
                    'ncm'                  => $item['ncm'] ?? '',
                    'cfop'                 => $item['cfop'] ?? '',
                    'unit'                 => $item['unit'] ?? 'UN',
                    'quantity'             => (float) ($item['quantity'] ?? 0),
                    'unit_price'           => (float) ($item['unit_price'] ?? 0),
                    'total_price'          => (float) ($item['total_price'] ?? 0),
                    'icms_value'           => (float) ($item['icms_value'] ?? 0),
                    'ipi_value'            => (float) ($item['ipi_value'] ?? 0),
                    'pis_value'            => (float) ($item['pis_value'] ?? 0),
                    'cofins_value'         => (float) ($item['cofins_value'] ?? 0),
                ]);
            }
        });

        $this->closeModal();
        session()->flash('message', $this->editingId ? 'NF de Entrada atualizada!' : 'NF de Entrada cadastrada!');
    }

    /* ─── Escriturar (Confirm Receipt) ─── */

    public function escriturar(int $id): void
    {
        $invoice = FiscalInvoiceIn::with('items')->findOrFail($id);

        if (!$invoice->isEscrituravel()) {
            session()->flash('error', 'Esta NF não pode ser escriturada.');
            return;
        }

        DB::transaction(function () use ($invoice) {
            // 1. Entrada no estoque por item
            foreach ($invoice->items as $item) {
                if ($item->product_id) {
                    StockMovement::create([
                        'product_id'  => $item->product_id,
                        'user_id'     => Auth::id(),
                        'quantity'    => $item->quantity,
                        'type'        => 'entrada',
                        'origin'      => 'nf_entrada',
                        'unit_cost'   => $item->unit_price,
                        'observation' => "NF-e Entrada #{$invoice->invoice_number} / {$invoice->supplier_name}",
                    ]);
                }
            }

            // 2. Gerar conta a pagar
            $payable = AccountPayable::create([
                'description_title'   => "NF Entrada #{$invoice->invoice_number} - {$invoice->supplier_name}",
                'supplier_id'         => $invoice->supplier_id,
                'amount'              => $invoice->total_value,
                'due_date_at'         => now()->addDays(30)->format('Y-m-d'),
                'status'              => PayableStatus::Pending->value,
                'observation'         => "Gerado automaticamente a partir da NF de Entrada #{$invoice->invoice_number}",
            ]);

            // 3. Atualizar status
            $invoice->update([
                'status'             => 'escriturada',
                'account_payable_id' => $payable->id,
                'escriturada_at'     => now(),
            ]);
        });

        session()->flash('message', 'NF escriturada! Estoque e Contas a Pagar atualizados.');
    }

    /* ─── Cancel ─── */

    public function cancel(int $id): void
    {
        $invoice = FiscalInvoiceIn::findOrFail($id);

        if (!$invoice->isCancellable()) {
            session()->flash('error', 'Esta NF não pode ser cancelada.');
            return;
        }

        $invoice->update(['status' => 'cancelada']);
        session()->flash('message', 'NF de Entrada cancelada.');
    }

    /* ─── Delete ─── */

    public function delete(int $id): void
    {
        $invoice = FiscalInvoiceIn::findOrFail($id);

        if ($invoice->status === 'escriturada') {
            session()->flash('error', 'Não é possível excluir uma NF já escriturada.');
            return;
        }

        $invoice->delete();
        session()->flash('message', 'NF de Entrada removida.');
    }

    /* ─── Helpers ─── */

    private function resetForm(): void
    {
        $this->editingId        = null;
        $this->activeTab        = 'cabecalho';
        $this->supplier_id      = '';
        $this->supplier_name    = '';
        $this->supplier_cnpj    = '';
        $this->supplier_ie      = '';
        $this->purchase_order_id= '';
        $this->invoice_number   = '';
        $this->series           = '1';
        $this->access_key       = '';
        $this->doc_type         = 'nfe';
        $this->issue_date       = '';
        $this->entry_date       = '';
        $this->cfop             = '';
        $this->operation_nature = '';
        $this->status           = 'digitada';
        $this->notes            = '';
        $this->products_total   = '0';
        $this->shipping_total   = '0';
        $this->insurance_total  = '0';
        $this->other_expenses   = '0';
        $this->discount_total   = '0';
        $this->tax_total        = '0';
        $this->total_value      = '0';
        $this->invoiceItems     = [];
        $this->searchResults    = [];
    }

    /* ─── Render ─── */

    public function render(): View
    {
        return view('livewire.fiscal.nota-fiscal-entrada');
    }
}

