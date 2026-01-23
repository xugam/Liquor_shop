<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    use ResponseTrait;
    use PaginationTrait;
    public function index()
    {
        $cartItems = CartItem::all();
        return $this->apiSuccess('Cart Items', $cartItems);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        return $user;
        $validated = $request->validate([
            'product_id' => 'required|array|min:1'

        ]);
        foreach ($validated['product_id'] as $product_id) {
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product_id,
            ]);
        }

        return $this->apiSuccess('Cart Item added successfully');
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return $this->apiSuccess('Cart Item removed successfully');
    }
}
