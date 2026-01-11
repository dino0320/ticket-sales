<?php

namespace Tests\Unit\Services;

use App\Consts\CartConst;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CartServiceRedisTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Redis::flushDB();
    }

    /**
     * Test getCart()
     */
    public function test_get_cart(): void
    {
        $user = User::factory()->make(['id' => 1]);
        $userCartKey = sprintf(CartConst::CART_KEY, $user->id);
        $numbersOfTickets = [
            1 => 1,
            2 => 3,
        ];
        Redis::hMSet($userCartKey, $numbersOfTickets);
        $this->assertSame($numbersOfTickets, CartService::getCart($user->id));
    }

    /**
     * Test overwriteUserCartWithGuestCart() (guest cart exists)
     */
    public function test_overwrite_user_cart_with_guest_cart_guest_cart_exists(): void
    {
        $guestCartId = 'TEST_GUEST_CART_ID';
        Session::put(CartConst::GUEST_CART_ID_KEY, $guestCartId);
        $guestCartKey = sprintf(CartConst::CART_KEY, $guestCartId);
        Redis::hMSet($guestCartKey, [
            1 => 1,
            2 => 3,
        ]);

        $user = User::factory()->make(['id' => 1]);
        $userCartKey = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hMSet($userCartKey, [
            3 => 1,
        ]);
        CartService::overwriteUserCartWithGuestCart($user);

        $this->assertSame([
            1 => '1',
            2 => '3',
        ], Redis::hGetAll($userCartKey));
        $this->assertEmpty(Redis::hGetAll($guestCartKey));
    }

    /**
     * Test overwriteUserCartWithGuestCart() (guest cart doesn't exist)
     */
    public function test_overwrite_user_cart_with_guest_cart_guest_cart_does_not_exists(): void
    {
        Session::put(CartConst::GUEST_CART_ID_KEY, 'TEST_GUEST_CART_ID');

        $user = User::factory()->make(['id' => 1]);
        $userCartKey = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hMSet($userCartKey, [
            3 => 1,
        ]);
        CartService::overwriteUserCartWithGuestCart($user);

        $this->assertSame([
            3 => '1',
        ], Redis::hGetAll($userCartKey));
    }
}
