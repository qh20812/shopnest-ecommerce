<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return Inertia::render('wish-list', [
                'wishlistItems' => [],
            ]);
        }

        // Get or create user's default wishlist
        $wishlist = Wishlist::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => 'My Wishlist', 'is_public' => false]
        );

        // Get wishlist items with product details
        $wishlistItems = WishlistItem::where('wishlist_id', $wishlist->id)
            ->with(['product' => function($query) {
                $query->where('status', 'active')
                    ->with(['images' => function($q) {
                        $q->where('is_primary', true);
                    }, 'variants' => function($q) {
                        $q->where('is_active', true)
                            ->orderBy('price', 'asc');
                    }]);
            }])
            ->get()
            ->map(function ($item) {
                if (!$item->product) {
                    return null;
                }

                $product = $item->product;
                $primaryImage = $product->images->first();
                $cheapestVariant = $product->variants->first();

                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'name' => $product->product_name,
                    'image' => $primaryImage ? $primaryImage->image_url : null,
                    'price' => $cheapestVariant ? $cheapestVariant->price : $product->base_price,
                ];
            })
            ->filter() // Remove null items
            ->values(); // Reset array keys

        return Inertia::render('wish-list', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    /**
     * Add a product to wishlist
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm vào danh sách yêu thích',
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Get or create user's default wishlist
        $wishlist = Wishlist::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => 'My Wishlist', 'is_public' => false]
        );

        // Check if product is active
        $product = Product::where('id', $validated['product_id'])
            ->where('status', 'active')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại hoặc không còn hoạt động',
            ], 404);
        }

        // Check if already in wishlist
        $existingItem = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm đã có trong danh sách yêu thích',
            ], 409);
        }

        // Add to wishlist
        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào danh sách yêu thích',
        ]);
    }

    /**
     * Remove a product from wishlist
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        $wishlist = Wishlist::where('user_id', $user->id)->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh sách yêu thích',
            ], 404);
        }

        $item = WishlistItem::where('id', $id)
            ->where('wishlist_id', $wishlist->id)
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong danh sách yêu thích',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi danh sách yêu thích',
        ]);
    }

    /**
     * Remove all items from wishlist
     */
    public function clear()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        $wishlist = Wishlist::where('user_id', $user->id)->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh sách yêu thích',
            ], 404);
        }

        $deletedCount = WishlistItem::where('wishlist_id', $wishlist->id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$deletedCount} sản phẩm khỏi danh sách yêu thích",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Add all wishlist items to cart
     */
    public function addAllToCart()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        $wishlist = Wishlist::where('user_id', $user->id)->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh sách yêu thích',
            ], 404);
        }

        $wishlistItems = WishlistItem::where('wishlist_id', $wishlist->id)
            ->with(['product' => function($query) {
                $query->where('status', 'active')
                    ->with(['variants' => function($q) {
                        $q->where('is_active', true)
                            ->orderBy('price', 'asc');
                    }]);
            }])
            ->get();

        if ($wishlistItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Danh sách yêu thích trống',
            ], 400);
        }

        $addedCount = 0;
        
        foreach ($wishlistItems as $item) {
            if (!$item->product) {
                continue;
            }

            $product = $item->product;
            $cheapestVariant = $product->variants->first();

            if (!$cheapestVariant) {
                continue;
            }

            // Check if already in cart
            $existingCartItem = DB::table('cart_items')
                ->where('user_id', $user->id)
                ->where('product_variant_id', $cheapestVariant->id)
                ->first();

            if ($existingCartItem) {
                // Update quantity
                DB::table('cart_items')
                    ->where('id', $existingCartItem->id)
                    ->increment('quantity', 1);
            } else {
                // Add new item
                DB::table('cart_items')->insert([
                    'user_id' => $user->id,
                    'product_variant_id' => $cheapestVariant->id,
                    'quantity' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $addedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Đã thêm {$addedCount} sản phẩm vào giỏ hàng",
            'added_count' => $addedCount,
        ]);
    }
}
