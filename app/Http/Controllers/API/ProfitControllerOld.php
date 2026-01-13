<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProfitService;

class ProfitController extends Controller
{
    public function productWise()
    {
        $products = Product::where('is_active', true)->get();

        $data = [];

        foreach ($products as $product) {
            $profit = ProfitService::productProfit($product->id);

            $data[] = [
                'product' => $product->name,
                'metal'   => $product->metal,
                'purity'  => $product->purity,
                'profit'  => $profit
            ];
        }

        return response()->json($data);
    }

     public function productWiseNew()
    {
        $products = Product::all();

        $result = [];

        foreach ($products as $product) {

            $calc = ProfitService::calculateProductProfit($product->id);

            $result[] = [
                'product'        => $product->name,
                'metal'          => $product->metal,
                'purity'         => $product->purity,
                'purchase_cost' => $calc['purchase_cost'],
                'sale_amount'   => $calc['sale_amount'],
                'profit'        => $calc['profit'],
            ];
        }

        return response()->json($result);
    }
}
