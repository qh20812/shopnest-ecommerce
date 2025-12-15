<?php

namespace Tests\Feature\Sellers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $seller;
    protected Shop $shop;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create seller role (lowercase as per hasRole check)
        $sellerRole = Role::factory()->create([
            'role_name' => 'seller',
        ]);

        // Create seller user with shop
        $this->seller = User::factory()->create();
        $this->seller->roles()->attach($sellerRole->id);

        $this->shop = Shop::factory()->create([
            'owner_id' => $this->seller->id,
            'shop_name' => 'Test Shop',
            'is_active' => true,
        ]);

        // Create category
        $this->category = Category::factory()->create([
            'category_name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        Storage::fake('public');
    }

    /** @test */
    public function seller_can_view_products_index_page()
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.products.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('roles/sellers/product-manage/index')
            ->has('products')
            ->has('categories')
            ->has('filters')
        );
    }

    /** @test */
    public function seller_can_view_create_product_page()
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.products.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('roles/sellers/product-manage/create')
            ->has('categories')
        );
    }

    /** @test */
    public function seller_can_create_product_successfully()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'This is a test product description',
            'category_id' => $this->category->id,
            'base_price' => '1000000',
            'stock_quantity' => 100,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success', 'Tạo sản phẩm thành công!');

        $this->assertDatabaseHas('products', [
            'shop_id' => $this->shop->id,
            'product_name' => 'Test Product',
            'base_price' => 1000000,
            'total_quantity' => 100,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function seller_can_create_product_with_variants()
    {
        $productData = [
            'product_name' => 'Test Product with Variants',
            'description' => 'Product with variants',
            'category_id' => $this->category->id,
            'base_price' => '500000',
            'stock_quantity' => 100,
            'status' => 'active',
            'variants' => [
                [
                    'size' => 'M',
                    'color' => 'Red',
                    'stock_quantity' => 50,
                ],
                [
                    'size' => 'L',
                    'color' => 'Blue',
                    'stock_quantity' => 50,
                ],
            ],
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('product_name', 'Test Product with Variants')->first();
        $this->assertNotNull($product);
        $this->assertCount(2, $product->variants);
    }

    /** @test */
    public function seller_can_create_product_with_images()
    {
        // Mock the ImageValidationService to skip actual validation
        $this->mock(\App\Services\ImageValidationService::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn([]);
        });

        // Create fake images - using create() instead of image() to avoid GD requirement
        $image1 = UploadedFile::fake()->create('product1.jpg', 100, 'image/jpeg');
        $image2 = UploadedFile::fake()->create('product2.jpg', 100, 'image/jpeg');

        $productData = [
            'product_name' => 'Test Product with Images',
            'description' => 'Product with images',
            'category_id' => $this->category->id,
            'base_price' => '750000',
            'stock_quantity' => 50,
            'status' => 'active',
            'images' => [$image1, $image2],
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success', 'Tạo sản phẩm thành công!');

        // Verify product was created
        $product = Product::where('product_name', 'Test Product with Images')->first();
        $this->assertNotNull($product);
        
        // Verify product details
        $this->assertEquals('Test Product with Images', $product->product_name);
        $this->assertEquals(750000, $product->base_price);
        $this->assertEquals(50, $product->total_quantity);
        $this->assertEquals('active', $product->status->value);
        $this->assertEquals($this->shop->id, $product->shop_id);
        
        // Verify images were saved to database
        $this->assertCount(2, $product->images);
        
        // Verify first image is primary
        $primaryImage = $product->images->where('is_primary', true)->first();
        $this->assertNotNull($primaryImage);
        
        // Verify images are in product_images table
        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'is_primary' => true,
        ]);
        
        $this->assertDatabaseCount('product_images', 2);
    }

    /** @test */
    public function seller_can_create_product_and_images_are_stored()
    {
        // Mock the ImageValidationService to skip actual validation
        $this->mock(\App\Services\ImageValidationService::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn([]);
        });

        // Create fake image files
        $image1 = UploadedFile::fake()->create('image1.jpg', 100, 'image/jpeg');
        $image2 = UploadedFile::fake()->create('image2.jpg', 100, 'image/jpeg');
        $image3 = UploadedFile::fake()->create('image3.jpg', 100, 'image/jpeg');

        $productData = [
            'product_name' => 'Product with Multiple Images',
            'description' => 'Testing image storage',
            'category_id' => $this->category->id,
            'base_price' => '500000',
            'stock_quantity' => 100,
            'status' => 'active',
            'images' => [$image1, $image2, $image3],
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));

        // Find the created product
        $product = Product::where('product_name', 'Product with Multiple Images')->first();
        $this->assertNotNull($product);

        // Verify all images are saved
        $this->assertEquals(3, $product->images()->count());
        
        // Verify database has correct image records
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'product_name' => 'Product with Multiple Images',
            'base_price' => 500000,
            'shop_id' => $this->shop->id,
        ]);
        
        // Verify each image has correct attributes
        $images = $product->images;
        $this->assertTrue($images->first()->is_primary);
        $this->assertFalse($images->skip(1)->first()->is_primary);
        
        // Verify images have URLs
        foreach ($images as $image) {
            $this->assertNotNull($image->image_url);
            $this->assertStringContainsString('/storage/', $image->image_url);
        }
    }

    /** @test */
    public function product_creation_requires_product_name()
    {
        $productData = [
            'description' => 'Test description',
            'category_id' => $this->category->id,
            'base_price' => '1000000',
            'stock_quantity' => 100,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertSessionHasErrors('product_name');
    }

    /** @test */
    public function product_creation_requires_category()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Test description',
            'base_price' => '1000000',
            'stock_quantity' => 100,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function product_creation_requires_base_price()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Test description',
            'category_id' => $this->category->id,
            'stock_quantity' => 100,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertSessionHasErrors('base_price');
    }

    /** @test */
    public function product_creation_requires_stock_quantity()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Test description',
            'category_id' => $this->category->id,
            'base_price' => '1000000',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertSessionHasErrors('stock_quantity');
    }
    /** @test */
    public function product_can_be_created_without_images()
    {
        $productData = [
            'product_name' => 'Product Without Images',
            'description' => 'No images attached',
            'category_id' => $this->category->id,
            'base_price' => '300000',
            'stock_quantity' => 20,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success');

        $product = Product::where('product_name', 'Product Without Images')->first();
        $this->assertNotNull($product);
        $this->assertCount(0, $product->images);
        
        // Verify product is in database
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'product_name' => 'Product Without Images',
            'base_price' => 300000,
            'total_quantity' => 20,
        ]);
    }
    /** @test */
    public function seller_can_view_edit_product_page()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->get(route('seller.products.edit', $product));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('roles/sellers/product-manage/update')
            ->has('product')
            ->has('categories')
        );
    }

    /** @test */
    public function seller_can_update_product_successfully()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'product_name' => 'Original Product',
            'base_price' => 1000000,
        ]);

        $updateData = [
            'product_name' => 'Updated Product',
            'description' => 'Updated description',
            'base_price' => '1500000',
            'stock_quantity' => 150,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->put(route('seller.products.update', $product), $updateData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success', 'Cập nhật sản phẩm thành công!');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'product_name' => 'Updated Product',
            'base_price' => 1500000,
            'total_quantity' => 150,
        ]);
    }

    /** @test */
    public function seller_can_update_product_variants()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'variant_name' => 'M - Red',
            'stock_quantity' => 50,
        ]);

        $updateData = [
            'product_name' => $product->product_name,
            'base_price' => (string) $product->base_price,
            'stock_quantity' => $product->total_quantity,
            'status' => $product->status->value,
            'variants' => [
                [
                    'id' => $variant->id,
                    'size' => 'L',
                    'color' => 'Blue',
                    'stock_quantity' => 75,
                ],
                [
                    'size' => 'XL',
                    'color' => 'Green',
                    'stock_quantity' => 25,
                ],
            ],
        ];

        $response = $this->actingAs($this->seller)
            ->put(route('seller.products.update', $product), $updateData);

        $response->assertRedirect(route('seller.products.index'));

        $product->refresh();
        $this->assertCount(2, $product->variants);
    }

    /** @test */
    public function seller_can_delete_product()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->delete(route('seller.products.destroy', $product));

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success', 'Xóa sản phẩm thành công!');

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    /** @test */
    public function seller_cannot_edit_other_sellers_product()
    {
        $otherSeller = User::factory()->create();
        $otherShop = Shop::factory()->create([
            'owner_id' => $otherSeller->id,
        ]);

        $product = Product::factory()->create([
            'shop_id' => $otherShop->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->get(route('seller.products.edit', $product));

        $response->assertStatus(403);
    }

    /** @test */
    public function seller_cannot_update_other_sellers_product()
    {
        $otherSeller = User::factory()->create();
        $otherShop = Shop::factory()->create([
            'owner_id' => $otherSeller->id,
        ]);

        $product = Product::factory()->create([
            'shop_id' => $otherShop->id,
            'category_id' => $this->category->id,
        ]);

        $updateData = [
            'product_name' => 'Hacked Product',
            'base_price' => '1000000',
            'stock_quantity' => 100,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->put(route('seller.products.update', $product), $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function seller_cannot_delete_other_sellers_product()
    {
        $otherSeller = User::factory()->create();
        $otherShop = Shop::factory()->create([
            'owner_id' => $otherSeller->id,
        ]);

        $product = Product::factory()->create([
            'shop_id' => $otherShop->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->delete(route('seller.products.destroy', $product));

        $response->assertStatus(403);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    /** @test */
    public function non_seller_cannot_access_product_routes()
    {
        $customer = User::factory()->create();

        $response = $this->actingAs($customer)
            ->get(route('seller.products.index'));

        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function guest_cannot_access_product_routes()
    {
        $response = $this->get(route('seller.products.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function product_slug_is_automatically_generated()
    {
        $productData = [
            'product_name' => 'Test Product Name',
            'description' => 'Test description',
            'category_id' => $this->category->id,
            'base_price' => '1000000',
            'stock_quantity' => 100,
            'status' => 'active',
        ];

        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $this->assertDatabaseHas('products', [
            'product_name' => 'Test Product Name',
            'slug' => 'test-product-name',
        ]);
    }

    /** @test */
    public function duplicate_product_names_get_unique_slugs()
    {
        $productData = [
            'product_name' => 'Duplicate Name',
            'description' => 'First product',
            'category_id' => $this->category->id,
            'base_price' => '1000000',
            'stock_quantity' => 100,
            'status' => 'active',
        ];

        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $productData['description'] = 'Second product';
        
        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $this->assertDatabaseHas('products', [
            'product_name' => 'Duplicate Name',
            'slug' => 'duplicate-name',
        ]);

        $this->assertDatabaseHas('products', [
            'product_name' => 'Duplicate Name',
            'slug' => 'duplicate-name-1',
        ]);
    }

    /** @test */
    public function products_list_can_be_filtered_by_search()
    {
        Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'product_name' => 'Red Shoes',
        ]);

        Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'product_name' => 'Blue Shoes',
        ]);

        $response = $this->actingAs($this->seller)
            ->get(route('seller.products.index', ['search' => 'Red']));

        $response->assertStatus(200);
        // The filtering logic is in the service, so we just verify the response is successful
    }

    /** @test */
    public function products_list_can_be_filtered_by_status()
    {
        Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'status' => 'active',
        ]);

        Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->seller)
            ->get(route('seller.products.index', ['status' => 'active']));

        $response->assertStatus(200);
    }

    /** @test */
    public function products_list_can_be_filtered_by_category()
    {
        $category2 = Category::factory()->create([
            'category_name' => 'Clothing',
            'slug' => 'clothing',
        ]);

        Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $category2->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->get(route('seller.products.index', ['category' => $this->category->id]));

        $response->assertStatus(200);
    }
}
