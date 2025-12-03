<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\FlashSaleEvent;
use App\Models\FlashSaleProduct;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductView;
use App\Models\Review;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\CartService;
use App\Models\User;

class HomeController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $user = User::with('roles')->find($user->id);
        }
        
        $locale = app()->getLocale();

        $categories = Cache::remember("home_categories_{$locale}", 900, function () use ($locale) {
            try {
                return Category::where('is_active', true)
                    ->whereNotNull('image_url')
                    ->orderBy('name') // Order by name alphabetically
                    ->limit(30)
                    ->get()
                    ->map(function ($category) use ($locale) {
                        return [
                            'id' => $category->category_id,
                            'name' => $category->getTranslation('name', $locale) ?? $category->name,
                            'img' => $category->image_url,
                            'slug' => $category->slug, // Add slug for routing
                        ];
                    })
                    ->values()
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Failed to fetch categories: ' . $e->getMessage());
                return []; // Return empty array instead of crashing
            }
        });

        $flashSaleData = Cache::remember("home_flash_sale_{$locale}", 900, function () use ($locale) {
            return $this->getFlashSaleData($locale);
        });

        $dailyDiscoverProducts = $this->getDailyDiscoverProducts($user, $locale);

        $cartItems = $user ? $this->cartService->getCartItems($user) : collect();

        return Inertia::render('Home/Index', [
            'categories' => $categories,
            'flashSale' => $flashSaleData,
            'dailyDiscover' => $dailyDiscoverProducts,
            'cartItems' => $cartItems->values()->all(),
            'user' => $user ? [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->roles->pluck('name')->toArray(), // Load roles as array
            ] : null,
            'isLoadingCategories' => empty($categories), // Add loading indicator
        ]);
    }

    private function getFlashSaleData(string $locale): ?array
    {
        try {
            $activeEvent = FlashSaleEvent::query()
                ->where('status', 'active')
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->first();

            if (!$activeEvent) {
                return null;
            }

            $flashSaleProducts = FlashSaleProduct::query()
                ->where('flash_sale_event_id', $activeEvent->id)
                ->where(function (Builder $query) {
                    $query->whereColumn('sold_count', '<', 'quantity_limit')
                        ->orWhere('quantity_limit', 0)
                        ->orWhereNull('quantity_limit');
                })
                ->whereHas('productVariant.product', function (Builder $productQuery) {
                    $productQuery->where('is_active', true);
                    $this->applyPublishedStatusFilter($productQuery);
                })
                ->with([
                    'productVariant.product.category',
                    'productVariant.product.brand',
                    'productVariant.product.images' => function ($query) {
                        $query->orderByDesc('is_primary')->orderBy('display_order');
                    },
                ])
                ->limit(10)
                ->get()
                ->map(function (FlashSaleProduct $flashSaleProduct) use ($locale) {
                    $variant = $flashSaleProduct->productVariant;
                    $product = $variant?->product;

                    if (!$product) {
                        return null;
                    }

                    return [
                        'id' => $product->product_id,
                        'name' => $product->getTranslation('name', $locale),
                        'image' => $this->resolveVariantImage($variant),
                        'original_price' => (float) ($variant?->price ?? 0),
                        'flash_sale_price' => (float) $flashSaleProduct->flash_sale_price,
                        'discount_percentage' => max(0, round((float) $flashSaleProduct->calculated_discount_percentage, 2)),
                        'sold_count' => (int) $flashSaleProduct->sold_count,
                        'quantity_limit' => (int) $flashSaleProduct->quantity_limit,
                        'remaining_quantity' => (int) $flashSaleProduct->remaining_quantity,
                        'max_quantity_per_user' => (int) $flashSaleProduct->max_quantity_per_user,
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
                    'status' => $activeEvent->status,
                    'start_time' => optional($activeEvent->start_time)?->toIso8601String(),
                    'end_time' => optional($activeEvent->end_time)?->toIso8601String(),
                    'banner_image' => $activeEvent->banner_image,
                ],
                'products' => $flashSaleProducts,
            ];
        } catch (\Throwable $e) {
            Log::error('Error in getFlashSaleData: ' . $e->getMessage(), ['exception' => $e]);

            return null;
        }
    }

    private function getDailyDiscoverProducts($user, string $locale): array
    {
        try {
            if ($user) {
                $query = $this->buildBaseProductQuery();

                $userPreference = UserPreference::where('user_id', $user->id)->first();

                if ($userPreference) {
                    if ($userPreference->preferred_category_id) {
                        $query->where('category_id', $userPreference->preferred_category_id);
                    }

                    if ($userPreference->preferred_brand_id) {
                        $query->where('brand_id', $userPreference->preferred_brand_id);
                    }

                    if ($userPreference->min_price_range || $userPreference->max_price_range) {
                        $query->whereHas('variants', function ($variantQuery) use ($userPreference) {
                            if ($userPreference->min_price_range) {
                                $variantQuery->where('price', '>=', $userPreference->min_price_range);
                            }
                            if ($userPreference->max_price_range) {
                                $variantQuery->where('price', '<=', $userPreference->max_price_range);
                            }
                        });
                    }
                } else {
                    $viewedCategories = ProductView::where('user_id', $user->id)
                        ->join('products', 'product_views.product_id', '=', 'products.product_id')
                        ->select('products.category_id')
                        ->distinct()
                        ->pluck('category_id')
                        ->take(3);

                    if ($viewedCategories->isNotEmpty()) {
                        $query->whereIn('category_id', $viewedCategories);
                    }
                }

                $viewedProductIds = ProductView::where('user_id', $user->id)
                    ->pluck('product_id');

                if ($viewedProductIds->isNotEmpty()) {
                    $query->whereNotIn('product_id', $viewedProductIds);
                }

                $products = $query->limit(20)->get();

                return $this->formatDailyDiscoverProducts($products, $locale);
            }

            return Cache::remember("home_daily_discover_guest_{$locale}", 900, function () use ($locale) {
                $products = $this->buildBaseProductQuery()
                    ->inRandomOrder()
                    ->limit(20)
                    ->get();

                return $this->formatDailyDiscoverProducts($products, $locale);
            });
        } catch (\Throwable $e) {
            Log::error('Error in getDailyDiscoverProducts: ' . $e->getMessage(), ['exception' => $e]);

            return [];
        }
    }

    private function buildBaseProductQuery(): Builder
    {
        $query = Product::with(['variants', 'images', 'category', 'brand'])
            ->where('is_active', true);

        $this->applyPublishedStatusFilter($query);

        return $query;
    }

    private function formatDailyDiscoverProducts(Collection $products, string $locale): array
    {
        if ($products->isEmpty()) {
            return [];
        }

        $productIds = $products->pluck('product_id')->all();
        $averageRatings = $this->fetchAverageRatings($productIds);
        $soldCounts = $this->fetchSoldCounts($productIds);

        return $products->map(function ($product) use ($locale, $averageRatings, $soldCounts) {
            $mainVariant = $product->variants->first();
            $mainImage = $product->images->where('is_primary', true)->first()
                ?? $product->images->first();

            return [
                'id' => $product->product_id,
                'name' => $product->getTranslation('name', $locale),
                'description' => $product->getTranslation('description', $locale),
                'image' => $mainImage?->image_url ?? '/images/placeholder.jpg',
                'price' => (float) ($mainVariant?->price ?? 0),
                'discount_price' => $mainVariant?->discount_price !== null ? (float) $mainVariant->discount_price : null,
                'category' => $product->category ? $product->category->getTranslation('name', $locale) : '',
                'brand' => $product->brand?->name ?? '',
                'rating' => $averageRatings[$product->product_id] ?? null,
                'sold_count' => $soldCounts[$product->product_id] ?? 0,
            ];
        })->values()->toArray();
    }

    private function applyPublishedStatusFilter(Builder $query): Builder
    {
        return $query->where(function (Builder $statusQuery) {
            $statusQuery->where('status', ProductStatus::PUBLISHED->value)
                ->orWhere('status', ProductStatus::PUBLISHED->name)
                ->orWhere('status', 3)
                ->orWhere('status', '3');
        });
    }

    private function resolveVariantImage(?ProductVariant $variant): string
    {
        if (!$variant) {
            return '/images/placeholder.jpg';
        }

        $product = $variant->relationLoaded('product')
            ? $variant->product
            : $variant->product()->with(['images' => function ($query) {
                $query->orderByDesc('is_primary')->orderBy('display_order');
            }])->first();

        if (!$product) {
            return '/images/placeholder.jpg';
        }

        $images = $product->relationLoaded('images')
            ? $product->images
            : $product->images()->orderByDesc('is_primary')->orderBy('display_order')->get();

        if ($variant->image_id) {
            $variantImage = $images->firstWhere('image_id', $variant->image_id);
            if ($variantImage) {
                return $variantImage->image_url;
            }
        }

        $primaryImage = $images->firstWhere('is_primary', true);
        if ($primaryImage) {
            return $primaryImage->image_url;
        }

        $fallbackImage = $images->sortBy('display_order')->first();
        if ($fallbackImage) {
            return $fallbackImage->image_url;
        }

        return '/images/placeholder.jpg';
    }

    private function fetchAverageRatings(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        return Review::query()
            ->whereIn('product_id', $productIds)
            ->where('is_approved', true)
            ->selectRaw('product_id, AVG(rating) as avg_rating')
            ->groupBy('product_id')
            ->pluck('avg_rating', 'product_id')
            ->map(function ($average) {
                return round((float) $average, 1);
            })
            ->toArray();
    }

    private function fetchSoldCounts(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        return OrderItem::query()
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
            ->whereIn('product_variants.product_id', $productIds)
            ->selectRaw('product_variants.product_id as product_id, SUM(order_items.quantity) as total_sold')
            ->groupBy('product_variants.product_id')
            ->pluck('total_sold', 'product_id')
            ->map(function ($count) {
                return (int) $count;
            })
            ->toArray();
    }
}