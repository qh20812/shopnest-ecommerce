<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Sellers\StoreProductRequest;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreProductRequestTest extends TestCase
{
    public function test_authorize_allows_when_user_is_seller_and_has_shop()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['role_name' => 'seller']);
        $user->roles()->attach($role->id);

        Shop::factory()->create(['owner_id' => $user->id]);

        $base = HttpRequest::create('/', 'POST');
        $request = StoreProductRequest::createFromBase($base);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->assertTrue($request->authorize());
    }

    public function test_authorize_denies_when_seller_has_no_shop()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['role_name' => 'seller']);
        $user->roles()->attach($role->id);

        $base = HttpRequest::create('/', 'POST');
        $request = StoreProductRequest::createFromBase($base);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->assertFalse($request->authorize());
    }

    public function test_prepare_for_validation_normalizes_prices_and_validates()
    {
        $category = \App\Models\Category::factory()->create();

        $data = [
            'product_name' => 'Test',
            'category_id' => $category->id,
            'base_price' => '1.234.567đ',
            'compare_price' => '2,000,000',
            'stock_quantity' => 10,
            'status' => 'active',
        ];

        $base = HttpRequest::create('/', 'POST', $data);
        $request = StoreProductRequest::createFromBase($base);

        $ref = new \ReflectionMethod($request, 'prepareForValidation');
        $ref->setAccessible(true);
        $ref->invoke($request);

        $this->assertSame('1234567', $request->input('base_price'));
        $this->assertSame('2000000', $request->input('compare_price'));

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_variants_max_limit_enforced()
    {
        $variants = [];
        for ($i = 0; $i < 11; $i++) {
            $variants[] = ['size' => 'M', 'stock_quantity' => 1];
        }

        $data = [
            'product_name' => 'Test',
            'category_id' => 1,
            'base_price' => '1000',
            'stock_quantity' => 10,
            'variants' => $variants,
        ];

        $validator = Validator::make($data, (new StoreProductRequest())->rules());
        $this->assertFalse($validator->passes());
    }
}
