<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page with cart items.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return Inertia::render('checkout', [
                'cartItems' => [],
                'subtotal' => 0,
                'shipping' => 0,
                'total' => 0,
                'addresses' => [],
            ]);
        }

        // Get cart items with relationships
        $cartItems = CartItem::with([
            'productVariant.product.images',
            'productVariant.image',
            'productVariant.attributeValues.attribute',
        ])
            ->where('user_id', $user->id)
            ->whereHas('productVariant.product', function ($query) {
                $query->where('status', 'active');
            })
            ->whereHas('productVariant', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->map(function ($item) {
                $variant = $item->productVariant;
                $product = $variant->product;

                // Get image URL
                $imageUrl = $variant->image?->image_url 
                    ?? $product->images->first()?->image_url 
                    ?? 'https://via.placeholder.com/150';

                // Get color attribute
                $colorAttribute = $variant->attributeValues
                    ->where('attribute.attribute_name', 'Màu sắc')
                    ->first();

                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'name' => $product->product_name,
                    'image' => $imageUrl,
                    'color' => $colorAttribute?->value_name ?? 'Không có',
                    'price' => (float) $variant->price,
                    'quantity' => $item->quantity,
                ];
            });

        $subtotal = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = $this->calculateShippingFee($subtotal);
        $total = $subtotal + $shipping;

        // Get user addresses
        $addresses = $user->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'label' => $address->address_label ?? 'Địa chỉ',
                    'recipient_name' => $address->recipient_name ?? '',
                    'phone_number' => $address->phone_number ?? '',
                    'address_line1' => $address->address_line1 ?? '',
                    'address_line2' => $address->address_line2 ?? '',
                    'is_default' => (bool) $address->is_default,
                ];
            });

        return Inertia::render('checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'addresses' => $addresses,
        ]);
    }

    /**
     * Process checkout and create order.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập'], 401);
        }

        $validated = $request->validate([
            'shipping_address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:cod,credit_card,e_wallet,bank_transfer',
            'shipping_method' => 'required|in:standard,express',
            'note' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        // Verify address belongs to user
        $address = $user->addresses()->where('id', $validated['shipping_address_id'])->first();
        if (!$address) {
            return response()->json(['message' => 'Địa chỉ không hợp lệ'], 422);
        }

        // Get cart items
        $cartItems = CartItem::with('productVariant.product')
            ->where('user_id', $user->id)
            ->whereHas('productVariant.product', function ($query) {
                $query->where('status', 'active');
            })
            ->whereHas('productVariant', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng trống'], 400);
        }

        // Calculate totals
        $subtotal = $cartItems->sum(fn($item) => $item->productVariant->price * $item->quantity);
        $shippingFee = $validated['shipping_method'] === 'express' ? 50000 : 30000;
        $total = $subtotal + $shippingFee;

        DB::beginTransaction();

        try {
            // Group items by shop
            $itemsByShop = $cartItems->groupBy(fn($item) => $item->productVariant->product->shop_id);

            $orders = [];

            foreach ($itemsByShop as $shopId => $items) {
                $shopSubtotal = $items->sum(fn($item) => $item->productVariant->price * $item->quantity);
                $shopTotal = $shopSubtotal + $shippingFee;

                // Create order
                $order = Order::create([
                    'order_number' => $this->generateOrderNumber(),
                    'customer_id' => $user->id,
                    'shop_id' => $shopId,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'subtotal' => $shopSubtotal,
                    'discount_amount' => 0,
                    'shipping_fee' => $shippingFee,
                    'tax_amount' => 0,
                    'total_amount' => $shopTotal,
                    'currency' => 'VND',
                    'shipping_address_id' => $validated['shipping_address_id'],
                    'payment_method' => $validated['payment_method'],
                    'note' => $validated['note'] ?? null,
                ]);

                // Create order items
                foreach ($items as $cartItem) {
                    $variant = $cartItem->productVariant;
                    $product = $variant->product;
                    $itemSubtotal = $variant->price * $cartItem->quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $cartItem->product_variant_id,
                        'product_name' => $product->product_name,
                        'variant_name' => $variant->variant_name ?? 'Standard',
                        'sku' => $variant->sku ?? 'SKU-' . $variant->id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $variant->price,
                        'subtotal' => $itemSubtotal,
                        'discount_amount' => 0,
                        'total_price' => $itemSubtotal,
                    ]);

                    // Reduce stock
                    $variant->stock_quantity = max(0, $variant->stock_quantity - $cartItem->quantity);
                    $variant->save();
                }

                $orders[] = $order;
            }

            // Clear cart
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Đơn hàng đã được tạo thành công',
                'orders' => collect($orders)->map(fn($o) => [
                    'id' => $o->id,
                    'order_number' => $o->order_number,
                    'total_amount' => $o->total_amount,
                ])->toArray(),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại'], 500);
        }
    }

    /**
     * Calculate shipping fee based on subtotal.
     */
    private function calculateShippingFee(float $subtotal): float
    {
        // Free shipping for orders over 1,000,000 VND
        if ($subtotal >= 1000000) {
            return 0;
        }

        return 30000;
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
