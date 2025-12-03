<?php

namespace App\Http\Controllers\Examples;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rules\Enum;

/**
 * Example controller demonstrating enum usage
 * 
 * This is a demonstration file showing best practices for using enums
 * in Laravel controllers. You can use these patterns in your real controllers.
 */
class EnumExampleController extends Controller
{
    /**
     * Get all available enum options for forms
     * 
     * GET /api/enums
     */
    public function getEnumOptions()
    {
        return response()->json([
            'order_statuses' => OrderStatus::options(),
            'payment_methods' => PaymentMethod::options(),
            'payment_statuses' => PaymentStatus::options(),
            'product_statuses' => ProductStatus::options(),
        ]);
    }

    /**
     * Create new order with enum values
     * 
     * POST /api/orders
     */
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'total_amount' => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . time(),
            'customer_id' => $validated['customer_id'],
            'status' => OrderStatus::PENDING, // Using enum
            'payment_status' => PaymentStatus::UNPAID, // Using enum
            'payment_method' => PaymentMethod::from($validated['payment_method']), // Convert string to enum
            'total_amount' => $validated['total_amount'],
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'payment_method' => $order->payment_method->value,
                'payment_method_label' => $order->payment_method->label(),
            ],
        ], 201);
    }

    /**
     * Update order status
     * 
     * PATCH /api/orders/{id}/status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);

        $newStatus = OrderStatus::from($validated['status']);

        // Business logic based on status transition
        $message = match($newStatus) {
            OrderStatus::CONFIRMED => $this->confirmOrder($order),
            OrderStatus::SHIPPING => $this->startShipping($order),
            OrderStatus::DELIVERED => $this->completeDelivery($order),
            OrderStatus::CANCELLED => $this->cancelOrder($order),
            default => 'Status updated',
        };

        $order->status = $newStatus;
        $order->save();

        return response()->json([
            'message' => $message,
            'order' => [
                'id' => $order->id,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
            ],
        ]);
    }

    /**
     * Get orders by status
     * 
     * GET /api/orders?status=pending,confirmed
     */
    public function getOrders(Request $request)
    {
        $query = Order::query();

        // Filter by status
        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            
            // Validate each status
            $validStatuses = array_filter($statuses, function($status) {
                return OrderStatus::tryFrom($status) !== null;
            });

            if (!empty($validStatuses)) {
                $query->whereIn('status', $validStatuses);
            }
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $paymentMethod = PaymentMethod::tryFrom($request->payment_method);
            if ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            }
        }

        $orders = $query->with('customer')->paginate(15);

        // Transform response
        $orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer' => $order->customer->name,
                'status' => [
                    'value' => $order->status->value,
                    'label' => $order->status->label(),
                ],
                'payment_method' => [
                    'value' => $order->payment_method->value,
                    'label' => $order->payment_method->label(),
                ],
                'total_amount' => $order->total_amount,
            ];
        });

        return response()->json($orders);
    }

    /**
     * Get order statistics by status
     * 
     * GET /api/orders/statistics
     */
    public function getOrderStatistics()
    {
        $statistics = [];

        foreach (OrderStatus::cases() as $status) {
            $count = Order::where('status', $status)->count();
            $statistics[] = [
                'status' => $status->value,
                'label' => $status->label(),
                'count' => $count,
            ];
        }

        return response()->json([
            'statistics' => $statistics,
            'total' => Order::count(),
        ]);
    }

    /**
     * Update product status
     * 
     * PATCH /api/products/{id}/status
     */
    public function updateProductStatus(Request $request, Product $product)
    {
        $validated = $request->validate([
            'status' => ['required', new Enum(ProductStatus::class)],
        ]);

        $oldStatus = $product->status;
        $newStatus = ProductStatus::from($validated['status']);

        // Check if transition is allowed
        if (!$this->isValidProductStatusTransition($oldStatus, $newStatus)) {
            return response()->json([
                'message' => 'Invalid status transition',
                'from' => $oldStatus->label(),
                'to' => $newStatus->label(),
            ], 422);
        }

        $product->status = $newStatus;
        $product->save();

        return response()->json([
            'message' => 'Product status updated successfully',
            'product' => [
                'id' => $product->id,
                'name' => $product->product_name,
                'status' => [
                    'value' => $product->status->value,
                    'label' => $product->status->label(),
                ],
            ],
        ]);
    }

    /**
     * Get products by status
     * 
     * GET /api/products?status=active
     */
    public function getProducts(Request $request)
    {
        $query = Product::query();

        if ($request->has('status')) {
            $status = ProductStatus::tryFrom($request->status);
            if ($status) {
                $query->where('status', $status);
            }
        }

        $products = $query->paginate(20);

        return response()->json($products);
    }

    // Private helper methods

    private function confirmOrder(Order $order): string
    {
        $order->confirmed_at = now();
        return 'Order has been confirmed';
    }

    private function startShipping(Order $order): string
    {
        // Create shipping record, notify customer, etc.
        return 'Shipping started';
    }

    private function completeDelivery(Order $order): string
    {
        $order->delivered_at = now();
        return 'Order has been delivered';
    }

    private function cancelOrder(Order $order): string
    {
        $order->cancelled_at = now();
        return 'Order has been cancelled';
    }

    private function isValidProductStatusTransition(ProductStatus $from, ProductStatus $to): bool
    {
        // Define allowed transitions
        $allowedTransitions = [
            ProductStatus::DRAFT->value => [
                ProductStatus::ACTIVE,
                ProductStatus::INACTIVE,
            ],
            ProductStatus::ACTIVE->value => [
                ProductStatus::INACTIVE,
                ProductStatus::OUT_OF_STOCK,
            ],
            ProductStatus::INACTIVE->value => [
                ProductStatus::ACTIVE,
            ],
            ProductStatus::OUT_OF_STOCK->value => [
                ProductStatus::ACTIVE,
                ProductStatus::INACTIVE,
            ],
        ];

        $allowed = $allowedTransitions[$from->value] ?? [];
        return in_array($to, $allowed);
    }
}
