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
        $user = Auth::user();

        $orders = Order::with('order_items')->where('user_id', $user->id)->get();

        return response()->json(
        [
            'orders' => $orders,
            'message' => 'Orders Successfully Found',
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     * 
     * http://api-endpoints.test/api/v1/orders/create
     */
    public function store(Request $request)
    {
        
        $request->validate([
            "order_status" => "required|string",
            "payment_method" => "required|string",
            "shipping_address" => "required|string",
            'order_items' => 'nullable|array',  // Make order_items nullable
            'order_items.*.product_variant_id' => 'required_if:order_items,integer|min:1',  // Validate only if order_items exists
            'order_items.*.quantity' => 'required_if:order_items,integer|min:1',            // Validate only if order_items exists
        ]);

        //ShoppingCart status
        $user = Auth::user();
        $shopping_cart_items = $user->shopping_carts;

        if(!$shopping_cart_items) {
            return response()->json(
                [
                    'error' => 'Your shopping cart is empty. Please add items before proceeding.'
                ], 422);
        }
        

        //Process of creating a new Order
        $newOrder_info = array_merge(['user_id'=> $user->id], $request->except('order_items'));
        $newOrder = Order::create($newOrder_info);

        $total_amount = 0;
        
        //Cheacking each item
        foreach ($request['order_items'] as $item) {

            $product_variant = ProductVariant::find($item['product_variant_id']);


            if (!$product_variant) {
                (Order::with('order_items')->find($newOrder->id))->delete();
                return response()->json([
                    "message" => "The product_variant_id is not valid. Please adjust the value and try again.",
                ], 422);
                break;
            }
            
            $product = Product::find( $product_variant->product_id );

            if( !$product ) {
                (Order::with('order_items')->find($newOrder->id))->delete();
                return response()->json([
                    "message" => "The product_variant_id is not valid. Please adjust the value and try again.",
                ], 422);
            }
            
            $newOrderItems_info = array_merge(
                ['order_id'=> $newOrder->id], 
                $item,
                ['unit_price'=> $product->price]
                );
            
            OrderItem::create($newOrderItems_info);
            
            $total_amount += $product->price;
            
        }

        $newOrder->update(['total_amount' => $total_amount]);

        return response()->json(
            [
                'orders' => Order::with('order_items')->find($newOrder->id),
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

        //Verifies the item's existance
        if (!$order) {
            return response()->json([
                "message" => "Product Not Found"
            ], 404);
        }

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
