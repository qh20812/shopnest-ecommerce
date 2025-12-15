<?php

namespace Tests\Feature;

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

class ProductCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected Shop $shop;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        // Create a seller user with shop
        $this->seller = User::factory()->create();
        $this->shop = Shop::factory()->create(['user_id' => $this->seller->id]);
        
        // Assign seller role
        $this->seller->assignRole('seller');
        
        // Create a category
        $this->category = Category::factory()->create([
            'category_name' => 'Electronics',
            'slug' => 'electronics',
        ]);
    }

    /** @test */
    public function it_can_create_product_with_variants_and_images()
    {
        $this->actingAs($this->seller);

        $productData = [
            'product_name' => 'Test Product',
            'description' => 'Test Description',
            'base_price' => '100000',
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'active',
            'variants' => [
                [
                    'size' => 'M',
                    'color' => 'Red',
                    'stock_quantity' => 20,
                ],
                [
                    'size' => 'L',
                    'color' => 'Blue',
                    'stock_quantity' => 30,
                ],
            ],
            'images' => [
                UploadedFile::fake()->image('product1.jpg'),
                UploadedFile::fake()->image('product2.jpg'),
            ],
        ];

        $response = $this->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success');

        // Assert product was created
        $this->assertDatabaseHas('products', [
            'product_name' => 'Test Product',
            'shop_id' => $this->shop->id,
            'base_price' => 100000,
            'status' => 'active',
        ]);

        $product = Product::where('product_name', 'Test Product')->first();
        $this->assertNotNull($product);

        // Assert variants were created
        $this->assertCount(2, $product->variants);
        
        $variant1 = $product->variants->first();
        $this->assertEquals('M', json_decode($variant1->attribute_values, true)['size']);
        $this->assertEquals('Red', json_decode($variant1->attribute_values, true)['color']);
        $this->assertEquals(20, $variant1->stock_quantity);

        // Assert images were created
        $this->assertCount(2, $product->images);
        
        $primaryImage = $product->images->where('is_primary', true)->first();
        $this->assertNotNull($primaryImage);
    }

    /** @test */
    public function it_can_update_product_basic_info()
    {
        $this->actingAs($this->seller);

        // Create a product first
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'product_name' => 'Original Name',
            'description' => 'Original Description',
            'base_price' => 100000,
            'total_quantity' => 50,
            'category_id' => $this->category->id,
            'status' => 'active',
        ]);

        // Update the product
        $updateData = [
            'product_name' => 'Updated Name',
            'description' => 'Updated Description',
            'base_price' => '150000',
            'stock_quantity' => '75',
            'category_id' => $this->category->id,
            'status' => 'inactive',
        ];

        $response = $this->put(route('seller.products.update', $product), $updateData);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success');

        // Assert database was updated
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'product_name' => 'Updated Name',
            'description' => 'Updated Description',
            'base_price' => 150000,
            'total_quantity' => 75,
            'status' => 'inactive',
        ]);

        // Refresh and verify
        $product->refresh();
        $this->assertEquals('Updated Name', $product->product_name);
        $this->assertEquals('Updated Description', $product->description);
        $this->assertEquals(150000, $product->base_price);
        $this->assertEquals(75, $product->total_quantity);
        $this->assertEquals('inactive', $product->status->value);
    }

    /** @test */
    public function it_can_update_product_variants()
    {
        $this->actingAs($this->seller);

        // Create a product with variants
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        $variant1 = ProductVariant::factory()->create([
            'product_id' => $product->product_id,
            'stock_quantity' => 10,
            'attribute_values' => json_encode(['size' => 'S', 'color' => 'Red']),
        ]);

        $variant2 = ProductVariant::factory()->create([
            'product_id' => $product->product_id,
            'stock_quantity' => 20,
            'attribute_values' => json_encode(['size' => 'M', 'color' => 'Blue']),
        ]);

        // Update variants
        $updateData = [
            'product_name' => $product->product_name,
            'base_price' => (string)$product->base_price,
            'stock_quantity' => (string)$product->total_quantity,
            'category_id' => $this->category->id,
            'status' => $product->status->value,
            'variants' => [
                [
                    'id' => $variant1->id,
                    'size' => 'S',
                    'color' => 'Red',
                    'stock_quantity' => 15, // Updated from 10
                ],
                [
                    'id' => $variant2->id,
                    'size' => 'M',
                    'color' => 'Green', // Updated from Blue
                    'stock_quantity' => 25, // Updated from 20
                ],
                [
                    // New variant
                    'size' => 'L',
                    'color' => 'Yellow',
                    'stock_quantity' => 30,
                ],
            ],
        ];

        $response = $this->put(route('seller.products.update', $product), $updateData);

        $response->assertRedirect(route('seller.products.index'));

        // Refresh product
        $product->refresh();

        // Assert we now have 3 variants
        $this->assertCount(3, $product->variants);

        // Assert first variant was updated
        $variant1->refresh();
        $this->assertEquals(15, $variant1->stock_quantity);

        // Assert second variant was updated
        $variant2->refresh();
        $this->assertEquals('Green', json_decode($variant2->attribute_values, true)['color']);
        $this->assertEquals(25, $variant2->stock_quantity);

        // Assert third variant was created
        $newVariant = $product->variants()->whereJsonContains('attribute_values->size', 'L')->first();
        $this->assertNotNull($newVariant);
        $this->assertEquals('Yellow', json_decode($newVariant->attribute_values, true)['color']);
        $this->assertEquals(30, $newVariant->stock_quantity);
    }

    /** @test */
    public function it_can_add_images_to_existing_product()
    {
        $this->actingAs($this->seller);

        // Create a product
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        // Add initial image
        ProductImage::factory()->create([
            'product_id' => $product->product_id,
            'is_primary' => true,
        ]);

        $this->assertCount(1, $product->images);

        // Add more images via update
        $updateData = [
            'product_name' => $product->product_name,
            'base_price' => (string)$product->base_price,
            'stock_quantity' => (string)$product->total_quantity,
            'category_id' => $this->category->id,
            'status' => $product->status->value,
            'images' => [
                UploadedFile::fake()->image('new_image1.jpg'),
                UploadedFile::fake()->image('new_image2.jpg'),
            ],
        ];

        $response = $this->put(route('seller.products.update', $product), $updateData);

        $response->assertRedirect(route('seller.products.index'));

        // Refresh and check
        $product->refresh();
        $this->assertCount(3, $product->images); // 1 original + 2 new
    }

    /** @test */
    public function it_can_delete_images_from_product()
    {
        $this->actingAs($this->seller);

        // Create a product with images
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        $image1 = ProductImage::factory()->create([
            'product_id' => $product->product_id,
            'is_primary' => true,
        ]);

        $image2 = ProductImage::factory()->create([
            'product_id' => $product->product_id,
            'is_primary' => false,
        ]);

        $this->assertCount(2, $product->images);

        // Delete second image
        $updateData = [
            'product_name' => $product->product_name,
            'base_price' => (string)$product->base_price,
            'stock_quantity' => (string)$product->total_quantity,
            'category_id' => $this->category->id,
            'status' => $product->status->value,
            'delete_images' => [$image2->id],
        ];

        $response = $this->put(route('seller.products.update', $product), $updateData);

        $response->assertRedirect(route('seller.products.index'));

        // Assert image was deleted
        $this->assertDatabaseMissing('product_images', [
            'id' => $image2->id,
        ]);

        // Refresh and check
        $product->refresh();
        $this->assertCount(1, $product->images);
    }

    /** @test */
    public function it_can_create_product_with_variant_images()
    {
        $this->actingAs($this->seller);

        $productData = [
            'product_name' => 'Product with Variant Images',
            'description' => 'Test variant images',
            'base_price' => '200000',
            'stock_quantity' => '100',
            'category_id' => $this->category->id,
            'status' => 'active',
            'variants' => [
                [
                    'size' => 'S',
                    'color' => 'Red',
                    'stock_quantity' => 50,
                    'images' => [
                        UploadedFile::fake()->image('variant_s_red1.jpg'),
                        UploadedFile::fake()->image('variant_s_red2.jpg'),
                    ],
                ],
                [
                    'size' => 'M',
                    'color' => 'Blue',
                    'stock_quantity' => 50,
                    'images' => [
                        UploadedFile::fake()->image('variant_m_blue.jpg'),
                    ],
                ],
            ],
            'images' => [
                UploadedFile::fake()->image('product_main.jpg'),
            ],
        ];

        $response = $this->post(route('seller.products.store'), $productData);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('product_name', 'Product with Variant Images')->first();
        $this->assertNotNull($product);

        // Check variant images
        $variants = $product->variants;
        $this->assertCount(2, $variants);

        $variant1 = $variants->first();
        $this->assertCount(2, $variant1->images);

        $variant2 = $variants->last();
        $this->assertCount(1, $variant2->images);

        // Check main product images (should not include variant images)
        $mainImages = $product->images()->whereNull('variant_id')->get();
        $this->assertCount(1, $mainImages);
    }

    /** @test */
    public function it_prevents_unauthorized_user_from_updating_product()
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('seller');
        
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($otherUser);

        $updateData = [
            'product_name' => 'Hacked Name',
        ];

        $response = $this->put(route('seller.products.update', $product), $updateData);

        $response->assertForbidden();

        // Assert product was not updated
        $this->assertDatabaseMissing('products', [
            'product_id' => $product->product_id,
            'product_name' => 'Hacked Name',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $this->actingAs($this->seller);

        $response = $this->post(route('seller.products.store'), []);

        $response->assertSessionHasErrors(['product_name', 'base_price', 'stock_quantity', 'category_id']);
    }

    /** @test */
    public function it_handles_complete_create_update_flow()
    {
        $this->actingAs($this->seller);

        // Step 1: Create product
        $createData = [
            'product_name' => 'Flow Test Product',
            'description' => 'Initial description',
            'base_price' => '100000',
            'stock_quantity' => '50',
            'category_id' => $this->category->id,
            'status' => 'active',
            'variants' => [
                [
                    'size' => 'S',
                    'color' => 'Red',
                    'stock_quantity' => 25,
                ],
                [
                    'size' => 'M',
                    'color' => 'Blue',
                    'stock_quantity' => 25,
                ],
            ],
            'images' => [
                UploadedFile::fake()->image('initial1.jpg'),
                UploadedFile::fake()->image('initial2.jpg'),
            ],
        ];

        $createResponse = $this->post(route('seller.products.store'), $createData);
        $createResponse->assertRedirect(route('seller.products.index'));

        $product = Product::where('product_name', 'Flow Test Product')->first();
        $this->assertNotNull($product);

        // Verify initial state
        $this->assertEquals(100000, $product->base_price);
        $this->assertEquals('Initial description', $product->description);
        $this->assertCount(2, $product->variants);
        $this->assertCount(2, $product->images);

        // Step 2: Update product - change name, price, add variant, delete image
        $image1 = $product->images->first();
        $variant1 = $product->variants->first();

        $updateData = [
            'product_name' => 'Updated Flow Test Product',
            'description' => 'Updated description',
            'base_price' => '150000',
            'stock_quantity' => '100',
            'category_id' => $this->category->id,
            'status' => 'inactive',
            'variants' => [
                [
                    'id' => $variant1->id,
                    'size' => 'S',
                    'color' => 'Red',
                    'stock_quantity' => 30, // Updated
                ],
                [
                    // New variant (variant2 is implicitly deleted by not including it)
                    'size' => 'L',
                    'color' => 'Green',
                    'stock_quantity' => 70,
                ],
            ],
            'delete_images' => [$image1->id],
            'images' => [
                UploadedFile::fake()->image('new_image.jpg'),
            ],
        ];

        $updateResponse = $this->put(route('seller.products.update', $product), $updateData);
        $updateResponse->assertRedirect(route('seller.products.index'));

        // Step 3: Verify all updates
        $product->refresh();

        // Check basic fields
        $this->assertEquals('Updated Flow Test Product', $product->product_name);
        $this->assertEquals('Updated description', $product->description);
        $this->assertEquals(150000, $product->base_price);
        $this->assertEquals(100, $product->total_quantity);
        $this->assertEquals('inactive', $product->status->value);

        // Check variants (should have 2: updated S and new L)
        $this->assertCount(2, $product->variants);
        
        $updatedVariant = $product->variants()->where('id', $variant1->id)->first();
        $this->assertNotNull($updatedVariant);
        $this->assertEquals(30, $updatedVariant->stock_quantity);

        $newVariant = $product->variants()->whereJsonContains('attribute_values->size', 'L')->first();
        $this->assertNotNull($newVariant);
        $this->assertEquals('Green', json_decode($newVariant->attribute_values, true)['color']);

        // Check images (1 deleted, 1 remaining, 1 new = 2 total)
        $this->assertCount(2, $product->images);
        $this->assertDatabaseMissing('product_images', [
            'id' => $image1->id,
        ]);
    }
}
