<?php
namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockLedger;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
    public function index() {
        return Product::with('category')->whereNull('deleted_at')->where('is_active', true)->get();
        // return Product::with('category')->whereNull('deleted_at')->get();
    }
    public function getAllProducts() {
        return Product::with('category')->get();
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'metal'       => 'required|string',
            'purity'      => 'required|string',
            'weight'      => 'required|numeric|min:0',
            'price'       => 'required|numeric|min:0',
        ]);

        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'metal'       => 'required|string',
            'purity'      => 'required|string',
            'weight'      => 'required|numeric|min:0',
            'price'       => 'required|numeric|min:0',
        ]);

        return Product::create($data);
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // 🔒 CHECK STOCK LEDGER
        $hasLedger = StockLedger::where('product_id', $id)->exists();

        if ($hasLedger) {
            return response()->json([
                'message' => 'Cannot delete product. Stock transactions exist.'
            ], 422);
        }

        // 🔒 CHECK PURCHASE / SALES
        $usedInPurchase = PurchaseItem::where('product_id', $id)->exists();
        $usedInSale = SaleItem::where('product_id', $id)->exists();

        if ($usedInPurchase || $usedInSale) {
            return response()->json([
                'message' => 'Cannot delete product. Product is used in transactions.'
            ], 422);
        }

        $product->delete(); // SOFT DELETE

        return response()->json([
            'message' => 'Product deleted safely'
        ]);
    }
    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);

        $product->is_active = ! $product->is_active;
        $product->save();

        return response()->json([
            'message' => 'Product status updated',
            'is_active' => $product->is_active
        ]);
    }
    public function byCategory($categoryId)
    {
        $products = DB::table('products')
            ->leftJoin('stock_ledgers', 'products.id', '=', 'stock_ledgers.product_id')
            ->select(
                'products.id',
                'products.name',
                'products.is_active',
                DB::raw('IFNULL(SUM(stock_ledgers.weight_in - stock_ledgers.weight_out),0) as stock')
            )
            ->where('products.category_id', $categoryId)
            ->groupBy('products.id', 'products.name', 'products.is_active')
            ->orderBy('products.name')
            ->get();

        return response()->json($products);
    }
}
