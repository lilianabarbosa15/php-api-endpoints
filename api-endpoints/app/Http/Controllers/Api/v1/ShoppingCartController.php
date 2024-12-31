<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\ShoppingCart;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{
    
    /**
     * Display a listing of the resource (Shopping Carts).
     * 
     * http://api-endpoints.test/api/v1/cart/
     */
    public function index()
    {
        //Authentication
        $user = Auth::user();

        //Obtains the carts associated with the user
        $carts = ShoppingCart::with('cart_items')->get()->where( 'user_id', $user->id );
        
        return response()->json(
        [
            'shopping_carts' => $carts,
            'message' => 'Cart Successfully Found',
        ], 200);
    }

    
    /**
     * Store a newly created resource in storage.
     * 
     * http://api-endpoints.test/api/v1/cart/add
     */
    public function store(Request $request)
    {
        
        // Data validation
        $validatedData = $request->validate([
            'cart_items' => 'required|array', // Ensure the order_items is an array
            'cart_items.*.product_variant_id' => 'required|integer|min:1', // Validate each item has a valid product_variant_id
            'cart_items.*.quantity' => 'required|integer|min:0', // Validate each item has a valid quantity
        ]);

        //Authentication
        $user = Auth::user();

        //Adding the new ProductVariant to the CartItems
        $newCart_info = array_merge(
            ['user_id'=> $user->id],
            ['status'=> 'pending']
            );
        
        $newCart = ShoppingCart::create($newCart_info);

        // Iterate through each order item
        foreach ($validatedData['cart_items'] as $item) {

            //Verification (stock)
            $product_variant = ProductVariant::find($item['product_variant_id']);

            if (!$product_variant) {
                (ShoppingCart::with('cart_items')->find($newCart->id))->delete();
                return response()->json([
                    "message" => "Product Variant " . $item['product_variant_id'] . " Not Found",
                ], 404);
            }
            
            if ($product_variant->stock_quantity >= $item['quantity']) {
                
                //Actualization of the stock
                $new_stock = $product_variant->stock_quantity - $item['quantity'];
                
                $product_variant->update(['stock_quantity' => $new_stock]);
                
                
                $cartItem_info = array_merge(
                    ['shopping_cart_id'=> $newCart->id],
                    ['product_variant_id'=> $product_variant->id],
                    ['quantity'=> $item['quantity']],
                    ['unit_price'=> $product_variant->product->price]
                    );

                $cartItem = CartItem::create($cartItem_info);
                
            } else {
                (ShoppingCart::with('cart_items')->find($newCart->id))->delete();
                return response()->json([
                    "message" => "The requested quantity for product_variant_id ".$item['product_variant_id']." is not available. Please adjust the quantity and try again.",
                ], 422);
            }
        }
        
        //Obtains the carts associated with the user
        $carts = ShoppingCart::with('cart_items')->get()->where( 'user_id', $user->id );
        
        return response()->json(
            [
                //'product_variant' => $product_variant,
                //'cart_item' => $cartItem,
                'shopping_carts' => $carts, //return all the shopping_carts associated with the user
                'message' => 'Cart Successfully Created with Items',
            ], 201);
    }


    /**
     * Update the specified resource in storage.
     * 
     * e.g. http://api-endpoints.test/api/v1/cart/update/2
     */
    public function update(Request $request, string $id)
    {
        //Data validation
        $validatedData = $request->validate([
            "quantity" => "required|integer|min:0",
        ]);

        //Authentication
        $user = Auth::user();
        
        //Verification of the ID joined ($id = card_item->id)
        if (!is_numeric($id)) {
            return response()->json([
                "message"=> "The ID must be a numeric value",
            ], 400);
        }
        
        //Last shopping cart associated with the user 
        $shopping_cart = ShoppingCart::with('cart_items')->get()->where( 'user_id', $user->id )->last();
        
        if (!$shopping_cart) {
            return response()->json([
                "shopping_cart" => $shopping_cart,
                "message" => "No shopping cart is associated with the user.",
            ], 404);
        }
                
        //Items in the last shopping cart
        $cartItems = $shopping_cart['cart_items'];
        $cartItem = $cartItems->find($id);  //

        if (!$cartItem) {
            return response()->json([
                "message" => "The last shopping cart does not have any items associated with it.",
            ], 404);
        }
            
        //ProductVariant related with the CartItem
        $product_variant = ProductVariant::find($cartItem->product_variant_id);
        
        //Verification in stock
        if($product_variant->stock_quantity >= ($validatedData['quantity'] - $cartItem->quantity)) {
            
            //ProductVariant
            $new_stock = ($product_variant->stock_quantity - ($validatedData['quantity'] - $cartItem->quantity));
            $product_variant->update(['stock_quantity' => $new_stock]);
            
            //CartItem
            $cartItem->update(['quantity' => $validatedData['quantity']]);

        } else {
            return response()->json([
                "message" => "The requested quantity is not available. Please adjust the quantity and try again",
            ], 422);
        }
        
        return response()->json([
            "product_variant" => $product_variant,
            //"cart_items" => $cartItems,
            "shopping_cart" => $shopping_cart,
            "message"=> "Cart item updated successfully",
        ], 201);

    }
      

    /**
     * Remove the specified resource from storage.
     * 
     * 
     */
    public function destroy(string $id)
    {
        //Authentication
        $user = Auth::user();
        
        //Verification of the ID joined ($id = card_item->id)
        if (!is_numeric($id)) {
            return response()->json([
                "message"=> "The ID must be a numeric value",
            ], 400);
        }

        //Last shopping cart associated with the user 
        $shopping_cart = ShoppingCart::with('cart_items')->get()->where( 'user_id', $user->id )->last();
        
        if (!$shopping_cart) {
            return response()->json([
                "shopping_cart" => $shopping_cart,
                "message" => "No shopping cart is associated with the user.",
            ], 404);
        }

        //Items in the last shopping cart
        $cartItems = $shopping_cart['cart_items'];
        $cartItem = $cartItems->find($id);  //

        if (!$cartItem) {
            return response()->json([
                "message" => "The last shopping cart does not have any items associated with it.",
            ], 404);
        }

        //ProductVariant related with the CartItem
        $product_variant = ProductVariant::find($cartItem->product_variant_id);

        //Updating the stock
        $new_stock = ($product_variant->stock_quantity + $cartItem->quantity);
        $product_variant->update(['stock_quantity' => $new_stock]);
        
        //Delete
        $cartItem->delete();

        return response()->noContent();

    }
}
