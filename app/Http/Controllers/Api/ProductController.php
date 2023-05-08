<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        $products = Product::latest()->get();
        
        if ($products->isEmpty()) {
            return $this->successResponse(['data' => 'No products found'], 204);
        }

        return $this->successResponse(ProductResource::collection($products));
    }
}