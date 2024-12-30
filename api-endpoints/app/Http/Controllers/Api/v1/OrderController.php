<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Obtains the orders associated with the user
        $orders = Auth::user()->orders;

        return response()->json(
        [
            'orders' => $orders,
            'message' => 'Orders Successfully Found',
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            "order_status" => "required|string",
            "payment_method" => "required|string",
            "shipping_address" => "required|string",
            'order_items' => 'required|array',
            'order_items.*.product_variant_id' => 'required|integer|min:1',
            'order_items.*.quantity' => 'required|integer|min:0',
        ]);

        //ShoppingCart status
        $shopping_cart_items = Auth::user()->shopping_carts;

        if(!$shopping_cart_items) {
            return response()->json(
                [
                    'error' => 'Your shopping cart is empty. Please add items before proceeding.'
                ], 422);
        }
        

        //Process of creating a new Order

        $newOrder = new Order();
        $newOrder->user_id = Auth::user()->id;
        
        $total_amount = 0;
        $newOrder->order_status = $validatedData['order_status'];
        $newOrder->payment_method = $validatedData['payment_method'];
        $newOrder->shipping_address= $validatedData['shipping_address'];
        $newOrder->save();

        // 
        foreach ($validatedData['order_items'] as $item) {

            $product_variant = ProductVariant::find($item['product_variant_id']);


            if (!$product_variant) {
                return response()->json([
                    "message" => "The product_variant_id is not valid. Please adjust the value and try again.",
                ], 422);
                break;
            }
            
            $product = Product::find( $product_variant->product_id );

            if( !$product ) {
                return response()->json([
                    "message" => "The product_variant_id is not valid. Please adjust the value and try again.",
                ], 422);
            }

            $newOrderItems = new OrderItem();
            $newOrderItems->order_id = $newOrder->id;
            $newOrderItems->product_variant_id = $item['product_variant_id'];
            $newOrderItems->quantity = $item['quantity'];
            $newOrderItems->unit_price = $product->price;
            $newOrderItems->save();
                    
            $total_amount += $product->price;
                    
            ($newOrder->order_items)->push($newOrderItems);
                
        }

        $newOrder->total_amount = $total_amount;
        $newOrder->save();
            
        return response()->json(
            [
                'orders' => $newOrder,
                'message' => 'Orders Successfully Created',
            ], 201);
               
            
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //Get user orders by id
        $order = Order::with('order_items')->find($id);

        //Use Gate so that the logged in user can see only their orders. Gate rules are in the boot method of the AppServiceProviders class
        if (! Gate::allows('user-view-order', $order)) {
            return response()->json(
                [
                    'message' => 'Sorry, You do not have access to this resources',
                ], 403);
        }

        return response()->json($order,200);
    }

}
