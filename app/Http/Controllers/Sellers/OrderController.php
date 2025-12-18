<?php

namespace App\Http\Controllers\Sellers;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * Helper method to check if seller has access to an order.
     */
    private function authorizeSellerAccess(Order $order): void
    {
        $user = Auth::user();
        $shop = $user->shops()->first();

        if (!$shop || $order->shop_id !== $shop->id) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }
    }

    /**
     * Display a listing of orders for the seller's shop.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $shop = $user->shops()->first();

        if (!$shop) {
            return Inertia::render('roles/sellers/order-manage/index', [
                'orders' => ['data' => []],
                'filters' => [
                    'search' => '',
                    'status' => 'all',
                    'date' => '',
                ],
            ]);
        }

        $filters = [
            'search' => $request->input('search', ''),
            'status' => $request->input('status', 'all'),
            'date' => $request->input('date', ''),
        ];

        $query = Order::where('shop_id', $shop->id)
            ->with(['customer', 'items.productVariant.product']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['search']}%")
                    ->orWhereHas('customer', function ($q) use ($filters) {
                        $q->where('full_name', 'like', "%{$filters['search']}%");
                    });
            });
        }

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        $orders = $query->latest()->paginate(15);

        // Format orders for frontend
        $formattedOrders = $orders->through(function ($order) {
            return [
                'id' => '#' . $order->order_number,
                'actual_id' => $order->id,
                'customer' => $order->customer->full_name ?? 'N/A',
                'products' => $order->items->pluck('product_name')->implode(', '),
                'total' => number_format($order->total_amount, 0, ',', '.') . 'đ',
                'payment_method' => $order->payment_method->label(),
                'status' => $order->status->value,
                'date' => $order->created_at->format('Y-m-d'),
            ];
        });

        return Inertia::render('roles/sellers/order-manage/index', [
            'orders' => $formattedOrders,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): Response
    {
        $this->authorizeSellerAccess($order);

        $order->load(['customer', 'shippingAddress', 'items.productVariant.product.images']);

        // Format order data for frontend
        $formattedOrder = [
            'id' => '#' . $order->order_number,
            'date' => $order->created_at->format('d \t\há\n\g n, Y'),
            'customer' => [
                'name' => $order->customer->full_name ?? 'N/A',
                'email' => $order->customer->email ?? 'N/A',
                'phone' => $order->customer->phone_number ?? 'N/A',
                'address' => $order->shippingAddress ? 
                    "{$order->shippingAddress->address_line1}, {$order->shippingAddress->ward}, {$order->shippingAddress->district}, {$order->shippingAddress->city}" : 
                    'N/A',
            ],
            'products' => $order->items->map(function ($item) {
                $image = $item->productVariant?->product?->images?->first()?->image_url ?? 'https://via.placeholder.com/100';
                if ($image && !str_starts_with($image, 'http')) {
                    $image = asset($image);
                }

                return [
                    'id' => $item->id,
                    'name' => $item->product_name,
                    'image' => $image,
                    'variant' => $item->variant_name ?? 'N/A',
                    'quantity' => $item->quantity,
                    'price' => number_format($item->unit_price, 0, ',', '.') . 'đ',
                    'total' => number_format($item->total_price, 0, ',', '.') . 'đ',
                ];
            })->values(),
            'subtotal' => number_format($order->subtotal, 0, ',', '.') . 'đ',
            'shipping' => number_format($order->shipping_fee, 0, ',', '.') . 'đ',
            'discount' => number_format($order->discount_amount, 0, ',', '.') . 'đ',
            'total' => number_format($order->total_amount, 0, ',', '.') . 'đ',
            'payment_method' => $order->payment_method->label(),
            'payment_status' => $order->payment_status->value,
            'status' => $order->status->value,
        ];

        return Inertia::render('roles/sellers/order-manage/read', [
            'order' => $formattedOrder,
        ]);
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeSellerAccess($order);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                OrderStatus::PENDING->value,
                OrderStatus::CONFIRMED->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::SHIPPING->value,
                OrderStatus::DELIVERED->value,
                OrderStatus::CANCELLED->value,
            ])],
        ]);

        $order->update(['status' => $validated['status']]);

        // Update timestamps based on status
        if ($validated['status'] === OrderStatus::CONFIRMED->value && !$order->confirmed_at) {
            $order->update(['confirmed_at' => now()]);
        } elseif ($validated['status'] === OrderStatus::DELIVERED->value && !$order->delivered_at) {
            $order->update(['delivered_at' => now()]);
        } elseif ($validated['status'] === OrderStatus::CANCELLED->value && !$order->cancelled_at) {
            $order->update(['cancelled_at' => now()]);
        }

        return redirect()
            ->route('seller.orders.show', $order->id)
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}
