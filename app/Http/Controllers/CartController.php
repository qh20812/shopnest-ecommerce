<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the cart page with all cart items.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return Inertia::render('cart', [
                'cartItems' => [],
                'subtotal' => 0,
                'shipping' => 0,
                'total' => 0,
            ]);
        }

        $cartItems = CartItem::with([
            'productVariant.product.images',
            'productVariant.product.category',
            'productVariant.product.brand',
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

                // Get the image URL (variant image or first product image)
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
                    'compare_at_price' => (float) $variant->compare_at_price,
                    'quantity' => $item->quantity,
                    'stock_quantity' => $variant->stock_quantity,
                ];
            });

        $subtotal = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 0; // Free shipping
        $total = $subtotal + $shipping;

        return Inertia::render('cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    /**
     * Add a product variant to cart or update quantity if exists.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập để thêm vào giỏ hàng'], 401);
        }

        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::where('id', $request->product_variant_id)
            ->where('is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->with('product')
            ->first();

        if (!$variant) {
            return response()->json(['message' => 'Sản phẩm không tồn tại hoặc không khả dụng'], 404);
        }

        if ($request->quantity > $variant->stock_quantity) {
            return response()->json(['message' => 'Số lượng vượt quá tồn kho'], 400);
        }

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $variant->stock_quantity) {
                return response()->json(['message' => 'Số lượng vượt quá tồn kho'], 400);
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => auth()->id(),
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Đã thêm vào giỏ hàng'], 200);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập'], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
        }

        $variant = ProductVariant::find($cartItem->product_variant_id);

        if ($request->quantity > $variant->stock_quantity) {
            return response()->json(['message' => 'Số lượng vượt quá tồn kho'], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['message' => 'Đã cập nhật số lượng'], 200);
    }

    /**
     * Remove a cart item.
     */
    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập'], 401);
        }

        $cartItem = CartItem::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng'], 200);
    }

    /**
     * Clear all cart items.
     */
    public function clear()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập'], 401);
        }

        CartItem::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng'], 200);
    }

    /**
     * Apply coupon code.
     */
    public function applyCoupon(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập'], 401);
        }

        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        // TODO: Implement coupon logic when Promotion model is ready
        return response()->json(['message' => 'Mã giảm giá không hợp lệ'], 400);
    }
}
