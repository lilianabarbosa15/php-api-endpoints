<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
/*

Productos (Products y ProductVariants)
 Nota: Desarrollar todos los métodos de endpoints para productos más allá
 que para la tienda solo se usen los métodos GET de producto.
 1. GET/products- Listar todos los productos disponibles con sus variantes.
 2. GET/products/{ProductID}- Obtener los detalles de un producto específico.
 3. GET/products/search- Este endpoint deberá buscar producto por nombre, color, talla, brand, collection, precio, genero.


Route::prefix('products')->group( function () {
        Route::get('/search', [ProductController::class, 'search']);
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
*/

    /**
     * Display a listing of the resource.
     */
    public function index() //Request $request) 
    {
        /**
        * Get the number of records per page. 
        * This is passed in the route as parameter /products?per_page=1, 
        * if the parameter is not sent it takes 10 by default.
        */
        //$perPage = $request->query('per_page', 10);
        /**
        * Obtain products with information about their variants using 
        * the relationship between models, using paginate for pagination.
        */
        //$products = Product::with('variants')->paginate($perPage);

        $products = Product::all();
        return response()->json($products, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) //)
    {
        $product = Product::find($id);
        return response()->json($product,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) 
    {
        $product = Product::create($request->all());
        return response()->json($product,201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return response()->json($product,200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $product = Product::find($id);
        $product->delete();
        return response()->noContent();
    }

    /**
     * Search
     */
    public function search(Request $request)
    {
        $query = Product::with('product_variants');

        if($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }
        
        if($request->has('attributes') && $request->has('value')) {
            $attributes = $request->input('attributes');
            $value = $request->input('value');

            $query->whereJsonContains('other_attributes->' .  $attributes, $value);
        }

        if($request->has('color')) {
            $color = $request->input('color');
            $query->whereHas('variants', function (Builder $q) use ($color) {
                $q->where('color', $color);
            });
        }
        
        $products = $query->paginate();

        return response()->json( $products, 200);
    }
}
