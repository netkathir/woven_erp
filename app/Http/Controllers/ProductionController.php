<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\WorkOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $query = Production::with(['workOrder', 'product'])->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('workOrder', function ($qw) use ($search) {
                    $qw->where('work_order_number', 'like', "%{$search}%");
                })->orWhereHas('product', function ($qp) use ($search) {
                    $qp->where('product_name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $productions = $query->paginate(15)->withQueryString();

        return view('transactions.productions.index', compact('productions'));
    }

    public function create(Request $request)
    {
        $workOrders = WorkOrder::orderByDesc('id')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();

        $selectedWorkOrderId = $request->get('work_order_id');
        $selectedWorkOrder = null;
        if ($selectedWorkOrderId) {
            $selectedWorkOrder = WorkOrder::with('product')->find($selectedWorkOrderId);
        }

        return view('transactions.productions.create', compact('workOrders', 'products', 'selectedWorkOrder'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $totals = $this->calculateTotals($data['produced_quantity'], $data['weight_per_unit']);

        $production = new Production();
        $production->fill([
            'work_order_id' => $data['work_order_id'] ?? null,
            'product_id' => $data['product_id'],
            'produced_quantity' => $data['produced_quantity'],
            'weight_per_unit' => $data['weight_per_unit'],
            'total_weight' => $totals['total_weight'],
            'remarks' => $data['remarks'] ?? null,
        ]);

        $user = Auth::user();
        if ($user) {
            $production->organization_id = $user->organization_id ?? null;
            $production->branch_id = session('active_branch_id');
            $production->created_by = $user->id;
        }

        $production->save();

        $this->incrementProductStock($production->product, $production->produced_quantity);

        return redirect()->route('productions.index')
            ->with('success', 'Production entry created successfully.');
    }

    public function show(Production $production)
    {
        $production->load(['workOrder', 'product']);
        return view('transactions.productions.show', compact('production'));
    }

    public function edit(Production $production)
    {
        $workOrders = WorkOrder::orderByDesc('id')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        $production->load(['workOrder', 'product']);
        return view('transactions.productions.edit', compact('production', 'workOrders', 'products'));
    }

    public function update(Request $request, Production $production)
    {
        $data = $this->validateRequest($request);

        // revert previous stock
        $this->decrementProductStock($production->product, $production->produced_quantity);

        $totals = $this->calculateTotals($data['produced_quantity'], $data['weight_per_unit']);

        $production->work_order_id = $data['work_order_id'] ?? null;
        $production->product_id = $data['product_id'];
        $production->produced_quantity = $data['produced_quantity'];
        $production->weight_per_unit = $data['weight_per_unit'];
        $production->total_weight = $totals['total_weight'];
        $production->remarks = $data['remarks'] ?? null;
        $production->save();

        // apply new stock
        $this->incrementProductStock($production->product, $production->produced_quantity);

        return redirect()->route('productions.index')
            ->with('success', 'Production entry updated successfully.');
    }

    public function destroy(Production $production)
    {
        // revert stock
        $this->decrementProductStock($production->product, $production->produced_quantity);

        $production->delete();

        return redirect()->route('productions.index')
            ->with('success', 'Production entry deleted successfully.');
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'work_order_id' => ['nullable', 'exists:work_orders,id'],
            'product_id' => ['required', 'exists:products,id'],
            'produced_quantity' => ['required', 'numeric', 'min:0.0001'],
            'weight_per_unit' => ['required', 'numeric', 'min:0.0001'],
            'remarks' => ['nullable', 'string'],
        ]);
    }

    protected function calculateTotals($producedQuantity, $weightPerUnit): array
    {
        $producedQuantity = (float) $producedQuantity;
        $weightPerUnit = (float) $weightPerUnit;

        $totalWeight = $producedQuantity * $weightPerUnit;

        return [
            'total_weight' => $totalWeight,
        ];
    }

    protected function incrementProductStock(?Product $product, float $qty): void
    {
        if (!$product || $qty == 0.0) {
            return;
        }

        $product->stock_quantity = ((float) $product->stock_quantity) + $qty;
        $product->save();
    }

    protected function decrementProductStock(?Product $product, float $qty): void
    {
        if (!$product || $qty == 0.0) {
            return;
        }

        $product->stock_quantity = ((float) $product->stock_quantity) - $qty;
        $product->save();
    }
}


