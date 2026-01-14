<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
class ProfitController extends Controller
{
    // 🔹 Product-wise profit (simple)
    // public function productProfit()
    // {
    //     $data = DB::table('stock_ledgers as sl')
    //         ->join('products as p', 'p.id', '=', 'sl.product_id')
    //         ->select(
    //             'p.name as product',
    //             'p.metal',
    //             'p.purity',
    //             DB::raw('
    //                 SUM(
    //                     (IFNULL(sl.weight_out,0) - IFNULL(sl.weight_in,0))
    //                     * IFNULL(sl.rate,0)
    //                 ) as profit
    //             ')
    //         )
    //         ->groupBy('p.id', 'p.name', 'p.metal', 'p.purity')
    //         ->get();

    //     return response()->json($data);
    // }
    public function productProfit()
{
    $data = DB::table('sale_items as si')
        ->join('products as p', 'p.id', '=', 'si.product_id')
        ->leftJoin('purchase_items as pi', 'pi.product_id', '=', 'si.product_id')
        ->select(
            'p.name as product',
            'p.metal',
            'p.purity',

            // sale value
            DB::raw('SUM(si.weight * si.rate) as sale_amount'),

            // purchase cost of sold qty
            DB::raw('SUM(si.weight * pi.rate) as purchase_cost'),

            // profit
            DB::raw('SUM(si.weight * si.rate) - SUM(si.weight * pi.rate) as profit')
        )
        ->groupBy('p.id', 'p.name', 'p.metal', 'p.purity')
        ->get();

    return response()->json($data);
}

    // 🔹 Purchase cost vs sale profit
    public function purchaseCostProfit()
    {
        $data = DB::table('sale_items as si')
            ->join('products as p', 'p.id', '=', 'si.product_id')
            ->leftJoin('purchase_items as pi', 'pi.product_id', '=', 'p.id')
            ->select(
                'p.name as product',
                'p.metal',
                'p.purity',
                DB::raw('SUM(pi.amount) as purchase_cost'),
                DB::raw('SUM(si.amount) as sale_amount'),
                DB::raw('SUM(si.amount) - SUM(pi.amount) as profit')
            )
            ->groupBy('p.id', 'p.name', 'p.metal', 'p.purity')
            ->get();

        return response()->json($data);
    }

     public function fifoProfit()
    {
        $result = [];

        $products = Product::all();

        foreach ($products as $product) {

            $purchases = PurchaseItem::where('product_id', $product->id)
                ->orderBy('id')
                ->get();

            $sales = SaleItem::where('product_id', $product->id)
                ->orderBy('id')
                ->get();

            $purchaseIndex = 0;
            $purchaseBalance = $purchases->sum('weight');

            $profit = 0;
            $soldWeight = 0;

            foreach ($sales as $sale) {

                $remainingSaleWeight = $sale->weight;

                while ($remainingSaleWeight > 0 && $purchaseIndex < count($purchases)) {

                    $purchase = $purchases[$purchaseIndex];

                    if ($purchase->weight <= 0) {
                        $purchaseIndex++;
                        continue;
                    }

                    $usedWeight = min(
                        $purchase->weight,
                        $remainingSaleWeight
                    );

                    // PROFIT CALCULATION
                    $profit += $usedWeight * ($sale->rate - $purchase->rate);

                    // UPDATE BALANCES
                    $purchase->weight -= $usedWeight;
                    $remainingSaleWeight -= $usedWeight;
                    $soldWeight += $usedWeight;

                    if ($purchase->weight == 0) {
                        $purchaseIndex++;
                    }
                }
            }

            if ($soldWeight > 0) {
                $result[] = [
                    'product' => $product->name,
                    'metal'   => $product->metal,
                    'purity'  => $product->purity,
                    'sold_weight' => round($soldWeight, 3),
                    'profit'  => round($profit, 2)
                ];
            }
        }

        return response()->json($result);
    }
}
