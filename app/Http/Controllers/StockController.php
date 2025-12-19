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
        $query = RawMaterial::where('is_active', true)->orderBy('raw_material_name');

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
            // Get initial quantity from raw_materials table
            $initialQuantity = $rawMaterial->quantity_available ?? 0;

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
     */
    public function finishedGoodsStock(Request $request)
    {
        $query = Product::where('is_active', true)->orderBy('product_name');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        // Calculate current stock for each product
        $stockData = [];
        foreach ($products as $product) {
            // Get initial quantity from products table
            $initialQuantity = $product->stock_quantity ?? 0;

            // Calculate total produced from Production
            $totalProduced = Production::where('product_id', $product->id)
                ->sum('produced_quantity') ?? 0;

            // Calculate total sold from Sales Invoices
            $totalSold = SalesInvoiceItem::where('product_id', $product->id)
                ->sum('quantity_sold') ?? 0;

            // Current stock = Initial + Produced - Sold
            $currentStock = $initialQuantity + $totalProduced - $totalSold;

            $stockData[] = [
                'product' => $product,
                'initial_quantity' => $initialQuantity,
                'total_produced' => $totalProduced,
                'total_sold' => $totalSold,
                'current_stock' => max(0, $currentStock), // Ensure non-negative
            ];
        }

        return view('stock.finished-goods-stock', compact('stockData'));
    }
}
