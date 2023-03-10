<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends BaseController
{
    public function index()
    {
        $products = Product::select("products.id", "products.name", "products.detail", "categories.name as product_category")->join('categories', 'categories.id', '=', 'products.category_id')->get();

        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.', 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = FacadesValidator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product = Product::create($input);

        return $this->sendResponse(new ProductResource($product), 'Product created successfully.', 201);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.', 200);
    }

    public function update(Request $request, Product $product)
    {
        $input = $request->all();

        $validator = FacadesValidator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->category_id = $input['category_id'];
        $product->save();

        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.', 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully.', 202);
    }

    public function search($name)
    {
        $product = Product::where('name', 'like', '%' . $name . '%')->get();
        return $this->sendResponse(new ProductResource($product), 'Product.', 200);
    }
}
