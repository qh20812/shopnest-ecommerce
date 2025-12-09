<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DetailController extends Controller
{
    /**
     * Show product detail page
     */
    public function show(Request $request, int $productId): Response
    {
        $locale = app()->getLocale();

        // Fetch product with relationships
        $product = Product::with([
            'variants.attributeValues.attribute',
            'images',
            'category',
            'brand',
            'shop'
        ])
            ->where('id', $productId)
            ->where('status', 'active')
            ->firstOrFail();

        // Transform product data
        $productData = $this->transformProduct($product, $locale);

        // Get rating summary
        $ratingSummary = $this->calculateRatingSummary($productId);

        // Get sold count
        $soldCount = $this->calculateSoldCount($productId);

        // Get related products
        $relatedProducts = $this->getRelatedProducts(
            $productId,
            $product->category_id,
            $product->brand_id,
            $locale
        );

        // Get reviews with pagination
        $reviews = $this->getReviews($request, $productId);

        // Get user and cart info
        $user = Auth::user();
        $cartCount = 0;
        if ($user) {
            $cartCount = DB::table('cart_items')
                ->where('user_id', $user->id)
                ->sum('quantity');
        }

        return Inertia::render('detail', [
            'product' => $productData,
            'rating' => $ratingSummary,
            'soldCount' => $soldCount,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->full_name ?? $user->name ?? '',
                'email' => $user->email,
                'avatar' => $user->avatar ?? null,
            ] : null,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Add product to cart
     */
    public function addToCart(Request $request, int $productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.',
                'action' => 'login_required',
            ], 401);
        }

        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        try {
            $variant = ProductVariant::where('id', $data['variant_id'])
                ->where('product_id', $productId)
                ->firstOrFail();

            // Check stock availability
            $availableStock = $variant->stock_quantity - ($variant->reserved_quantity ?? 0);
            if ($availableStock < $data['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không đủ số lượng trong kho.',
                ], 422);
            }

            $user = Auth::user();

            // Check if item already exists in cart
            $cartItem = DB::table('cart_items')
                ->where('user_id', $user->id)
                ->where('product_variant_id', $variant->id)
                ->first();

            if ($cartItem) {
                // Update quantity
                $newQuantity = $cartItem->quantity + $data['quantity'];
                if ($newQuantity > $availableStock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Số lượng vượt quá hàng tồn kho.',
                    ], 422);
                }

                DB::table('cart_items')
                    ->where('id', $cartItem->id)
                    ->update([
                        'quantity' => $newQuantity,
                        'updated_at' => now(),
                    ]);
            } else {
                // Add new item
                DB::table('cart_items')->insert([
                    'user_id' => $user->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $data['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Get updated cart count
            $cartCount = DB::table('cart_items')
                ->where('user_id', $user->id)
                ->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
                'cartCount' => $cartCount,
            ]);
        } catch (\Throwable $exception) {
            Log::error('DetailController@addToCart failed', [
                'product_id' => $productId,
                'user_id' => Auth::id(),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể thêm sản phẩm vào giỏ hàng lúc này.',
            ], 500);
        }
    }

    /**
     * Buy product now (direct checkout)
     */
    public function buyNow(Request $request, int $productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để mua hàng.',
                'action' => 'login_required',
            ], 401);
        }

        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        try {
            $variant = ProductVariant::with('product')
                ->where('id', $data['variant_id'])
                ->where('product_id', $productId)
                ->firstOrFail();

            // Check stock availability
            $availableStock = $variant->stock_quantity - ($variant->reserved_quantity ?? 0);
            if ($availableStock < $data['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không đủ số lượng trong kho.',
                ], 422);
            }

            // Redirect to checkout with product info
            return response()->json([
                'success' => true,
                'redirect' => route('checkout', [
                    'variant_id' => $variant->variant_id,
                    'quantity' => $data['quantity'],
                    'buy_now' => true,
                ]),
            ]);
        } catch (\Throwable $exception) {
            Log::error('DetailController@buyNow failed', [
                'product_id' => $productId,
                'user_id' => Auth::id(),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể xử lý yêu cầu mua hàng lúc này.',
            ], 500);
        }
    }

    /**
     * Transform product model to array
     */
    private function transformProduct(Product $product, string $locale): array
    {
        // Transform variants
        $variants = $product->variants->map(function (ProductVariant $variant) {
            $attributeValues = $variant->attributeValues->map(function ($attributeValue) {
                return [
                    'attribute_id' => $attributeValue->attribute->attribute_id ?? null,
                    'attribute_name' => $attributeValue->attribute->name ?? '',
                    'value_id' => $attributeValue->attribute_value_id,
                    'value' => $attributeValue->value,
                ];
            })->values()->all();

            $availableStock = max(0, (int) $variant->stock_quantity - (int) ($variant->reserved_quantity ?? 0));

            return [
                'variant_id' => (int) $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'compare_price' => $variant->compare_at_price ? (float) $variant->compare_at_price : null,
                'final_price' => (float) $variant->price,
                'stock_quantity' => (int) $variant->stock_quantity,
                'available_quantity' => $availableStock,
                'attribute_values' => $attributeValues,
                'in_stock' => $availableStock > 0,
            ];
        })->values()->all();

        // Group attributes
        $attributes = [];
        foreach ($variants as $variant) {
            foreach ($variant['attribute_values'] as $attrValue) {
                $attrId = $attrValue['attribute_id'];
                if (!isset($attributes[$attrId])) {
                    $attributes[$attrId] = [
                        'attribute_id' => $attrId,
                        'name' => $attrValue['attribute_name'],
                        'values' => [],
                    ];
                }
                $valueId = $attrValue['value_id'];
                if (!isset($attributes[$attrId]['values'][$valueId])) {
                    $attributes[$attrId]['values'][$valueId] = [
                        'value_id' => $valueId,
                        'value' => $attrValue['value'],
                    ];
                }
            }
        }

        $attributes = array_values(array_map(function ($attr) {
            $attr['values'] = array_values($attr['values']);
            return $attr;
        }, $attributes));

        // Transform images
        $images = $product->images->map(function ($image) {
            return [
                'id' => $image->image_id,
                'url' => $image->image_url,
                'alt' => $image->alt_text ?? 'Product image',
            ];
        })->values()->all();

        if (empty($images)) {
            $images = [[
                'id' => 0,
                'url' => 'https://via.placeholder.com/600x600?text=No+Image',
                'alt' => 'Product image placeholder',
            ]];
        }

        // Calculate price range
        $prices = array_column($variants, 'final_price');
        $minPrice = !empty($prices) ? min($prices) : 0;
        $maxPrice = !empty($prices) ? max($prices) : 0;

        return [
            'id' => (int) $product->id,
            'name' => $product->product_name ?? $product->name ?? '',
            'description' => $product->description ?? '',
            'price' => (float) $minPrice,
            'minPrice' => (float) $minPrice,
            'maxPrice' => (float) $maxPrice,
            'images' => $images,
            'variants' => $variants,
            'attributes' => $attributes,
            'default_variant_id' => $variants[0]['variant_id'] ?? null,
            'inStock' => count(array_filter($variants, fn($v) => $v['in_stock'])) > 0,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->category_name ?? $product->category->name ?? '',
            ] : null,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->brand_name ?? $product->brand->name ?? '',
            ] : null,
            'shop' => $product->shop ? [
                'id' => $product->shop->id,
                'name' => $product->shop->shop_name,
                'logo' => $product->shop->logo_url,
                'rating' => (float) ($product->shop->rating ?? 0),
            ] : null,
        ];
    }

    /**
     * Calculate rating summary
     */
    private function calculateRatingSummary(int $productId): array
    {
        $reviews = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->get();

        $totalReviews = $reviews->count();
        $average = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

        $breakdown = [];
        for ($rating = 5; $rating >= 1; $rating--) {
            $count = $reviews->where('rating', $rating)->count();
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $breakdown[] = [
                'rating' => $rating,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return [
            'average' => $average,
            'count' => $totalReviews,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate sold count
     */
    private function calculateSoldCount(int $productId): int
    {
        return (int) OrderItem::whereHas('order', function ($query) {
            $query->whereIn('status', ['completed', 'delivered']);
        })
            ->whereHas('productVariant', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->sum('quantity');
    }

    /**
     * Get related products
     */
    private function getRelatedProducts(int $productId, ?int $categoryId, ?int $brandId, string $locale): array
    {
        $query = Product::with(['images', 'variants', 'category'])
            ->where('id', '!=', $productId)
            ->where('status', 'active');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        } elseif ($brandId) {
            $query->where('brand_id', $brandId);
        }

        $products = $query->limit(4)->get();

        return $products->map(function (Product $product) use ($locale) {
            $firstImage = $product->images->first();
            $minPrice = $product->variants->min('price') ?? 0;

            // Get rating
            $avgRating = Review::where('product_id', $product->id)
                ->where('is_approved', true)
                ->avg('rating');
            $reviewCount = Review::where('product_id', $product->id)
                ->where('is_approved', true)
                ->count();

            return [
                'id' => (int) $product->id,
                'name' => $product->product_name ?? $product->name ?? '',
                'category' => $product->category?->category_name ?? $product->category?->name ?? '',
                'image' => $firstImage?->image_url ?? 'https://via.placeholder.com/300x300',
                'price' => (float) $minPrice,
                'rating' => round((float) $avgRating, 1),
                'reviewCount' => (int) $reviewCount,
                'isWishlisted' => false,
            ];
        })->values()->all();
    }

    /**
     * Get reviews with pagination
     */
    private function getReviews(Request $request, int $productId): array
    {
        $query = Review::with('user')
            ->where('product_id', $productId)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');

        $perPage = 10;
        $paginator = $query->paginate($perPage);

        $items = $paginator->through(function (Review $review) {
            return [
                'id' => $review->id,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->format('d/m/Y'),
                'user' => [
                    'name' => $review->user?->full_name ?? $review->user?->name ?? 'Người dùng',
                    'avatar' => $review->user?->avatar ?? null,
                ],
            ];
        });

        return [
            'data' => $items->all(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
