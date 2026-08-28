<?php

namespace App\Services;

use App\Enums\PayableStatus;
use App\Models\AccountPayable;
use App\Models\FiscalEntryRecord;
use App\Models\FiscalInvoiceIn;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FiscalEntryPostingService
{
    public function post(FiscalInvoiceIn $invoice, User $user): void
    {
        DB::transaction(function () use ($invoice, $user): void {
            $invoice = FiscalInvoiceIn::where('id', $invoice->id)->lockForUpdate()->with('items')->firstOrFail();
            if ($invoice->escriturada_at) {
                return;
            }
            if (! $invoice->isEscrituravel()) {
                throw new \RuntimeException('A NF-e possui divergências ou estado incompatível com a escrituração.');
            }

            $productsTotal = max((float) $invoice->products_total, 0.01);
            foreach ($invoice->items as $item) {
                $ratio = (float) $item->total_price / $productsTotal;
                $item->update([
                    'allocated_shipping' => round((float) $invoice->shipping_total * $ratio, 2),
                    'allocated_insurance' => round((float) $invoice->insurance_total * $ratio, 2),
                    'allocated_other' => round((float) $invoice->other_expenses * $ratio, 2),
                ]);
                FiscalEntryRecord::create([
                    'fiscal_invoice_in_id' => $invoice->id,
                    'item_id' => $item->id,
                    'taxes' => $item->tax_data ?? [
                        'icms' => ['base' => $item->icms_base, 'rate' => $item->icms_rate, 'amount' => $item->icms_value],
                        'ipi' => ['amount' => $item->ipi_value],
                        'pis' => ['amount' => $item->pis_value],
                        'cofins' => ['amount' => $item->cofins_value],
                    ],
                    'credit_allowed' => $item->credit_allowed,
                    'posted_at' => now(),
                ]);

                if ($item->product_id) {
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'user_id' => $user->id,
                        'quantity' => $item->quantity,
                        'type' => 'input',
                        'origin' => "fiscal_invoice_in:{$invoice->id}",
                        'unit_cost' => $item->unit_price,
                        'observation' => "NF-e Entrada #{$invoice->invoice_number}",
                    ]);
                }
            }

            $installments = $invoice->installment_data ?: [[
                'number' => 1,
                'due_date' => now()->addDays(30)->format('Y-m-d'),
                'amount' => (float) $invoice->total_value,
            ]];
            foreach ($installments as $installment) {
                AccountPayable::create([
                    'fiscal_invoice_in_id' => $invoice->id,
                    'installment_number' => $installment['number'],
                    'description_title' => "NF Entrada #{$invoice->invoice_number} - Parcela {$installment['number']}",
                    'supplier_id' => $invoice->supplier_id,
                    'chart_of_account_id' => $invoice->chart_of_account_id,
                    'amount' => $installment['amount'],
                    'due_date_at' => $installment['due_date'],
                    'status' => PayableStatus::Pending,
                    'observation' => $invoice->financial_nature,
                ]);
            }

            $invoice->update([
                'status' => 'escriturada',
                'account_payable_id' => AccountPayable::where('fiscal_invoice_in_id', $invoice->id)->value('id'),
                'escriturada_at' => now(),
            ]);
        });
    }
}
