<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;


class ProductController extends Controller
{


    private function _decodeJsonAttributes( Object $items ): Object
    {
        // Transform the collection by iterating through each item
        $items->transform(function ($item) {

            // Check if 'other_attributes' contains a valid JSON string
            // Decode the JSON string into an associative array
            $item->other_attributes = json_decode($item->other_attributes, true);

            // Return the item with the 'other_attributes' field transformed into an array
            return $item;
        });

        // Return the collection after all items have been transformed
        return $items;
    }


    /**
     * Display a listing of the resource (Product's database)
     * http://api-endpoints.test/api/v1/product
     * e.g. http://api-endpoints.test/api/v1/products?per_page=4&page=4
     * e.g. http://api-endpoints.test/api/v1/products?page=4
     * e.g. http://api-endpoints.test/api/v1/products?per_page=4
     */
    public function index(Request $request) 
    {
        /**
        * Get the number of records per page.
        * This is passed in the route as parameter /products?per_page=5&page=4,
        * if the parameter is not sent it takes 10 product per page and the
        * first page by default.
        */
        $perPage = $request->query('per_page', 10);
                
        /**
        * Obtain products with information about their variants using 
        * the relationship between models, using paginate for pagination.
        */
        $products = Product::with('product_variants')->paginate($perPage);
       
        //If there are no products in the database we return a 404.
        if($products->isEmpty()) {
            return response()->json(["message" => "Products Not Found"], 404);
        }

        //Tranformation of the JSON element other_atributes.
        $items = collect($products->items());
        $items = $this->_decodeJsonAttributes($items);
        
        return response()->json( $items, 200 );
    }

    
    /**
     * Search and return a list of resources of Product's database.
     * This filter by name, color, size, band, collection, price and gender.
     * 
     * Output:
     *  A list of products that satisfy all applied filters
     */

    // e.g. http://api-endpoints.test/api/v1/products/search?color=%2308682a
    private function _filterByColor(Request $request, Builder $query): Builder
    {
        /*
        The last six characters of the URL represent the color to filter by.
        */
        if($request->has('color')) {
            $color = $request->input('color');
            $query->whereHas('product_variants', function (Builder $q) use ($color) {
                $q->where('color', $color);
            });
        }
        return $query;
    }

    // e.g. http://api-endpoints.test/api/v1/products/search?name=Molestias_saepe_consequatur
    private function _filterByName(Request $request, Builder $query): Builder
    {
        if($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        return $query;
    }

    // e.g. http://api-endpoints.test/api/v1/products/search?max_price=192.38&min_price=100
    private function _filterByPrice(Request $request, Builder $query): Builder
    {
        if($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }
        return $query;
    }

    // e.g. http://api-endpoints.test/api/v1/products/search?size=L
    private function _filterBySize(Request $request, Builder $query): Builder
    {
        if($request->has('size')) {
            $size = $request->input('size');
            $query->whereHas('product_variants', function (Builder $q) use ($size) {
                $q->where('size', $size);
            });
        }
        return $query;
    }

    // e.g. http://api-endpoints.test/api/v1/search?attributes=brand&value=GUCCI
    private function _filterByAttribute(Request $request, Builder $query): Builder
    {
        /*
        To apply another kind of filters (brand, material, pattern,
        care_instructions, collection and gender).
        */
        if($request->has('attributes') && $request->has('value')) {
            $attributes = $request->input('attributes');
            $value = $request->input('value');

            $query->whereJsonContains('other_attributes->' .  $attributes, $value);
        }
        return $query;
    }
    
    //
    public function search(Request $request)
    {
        
        $query = Product::with('product_variants');

        $query = $this->_filterByName( $request, $query );

        $query = $this->_filterByColor( $request, $query );

        $query = $this->_filterBySize( $request, $query );

        $query = $this->_filterByPrice( $request, $query );

        $query = $this->_filterByAttribute( $request, $query );
        
        $products =$query->paginate();

        //Tranformation of the JSON element other_atributes
        $items = collect($products->items());
        $items = $this->_decodeJsonAttributes($items);
        $products->items($items);

        return response()->json( [
            'products' => $products,

        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $product = Product::find($id);
        
        //If there is no product in the database we return a 404.
        if(!$product) {
            return response()->json(["message" => "Product Not Found"], 404);
        }

        //Tranformation of the JSON element other_atributes.
        $product->other_attributes = json_decode($product->other_attributes, true);

        return response()->json([
            'product' => $product,
        ], 200);
    }


    /**
     * 
     * 
     * The functions below are private and can only be accessed by the administrator.
     * This is why the routes require the Sanctum middleware for authentication.
     * 
     * 
     */

    private function _validations(?Request $request = null) : void
    {
        //Data validation
        if ($request != null) {
            $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'other_attributes' => 'nullable|array',
                'other_attributes.material' => 'nullable|string',
                'other_attributes.pattern' => 'nullable|string',
                'other_attributes.brand' => 'nullable|string',
                'other_attributes.care_instructions' => 'nullable|string',
                'other_attributes.collection' => 'nullable|string',
                'other_attributes.gender' => 'nullable|string',
            ]);
        }
        
        //Verification of the type of User (admin)
        /////

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) 
    {
        /**
         * This function needs to be protected, just the administrator
         * can add items to the stock.
         */
        $this->_validations( $request );

        $request['other_attributes'] = json_encode($request['other_attributes']);

        $product = Product::create($request->all()); 
        
        //Tranformation of the JSON element other_atributes.
        $product->other_attributes = json_decode($product->other_attributes, true);

        return response()->json([
            'product' =>$product,
        ], 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        /**
         * This function needs to be protected, just the administrator
         * can update items in the stock.
         */
        $this->_validations( $request );

        $request['other_attributes'] = json_encode($request['other_attributes']);

        $product = Product::find($id);
        
        //If there are no product with that id in the database we return a 404.
        if( !$product ) {
            return response()->json([
                "message" => "Product Not Found"
            ], 404);
        }
        
        $product->update($request->all());
        
        //Tranformation of the JSON element other_atributes.
        $product->other_attributes = json_decode($product->other_attributes, true);

        return response()->json([
            'product' =>$product,
        ], 200);
    }

    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        /**
         * This function needs to be protected, just the administrator
         * can delete items in the stock.
         */
        $this->_validations();

        $product = Product::find($id);

        //If there are no product with that id in the database we return a 404.
        if( !$product ) {
            return response()->json([
                "message" => "Product Not Found"
            ], 404);
        }

        $product->delete();
        
        return response()->noContent();
    }


}
