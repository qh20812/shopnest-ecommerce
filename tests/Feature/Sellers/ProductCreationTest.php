<?php

namespace Tests\Feature\Sellers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected Shop $shop;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a seller user with a shop
        $this->seller = User::factory()->create();
        
        // Assign seller role
        $sellerRole = \App\Models\Role::firstOrCreate(
            ['role_name' => 'seller'],
            ['description' => 'Seller role for testing']
        );
        $this->seller->roles()->attach($sellerRole);

        $this->shop = Shop::factory()->create([
            'owner_id' => $this->seller->id,
            'shop_name' => 'Test Shop',
            'status' => 'active',
        ]);

        $this->category = Category::factory()->create([
            'category_name' => 'Test Category',
        ]);

        Storage::fake('public');
    }

    /** @test */
    public function seller_can_create_basic_product()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'This is a test product description',
            'base_price' => '100000',
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success', 'Tạo sản phẩm thành công!');

        $this->assertDatabaseHas('products', [
            'shop_id' => $this->shop->id,
            'seller_id' => $this->seller->id,
            'product_name' => 'Test Product',
            'base_price' => 100000,
            'total_quantity' => 50,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function seller_can_create_product_with_variants()
    {
        $productData = [
            'product_name' => 'T-Shirt',
            'description' => 'A nice t-shirt',
            'base_price' => '150000',
            'stock_quantity' => '100',
            'category_id' => $this->category->id,
            'status' => 'active',
            'variants' => [
                [
                    'size' => 'M',
                    'color' => 'Red',
                    'stock_quantity' => 30,
                ],
                [
                    'size' => 'L',
                    'color' => 'Blue',
                    'stock_quantity' => 40,
                ],
                [
                    'size' => 'XL',
                    'color' => 'Black',
                    'stock_quantity' => 30,
                ],
            ],
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success', 'Tạo sản phẩm thành công!');

        $product = Product::where('product_name', 'T-Shirt')->first();
        $this->assertNotNull($product);

        // Check variants were created
        $this->assertEquals(3, $product->variants()->count());

        // Check first variant
        $variant = $product->variants()->where('variant_name', 'M - Red')->first();
        $this->assertNotNull($variant);
        $this->assertEquals(30, $variant->stock_quantity);

        $attributeValues = json_decode($variant->attribute_values, true);
        $this->assertEquals('M', $attributeValues['size']);
        $this->assertEquals('Red', $attributeValues['color']);
    }

    /** @test */
    public function seller_can_create_product_with_images()
    {
        $productData = [
            'product_name' => 'Product with Images',
            'description' => 'Test product with multiple images',
            'base_price' => '200000',
            'stock_quantity' => '25',
            'category_id' => $this->category->id,
            'status' => 'active',
            'images' => [
                UploadedFile::fake()->image('product1.jpg', 800, 800),
                UploadedFile::fake()->image('product2.jpg', 800, 800),
                UploadedFile::fake()->image('product3.jpg', 800, 800),
            ],
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success', 'Tạo sản phẩm thành công!');

        $product = Product::where('product_name', 'Product with Images')->first();
        $this->assertNotNull($product);

        // Check images were uploaded
        $this->assertEquals(3, $product->images()->count());

        // Check first image is primary
        $primaryImage = $product->images()->where('is_primary', true)->first();
        $this->assertNotNull($primaryImage);
        $this->assertEquals(0, $primaryImage->display_order);

        // Check files were actually stored
        foreach ($product->images as $image) {
            $path = str_replace('/storage/', '', parse_url($image->image_url, PHP_URL_PATH));
            Storage::disk('public')->assertExists($path);
        }
    }

    /** @test */
    public function seller_can_create_product_with_variants_and_images()
    {
        $productData = [
            'product_name' => 'Complete Product',
            'description' => 'Product with everything',
            'base_price' => '300000',
            'stock_quantity' => '60',
            'category_id' => $this->category->id,
            'status' => 'active',
            'variants' => [
                [
                    'size' => 'S',
                    'color' => 'White',
                    'stock_quantity' => 20,
                ],
                [
                    'size' => 'M',
                    'color' => 'Black',
                    'stock_quantity' => 40,
                ],
            ],
            'images' => [
                UploadedFile::fake()->image('main.jpg', 800, 800),
                UploadedFile::fake()->image('side.jpg', 800, 800),
            ],
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success', 'Tạo sản phẩm thành công!');

        $product = Product::where('product_name', 'Complete Product')->first();
        $this->assertNotNull($product);

        // Verify product
        $this->assertEquals(300000, $product->base_price);
        $this->assertEquals(60, $product->total_quantity);

        // Verify variants
        $this->assertEquals(2, $product->variants()->count());

        // Verify images
        $this->assertEquals(2, $product->images()->count());
    }

    /** @test */
    public function validation_fails_without_required_fields()
    {
        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), []);

        $response->assertSessionHasErrors([
            'product_name',
            'base_price',
            'stock_quantity',
            'category_id',
            'status',
        ]);
    }

    /** @test */
    public function validation_fails_with_invalid_category()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Description',
            'base_price' => '100000',
            'stock_quantity' => '50',
            'category_id' => 99999, // Non-existent category
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertSessionHasErrors(['category_id']);
    }

    /** @test */
    public function validation_fails_with_invalid_status()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Description',
            'base_price' => '100000',
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'invalid_status',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function validation_fails_with_invalid_image_type()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Description',
            'base_price' => '100000',
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'active',
            'images' => [
                UploadedFile::fake()->create('document.pdf', 100),
            ],
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertSessionHasErrors(['images.0']);
    }

    /** @test */
    public function non_seller_cannot_create_product()
    {
        $customer = User::factory()->create();
        
        // Assign customer role
        $customerRole = \App\Models\Role::firstOrCreate(
            ['role_name' => 'customer'],
            ['description' => 'Customer role for testing']
        );
        $customer->roles()->attach($customerRole);

        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Description',
            'base_price' => '100000',
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($customer)
            ->post(route('seller.products.store'), $productData);

        $response->assertForbidden();
    }

    /** @test */
    public function seller_without_shop_cannot_create_product()
    {
        // Create a seller without a shop
        $newSeller = User::factory()->create();
        
        // Assign seller role
        $sellerRole = \App\Models\Role::firstOrCreate(
            ['role_name' => 'seller'],
            ['description' => 'Seller role for testing']
        );
        $newSeller->roles()->attach($sellerRole);

        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Description',
            'base_price' => '100000',
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($newSeller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Bạn chưa có cửa hàng. Vui lòng tạo cửa hàng trước.');
    }

    /** @test */
    public function price_formatting_is_parsed_correctly()
    {
        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Description',
            'base_price' => '1.500.000đ', // Vietnamese formatting
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'product_name' => 'Test Product',
            'base_price' => 1500000,
        ]);
    }

    /** @test */
    public function product_slug_is_generated_automatically()
    {
        $productData = [
            'product_name' => 'Áo Thun Nam Cao Cấp',
            'description' => 'Description',
            'base_price' => '200000',
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('product_name', 'Áo Thun Nam Cao Cấp')->first();
        $this->assertNotNull($product);
        $this->assertEquals('ao-thun-nam-cao-cap', $product->slug);
    }

    /** @test */
    public function duplicate_product_names_get_unique_slugs()
    {
        // Create first product
        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), [
                'product_name' => 'Test Product',
                'description' => 'First product',
                'base_price' => '100000',
                'stock_quantity' => '50',
                'category_id' => $this->category->id,
                'status' => 'active',
            ]);

        // Create second product with same name
        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), [
                'product_name' => 'Test Product',
                'description' => 'Second product',
                'base_price' => '150000',
                'stock_quantity' => '30',
                'category_id' => $this->category->id,
                'status' => 'active',
            ]);

        $products = Product::where('product_name', 'Test Product')->get();
        $this->assertEquals(2, $products->count());

        // Check slugs are unique
        $slugs = $products->pluck('slug')->toArray();
        $this->assertContains('test-product', $slugs);
        $this->assertContains('test-product-1', $slugs);
    }
}
