<?php

namespace App\Http\Controllers\Seller;

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
     * Helper method để kiểm tra quyền truy cập của Seller vào một đơn hàng.
     *
     * @param Order $order
     */
    private function authorizeSellerAccess(Order $order): void
    {
        $isSellerOrder = $order->items()
            ->whereHas('variant.product', function ($query) {
                $query->where('seller_id', Auth::id());
            })->exists();

        if (!$isSellerOrder) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }
    }

    /**
     * Hiển thị danh sách các đơn hàng có chứa sản phẩm của Seller.
     */
    public function index(): Response
    {
        $orders = Order::whereHas('items.variant.product', function ($query) {
            $query->where('seller_id', Auth::id());
        })
        ->with('customer') // Tải sẵn thông tin khách hàng để tránh N+1 query
        ->latest()
        ->paginate(15);

        return Inertia::render('Seller/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Hiển thị thông tin chi tiết của một đơn hàng.
     */
    public function show(Order $order): Response
    {
        $this->authorizeSellerAccess($order);

        // Tải tất cả các thông tin liên quan cần thiết
        $order->load(['customer', 'shippingAddress', 'items.variant.product']);

        return Inertia::render('Seller/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Cập nhật trạng thái của một đơn hàng.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeSellerAccess($order);

        // Giả sử model Order có các hằng số định nghĩa trạng thái
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                'pending_confirmation',
                'processing',
                'pending_assignment',
                'assigned_to_shipper',
                'delivering',
                'delivered',
                'completed',
                'cancelled',
                'returned',
            ])],
        ]);

        $order->update(['status' => $validated['status']]);

        // Nâng cao: Gửi thông báo cho khách hàng về việc cập nhật trạng thái.
        // Notification::send($order->customer, new OrderStatusUpdated($order));

        return redirect()->route('seller.orders.show', $order->order_id)
                         ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}

