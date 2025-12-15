<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sellers\StoreProductRequest;
use App\Http\Requests\Sellers\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Display a listing of products
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $shop = $user->shops()->first();

        if (!$shop) {
            return Inertia::render('roles/sellers/product-manage/index', [
                'products' => [],
                'categories' => [],
                'filters' => [
                    'search' => '',
                    'category' => 'all',
                    'status' => 'all',
                ],
            ]);
        }

        $filters = [
            'search' => $request->input('search', ''),
            'category' => $request->input('category', 'all'),
            'status' => $request->input('status', 'all'),
        ];

        $products = $this->productService->getProducts($shop, $filters, 15);

        // Format products for frontend
        $formattedProducts = $products->through(function ($product) {
            $imageUrl = $product->images->first()?->image_url ?? 'https://via.placeholder.com/100';
            
            // Ensure image URL has proper domain for absolute URLs
            if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                $imageUrl = asset($imageUrl);
            }
            
            return [
                'id' => '#' . str_pad($product->id, 5, '0', STR_PAD_LEFT),
                'actual_id' => $product->id,
                'image' => $imageUrl,
                'name' => $product->product_name,
                'price' => number_format($product->base_price, 0, ',', '.') . 'đ',
                'stock' => $product->total_quantity,
                'category' => $product->category?->category_name ?? 'N/A',
                'status' => $product->status,
            ];
        });

        $categories = Category::select('id', 'category_name')->get();

        return Inertia::render('roles/sellers/product-manage/index', [
            'products' => $formattedProducts,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new product
     */
    public function create(): Response
    {
        $categories = Category::select('id', 'category_name', 'slug')->get();

        return Inertia::render('roles/sellers/product-manage/create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $shop = $user->shops()->first();

        if (!$shop) {
            return redirect()->back()->with('error', 'Bạn chưa có cửa hàng. Vui lòng tạo cửa hàng trước.');
        }

        try {
            \Log::info('Creating product', [
                'shop_id' => $shop->id,
                'data' => $request->except(['images']),
                'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            ]);

            $product = $this->productService->createProduct($shop, $request->validated());

            \Log::info('Product created successfully', [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
            ]);

            return redirect()
                ->route('seller.products.index')
                ->with('success', 'Tạo sản phẩm thành công!');
        } catch (\Exception $e) {
            \Log::error('Failed to create product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product
     */
    public function show(Product $product): Response
    {
        $user = Auth::user();
        $shop = $user->shops()->first();

        // Ensure the product belongs to the seller's shop
        if (!$shop || $product->shop_id !== $shop->id) {
            abort(403, 'Bạn không có quyền xem sản phẩm này.');
        }

        $product->load(['images', 'variants.images', 'category']);

        // Format product data for frontend
        $formattedProduct = [
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'slug' => $product->slug,
            'description' => $product->description,
            'base_price' => $product->base_price,
            'total_quantity' => $product->total_quantity,
            'status' => $product->status->value,
            'created_at' => $product->created_at->toISOString(),
            'category' => $product->category ? [
                'category_name' => $product->category->category_name,
            ] : null,
            'images' => $product->images->whereNull('variant_id')->map(function ($image) {
                $imageUrl = $image->image_url;
                if (!str_starts_with($imageUrl, 'http')) {
                    $imageUrl = asset($imageUrl);
                }
                return [
                    'id' => $image->id,
                    'image_url' => $imageUrl,
                    'is_primary' => $image->is_primary,
                    'display_order' => $image->display_order,
                ];
            })->sortBy('display_order')->values(),
            'variants' => $product->variants->map(function ($variant) {
                return [
                    'variant_id' => $variant->id,
                    'variant_name' => $variant->variant_name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'stock_quantity' => $variant->stock_quantity,
                    'attribute_values' => $variant->attribute_values,
                    'images' => $variant->images->map(function ($img) {
                        $imageUrl = $img->image_url;
                        if (!str_starts_with($imageUrl, 'http')) {
                            $imageUrl = asset($imageUrl);
                        }
                        return [
                            'id' => $img->id,
                            'image_url' => $imageUrl,
                        ];
                    })->values(),
                ];
            }),
        ];

        return Inertia::render('roles/sellers/product-manage/read', [
            'product' => $formattedProduct,
        ]);
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product): Response
    {
        $user = Auth::user();
        $shop = $user->shops()->first();

        // Ensure the product belongs to the seller's shop
        if (!$shop || $product->shop_id !== $shop->id) {
            abort(403, 'Bạn không có quyền chỉnh sửa sản phẩm này.');
        }

        $product->load(['images', 'variants.images', 'category']);
        $categories = Category::select('id', 'category_name', 'slug')->get();

        // Format product data for frontend
        $formattedProduct = [
            'id' => '#' . str_pad($product->id, 5, '0', STR_PAD_LEFT),
            'name' => $product->product_name,
            'description' => $product->description,
            'price' => number_format($product->base_price, 0, ',', '.') . 'đ',
            'compare_price' => '',
            'stock' => $product->total_quantity,
            'category' => $product->category_id,
            'status' => $product->status,
            'variants' => $product->variants->map(function ($variant) {
                $attributes = json_decode($variant->attribute_values, true) ?? [];
                return [
                    'id' => $variant->id,
                    'size' => $attributes['size'] ?? '',
                    'color' => $attributes['color'] ?? '',
                    'stock_quantity' => $variant->stock_quantity,
                    'existing_images' => $variant->images->map(function ($img) {
                        $imageUrl = $img->image_url;
                        if (!str_starts_with($imageUrl, 'http')) {
                            $imageUrl = asset($imageUrl);
                        }
                        return [
                            'id' => $img->id,
                            'url' => $imageUrl,
                        ];
                    })->values()->toArray(),
                ];
            }),
            'images' => $product->images->whereNull('variant_id')->map(function ($image) {
                $imageUrl = $image->image_url;
                if (!str_starts_with($imageUrl, 'http')) {
                    $imageUrl = asset($imageUrl);
                }
                return [
                    'id' => $image->id,
                    'url' => $imageUrl,
                    'is_primary' => $image->is_primary,
                ];
            })->values()->toArray(),
        ];

        return Inertia::render('roles/sellers/product-manage/update', [
            'product' => $formattedProduct,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            \Log::info('Product update request received', [
                'product_id' => $product->id,
                'validated_data' => $request->validated(),
                'has_images' => $request->hasFile('images'),
            ]);

            $this->productService->updateProduct($product, $request->validated());

            \Log::info('Product updated successfully', [
                'product_id' => $product->id,
            ]);

            return redirect()
                ->route('seller.products.index')
                ->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            \Log::error('Failed to update product', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product): RedirectResponse
    {
        $user = Auth::user();
        $shop = $user->shops()->first();

        // Ensure the product belongs to the seller's shop
        if (!$shop || $product->shop_id !== $shop->id) {
            abort(403, 'Bạn không có quyền xóa sản phẩm này.');
        }

        try {
            $this->productService->deleteProduct($product);

            return redirect()
                ->route('seller.products.index')
                ->with('success', 'Xóa sản phẩm thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
