<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Category;
use App\Models\FlashSaleEvent;
use App\Models\FlashSaleProduct;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page with categories, flash sales, and product recommendations
     */
    public function index(Request $request): Response
    {
        try {
            // Get data for welcome page
            $categories = $this->getCategories();
            $flashSale = $this->getFlashSaleData();
            $suggestedProducts = $this->getSuggestedProducts();
            $bestSellers = $this->getBestSellerProducts();

            return Inertia::render('welcome', [
                'categories' => $categories,
                'flashSale' => $flashSale,
                'banners' => $this->getBanners(),
                'suggestedProducts' => $suggestedProducts,
                'bestSellers' => $bestSellers,
                'canRegister' => true, // Keep for compatibility
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in WelcomeController: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Return page with empty data on error
            return Inertia::render('welcome', [
                'categories' => [],
                'flashSale' => null,
                'suggestedProducts' => [],
                'bestSellers' => [],
                'canRegister' => true,
            ]);
        }
    }

    /**
     * Get categories for display
     */
    private function getCategories(): array
    {
        return Cache::remember('welcome_categories', 900, function () {
            try {
                return Category::where('is_active', true)
                    ->orderBy('category_name')
                    ->limit(20)
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->category_name,
                            'slug' => $category->slug,
                            'icon' => $this->getCategoryIcon($category->category_name),
                            'image_url' => $category->image_url,
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Failed to fetch categories: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get banners to show on the welcome page
     */
    private function getBanners(): array
    {
        return Cache::remember('welcome_banners', 900, function () {
            try {
                $banners = Banner::where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_time')->orWhere('start_time', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_time')->orWhere('end_time', '>=', now());
                    })
                    ->orderBy('display_order')
                    ->get()
                    ->map(function (Banner $banner) {
                        return [
                            'id' => $banner->id,
                            'title' => $banner->title,
                            'subtitle' => $banner->subtitle,
                            'link' => $banner->link,
                            'image' => $banner->image_url ?: '/images/banner-placeholder.png',
                            'alt' => $banner->alt_text,
                            'placement' => $banner->placement,
                        ];
                    })
                    ->values()
                    ->toArray();

                // If no banners found, return empty array to let frontend show fallback
                return $banners;
            } catch (\Throwable $e) {
                Log::error('Failed to fetch banners: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get flash sale data with products
     */
    private function getFlashSaleData(): ?array
    {
        return Cache::remember('welcome_flash_sale', 900, function () {
            try {
                // Find active flash sale event
                $activeEvent = FlashSaleEvent::where('status', 'active')
                    ->where('start_time', '<=', now())
                    ->where('end_time', '>=', now())
                    ->first();

                if (!$activeEvent) {
                    return null;
                }

                // Get flash sale products
                $flashSaleProducts = FlashSaleProduct::where('flash_sale_event_id', $activeEvent->id)
                    ->where(function (Builder $query) {
                        $query->whereColumn('sold_count', '<', 'quantity_limit')
                            ->orWhere('quantity_limit', 0)
                            ->orWhereNull('quantity_limit');
                    })
                    ->whereHas('productVariant.product', function (Builder $query) {
                        $query->where('is_active', true)
                            ->where('status', ProductStatus::ACTIVE);
                    })
                    ->with([
                        'productVariant.product.images' => function ($query) {
                            $query->orderByDesc('is_primary')->orderBy('display_order');
                        },
                    ])
                    ->limit(10)
                    ->get()
                    ->map(function (FlashSaleProduct $flashSaleProduct) {
                        $variant = $flashSaleProduct->productVariant;
                        $product = $variant?->product;

                        if (!$product) {
                            return null;
                        }

                        $image = $product->images->first();

                        return [
                            'id' => $product->product_id,
                            'name' => $product->product_name,
                            'image' => $image?->image_url ?? '/images/placeholder.jpg',
                            'price' => (float) $flashSaleProduct->flash_sale_price,
                            'oldPrice' => (float) ($variant?->price ?? 0),
                            'discount' => (int) round($flashSaleProduct->calculated_discount_percentage ?? 0),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray();

                if (empty($flashSaleProducts)) {
                    return null;
                }

                return [
                    'event' => [
                        'id' => $activeEvent->id,
                        'name' => $activeEvent->name,
                        'start_time' => $activeEvent->start_time?->toIso8601String(),
                        'end_time' => $activeEvent->end_time?->toIso8601String(),
                    ],
                    'products' => $flashSaleProducts,
                ];
            } catch (\Throwable $e) {
                Log::error('Error in getFlashSaleData: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get suggested products for guests
     */
    private function getSuggestedProducts(): array
    {
        return Cache::remember('welcome_suggested_products', 900, function () {
            try {
                $products = Product::with(['variants', 'images', 'category', 'brand'])
                    ->where('is_active', true)
                    ->where('status', ProductStatus::ACTIVE)
                    ->inRandomOrder()
                    ->limit(20)
                    ->get();

                return $this->formatProducts($products);
            } catch (\Throwable $e) {
                Log::error('Error in getSuggestedProducts: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get best seller products
     */
    private function getBestSellerProducts(): array
    {
        return Cache::remember('welcome_best_sellers', 900, function () {
            try {
                // Get product IDs with most sales
                $topProductIds = OrderItem::selectRaw('product_variants.product_id, SUM(order_items.quantity) as total_sold')
                    ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
                    ->join('products', 'product_variants.product_id', '=', 'products.product_id')
                    ->where('products.is_active', true)
                    ->where('products.status', ProductStatus::ACTIVE->value)
                    ->groupBy('product_variants.product_id')
                    ->orderByDesc('total_sold')
                    ->limit(20)
                    ->pluck('product_id');

                if ($topProductIds->isEmpty()) {
                    // Fallback to random products if no sales data
                    return $this->getSuggestedProducts();
                }

                $products = Product::with(['variants', 'images', 'category', 'brand'])
                    ->whereIn('product_id', $topProductIds)
                    ->get();

                return $this->formatProducts($products);
            } catch (\Throwable $e) {
                Log::error('Error in getBestSellerProducts: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Format products for frontend
     */
    private function formatProducts($products): array
    {
        if ($products->isEmpty()) {
            return [];
        }

        $productIds = $products->pluck('product_id')->all();
        $averageRatings = $this->getAverageRatings($productIds);
        $soldCounts = $this->getSoldCounts($productIds);

        return $products->map(function ($product) use ($averageRatings, $soldCounts) {
            $mainVariant = $product->variants->first();
            $mainImage = $product->images->where('is_primary', true)->first()
                ?? $product->images->first();

            return [
                'id' => $product->product_id,
                'name' => $product->product_name,
                'description' => $product->description,
                'image' => $mainImage?->image_url ?? '/images/placeholder.jpg',
                'price' => (float) ($mainVariant?->price ?? 0),
                'category' => $product->category?->name ?? '',
                'brand' => $product->brand?->name ?? '',
                'rating' => $averageRatings[$product->product_id] ?? 0,
                'reviews' => $soldCounts[$product->product_id] ?? 0,
            ];
        })->values()->toArray();
    }

    /**
     * Get average ratings for products
     */
    private function getAverageRatings(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        return Review::whereIn('product_id', $productIds)
            ->where('is_approved', true)
            ->selectRaw('product_id, AVG(rating) as avg_rating')
            ->groupBy('product_id')
            ->pluck('avg_rating', 'product_id')
            ->map(fn($avg) => round((float) $avg, 1))
            ->toArray();
    }

    /**
     * Get sold counts for products
     */
    private function getSoldCounts(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        return OrderItem::join('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
            ->whereIn('product_variants.product_id', $productIds)
            ->selectRaw('product_variants.product_id, SUM(order_items.quantity) as total_sold')
            ->groupBy('product_variants.product_id')
            ->pluck('total_sold', 'product_id')
            ->map(fn($count) => (int) $count)
            ->toArray();
    }

    /**
     * Get icon for category (fallback icons)
     */
    private function getCategoryIcon(string $categoryName): string
    {
        $icons = [
            'điện tử' => '💻',
            'thời trang' => '👔',
            'nhà' => '🌿',
            'vườn' => '🌿',
            'sắc đẹp' => '✨',
            'thể thao' => '⚽',
            'đồ chơi' => '🧸',
            'sách' => '📚',
            'điện thoại' => '📱',
            'laptop' => '💻',
            'máy tính' => '💻',
            'phụ kiện' => '🎧',
            'giày dép' => '👟',
            'túi xách' => '👜',
            'đồng hồ' => '⌚',
            'mỹ phẩm' => '💄',
            'gia dụng' => '🏠',
            'thực phẩm' => '🍔',
            'đồ uống' => '☕',
            'xe cộ' => '🚗',
        ];

        $lowerName = mb_strtolower($categoryName);

        foreach ($icons as $key => $icon) {
            if (str_contains($lowerName, $key)) {
                return $icon;
            }
        }

        return '📦'; // Default icon
    }
}
