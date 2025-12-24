<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Product;
use App\Models\MaterialInwardItem;
use App\Models\Production;
use App\Models\SalesInvoiceItem;
use App\Models\WorkOrderMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Display Raw Material Stock Report
     */
    public function rawMaterialStock(Request $request)
    {
        $query = RawMaterial::orderBy('raw_material_name');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('raw_material_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $rawMaterials = $query->get();

        // Calculate current stock for each raw material
        $stockData = [];
        foreach ($rawMaterials as $rawMaterial) {
            // Initial quantity is 0 (quantity_available field was removed)
            $initialQuantity = 0;

            // Calculate total received from Material Inward
            $totalReceived = MaterialInwardItem::where('raw_material_id', $rawMaterial->id)
                ->sum('quantity_received') ?? 0;

            // Calculate total consumed in Production (through WorkOrderMaterials)
            // Get work order IDs that have productions
            $workOrderIdsWithProduction = Production::distinct()
                ->pluck('work_order_id')
                ->filter()
                ->toArray();

            // Sum of consumption or material_required for work orders that have productions
            $totalConsumed = WorkOrderMaterial::whereIn('work_order_id', $workOrderIdsWithProduction)
                ->where('raw_material_id', $rawMaterial->id)
                ->get()
                ->sum(function($item) {
                    return $item->consumption ?? $item->material_required ?? 0;
                });

            // Current stock = Initial + Received - Consumed
            $currentStock = $initialQuantity + $totalReceived - $totalConsumed;

            $stockData[] = [
                'raw_material' => $rawMaterial,
                'initial_quantity' => $initialQuantity,
                'total_received' => $totalReceived,
                'total_consumed' => $totalConsumed,
                'current_stock' => max(0, $currentStock), // Ensure non-negative
                'reorder_level' => $rawMaterial->reorder_level ?? 0,
                'is_low_stock' => $currentStock <= ($rawMaterial->reorder_level ?? 0),
            ];
        }

        return view('stock.raw-material-stock', compact('stockData'));
    }

    /**
     * Display Finished Goods Stock Report
     * Shows only products that have production entries
     */
    public function finishedGoodsStock(Request $request)
    {
        // Get all production entries grouped by product_id with product relationship
        $productionsQuery = Production::select('product_id', 
                DB::raw('SUM(produced_quantity) as total_produced'), 
                DB::raw('COUNT(*) as production_count'))
            ->groupBy('product_id');

        // Search filter - filter by product name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $productionsQuery->whereHas('product', function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $productions = $productionsQuery->get();

        // Calculate current stock for each product
        $stockData = [];
        foreach ($productions as $production) {
            // Load product relationship
            $product = Product::find($production->product_id);
            
            if (!$product) {
                continue; // Skip if product not found
            }

            // Initial quantity is 0 (stock_quantity field was removed)
            $initialQuantity = 0;

            // Total produced from this production entry (already grouped)
            $totalProduced = (float) $production->total_produced;
            $productionCount = (int) $production->production_count;

            // Calculate total sold from Sales Invoices
            $totalSold = SalesInvoiceItem::where('product_id', $product->id)
                ->sum('quantity_sold') ?? 0;

            // Current stock = Initial + Produced - Sold
            $currentStock = $initialQuantity + $totalProduced - $totalSold;

            $stockData[] = [
                'product' => $product,
                'initial_quantity' => $initialQuantity,
                'total_produced' => $totalProduced,
                'production_count' => $productionCount,
                'total_sold' => $totalSold,
                'current_stock' => max(0, $currentStock), // Ensure non-negative
            ];
        }

        // Sort by product name
        usort($stockData, function($a, $b) {
            return strcmp($a['product']->product_name ?? '', $b['product']->product_name ?? '');
        });

        return view('stock.finished-goods-stock', compact('stockData'));
    }
}
