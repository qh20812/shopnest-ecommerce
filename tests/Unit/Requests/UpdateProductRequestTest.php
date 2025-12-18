<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Sellers\UpdateProductRequest;
use App\Models\Product;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateProductRequestTest extends TestCase
{
    public function test_authorize_allows_when_user_is_seller_and_owns_product()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['role_name' => 'seller']);
        $user->roles()->attach($role->id);

        $shop = Shop::factory()->create(['owner_id' => $user->id]);
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $base = HttpRequest::create('/', 'PUT');
        $request = UpdateProductRequest::createFromBase($base);

        // Route resolver returns an object with parameter() method
        $request->setRouteResolver(function () use ($product) {
            return new class($product) {
                private $product;
                public function __construct($product)
                {
                    $this->product = $product;
                }
                public function parameter($key = null)
                {
                    if ($key === 'product') {
                        return $this->product;
                    }
                    return null;
                }
            };
        });

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->assertTrue($request->authorize());
    }

    public function test_authorize_denies_when_user_not_owner_or_not_seller()
    {
        $user = User::factory()->create();
        // user does not have seller role

        $otherUser = User::factory()->create();
        $shop = Shop::factory()->create(['owner_id' => $otherUser->id]);
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $base = HttpRequest::create('/', 'PUT');
        $request = UpdateProductRequest::createFromBase($base);

        $request->setRouteResolver(function () use ($product) {
            return new class($product) {
                private $product;
                public function __construct($product)
                {
                    $this->product = $product;
                }
                public function parameter($key = null)
                {
                    if ($key === 'product') {
                        return $this->product;
                    }
                    return null;
                }
            };
        });

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->assertFalse($request->authorize());
    }

    public function test_prepare_for_validation_normalizes_prices()
    {
        $data = [
            'base_price' => '1.234.567đ',
            'compare_price' => '2,000,000',
            'variants' => [
                ['price' => '1.000'],
            ],
        ];

        $base = HttpRequest::create('/', 'PUT', $data);
        $request = UpdateProductRequest::createFromBase($base);

        // call protected prepareForValidation via reflection
        $ref = new \ReflectionMethod($request, 'prepareForValidation');
        $ref->setAccessible(true);
        $ref->invoke($request);

        $this->assertSame('1234567', $request->input('base_price'));
        $this->assertSame('2000000', $request->input('compare_price'));
        $this->assertSame('1000', $request->input('variants')[0]['price']);

        // Now validate using the rules - should pass because numbers normalized
        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->passes());
    }
}
