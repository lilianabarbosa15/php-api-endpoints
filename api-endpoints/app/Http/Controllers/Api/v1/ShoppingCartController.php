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
     */
    public function store(Request $request)
    {
        //Data validation
        $validatedData = $request->validate([
            "product_variant_id" => "required|integer|min:0",
            "quantity" => "required|integer|min:0",
        ]);

        //Verification (stock)
        $product_variant = ProductVariant::all()->firstWhere('id', $validatedData['product_variant_id']);
        if ($product_variant->stock_quantity >= $validatedData['quantity']) {
            
            //Actualization of the stock
            $new_stock = $product_variant->stock_quantity - $validatedData['quantity'];
            $product_variant->stock_quantity = $new_stock;
            $product_variant->save();

            //Adding the new ProductVariant to the CartItems
            $newCart = new ShoppingCart();
            $newCart->user_id = Auth::user()->id;
            $newCart->status = 'pending';
            $newCart->save();

            $cartItem = new CartItem();
            $cartItem->shopping_cart_id = $newCart->id;
            $cartItem->product_variant_id = $product_variant->id;  // Suponiendo que cada item tiene un 'product_id'
            $cartItem->quantity = $validatedData['quantity'];  // Suponiendo que cada item tiene una cantidad
            $cartItem->unit_price = $product_variant
                                    ->product
                                    ->price;
            $cartItem->save();
        } else {
            return response()->json([
                "message" => "The requested quantity is not available. Please adjust the quantity and try again.",
            ], 422);
        }
        
        //Obtains the carts associated with the user
        $carts = Auth::user()->shopping_carts;

        return response()->json(
            [
                'product_variant' => $product_variant,
                'cart_item' => $cartItem,
                'shopping_carts' => $carts,
                'message' => 'Cart Successfully Created with Items',
            ], 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Data validation
        $validatedData = $request->validate([
            "quantity" => "required|integer|min:0",
        ]);
        
        //Verification of the ID joined
        if (is_numeric($id)) {
            
            //CartItem to be updated
            $shopping_carts = ShoppingCart::all()
                ->where('user_id', Auth::user()->id);
            $shopping_cart_ids = $shopping_carts->pluck('id')->toArray();       
    
            
            if ($shopping_carts) {

                //Items' details
                $cartItem = CartItem::all()
                    ->whereIn('shopping_cart_id', $shopping_cart_ids)
                    ->firstWhere('id', $id);

                if ($cartItem) {

                    //ProductVariant related with the CartItem 
                    $product_variant = ProductVariant::find($cartItem->product_variant_id);
                
                    //Verification in stock
                    if($product_variant->stock_quantity >= ($validatedData['quantity'] - $cartItem->quantity)) {
                        
                        //ProductVariant
                        $product_variant->stock_quantity -= ($validatedData['quantity'] - $cartItem->quantity);
                        $product_variant->save();

                        //CartItem
                        $cartItem->quantity = $validatedData['quantity'];
                        $cartItem->save();

                    } else {
                        return response()->json([
                            "message" => "The requested quantity is not available. Please adjust the quantity and try again",
                        ], 422);
                    }

                    return response()->json([
                        'variant' => $product_variant,
                        'item' => $cartItem,
                        'shopping_carts' => $shopping_carts,
                        "message"=> "Cart item updated successfully",
                    ], 201);
                    
                }
                
            }

            return response()->json([
                "message"=> "Product not found with the given ID",
            ], 404);
            
        }
        
        return response()->json([
            "message"=> "The ID must be a numeric value",
        ], 400);

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        //Verification of the ID joined
        if (is_numeric($id)) {
            
            //CartItem to be updated
            $shopping_carts = ShoppingCart::all()
                ->where('user_id', Auth::user()->id);
            $shopping_cart_ids = $shopping_carts->pluck('id')->toArray();       

            
            if ($shopping_carts) {

                //Items' details
                $cartItem = CartItem::all()
                    ->whereIn('shopping_cart_id', $shopping_cart_ids)
                    ->firstWhere('id', $id);
                                
                if ($cartItem) {

                    //ProductVariant related with the CartItem 
                    $product_variant = ProductVariant::find($cartItem->product_variant_id);

                    $product_variant->stock_quantity += $cartItem->quantity;
                    $product_variant->save();
                
                    //Delete
                    $cartItem->delete();


                    return response()->noContent();
                    
                }
                
            }

            return response()->json([
                "message"=> "Product not found with the given ID",
            ], 404);
            
        }
        
        return response()->json([
            "message"=> "The ID must be a numeric value",
        ], 400);   
    }
}
