<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShoppingController extends Controller
{
    /**
     * Display the shopping page with products, categories, and brands
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $categoryId = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $brandIds = $request->input('brands', []);
        // Convert brand IDs to integers if they are strings
        if (!empty($brandIds)) {
            $brandIds = array_map('intval', (array) $brandIds);
        }
        $sortBy = $request->input('sort_by', 'popular');
        $perPage = $request->input('per_page', 12);

        // Build query
        $query = Product::with(['images', 'brand', 'category'])
            ->where('status', 'active');

        // Apply category filter
        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        // Apply price filter
        if ($minPrice) {
            $query->where('base_price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('base_price', '<=', $maxPrice);
        }

        // Apply brand filter
        if (!empty($brandIds)) {
            $query->whereIn('brand_id', $brandIds);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price-asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'popular':
            default:
                $query->orderBy('total_sold', 'desc')
                      ->orderBy('view_count', 'desc');
                break;
        }

        // Paginate products
        $products = $query->paginate($perPage);

        // Transform products for frontend
        $transformedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'image' => $product->images->first()?->image_url ?? '',
                'price' => (float) $product->base_price,
                'originalPrice' => null, // Can be calculated from promotions if exists
                'rating' => (float) $product->rating,
                'reviews' => $product->review_count,
                'brand' => $product->brand?->brand_name ?? '',
            ];
        });

        // Get all categories for filter
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('display_order')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->category_name,
                ];
            });

        // Add "All" category at the beginning
        $categories->prepend([
            'id' => 'all',
            'name' => 'Tất cả sản phẩm',
        ]);

        // Get all brands for filter
        $brands = Brand::where('is_active', true)
            ->orderBy('brand_name')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->brand_name,
                ];
            });

        return Inertia::render('shopping', [
            'products' => $transformedProducts,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'categories' => $categories,
            'brands' => $brands,
            'filters' => [
                'category' => $categoryId ?? 'all',
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'selected_brands' => $brandIds,
                'sort_by' => $sortBy,
            ],
        ]);
    }
}
