<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    use ResponseTrait;
    use PaginationTrait;
    public function index(Request $request)
    {
        $user = auth()->user();
        $cartItems = CartItem::where('user_id', $user->id)->get();
        // return $cartItems;
        $data = CartItemResource::collection($cartItems);
        return $this->apiSuccess('Cart Items', $data);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',

        ]);
        $cartItem = CartItem::where('user_id', $user->id)->where('product_id', $validated['product_id'])->first();
        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
            return $this->apiSuccess('Cart Item updated successfully', $cartItem);
        } else {
            $data = CartItem::create([
                'user_id' => $user->id,
                'quantity' => $validated['quantity'],
                'product_id' => $validated['product_id'],
            ]);
            return $this->apiSuccess('Cart Item added successfully', $data);
        }
    }

    public function destroy(CartItem $cartItem)
    {
        $user = auth()->user();
        $cartItem = CartItem::where('user_id', $user->id)->where('product_id', $cartItem->product_id)->first();
        $cartItem->delete();
        return $this->apiSuccess('Cart Item removed successfully');
    }
}
