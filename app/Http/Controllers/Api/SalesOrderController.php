<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalesOrderRequest;
use App\Http\Requests\UpdateSalesOrderRequest;
use App\Services\SalesOrderService;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SalesOrderController extends Controller
{
    protected SalesOrderService $service;

    public function __construct(SalesOrderService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista todos os pedidos
     */
    public function index(Request $request): JsonResponse
    {
        $query = SalesOrder::with(['client', 'seller', 'items.product']);

        // Filtros
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        if ($request->has('is_fiscal')) {
            $query->where('is_fiscal', $request->boolean('is_fiscal'));
        }

        if ($request->has('needs_approval')) {
            $query->where('needs_approval', $request->boolean('needs_approval'));
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * Cria um novo pedido
     */
    public function store(StoreSalesOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->service->createOrder($request->validated());

            return response()->json([
                'message' => 'Pedido criado com sucesso!',
                'data' => $order->load(['items', 'addresses', 'payments.installments']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar pedido',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Exibe um pedido específico
     */
    public function show(SalesOrder $order): JsonResponse
    {
        $order->load([
            'client',
            'user',
            'seller',
            'items.product',
            'addresses',
            'payments.installments',
            'logs.user',
            'attachments',
            'priceTable',
            'carrier',
        ]);

        return response()->json([
            'data' => $order,
        ]);
    }

    /**
     * Atualiza um pedido
     */
    public function update(UpdateSalesOrderRequest $request, SalesOrder $order): JsonResponse
    {
        try {
            $updatedOrder = $this->service->updateOrder($order, $request->validated());

            return response()->json([
                'message' => 'Pedido atualizado com sucesso!',
                'data' => $updatedOrder->load(['items', 'addresses', 'payments.installments']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar pedido',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove um pedido
     */
    public function destroy(SalesOrder $order): JsonResponse
    {
        try {
            if (!$order->canCancel()) {
                return response()->json([
                    'message' => 'Este pedido não pode ser cancelado no status atual.',
                ], 422);
            }

            $this->service->cancelOrder($order, 'Cancelado via API');

            return response()->json([
                'message' => 'Pedido cancelado com sucesso!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao cancelar pedido',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Aprova um pedido
     */
    public function approve(Request $request, SalesOrder $order): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->service->approveOrder($order, $request->reason);

            return response()->json([
                'message' => 'Pedido aprovado com sucesso!',
                'data' => $order->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao aprovar pedido',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancela um pedido
     */
    public function cancel(Request $request, SalesOrder $order): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->service->cancelOrder($order, $request->reason);

            return response()->json([
                'message' => 'Pedido cancelado com sucesso!',
                'data' => $order->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao cancelar pedido',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Calcula totais do pedido (para preview antes de salvar)
     */
    public function calculate(Request $request): JsonResponse
    {
        $items = $request->get('items', []);
        $discountAmount = $request->get('discount_amount', 0);
        $shippingAmount = $request->get('shipping_amount', 0);
        $insuranceAmount = $request->get('insurance_amount', 0);
        $otherExpenses = $request->get('other_expenses', 0);
        $isFiscal = $request->boolean('is_fiscal');

        $subtotal = 0;
        $totalTaxes = 0;

        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? 0;
            $unitPrice = $item['unit_price'] ?? 0;
            $discount = $item['discount'] ?? 0;
            $discountPercent = $item['discount_percent'] ?? 0;
            $addition = $item['addition'] ?? 0;

            // Calcula desconto se for percentual
            if ($discountPercent > 0) {
                $discount = ($quantity * $unitPrice) * ($discountPercent / 100);
            }

            $itemSubtotal = ($quantity * $unitPrice) - $discount + $addition;
            $subtotal += $itemSubtotal;

            // Calcula impostos se for fiscal
            if ($isFiscal) {
                $icms = $itemSubtotal * (($item['icms_percent'] ?? 0) / 100);
                $ipi = $itemSubtotal * (($item['ipi_percent'] ?? 0) / 100);
                $pis = $itemSubtotal * (($item['pis_percent'] ?? 0) / 100);
                $cofins = $itemSubtotal * (($item['cofins_percent'] ?? 0) / 100);

                $totalTaxes += ($icms + $ipi + $pis + $cofins);
            }
        }

        $netTotal = $subtotal - $discountAmount;
        $totalAmount = $netTotal + $shippingAmount + $insuranceAmount + $otherExpenses;

        if ($isFiscal) {
            $totalAmount += $totalTaxes;
        }

        return response()->json([
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'net_total' => round($netTotal, 2),
            'shipping_amount' => round($shippingAmount, 2),
            'insurance_amount' => round($insuranceAmount, 2),
            'other_expenses' => round($otherExpenses, 2),
            'tax_amount' => round($totalTaxes, 2),
            'total_amount' => round($totalAmount, 2),
        ]);
    }

    /**
     * Histórico/logs do pedido
     */
    public function logs(SalesOrder $order): JsonResponse
    {
        $logs = $order->logs()->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $logs,
        ]);
    }

    /**
     * Anexos do pedido
     */
    public function attachments(SalesOrder $order): JsonResponse
    {
        $attachments = $order->attachments()->with('uploader')->get();

        return response()->json([
            'data' => $attachments,
        ]);
    }

    /**
     * Estatísticas gerais
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = SalesOrder::query();

        // Filtro por período
        if ($request->has('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $totalOrders = $query->count();
        $totalAmount = $query->sum('total_amount');
        $averageTicket = $totalOrders > 0 ? $totalAmount / $totalOrders : 0;

        $byStatus = SalesOrder::selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('status')
            ->get();

        $byChannel = SalesOrder::selectRaw('sales_channel, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('sales_channel')
            ->get();

        return response()->json([
            'total_orders' => $totalOrders,
            'total_amount' => round($totalAmount, 2),
            'average_ticket' => round($averageTicket, 2),
            'by_status' => $byStatus,
            'by_channel' => $byChannel,
        ]);
    }
}

